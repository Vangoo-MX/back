<?php

namespace App\Http\Controllers\Apartments;

use App\Http\Controllers\Controller;
use App\Traits\Web\HandlesEstate;
use App\Models\ApartmentsHighlights;
use App\Models\Apartments;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    use HandlesEstate;
    protected $model = Apartments::class;
    protected $highlightModel = ApartmentsHighlights::class;
    protected $directory = 'apartments';

    public function index()
    {
        $viewEstate = 'admin.apartments.index';
        return $this->indexEstate($viewEstate);
    }

    public function show($id)
    {
        $viewEstate = 'admin.apartments.show';
        return $this->showEstate($id, $viewEstate);
    }

    public function edit($id)
    {
        $viewEstate = 'admin.apartments.edit';
        return $this->editEstate($id, $viewEstate);
    }

    public function update(Request $request, Apartments $apartment)
    {
        $apartment->update($request->only([
            'title',
            'operation_type',
            'price',
            'price_maintenance',
            'description',
            'rooms',
            'bathrooms',
            'parkings',
            'floor',
            'dev_type',
            'map',
            'area',
            'id_municipio',
            'id_colonia',
            'street',
            'num_ext',
            'num_int',
            'cp',
            'map_lat',
            'map_long',
            'amenities',
            'sell_type',
            'share_conditions',
            'antiquity',
        ]));

        $this->handleImageProcessing($request, $apartment);

        $apartment->update([
            'location' => $this->getLocation($request->id_colonia, $request->id_municipio, $request->id_estado),
        ]);

        return redirect()->route('apartments.show', $apartment)->with('success', ('Departamento actualizado correctamente'));
    }

    public function destroy($id)
    {
        return $this->deleteEstate($id);
    }

    public function reorderImages(Request $request, Apartments $apartment)
    {
        return $this->reorderEstateImages($request, $apartment);
    }

    public function deleteImage(Apartments $apartments, $filename)
    {
        return $this->destroyImage($apartments, $filename);
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
