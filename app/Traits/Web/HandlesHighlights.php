<?php

namespace App\Traits\Web;

use App\Models\Municipios;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HandlesHighlights
{
    public function indexHighlights($viewState)
    {
        $estates = $this->modelHighlights::with(['estado', 'municipio', 'property'])
            ->orderBy('num_order')
            ->get();

        $municipios = Municipios::whereHas('propertiesHighlight')
            ->get();

        return view($viewState, compact('estates', 'municipios'));
    }

    public function storeHighlight($request)
    {
        $config = $this->getHighlightConfig();

        try {
            $validated = $request->validate([
                'id_municipio' => 'required|exists:info_municipios,id',
                $config['input_id'] => 'required|exists:post_properties,id'
            ]);

            $this->modelHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $validated['id_municipio'],
                $config['field_id'] => $validated[$config['input_id']],
            ]);

            return redirect()->back()->with('success', 'Highlight creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el highlight: ' . $e->getMessage()]);
        }
    }

    public function orderHightlight($request)
    {
        $config = $this->getHighlightConfig();

        $validated = $request->validate([
            'id' => 'required|integer|exists:post_properties_highlights,id_property',
            'num_order' => 'required|integer'
        ]);

        try {
            $this->modelHighlights::where($config['field_id'], $validated['id'])
                ->firstOrFail()
                ->update(['num_order' => $validated['num_order']]);

            return redirect()->route('admin.highlights.properties');
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Highlight para la propiedad ID ' . $validated['id'] . ' no encontrado'
            ], 404);
        }
    }
}
