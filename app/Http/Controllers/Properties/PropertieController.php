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
    protected $directory = 'properties';

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

    public function update(Request $request, Properties $propiedad)
    {
        $propiedad->update($request->only([
            'title',
            'operation_type',
            'price',
            'description',
            'rooms',
            'bathrooms',
            'parkings',
            'map',
            'area',
            'id_municipio',
            'id_colonia',
            'street',
            'num_ext',
            'num_int',
            'cp',
            'amenities',
            'sell_type',
            'share_conditions',
            'antiquity'
        ]));

        $propiedad->update([
            'location' => $this->getPropertyLocation($propiedad),
            'images' => $propiedad->images + ($request->hasFile('images') ? count($request->file('images')) : 0)
        ]);

        $this->handleImageProcessing($request, $propiedad);

        return redirect()->route('properties.show', $propiedad)
            ->with('success', 'Propiedad actualizada correctamente');
    }

    public function destroy(string $id)
    {
        //
    }
}
