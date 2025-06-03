<?php

namespace App\Http\Controllers\Apartments;

use App\Http\Controllers\Controller;
use App\Models\Apartments;
use App\Models\ApartmentsQueue;
use App\Models\ApartmentsHighlights;
use App\Traits\Api\HandlesHighlights;
use App\Traits\Api\HandlesEstate;
use App\Traits\Utility\HandlesImage;
use Illuminate\Http\Request;

class ApartmentApiController extends Controller
{
    use HandlesHighlights, HandlesEstate, HandlesImage;

    protected $model = Apartments::class;
    protected $highlightModel = ApartmentsHighlights::class;
    protected $modelQueue = ApartmentsQueue::class;
    protected $highlightRelationship = 'apartment';
    protected $directory = 'apartments';
    protected $basePath = 'postsqueue';

    public function getApartmentsHighlights(?int $municipioId = null)
    {
        return $this->getHighlitedItems($municipioId);
    }

    public function getApartment($id)
    {
        return $this->getEstate($id);
    }

    public function getApartmentFavorites($id)
    {
        return $this->getEstateFavorites($id);
    }

    public function getApartmentQueue(int $id)
    {
        return $this->getEstateQueue($id);
    }

    public function getApartmentRelated(int $id)
    {
        return $this->getEstateRelated($id);
    }

    public function getApartmentsSearch(Request $request)
    {
        return $this->getEstateSearch($request);
    }

    public function getUserApartments(int $id)
    {
        return $this->getUser($id);
    }

    public function getUserApartmentsQueue(int $id)
    {
        return $this->getUserQueue($id);
    }

    public function imagesUpload(Request $request, ApartmentsQueue $apartment)
    {
        return $this->handleImageProcessing($request, $apartment);
    }

    public function imagesDelete(ApartmentsQueue $apartment, $filename)
    {
        return $this->destroyImage($apartment, $filename);
    }

    protected function imagesOrdering(Request $request, ApartmentsQueue $apartment)
    {
        return $this->reorderEstateImages($request, $apartment);
    }

    public function deleteApartmentQueue(int $id)
    {
        return $this->deleteEstateQueue($id);
    }

    public function deleteApartment(int $id)
    {
        return $this->deleteEstate($id);
    }

    /**
     * Inicio Store
     */
    public function storeApartmentQueue(Request $request)
    {
        $fieldMapping = [
            'propertyTitle' => 'title',
            'propertySellPrice' => 'price',
            'propertyDescription' => 'description',
            'propertyFloor' => 'floor',
            'propertyRooms' => 'rooms',
            'propertyBathrooms' => 'bathrooms',
            'propertyParkings' => 'parkings',
            'propertyDevType' => 'dev_type',
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
            'propertyPriceMaintenance' => 'price_maintenance',
            'propertyOperationType' => 'operation_type',
            'propertyAmountPriceBasedM2' => 'price_m2',
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

        $apartment = new ApartmentsQueue($data);
        $apartment->location = $this->getLocation(
            $request->input('propertyColonia'),
            $request->input('propertyMunicipio'),
            $request->input('propertyEstado')
        );
        $apartment->save();

        return response()->json(['id' => $apartment->id]);
    }

    /**
     * Inicio update
     */
    public function updateApartmentQueue(Request $request)
    {
        $apartment = ApartmentsQueue::findOrFail($request->id);

        $fieldMapping = [
            'propertyTitle' => 'title',
            'propertySellPrice' => 'price',
            'propertyDescription' => 'description',
            'propertyFloor' => 'floor',
            'propertyRooms' => 'rooms',
            'propertyBathrooms' => 'bathrooms',
            'propertyParkings' => 'parkings',
            'propertyDevType' => 'dev_type',
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
            'propertyPriceMaintenance' => 'price_maintenance',
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

        $apartment->fill($data);

        if ($request->has('status_aproved')) {
            $apartment->status_aproved = $request->status_aproved;
        }

        $apartment->location = $this->getLocation(
            $request->input('propertyColonia'),
            $request->input('propertyMunicipio'),
            $request->input('propertyEstado')
        );
        $apartment->save();

        return response()->json(['id' => $apartment->id]);
    }
}
