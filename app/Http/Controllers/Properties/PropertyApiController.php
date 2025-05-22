<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Properties;
use App\Models\PropertiesHighlights;
use App\Models\PropertiesQueue;
use App\Traits\Api\HandlesHighlights;
use App\Traits\Api\HandlesEstate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropertyApiController extends Controller
{

    use HandlesHighlights, HandlesEstate;

    protected $model = Properties::class;
    protected $highlightModel = PropertiesHighlights::class;
    protected $modelQueue = PropertiesQueue::class;
    protected $highlightRelationship = 'property';
    protected $directory = 'properties';

    public function getPropertiesHighlights(?int $municipioId = null)
    {
        return $this->getHighlitedItems($municipioId);
    }

    public function getProperty($id)
    {
        return $this->getEstate($id);
    }

    public function getPropertyFavorites($id)
    {
        return $this->getEstateFavorites($id);
    }

    public function getPropertyQueue(int $id)
    {
        return $this->getEstateQueue($id);
    }

    public function getPropertyRelated(int $id)
    {
        return $this->getEstateRelated($id);
    }

    public function getPropertiesSearch(Request $request)
    {
        return $this->getEstateSearch($request);
    }

    public function getUserProperties(int $id)
    {
        return $this->getUser($id);
    }

    public function getUserPropertiesQueue(int $id)
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

    protected function imagesOrdering(Request $request, int $id)
    {
        return $this->processImageOrdering($request, $id);
    }

    public function deletePropertyQueue(int $id)
    {
        return $this->deleteEstateQueue($id);
    }

    public function deleteProperty(int $id)
    {
        return $this->deleteEstate($id);
    }

    /**
     * Inicio Store
     */

    public function storePropertyQueue(Request $request)
    {
        Log::info('Storing property queue', ['request' => $request->all()]);
        $fieldMapping = [
            'propertyTitle' => 'title',
            'propertySellPrice' => 'price',
            'propertyDescription' => 'description',
            'propertyRooms' => 'rooms',
            'propertyBathrooms' => 'bathrooms',
            'propertyParkings' => 'parkings',
            'propertyMap' => 'map',
            'propertyAreaConstruction' => 'area',
            'propertyColonia' => 'id_colonia',
            'propertyMunicipio' => 'id_municipio',
            'propertyEstado' => 'id_estado',
            'propertyStreet' => 'street',
            'propertyIntNumber' => 'num_int',
            'propertyExtNumber' => 'num_ext',
            'propertyCP' => 'cp',
            'propertyMapLat' => 'map_lat',
            'propertyMapLong' => 'map_long',
            'propertyAmenities' => 'amenities',
            'propertySellType' => 'sell_type',
            'propertyShareConditions' => 'share_conditions',
            'propertyAgeConstruction' => 'antiquity',
            'propertyOperationType' => 'operation_type',
            'propertyAmountPriceBasedM2' => 'price_m2',
            'number_images' => 'images',
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

        $property = new PropertiesQueue($data);
        $property->location = $this->getLocationFromRelations($property);
        Log::info('Property data', $property->toArray());
        $property->save();

        return response()->json(['id' => $property->id]);
    }

    protected function getLocationFromRelations(PropertiesQueue $property): string
    {
        return collect([
            $property->colonia->nombre ?? null,
            $property->municipio->nombre ?? null,
            $property->estado->nombre ?? null
        ])
            ->filter()
            ->join(', ');
    }

    /**
     * Inicio update
     */
    public function updatePropertyQueue(Request $request)
    {
        Log::info('update property queue', ['request' => $request->all()]);
        $property = PropertiesQueue::findOrFail($request->id);

        $fieldMapping = [
            'propertyTitle' => 'title',
            'propertySellPrice' => 'price',
            'propertyDescription' => 'description',
            'propertyRooms' => 'rooms',
            'propertyBathrooms' => 'bathrooms',
            'propertyParkings' => 'parkings',
            'propertyMap' => 'map',
            'propertyAreaConstruction' => 'area',
            'propertyColonia' => 'id_colonia',
            'propertyMunicipio' => 'id_municipio',
            'propertyEstado' => 'id_estado',
            'propertyStreet' => 'street',
            'propertyIntNumber' => 'num_int',
            'propertyExtNumber' => 'num_ext',
            'propertyCP' => 'cp',
            'propertyMapLat' => 'map_lat',
            'propertyMapLong' => 'map_long',
            'propertyAmenities' => 'amenities',
            'propertySellType' => 'sell_type',
            'propertyShareConditions' => 'share_conditions',
            'propertyAgeConstruction' => 'antiquity',
            'propertyLocation' => 'location',
            'propertyOperationType' => 'operation_type',
            'propertyAmountPriceBasedM2' => 'price_m2',
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

        $property->fill($data);

        Log::info('Number_images', $request->number_images);
        if ($request->has('number_images')) {
            $property->images += $request->number_images;
            Log::info('images', $property->toArray());
        }

        if ($request->has('status_aproved')) {
            $property->status_aproved = $request->status_aproved;
        }

        $property->location = $this->getLocationFromRelations($property);
        $property->save();
        $this->imagesOrdering($request, $property->id);

        return response()->json(['id' => $property->id]);
    }
}
