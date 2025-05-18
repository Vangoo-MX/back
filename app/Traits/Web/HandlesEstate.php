<?php

namespace App\Traits\Web;

trait HandlesEstate
{
    public function getEstate($viewEstate)
    {
        $Estate = $this->model::all();

        return view($viewEstate, compact('Estate'));
    }
}
