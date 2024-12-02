<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\PropertiesFavorites;
use App\Models\DevelopmentsFavorites;
use App\Models\Developments;
use App\Models\Lots;
use App\Models\LotsFavorites;
use App\Models\Apartments;
use App\Models\ApartmentsFavorites;
use App\Models\Terrains;
use App\Models\TerrainsFavorites;
use App\Models\ListsUser;
use App\Models\User;

class FavoritesController extends Controller
{
    public function allFavoritesUsuarioData($id)
    {

        $propertiesFav = PropertiesFavorites::where('id_user', $id)
            ->where('id_list', null)
            ->get();
        $devFav = DevelopmentsFavorites::where('id_user', $id)
            ->where('id_list', null)
            ->get();
        $lotFav = LotsFavorites::where('id_user', $id)
            ->where('id_list', null)
            ->get();
        $apartmentFav = ApartmentsFavorites::where('id_user', $id)
            ->where('id_list', null)
            ->get();
        $terrainFav = TerrainsFavorites::where('id_user', $id)
            ->where('id_list', null)
            ->get();

        if (sizeof($propertiesFav) > 0) {
            $properties = Properties::selectRaw('id,title,price,location,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images');
            foreach ($propertiesFav as $value) {
                $properties = $properties->orwhere('id', $value['id_property']);
            }
            $properties = $properties->get();
        } else {
            $properties = [];
        }

        if (sizeof($devFav) > 0) {
            $dev = Developments::selectRaw('id,status,title,price_min,price_max,location,description,views,images');
            foreach ($devFav as $value) {
                $dev = $dev->orwhere('id', $value['id_development']);
            }
            $dev = $dev->get();
        } else {
            $dev = [];
        }
        if (sizeof($lotFav) > 0) {
            $lot = Lots::selectRaw('id, title, status, type_lots, price_min, price_max, location, description, commission_percentage, images');
            foreach ($lotFav as $value) {
                $lot = $lot->orwhere('id', $value['id_lot']);
            }
            $lot = $lot->get();
        } else {
            $lot = [];
        }
        if (sizeof($apartmentFav) > 0) {
            $apartment = Apartments::selectRaw('id, title, status, type_apartment, price_min, price_max, location, description, commission_percentage, images');
            foreach ($apartmentFav as $value) {
                $apartment = $apartment->orwhere('id', $value['id_property']);
            }
            $apartment = $apartment->get();
        } else {
            $apartment = [];
        }
        if (sizeof($terrainFav) > 0) {
            $terrain = Terrains::selectRaw('id, title, status, type_terrain, price_min, price_max, location, description, commission_percentage, images');
            foreach ($terrainFav as $value) {
                $terrain = $terrain->orwhere('id', $value['id_terrain']);
            }
            $terrain = $terrain->get();
        } else {
            $terrain = [];
        }

        $return = [];

        $return[0]['properties'] = $properties;
        $return[0]['developments'] = $dev;
        $return[0]['lots'] = $lot;
        $return[0]['apartments'] = $apartment;
        $return[0]['terrains'] = $terrain;

        return $return;
    }

    public function checkIfFav($id, $idproperty, $type)
    {
        switch ($type) {
            case 'property':
                $model = PropertiesFavorites::class;
                $id_type = 'id_property';
                break;
            case 'development':
                $model = DevelopmentsFavorites::class;
                $id_type = 'id_development';
                break;
            case 'lot':
                $model = LotsFavorites::class;
                $id_type = 'id_lot';
                break;
            case 'apartment':
                $model = ApartmentsFavorites::class;
                $id_type = 'id_property';
                break;
            case 'terrain':
                $model = TerrainsFavorites::class;
                $id_type = 'id_property';
                break;
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        $return = $model::where('id_user', $id)
            ->where($id_type, $idproperty)
            ->where('id_list', NULL)
            ->get();

        if (!$return->isEmpty()) {
            $return = 1;
        } else {
            $return = 0;
        }

        return $return;
    }

    public function allFavNoListUser($id)
    {

        $propertiesFav = PropertiesFavorites::where('id_user', $id)
            ->where('id_list', 0)
            ->orWhere('id_list', null)
            ->get();

        $devFav = DevelopmentsFavorites::where('id_user', $id)
            ->where('id_list', 0)
            ->orWhere('id_list', null)
            ->get();

        $lotFav = LotsFavorites::where('id_user', $id)
            ->where('id_list', 0)
            ->orWhere('id_list', null)
            ->get();

        $apartmentFav = ApartmentsFavorites::where('id_user', $id)
            ->where('id_list', 0)
            ->orWhere('id_list', null)
            ->get();
        $terrainFav = TerrainsFavorites::where('id_user', $id)
            ->where('id_list', 0)
            ->orWhere('id_list', null)
            ->get();

        $return = [];

        if (sizeof($propertiesFav) > 0) {
            $properties = Properties::selectRaw('id,title,price,location,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images');
            foreach ($propertiesFav as $value) {
                $properties = $properties->orWhere('id', $value['id_property']);
            }
            $properties = $properties->get();
            $return[0]['properties'] = $properties;
        } else {
            $return[0]['properties'] = [];
        }

        if (sizeof($devFav) > 0) {
            $dev = Developments::selectRaw('id,status,title,price_min,price_max,location,description,views,images');
            foreach ($devFav as $value) {
                $dev = $dev->orWhere('id', $value['id_development']);
            }
            $dev = $dev->get();
            $return[0]['developments'] = $dev;
        } else {
            $return[0]['developments'] = [];
        }

        if (sizeof($lotFav) > 0) {
            $lot = Lots::selectRaw('id, title, status, type_lots, price_min, price_max, location, description, commission_percentage, images');
            foreach ($lotFav as $value) {
                $lot = $lot->orWhere('id', $value['id_lot']);
            }
            $lot = $lot->get();
            $return[0]['lots'] = $lot;
        } else {
            $return[0]['lots'] = [];
        }

        if (sizeof($apartmentFav) > 0) {
            $apartment = Apartments::selectRaw('id, title, status, type_apartment, price, location, description, commission_percentage, images');
            foreach ($apartmentFav as $value) {
                $apartment = $apartment->orWhere('id', $value['id_property']);
            }
            $apartment = $apartment->get();
            $return[0]['apartments'] = $apartment;
        } else {
            $return[0]['apartments'] = [];
        }

        if (sizeof($terrainFav) > 0) {
            $terrain = Terrains::selectRaw('id, title, status, type_terrain, price, location, description, commission_percentage, images');
            foreach ($terrainFav as $value) {
                $terrain = $terrain->orWhere('id', $value['id_terrain']);
            }
            $terrain = $terrain->get();
            $return[0]['terrains'] = $terrain;
        } else {
            $return[0]['terrains'] = [];
        }

        return $return;
    }

    public function postPropertiesFavUser(Request $request)
    {

        $fav = new PropertiesFavorites();
        $fav->id_user = $request->id_user;
        $fav->id_property = $request->id_property;

        $fav->save();

        return response()->json(['message' => 'success']);
    }

    public function postDevFavUser(Request $request)
    {

        $fav = new DevelopmentsFavorites();
        $fav->id_user = $request->id_user;
        $fav->id_development = $request->id_dev;

        $fav->save();

        return response()->json(['message' => 'success']);
    }

    public function postLotFavUser(Request $request)
    {
        $fav = new LotsFavorites();
        $fav->id_user = $request->id_user;
        $fav->id_lot = $request->id_lot;
        $fav->save();
        return response()->json(['message' => 'success']);
    }

    public function postApartmentFavUser(Request $request)
    {
        $fav = new ApartmentsFavorites();
        $fav->id_user = $request->id_user;
        $fav->id_apartment = $request->id_apartment;
        $fav->save();
        return response()->json(['message' => 'success']);
    }

    public function postTerrainFavUser(Request $request)
    {
        $fav = new TerrainsFavorites();
        $fav->id_user = $request->id_user;
        $fav->id_terrain = $request->id_terrain;
        $fav->save();
        return response()->json(['message' => 'success']);
    }

    public function deletePropertyFavUser($id_list, $id_property, $type_property)
    {
        if (empty($id_list) || empty($id_property)) {
            return response()->json(['error' => 'Invalid parameters'], 400);
        }

        switch ($type_property) {
            case 'property':
                $model = PropertiesFavorites::class;
                $id_type = 'id_property';
                break;
            case 'development':
                $model = DevelopmentsFavorites::class;
                $id_type = 'id_development';
                break;
            case 'lot':
                $model = LotsFavorites::class;
                $id_type = 'id_lot';
                break;
            case 'apartment':
                $model = ApartmentsFavorites::class;
                $id_type = 'id_property';
                break;
            case 'terrain':
                $model = TerrainsFavorites::class;
                $id_type = 'id_property';
                break;
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }

        $fav = $model::where('id_list', $id_list)
            ->where($id_type, $id_property)
            ->first();

        if ($fav) {
            $fav->delete();
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['error' => 'Favorite not found'], 404);
        }
    }

    public function listsUser($id)
    {

        $list = listsUser::where('id_user', $id)
            ->get();

        return $list;
    }

    public function deleteListUser($id_user, $id_list)
    {
        $models = [
            ListsUser::class,
            PropertiesFavorites::class,
            DevelopmentsFavorites::class,
            LotsFavorites::class,
            ApartmentsFavorites::class,
            TerrainsFavorites::class,
        ];

        foreach ($models as $model) {
            $model::where('id_user', $id_user)
                ->where('id_list', $id_list)
                ->delete();
        }

        return response()->json(['status' => 'success']);
    }

    public function propertiesFromList($id)
    {
        $listdata = ListsUser::select('id', 'id_user', 'title', 'timestamp')
            ->find($id);

        if (!$listdata) {
            return response()->json(['error' => 'Lista no encontrada']);
        }

        $name_user = User::select('name')->find($listdata->id_user);

        if (!$name_user) {
            return response()->json(['error' => 'Usuario no encontrado']);
        }

        $return = [
            [
                'listdata' => array_merge(
                    $listdata->toArray(),
                    ['name_user' => $name_user->name]
                )
            ]
        ];

        $entities = [
            'properties' => [
                'model' => Properties::class,
                'relation' => PropertiesFavorites::class,
                'column' => 'id_property',
                'select' => 'id, title, price, location, rooms, parkings, type, bathrooms, area, area_terrain, description, commission_percentage, views, images'
            ],
            'developments' => [
                'model' => Developments::class,
                'relation' => DevelopmentsFavorites::class,
                'column' => 'id_development',
                'select' => 'id, status, title, price_min, price_max, location, description, commission_percentage, mode, views, images'
            ],
            'lots' => [
                'model' => Lots::class,
                'relation' => LotsFavorites::class,
                'column' => 'id_lot',
                'select' => 'id, title, status, type_lots, price_min, price_max, location, description, slope, lots_min, lots_max, type_terrain, initial_fee, price_mt2, commission_percentage, images'
            ],
            'apartments' => [
                'model' => Apartments::class,
                'relation' => ApartmentsFavorites::class,
                'column' => 'id_property',
                'select' => 'id, title, status, price, location, description, commission_percentage, views, images'
            ],
            'terrains' => [
                'model' => Terrains::class,
                'relation' => TerrainsFavorites::class,
                'column' => 'id_property',
                'select' => 'id, title, status, area_terrain, price, location, description, commission_percentage, views, images'
            ]
        ];

        foreach ($entities as $key => $entity) {
            $favoriteIds = $entity['relation']::where('id_list', $id)->pluck($entity['column'])->toArray();

            $data = $entity['model']::selectRaw($entity['select'])
                ->whereIn('id', $favoriteIds)
                ->get();

            $return[0][$key] = $data;
        }

        return $return;
    }

    public function createListUser(Request $request)
    {

        $fav = new ListsUser();
        $fav->id_user = $request->id_user;
        $fav->title = $request->title;

        $fav->save();

        return json_encode('success');
    }

    public function savePropertyInList(Request $request)
    {

        try {
            switch ($request->type) {
                case 'property':
                    $model = PropertiesFavorites::class;
                    $id_type = 'id_property';
                    break;
                case 'development':
                    $model = DevelopmentsFavorites::class;
                    $id_type = 'id_development';
                    break;
                case 'lot':
                    $model = LotsFavorites::class;
                    $id_type = 'id_lot';
                    break;
                case 'apartment':
                    $model = ApartmentsFavorites::class;
                    $id_type = 'id_property';
                    break;
                case 'terrain':
                    $model = TerrainsFavorites::class;
                    $id_type = 'id_property';
                    break;
                default:
                    return response()->json(['error' => 'Invalid type'], 400);
            }

            $favu = $model::where('id_user', $request->id_user)
                ->where($id_type, $request->id_property)
                ->where('id_list', $request->id_list)
                ->first();

            if (!$favu) {
                $fav = new $model();
                $fav->id_user = $request->id_user;
                $fav->$id_type = $request->id_property;
                $fav->id_list = $request->id_list;
                $fav->save();
            } else {
                return json_encode('error: Ya existe en la lista');
            }
        } catch (Exception $e) {
            return json_encode("error: " . $e->getMessage());
        }


        return json_encode('success');
    }
}
