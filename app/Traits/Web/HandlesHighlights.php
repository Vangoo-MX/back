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

        $municipios = Municipios::whereHas('propertiesHighlights')
            ->get();

        return view($viewState, compact('estates', 'municipios'));
    }
}
