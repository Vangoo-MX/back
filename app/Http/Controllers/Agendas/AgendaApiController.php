<?php

namespace App\Http\Controllers\Agendas;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class AgendaApiController extends Controller
{
    public function index(): Collection
    {
        return Agenda::all();
    }

    public function byUser(int $userId): Collection
    {
        return Agenda::where('id_user', $userId)->get();
    }

    public function show(Agenda $agenda): Agenda
    {
        return $agenda;
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $agendaData = [
                'id_user' => $request->id_user,
                'name'    => $request->name
            ];

            $optionalFields = [
                'phone' => 'phone',
                'email' => 'email',
                'address' => 'address',
                'credit_score' => 'credit_score',
                'notas' => 'notes'
            ];

            foreach ($optionalFields as $requestKey => $modelField) {
                if ($request->has($requestKey)) {
                    $agendaData[$modelField] = $request->{$requestKey};
                }
            }

            $agenda = Agenda::create($agendaData);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $agenda
        ]);
    }

    public function update(Request $request, Agenda $agenda): JsonResponse
    {
        try {
            $fieldMappings = [
                'name' => ['field' => 'name', 'check_null' => false],
                'phone' => ['field' => 'phone', 'check_null' => false],
                'email' => ['field' => 'email', 'check_null' => true],
                'address' => ['field' => 'address', 'check_null' => true],
                'credit_score' => ['field' => 'credit_score', 'check_null' => true],
                'notas' => ['field' => 'notes', 'check_null' => true],
            ];

            $updateData = [];

            foreach ($fieldMappings as $requestKey => $config) {
                $value = $request->input($requestKey);

                if ($config['check_null'] && $value === 'null') {
                    $value = null;
                }

                $updateData[$config['field']] = $value;
            }

            $agenda->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $agenda->fresh()
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Agenda $agenda): JsonResponse
    {
        try {
            $agenda->delete();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
