<?php

namespace App\Http\Controllers\Lots;

use App\Http\Controllers\Controller;
use App\Models\Lots;
use App\Models\LotsHighlights;
use App\Traits\Utility\HandlesImage;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LotController extends Controller
{
    use HandlesEstate, HandlesImage;
    protected $model = Lots::class;
    protected $highlightModel = LotsHighlights::class;
    protected $directory = 'lots';
    protected $basePath = 'posts';
    protected $idHighlight = 'id_lot';

    public function index()
    {
        $viewEstate = 'admin.lots.index';
        return $this->indexEstate($viewEstate);
    }

    public function create()
    {
        $viewEstate = 'admin.lots.create';
        return $this->createEstate($viewEstate);
    }

    public function store(Request $request)
    {
        $lot = Lots::create([
            'title' => $request->title,
            'type_lots' => $request->type_lots,
            'developers' => $request->developers,
            'status' => $request->status,
            'number_lots' => $request->number_lots,
            'lots_min' => $request->lots_min,
            'lots_max' => $request->lots_max,
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'description' => $request->description,
            'availability' => $request->availability,
            'financing' => $request->financing,
            'type_terrain' => $request->type_terrain,
            'slope' => $request->slope,
            'id_pais' => $request->id_pais,
            'id_estado' => $request->id_estado,
            'id_municipio' => $request->id_municipio,
            'id_colonia' => $request->id_colonia,
            'location' => $this->getLocation(
                $request->id_colonia,
                $request->id_municipio,
                $request->id_estado
            ),
            'num_ext' => $request->num_ext,
            'cp' => $request->cp,
            'map_lat' => $request->map_lat,
            'map_long' => $request->map_long,
            'broad' => $request->broad,
            'largue' => $request->largue,
            'price_mt2' => $request->price_mt2,
            'amenities' => $request->amenities,
            'initial_fee' => $request->initial_fee,
            'commission_percentage' => $request->commission_percentage,
            'id_user' => Auth::user()->id,
        ]);

        $this->handleImageProcessing($request, $lot);

        return redirect()->route('lots.index')->with('success', 'Lote creado exitosamente.');
    }

    public function edit($id)
    {
        $viewEstate = 'admin.lots.edit';
        return $this->editEstate($id, $viewEstate);
    }

    public function update(Request $request, $id)
    {
        $lot = Lots::findOrFail($id);

        $updateData = $request->only([
            'title',
            'type_lots',
            'developers',
            'status',
            'number_lots',
            'lots_min',
            'lots_max',
            'price_min',
            'price_max',
            'description',
            'availability',
            'financing',
            'type_terrain',
            'slope',
            'id_pais',
            'id_estado',
            'id_municipio',
            'id_colonia',
            'num_ext',
            'cp',
            'map_lat',
            'map_long',
            'broad',
            'largue',
            'price_mt2',
            'amenities',
            'initial_fee',
            'commission_percentage'
        ]);

        $this->handleImageProcessing($request, $lot);

        $updateData['location'] = $this->getLocation(
            $request->id_colonia,
            $request->id_municipio,
            $request->id_estado
        );

        $lot->update($updateData);

        return redirect()->route('lots.index')->with('success', __('messages.lot_updated'));
    }

    public function destroy($id)
    {
        return $this->deleteEstate($id);
    }

    public function reorderImages(Request $request, Lots $lot)
    {
        return $this->reorderEstateImages($request, $lot);
    }

    public function deleteImage(Lots $lot, $filename)
    {
        return $this->destroyImage($lot, $filename);
    }
}
