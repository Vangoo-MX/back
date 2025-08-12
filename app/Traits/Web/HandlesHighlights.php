<?php

namespace App\Traits\Web;

use App\Models\Municipios;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

trait HandlesHighlights
{
    public function indexHighlights($viewState)
    {
        $estates = $this->modelHighlights::with(['estado', 'municipio', $this->relationHighlight])
            ->orderBy('num_order')
            ->get();

        $municipios = Municipios::whereHas($this->relationMunicipio)
            ->get();

        return view($viewState, compact('estates', 'municipios'));
    }

    public function storeHighlight($request)
    {
        $config = $this->getHighlightConfig();

        $request->validate([
            'id_municipio' => [
                'required',
                'exists:info_municipios,id',
                Rule::prohibitedIf(function () use ($request) {
                    return $this->modelHighlights::where('id_municipio', $request->id_municipio)
                        ->count() >= 10;
                })
            ],
            $config['input_id'] => 'required|exists:post_properties,id'
        ], [
            'id_municipio.prohibited' => 'No se pueden agregar más de 10 registros para el mismo municipio.'
        ]);

        try {
            $this->modelHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                $config['field_id'] => $request->input($config['input_id']),
            ]);

            return redirect()->json(['success' => true, 'message' => 'Highlight creado exitosamente']);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }

    public function orderHightlight($request)
    {
        $config = $this->getHighlightConfig();

        try {
            $this->modelHighlights::where($config['field_id'], $request->id)
                ->firstOrFail()
                ->update(['num_order' => $request->num_order]);

            return redirect()->back()->with('success', 'Orden actualizado exitosamente');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Highlight para la propiedad ID ' . $request->id . ' no encontrado'
            ], 404);
        }
    }

    public function deleteHighlight($id)
    {
        $this->modelHighlights::destroy($id);
        return redirect()->back()->with('success', 'Highlight eliminado exitosamente');
    }

    public function estatesByMunicipio($id)
    {
        return response()
            ->json($this->model::where('id_municipio', $id)
                ->get());
    }
}
