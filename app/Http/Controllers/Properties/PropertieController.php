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
        return $this->indexEstate($viewEstate);
    }

    public function show($id)
    {
        $viewEstate = 'admin.properties.show';
        return $this->showEstate($id, $viewEstate);
    }

    public function edit($id)
    {
        $viewEstate = 'admin.properties.edit';
        return $this->editEstate($id, $viewEstate);
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
