<?php

namespace App\Traits\Web;

trait HandlesEstate
{
    public function indexEstate($viewEstate)
    {
        $estates = $this->model::all();

        return view($viewEstate, compact('estates'));
    }

    public function showEstate($id, $viewEstate)
    {
        $estate = $this->model::with('municipio')->find($id);

        return view($viewEstate, compact('estate'));
    }
}
