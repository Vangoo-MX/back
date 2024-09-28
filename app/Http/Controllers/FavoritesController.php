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
use App\Models\ListsUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FavoritesController extends Controller
{
    public function propertiesFavoritesUsuario($id)
    {
        $return = PropertiesFavorites::where('id_user', $id)
            ->get();

        return $return;
    }

    public function devFavoritesUsuario($id)
    {
        $return = DevelopmentsFavorites::where('id_user', $id)
            ->get();

        return $return;
    }

    public function lotFavoritesUsuario($id)
    {
        $return = LotsFavorites::where('id_user', $id)
            ->get();

        return $return;
    }

    public function allFavoritesUsuario($id)
    {
        $return = [];

        $properties = PropertiesFavorites::where('id_user', $id)
            ->get();
        $dev = DevelopmentsFavorites::where('id_user', $id)
            ->get();
        $lot = LotsFavorites::where('id_user', $id)
            ->get();

        if ($properties) {
            $return[0]['properties'] = $properties;
        } else {
            $return[0]['properties'] = [];
        }

        if ($dev) {
            $return[0]['developments'] = $dev;
        } else {
            $return[0]['developments'] = [];
        }

        if ($lot) {
            $return[0]['lots'] = $lot;
        } else {
            $return[0]['lots'] = [];
        }

        return $return;
    }

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

        $return = [];

        $return[0]['properties'] = $properties;
        $return[0]['developments'] = $dev;
        $return[0]['lots'] = $lot;

        return $return;
    }

    public function checkIfFav($id, $idproperty, $type)
    {

        if ($type == 'property') {
            $return = PropertiesFavorites::where('id_user', $id)
                ->where('id_property', $idproperty)
                ->where('id_list', NULL)
                ->get();
        } elseif ($type == 'development') {
            $return = DevelopmentsFavorites::where('id_user', $id)
                ->where('id_development', $idproperty)
                ->where('id_list', NULL)
                ->get();
        } else if ($type == 'lot') {
            $return = LotsFavorites::where('id_user', $id)
                ->where('id_lot', $idproperty)
                ->where('id_list', NULL)
                ->get();
        }

        if (sizeof($return) > 0) {
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
            ->orwhere('id_list', null)
            ->get();

        $devFav = DevelopmentsFavorites::where('id_user', $id)
            ->where('id_list', 0)
            ->orwhere('id_list', null)
            ->get();

        $lotFav = LotsFavorites::where('id_user', $id)
            ->where('id_list', 0)
            ->orwhere('id_list', null)
            ->get();

        $return = [];

        if (sizeof($propertiesFav) > 0) {
            $properties = Properties::selectRaw('id,title,price,location,rooms,parkings,type,bathrooms,area,area_terrain,description,views,images');
            foreach ($propertiesFav as $value) {
                $properties = $properties->orwhere('id', $value['id_property']);
            }
            $properties = $properties->get();
            $return[0]['properties'] = $properties;
        } else {
            $return[0]['properties'] = [];
        }

        if (sizeof($devFav) > 0) {
            $dev = Developments::selectRaw('id,status,title,price_min,price_max,location,description,views,images');
            foreach ($devFav as $value) {
                $dev = $dev->orwhere('id', $value['id_development']);
            }
            $dev = $dev->get();
            $return[0]['developments'] = $dev;
        } else {
            $return[0]['developments'] = [];
        }

        if (sizeof($lotFav) > 0) {
            $lot = Lots::selectRaw('id, title, status, type_lots, price_min, price_max, location, description, commission_percentage, images');
            foreach ($lotFav as $value) {
                $lot = $lot->orwhere('id', $value['id_lot']);
            }
            $lot = $lot->get();
            $return[0]['lots'] = $lot;
        } else {
            $return[0]['lots'] = [];
        }

        return $return;
    }

    public function postPropertiesFavUser(Request $request)
    {

        $fav = new PropertiesFavorites();
        $fav->id_user = $request->id_user;
        $fav->id_property = $request->id_property;

        $fav->save();

        return json_encode('success');
    }

    public function postDevFavUser(Request $request)
    {

        $fav = new DevelopmentsFavorites();
        $fav->id_user = $request->id_user;
        $fav->id_development = $request->id_dev;

        $fav->save();

        return json_encode('success');
    }

    public function postLotFavUser(Request $request)
    {
        $fav = new LotsFavorites();
        $fav->id_user = $request->id_user;
        $fav->id_lot = $request->id_lot;
        $fav->save();
        return json_encode('success');
    }

    public function deletePropertyFavUser($id_user, $id_property, $type_property)
    {

        if ($type_property == 'property') {
            $fav = PropertiesFavorites::where('id_user', $id_user)
                ->where('id_property', $id_property);
            $fav->delete();
        } elseif ($type_property == 'development') {
            $fav = DevelopmentsFavorites::where('id_user', $id_user)
                ->where('id_development', $id_property);
            $fav->delete();
        } else if ($type_property == 'lot') {
            $fav = LotsFavorites::where('id_user', $id_user)
                ->where('id_lot', $id_property);
            $fav->delete();
        }

        return json_encode('success');
    }

    public function listsUser($id)
    {

        $list = listsUser::where('id_user', $id)
            ->get();

        return $list;
    }

    public function deleteListUser($id_user, $id_list)
    {

        $fav = ListsUser::where('id_user', $id_user)->where('id', $id_list);
        $fav->delete();
        $propertiesFav = PropertiesFavorites::where('id_user', $id_user)->where('id_list', $id_list);
        $propertiesFav->delete();
        $devFav = DevelopmentsFavorites::where('id_user', $id_user)->where('id_list', $id_list);
        $devFav->delete();
        $lotFav = LotsFavorites::where('id_user', $id_user)->where('id_list', $id_list);
        $lotFav->delete();

        return json_encode('success');
    }

    public function propertiesFromList($id)
    {

        try {

            $propertiesFav = PropertiesFavorites::where('id_list', $id)->get();
            $devFav = DevelopmentsFavorites::where('id_list', $id)->get();
            $lotFav = LotsFavorites::where('id_list', $id)->get();
            $listdata = ListsUser::selectRaw('id,id_user,title,timestamp')
                ->where('id', $id)
                ->first();
            $id_user = $listdata['id_user'];
            $name_user = User::selectRaw('name')
                ->where('id', $id_user)
                ->first();

            $return = [];

            if ($listdata) {
                $return[0]['listdata'] = $listdata;
                $return[0]['listdata']['name_user'] = $name_user['name'];
            } else {
                $return[0]['listdata'] = [];
            }

            if (sizeof($propertiesFav) > 0) {
                $properties = Properties::selectRaw('id,title,price,location,rooms,parkings,type,bathrooms,area,area_terrain,description,commission_percentage,views,images');
                foreach ($propertiesFav as $value) {
                    $properties = $properties->orwhere('id', $value['id_property']);
                }
                $properties = $properties->get();
                $return[0]['properties'] = $properties;
            } else {
                $return[0]['properties'] = [];
            }

            if (sizeof($devFav) > 0) {
                $dev = Developments::selectRaw('id,status,title,price_min,price_max,location,description,commission_percentage,mode,views,images');
                foreach ($devFav as $value) {
                    $dev = $dev->orwhere('id', $value['id_development']);
                }
                $dev = $dev->get();
                $return[0]['developments'] = $dev;
            } else {
                $return[0]['developments'] = [];
            }

            if (sizeof($lotFav) > 0) {
                $lot = Lots::selectRaw('id, title, status, type_lots, price_min, price_max, location, description, commission_percentage, images');
                foreach ($lotFav as $value) {
                    $lot = $lot->orwhere('id', $value['id_lot']);
                }
                $lot = $lot->get();
                $return[0]['lots'] = $lot;
            } else {
                $return[0]['lots'] = [];
            }
        } catch (Exception $e) {
            return json_encode("error: " . $e->getMessage());
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
            if ($request->type == 'property') {

                $favu = PropertiesFavorites::where('id_user', $request->id_user)->where('id_property', $request->id_property)->where('id_list', $request->id_list)->first();

                if (!$favu) {
                    $fav = new PropertiesFavorites();
                    $fav->id_user = $request->id_user;
                    $fav->id_property = $request->id_property;
                    $fav->id_list = $request->id_list;
                    $fav->save();
                }
            } elseif ($request->type == 'development') {

                $favu = DevelopmentsFavorites::where('id_user', $request->id_user)->where('id_development', $request->id_development)->where('id_list', $request->id_list)->first();

                if (!$favu) {

                    $fav = new DevelopmentsFavorites();
                    $fav->id_user = $request->id_user;
                    $fav->id_development = $request->id_development;
                    $fav->id_list = $request->id_list;
                    $fav->save();
                }
            } elseif ($request->type == 'lot') {
                $favu = LotsFavorites::where('id_user', $request->id_user)
                    ->where('id_lot', $request->id_lot)
                    ->where('id_list', $request->id_list)
                    ->first();

                if (!$favu) {
                    $fav = new LotsFavorites();
                    $fav->id_user = $request->id_user;
                    $fav->id_lot = $request->id_lot;
                    $fav->id_list = $request->id_list;
                    $fav->save();
                }
            }
        } catch (Exception $e) {
            return json_encode("error: " . $e->getMessage());
        }


        return json_encode('success');
    }
}
