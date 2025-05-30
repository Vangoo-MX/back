<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\PropertiesHighlights;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    use HandlesEstate;

    protected $model = Properties::class;
    protected $highlightModel = PropertiesHighlights::class;
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

    public function update(Request $request, Properties $property)
    {
        $property->update($request->only([
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
            'map_lat',
            'map_long',
            'amenities',
            'sell_type',
            'share_conditions',
            'antiquity'
        ]));

        $this->handleImageProcessing($request, $property);

        $property->update([
            'location' => $this->getLocation($request->id_colonia, $request->id_municipio, $request->id_estado),
        ]);

        return redirect()->route('properties.show', $property)
            ->with('success', 'Propiedad actualizada correctamente');
    }

    public function destroy($id)
    {
        return $this->deleteEstate($id);
    }

    public function reorderImages(Request $request, Properties $property)
    {
        return $this->reorderEstateImages($request, $property);
    }

    public function deleteImage(Properties $property, $filename)
    {
        return $this->destroyImage($property, $filename);
    }

    public function deactive($id)
    {
        return $this->deactiveEstate($id);
    }

    public function active($id)
    {
        return $this->activeEstate($id);
    }
}
