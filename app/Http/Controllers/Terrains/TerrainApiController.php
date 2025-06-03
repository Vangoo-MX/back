<?php

namespace App\Http\Controllers\Terrains;

use App\Http\Controllers\Controller;
use App\Models\Terrains;
use App\Models\TerrainsHighlights;
use App\Models\TerrainsQueue;
use App\Traits\Api\HandlesHighlights;
use App\Traits\Api\HandlesEstate;
use Illuminate\Http\Request;

class TerrainApiController extends Controller
{
    use HandlesHighlights, HandlesEstate;

    protected $model = Terrains::class;
    protected $highlightModel = TerrainsHighlights::class;
    protected $modelQueue = TerrainsQueue::class;
    protected $highlightRelationship = 'terrain';
    protected $directory = 'terrains';
    protected $basePath = 'postsqueue';

    public function getTerrainsHighlights(?int $municipioId = null)
    {
        return $this->getHighlitedItems($municipioId);
    }

    public function getTerrain($id)
    {
        return $this->getEstate($id);
    }

    public function getTerrainFavorites($id)
    {
        return $this->getEstateFavorites($id);
    }

    public function getTerrainQueue(int $id)
    {
        return $this->getEstateQueue($id);
    }

    public function getTerrainRelated(int $id)
    {
        return $this->getEstateRelated($id);
    }

    public function getTerrainsSearch(Request $request)
    {
        return $this->getEstateSearch($request);
    }

    public function getUserTerrains(int $id)
    {
        return $this->getUser($id);
    }

    public function getUserTerrainsQueue(int $id)
    {
        return $this->getUserQueue($id);
    }

    public function imagesUpload(Request $request)
    {
        return $this->uploadImages($request);
    }

    public function imagesDelete(Request $request)
    {
        return $this->deleteImages($request);
    }

    protected function imagesOrdering(Request $request, int $terrainId)
    {
        return $this->processImageOrdering($request, $terrainId);
    }

    public function deleteTerrainQueue(int $id)
    {
        return $this->deleteEstateQueue($id);
    }

    public function deleteTerrain(int $id)
    {
        return $this->deleteEstate($id);
    }

    /**
     * Inicio Store
     */

    public function storeTerrainQueue(Request $request)
    {
        $fieldMapping = [
            'propertyTitle' => 'title',
            'propertySellPrice' => 'price',
            'propertyIntNumber' => 'num_int',
            'propertyExtNumber' => 'num_ext',
            'propertyStreet' => 'street',
            'propertyColonia' => 'id_colonia',
            'propertyMunicipio' => 'id_municipio',
            'propertyEstado' => 'id_estado',
            'propertyCP' => 'cp',
            'propertyAreaTerrain' => 'area_terrain',
            'propertyParkings' => 'parkings',
            'propertyDescription' => 'description',
            'propertyMap' => 'map',
            'propertyMapLat' => 'map_lat',
            'propertyMapLong' => 'map_long',
            'propertyAgeConstruction' => 'antiquity',
            'propertyOperationType' => 'operation_type',
            'propertyAmountPriceBasedM2' => 'price_m2',
            'propertySellType' => 'sell_type',
            'propertyShareConditions' => 'share_conditions',
            'propertyServices' => 'services',
            'id_user' => 'id_user',
        ];

        $data = collect($fieldMapping)
            ->filter(fn($_, $requestKey) => $request->has($requestKey))
            ->mapWithKeys(fn($modelField, $requestKey) => [$modelField => $request->input($requestKey)])
            ->merge([
                'id_pais' => 1,
                'views' => 0,
                'no_exact_location' => $request->boolean('propertyExactLocation') ? 0 : 1,
            ])
            ->toArray();

        $terrain = new TerrainsQueue($data);
        $terrain->location = $this->getLocation(
            $request->input('propertyColonia'),
            $request->input('propertyMunicipio'),
            $request->input('propertyEstado')
        );
        $terrain->save();

        return response()->json(['id' => $terrain->id]);
    }

    /**
     * Inicio update
     */
    public function updateTerrainQueue(Request $request)
    {
        $terrain = TerrainsQueue::findOrFail($request->id);

        $fieldMapping = [
            'propertyTitle' => 'title',
            'propertySellPrice' => 'price',
            'propertyIntNumber' => 'num_int',
            'propertyExtNumber' => 'num_ext',
            'propertyStreet' => 'street',
            'propertyColonia' => 'id_colonia',
            'propertyMunicipio' => 'id_municipio',
            'propertyEstado' => 'id_estado',
            'propertyCP' => 'cp',
            'propertyAreaTerrain' => 'area_terrain',
            'propertyParkings' => 'parkings',
            'propertyDescription' => 'description',
            'propertyMap' => 'map',
            'propertyMapLat' => 'map_lat',
            'propertyMapLong' => 'map_long',
            'propertyAgeConstruction' => 'antiquity',
            'propertyOperationType' => 'operation_type',
            'propertyAmountPriceBasedM2' => 'price_m2',
            'propertySellType' => 'sell_type',
            'propertyShareConditions' => 'share_conditions',
            'propertyServices' => 'services',
        ];

        $data = collect($fieldMapping)
            ->filter(fn($modelField, $requestKey) => $request->has($requestKey))
            ->mapWithKeys(fn($modelField, $requestKey) => [$modelField => $request->input($requestKey)])
            ->merge([
                'id_pais' => 1,
                'no_exact_location' => $request->boolean('propertyExactLocation') ? 0 : 1,
            ])
            ->toArray();

        $terrain->fill($data);

        if ($request->has('status_aproved')) {
            $terrain->status_aproved = $request->status_aproved;
        }

        $terrain->location = $this->getLocation(
            $request->input('propertyColonia'),
            $request->input('propertyMunicipio'),
            $request->input('propertyEstado')
        );
        $terrain->save();

        return response()->json(['id' => $terrain->id]);
    }
}
