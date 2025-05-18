<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;

class PropertieController extends Controller
{
    use HandlesEstate;

    protected $model = Properties::class;

    public function index()
    {
        $viewEstate = 'admin.properties.index';
        return $this->getEstate($viewEstate);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
