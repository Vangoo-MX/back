<?php

namespace App\Traits\Web;

use App\Models\Municipios;
use Illuminate\Support\Facades\Log;

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

        Log::debug('datos recibidos: ', $request->all());

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
}
