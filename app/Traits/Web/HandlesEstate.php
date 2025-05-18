<?php

namespace App\Traits\Web;

trait HandlesEstate
{
    public function getEstate($viewEstate)
    {
        $estates = $this->model::all();

        return view($viewEstate, compact('estates'));
    }
}
