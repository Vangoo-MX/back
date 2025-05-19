<?php

namespace App\Traits\Web;

use App\Models\Municipios;

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
        try {
            $this->modelHighlights::create([
                'id_estado' => 19,
                'id_municipio' => $request->id_municipio,
                $this->fieldID => $request->$this->inputID,
            ]);

            return redirect()->back()->with('success', 'Highlight creado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el highlight: ' . $e->getMessage()]);
        }
    }
}
