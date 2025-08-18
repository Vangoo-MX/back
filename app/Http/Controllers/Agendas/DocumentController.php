<?php

namespace App\Http\Controllers\Agendas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use App\Models\AgendaDocs;
use App\Events\NotaAdministradorActualizada;

class DocumentController extends Controller
{
    public function getDocuments(Agenda $agenda): Collection
    {
        return $agenda->agendaDocs;
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:list_agenda,id',
            'nota_admin' => 'nullable|string|max:1000',
        ]);

        $booleanFields = [
            'contacto_cliente',
            'papeleria',
            'visita_casas',
            'seleccion_casa',
            'seleccion_tipo_credito',
            'ingreso_papeleria',
            'respuesta_bancos',
            'seleccion_financiamiento',
            'firma_carta_promesa',
            'firma_carta_comision',
            'seleccion_notaria',
            'solicitud_avaluo',
            'revision_papeleria',
            'ingreso_pre_preventivo',
            'recepcion_avaluo',
            'recepcion_pre_preventivo',
            'ingreso_infonavit',
            'recepcion_infonavit',
            'cierre_numeros',
            'realizacion_contratos',
            'autorizacion_contratos',
            'firma_contrato',
            'pago_comision'
        ];

        $docsData = ['id_agenda' => $validatedData['id']];

        foreach ($booleanFields as $field) {
            $docsData[$field] = $request->has($field) ? 1 : 0;
        }

        $docsData['nota_admin'] = $validatedData['nota_admin'];

        $agendaDocs = AgendaDocs::firstOrNew(['id_agenda' => $validatedData['id']]);
        $notaAnterior = $agendaDocs->nota_admin;

        $agendaDocs->fill($docsData)->save();

        if ($agendaDocs->nota_admin !== $notaAnterior && !empty($agendaDocs->nota_admin)) {
            event(new NotaAdministradorActualizada($agendaDocs));
        }

        return redirect()->route('agenda.index')
            ->with('success', 'Documentos actualizados exitosamente.');
    }
}
