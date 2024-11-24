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
use App\Models\ListsUser;
use App\Models\User;

class FavoritesController extends Controller
{
    public function allFavoritesUsuarioData($id)
    {
        $favoritesModels = [
            'properties' => [
                'favoritesModel' => PropertiesFavorites::class,
                'mainModel' => Properties::class,
                'key' => 'id_property',
                'select' => 'id,title,price,location,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images',
            ],
            'developments' => [
                'favoritesModel' => DevelopmentsFavorites::class,
                'mainModel' => Developments::class,
                'key' => 'id_development',
                'select' => 'id,status,title,price_min,price_max,location,description,views,images',
            ],
            'lots' => [
                'favoritesModel' => LotsFavorites::class,
                'mainModel' => Lots::class,
                'key' => 'id_lot',
                'select' => 'id,title,status,type_lots,price_min,price_max,location,description,commission_percentage,images',
            ],
            'apartments' => [
                'favoritesModel' => ApartmentsFavorites::class,
                'mainModel' => Apartments::class,
                'key' => 'id_property',
                'select' => 'id,title,status,type_apartment,price_min,price_max,location,description,commission_percentage,images',
            ],
        ];

        $result = [];

        foreach ($favoritesModels as $key => $data) {
            $favorites = $data['favoritesModel']::where('id_user', $id)
                ->where('id_list', null)
                ->pluck($data['key']);

            $result[$key] = $favorites->isNotEmpty()
                ? $data['mainModel']::selectRaw($data['select'])->whereIn('id', $favorites)->get()
                : [];
        }

        return [$result];
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
        $favoritesModels = [
            'properties' => [
                'favoritesModel' => PropertiesFavorites::class,
                'mainModel' => Properties::class,
                'key' => 'id_property',
                'select' => 'id,title,price,location,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images',
            ],
            'developments' => [
                'favoritesModel' => DevelopmentsFavorites::class,
                'mainModel' => Developments::class,
                'key' => 'id_development',
                'select' => 'id,status,title,price_min,price_max,location,description,views,images',
            ],
            'lots' => [
                'favoritesModel' => LotsFavorites::class,
                'mainModel' => Lots::class,
                'key' => 'id_lot',
                'select' => 'id,title,status,type_lots,price_min,price_max,location,description,commission_percentage,images',
            ],
            'apartments' => [
                'favoritesModel' => ApartmentsFavorites::class,
                'mainModel' => Apartments::class,
                'key' => 'id_property',
                'select' => 'id,title,status,type_apartment,price_min,price_max,location,description,commission_percentage,images',
            ],
        ];

        $result = [];

        foreach ($favoritesModels as $key => $data) {
            $favorites = $data['favoritesModel']::where('id_user', $id)
                ->where(function ($query) {
                    $query->where('id_list', 0)
                        ->orWhereNull('id_list');
                })
                ->pluck($data['key']);

            $result[$key] = $favorites->isNotEmpty()
                ? $data['mainModel']::selectRaw($data['select'])->whereIn('id', $favorites)->get()
                : [];
        }

        return [$result];
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
        $favoritesModels = [
            ListsUser::class,
            PropertiesFavorites::class,
            DevelopmentsFavorites::class,
            LotsFavorites::class,
            ApartmentsFavorites::class,
        ];

        foreach ($favoritesModels as $model) {
            $model::where('id_user', $id_user)
                ->where('id_list', $id_list)
                ->delete();
        }

        return response()->json('success');
    }

    public function propertiesFromList($id)
    {
        try {
            $listData = ListsUser::select('id', 'id_user', 'title', 'timestamp')
                ->find($id);

            if (!$listData) {
                return response()->json(["error" => "Lista no encontrada"], 404);
            }

            $user = User::select('name')->find($listData->id_user);

            if (!$user) {
                return response()->json(["error" => "Usuario no encontrado"], 404);
            }

            $return = [
                'listdata' => array_merge(
                    $listData->toArray(),
                    ['name_user' => $user->name]
                ),
            ];

            $favorites = [
                'properties' => [
                    'model' => Properties::class,
                    'favorites' => PropertiesFavorites::where('id_list', $id)->pluck('id_property')->toArray(),
                    'columns' => 'id, title, price, location, rooms, parkings, type, bathrooms, area, area_terrain, description, commission_percentage, views, images',
                ],
                'developments' => [
                    'model' => Developments::class,
                    'favorites' => DevelopmentsFavorites::where('id_list', $id)->pluck('id_development')->toArray(),
                    'columns' => 'id, status, title, price_min, price_max, location, description, commission_percentage, mode, views, images',
                ],
                'lots' => [
                    'model' => Lots::class,
                    'favorites' => LotsFavorites::where('id_list', $id)->pluck('id_lot')->toArray(),
                    'columns' => 'id, title, status, type_lots, price_min, price_max, location, description, slope, lots_min, lots_max, type_terrain, initial_fee, price_mt2, commission_percentage, images',
                ],
                'apartments' => [
                    'model' => Apartments::class,
                    'favorites' => ApartmentsFavorites::where('id_list', $id)->pluck('id_property')->toArray(),
                    'columns' => 'id, title, status, price, location, description, commission_percentage, views, images',
                ],
            ];

            foreach ($favorites as $key => $data) {
                $model = $data['model']::selectRaw($data['columns']);
                $return[$key] = !empty($data['favorites'])
                    ? $model->whereIn('id', $data['favorites'])->get()
                    : [];
            }

            return $return;
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
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
