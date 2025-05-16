<?php

namespace App\Http\Controllers\Agendas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgendaDocs;
use Illuminate\Http\JsonResponse;
use App\Events\NotaVendedorActualizada;
use Exception;

class DocumentApiController extends Controller
{
    public function getDocuments(int $agendaId): JsonResponse
    {
        $docs = AgendaDocs::where('id_agenda', $agendaId)->get();

        return response()->json([
            'success' => true,
            'data' => $docs
        ]);
    }

    public function updateNotes(Request $request, AgendaDocs $agendaDoc): JsonResponse
    {
        $validated = $request->validate([
            'nota_vendedor' => 'required|string|max:500'
        ]);

        try {
            $nota = trim($validated['nota_vendedor']);
            $agendaDoc->nota_vendedor = $nota;

            if ($agendaDoc->isDirty('nota_vendedor')) {

                $agendaDoc->save();

                $agendaDoc->agendaRelation()->update([
                    'mensaje_leido' => false,
                    'timestamp_update' => now()
                ]);

                event(new NotaVendedorActualizada($agendaDoc));
            }

            return response()->json([
                'success' => true,
                'data' => $agendaDoc->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
