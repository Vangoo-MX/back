<?php

namespace App\Http\Controllers\Terrains;

use App\Http\Controllers\Controller;
use App\Models\Terrains;
use App\Models\TerrainsHighlights;
use App\Traits\Web\HandlesEstate;
use Illuminate\Http\Request;

class TerrainController extends Controller
{
    use HandlesEstate;

    protected $model = Terrains::class;
    protected $highlightModel = TerrainsHighlights::class;
    protected $directory = 'terrains';

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
            'location' => $this->getLocation($terrain),
            'images' => $terrain->images + ($request->hasFile('images') ? count($request->file('images')) : 0)
        ]);

        return redirect()->route('terrains.show', $terrain)->with('success', __('Terrain updated successfully.'));
    }

    public function destroy($id)
    {
        return $this->deleteEstate($id);
    }

    public function destroyImage($terrainId, $imageId)
    {
        $terrain = Terrains::findOrFail($terrainId);

        if ($this->deleteImage($terrain, $imageId)) {
            return redirect()->back()->with('success', 'Imagen eliminada correctamente');
        }

        return redirect()->back()->with('error', 'Imagen no encontrada');
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
