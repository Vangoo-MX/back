<?php

namespace App\Http\Controllers\Terrains;

use App\Http\Controllers\Controller;
use App\Models\Terrains;
use App\Models\TerrainsHighlights;
use App\Traits\Web\HandlesEstate;
use App\Traits\Utility\HandlesImage;
use Illuminate\Http\Request;

class TerrainController extends Controller
{
    use HandlesEstate, HandlesImage;

    protected $model = Terrains::class;
    protected $highlightModel = TerrainsHighlights::class;
    protected $directory = 'terrains';
    protected $basePath = 'posts';
    protected $idHighlight = 'id_property';

    public function index()
    {
        $viewEstate = 'admin.terrains.index';
        return $this->indexEstate($viewEstate);
    }

    public function show($id)
    {
        $viewEstate = 'admin.terrains.show';
        return $this->showEstate($id, $viewEstate);
    }

    public function edit($id)
    {
        $viewEstate = 'admin.terrains.edit';
        return $this->editEstate($id, $viewEstate);
    }

    public function update(Request $request, Terrains $terrain)
    {
        $terrain->update($request->only([
            'title',
            'operation_type',
            'price',
            'description',
            'parking',
            'map',
            'id_municipio',
            'id_colonia',
            'street',
            'num_ext',
            'num_int',
            'cp',
            'map_lat',
            'map_long',
            'services',
            'sell_type',
            'share_conditions',
            'antiquity',
            'area_terrain',
            'price_m2',
        ]));

        $this->handleImageProcessing($request, $terrain);

        $terrain->update([
            'location' => $this->getLocation(
                $request->id_colonia,
                $request->id_municipio,
                $request->id_estado
            ),
        ]);

        return redirect()->route('terrains.show', $terrain)->with('success', 'Terreno actualizado correctamente');
    }

    public function destroy($id)
    {
        return $this->deleteEstate($id);
    }

    public function reorderImages(Request $request, Terrains $terrain)
    {
        return $this->reorderEstateImages($request, $terrain);
    }

    public function deleteImage(Terrains $terrain, $filename)
    {
        return $this->destroyImage($terrain, $filename);
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
