<?php

namespace App\Traits\Web;

use App\Models\Municipios;

trait HandlesEstate
{
    public function indexEstate($viewEstate)
    {
        $estates = $this->model::all();

        return view($viewEstate, compact('estates'));
    }

    public function showEstate($id, $viewEstate)
    {
        $estate = $this->model::findOrFail($id);

        return view($viewEstate, compact('estate'));
    }

    public function editEstate($id, $viewEstate)
    {
        $estate = $this->model::findOrFail($id);
        $municipios = Municipios::where('id_estado', 19)->get();

        return view($viewEstate, compact('estate', 'municipios'));
    }
}
