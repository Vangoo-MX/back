<?php

namespace App\Http\Controllers;

use App\Events\NotaAdministradorActualizada;
use App\Events\NotaVendedorActualizada;
use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\AgendaDocs;
use App\Models\Tickets;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class ContactsController extends Controller
{
    public function saveContactDocs(Request $request)
    {
        try {
            $agendaDocs = AgendaDocs::firstOrNew(['id_agenda' => $request->id]);
            $docsData = [
                'id_agenda' => $request->id,
                'contacto_cliente' => $request->has('contacto_cliente') ? 1 : 0,
                'papeleria' => $request->has('papeleria') ? 1 : 0,
                'visita_casas' => $request->has('visita_casas') ? 1 : 0,
                'seleccion_casa' => $request->has('seleccion_casa') ? 1 : 0,
                'seleccion_tipo_credito' => $request->has('seleccion_tipo_credito') ? 1 : 0,
                'ingreso_papeleria' => $request->has('ingreso_papeleria') ? 1 : 0,
                'respuesta_bancos' => $request->has('respuesta_bancos') ? 1 : 0,
                'seleccion_financiamiento' => $request->has('seleccion_financiamiento') ? 1 : 0,
                'firma_carta_promesa' => $request->has('firma_carta_promesa') ? 1 : 0,
                'firma_carta_comision' => $request->has('firma_carta_comision') ? 1 : 0,
                'seleccion_notaria' => $request->has('seleccion_notaria') ? 1 : 0,
                'solicitud_avaluo' => $request->has('solicitud_avaluo') ? 1 : 0,
                'revision_papeleria' => $request->has('revision_papeleria') ? 1 : 0,
                'ingreso_pre_preventivo' => $request->has('ingreso_pre_preventivo') ? 1 : 0,
                'recepcion_avaluo' => $request->has('recepcion_avaluo') ? 1 : 0,
                'recepcion_pre_preventivo' => $request->has('recepcion_pre_preventivo') ? 1 : 0,
                'ingreso_infonavit' => $request->has('ingreso_infonavit') ? 1 : 0,
                'recepcion_infonavit' => $request->has('recepcion_infonavit') ? 1 : 0,
                'cierre_numeros' => $request->has('cierre_numeros') ? 1 : 0,
                'realizacion_contratos' => $request->has('realizacion_contratos') ? 1 : 0,
                'autorizacion_contratos' => $request->has('autorizacion_contratos') ? 1 : 0,
                'firma_contrato' => $request->has('firma_contrato') ? 1 : 0,
                'pago_comision' => $request->has('pago_comision') ? 1 : 0,
                'nota_admin' => $request->nota_admin,
            ];

            $agendaDocs->fill($docsData)->save();

            if ($agendaDocs->wasChanged('nota_admin')) {
                event(new NotaAdministradorActualizada($agendaDocs));
            }

            return redirect()->route('admin.contacts');
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }
    }
}
