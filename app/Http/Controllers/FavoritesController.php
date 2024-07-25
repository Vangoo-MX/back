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
use App\Models\Agenda;
use App\Models\AgendaDocs;
use App\Models\Tickets;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactAgentMail;

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
            $lot = Lots::selectRaw('id,status,title,price_min,price_max,location,description,views,images');
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
            $lot = Lots::selectRaw('id,status,title,price_min,price_max,location,description,views,images');
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
                $dev = Developments::selectRaw('id,status,title,price_min,price_max,location,description,mode,views,images');
                foreach ($devFav as $value) {
                    $dev = $dev->orwhere('id', $value['id_development']);
                }
                $dev = $dev->get();
                $return[0]['developments'] = $dev;
            } else {
                $return[0]['developments'] = [];
            }

            if (sizeof($lotFav) > 0) {
                $lot = Lots::selectRaw('id,title,price,location,area,area_terrain,description,views,images');
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



    public function getAgendaUser($id)
    {
        $return = Agenda::where('id_user', $id)
            ->get();

        return $return;
    }

    public function getAgenda($id)
    {
        $return = Agenda::where('id', $id)
            ->get();

        return $return;
    }

    public function saveAgendaUser(Request $request)
    {
        try {
            $agenda = new Agenda();
            $agenda->id_user = $request->id_user;
            $agenda->name = $request->name;

            if ($request->has('phone')) {
                $agenda->phone = $request->phone;
            }

            if ($request->has('email')) {
                $agenda->email = $request->email;
            }

            if ($request->has('address')) {
                $agenda->address = $request->address;
            }

            if ($request->has('credit_score')) {
                $agenda->credit_score = $request->credit_score;
            }

            if ($request->has('notas')) {
                $agenda->notes = $request->notas;
            }

            // if ($request->has('id_list')) {
            //     $agenda->id_list = $request->id_list;
            // }

            $agenda->save();
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }

        return json_encode($request);
    }

    public function saveAgendaDocsNotes($idDocs, $note)
    {
        try {
            $agenda = AgendaDocs::findOrFail($idDocs);

            $agenda->nota_vendedor = $note;
            $agenda->save();

            return json_encode($agenda);
        } catch (Exception $e) {
            return json_encode('error: ' . $e);
        }
    }

    public function saveContactDocs(Request $request)
    {
        try {
            $docsData = [
                'id_agenda' => $request->id,
                'contacto_cliente' => $request->has('contacto_cliente') ? 1 : 0,
                'papeleria' => $request->has('papeleria') ? 1 : 0,
                'visita_casas' => $request->has('visita_casas') ? 1 : 0,
                'seleccion_casa' => $request->has('seleccion_casa') ? 1 : 0,
                'seleccion_tipo_credito' => $request->has('seleccion_tipo_credito') ? 1 : 0,
                'ingreso_papeleria' => $request->has('ingreso_papeleria') ? 1 : 0,
                'respuesta_bancos' => $request->has('respuesta_bancos') ? 1 : 0,
                'seleccion_financiamiento' => $request->has('seleccion_financiamiento') ? 1 : 0,
                'firma_carta_promesa' => $request->has('firma_carta_promesa') ? 1 : 0,
                'firma_carta_comision' => $request->has('firma_carta_comision') ? 1 : 0,
                'seleccion_notaria' => $request->has('seleccion_notaria') ? 1 : 0,
                'solicitud_avaluo' => $request->has('solicitud_avaluo') ? 1 : 0,
                'revision_papeleria' => $request->has('revision_papeleria') ? 1 : 0,
                'ingreso_pre_preventivo' => $request->has('ingreso_pre_preventivo') ? 1 : 0,
                'recepcion_avaluo' => $request->has('recepcion_avaluo') ? 1 : 0,
                'recepcion_pre_preventivo' => $request->has('recepcion_pre_preventivo') ? 1 : 0,
                'ingreso_infonavit' => $request->has('ingreso_infonavit') ? 1 : 0,
                'recepcion_infonavit' => $request->has('recepcion_infonavit') ? 1 : 0,
                'cierre_numeros' => $request->has('cierre_numeros') ? 1 : 0,
                'realizacion_contratos' => $request->has('realizacion_contratos') ? 1 : 0,
                'autorizacion_contratos' => $request->has('autorizacion_contratos') ? 1 : 0,
                'firma_contrato' => $request->has('firma_contrato') ? 1 : 0,
                'pago_comision' => $request->has('pago_comision') ? 1 : 0,
                'nota_admin' => $request->nota_admin,
            ];

            AgendaDocs::updateOrInsert(
                ['id_agenda' => $request->id],
                $docsData
            );

            return redirect()->route('admin.contacts');
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function statusContact($id, $etapa)
    {
        try {
            $agenda = Agenda::findOrFail($id);

            $agenda->etapa = $etapa;
            $agenda->save();

            return redirect()->back();
        } catch (Exception $e) {
            return json_encode('error: ' . $e);
        }
    }

    public function getDocsAgenda($id)
    {
        $return = AgendaDocs::where('id_agenda', $id)
            ->get();

        return $return;
    }

    public function deleteAgendaUser($id)
    {
        $agenda = Agenda::find($id);

        if ($agenda) {
            $agenda->delete();
            return json_encode('success');
        } else {
            return json_encode('error: Agenda entry not found');
        }
    }

    public function updateAgendaUser(Request $request)
    {
        $agenda = Agenda::find($request->id);

        if ($agenda) {
            $agenda->name = $request->name;
            $agenda->phone = $request->phone;
            $agenda->email = $request->email === 'null' ? null : $request->email;
            $agenda->address = $request->address === 'null' ? null : $request->address;
            $agenda->credit_score = $request->credit_score === 'null' ? null : $request->credit_score;
            $agenda->notes = $request->notas === 'null' ? null : $request->notas;
            // $agenda->id_list = $request->id_list;
            $agenda->save();

            return json_encode('success');
        } else {
            return json_encode('error: Agenda entry not found');
        }
    }

    public function saveTicket(Request $request)
    {
        try {
            $tickets = new Tickets();
            $tickets->enviar = $request->enviar;
            $tickets->asunto = $request->asunto;
            $tickets->estado = $request->estado;
            $tickets->mensaje = $request->mensaje;
            $tickets->id_user = $request->id_user;
            $tickets->save();
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }

        return json_encode($request);
    }

    public function getTicket($id)
    {
        $return = Tickets::where('id', $id)
            ->get();

        return $return;
    }

    public function getTicketsUser($id)
    {
        $user = User::where('id', $id)->first();

        if ($user->rol == 1) {
            $return = DB::table('list_tickets')
                ->where('id_user', '!=', $id)
                ->join('app_users', 'list_tickets.id_user', '=', 'app_users.id')
                ->select('list_tickets.*', 'app_users.name as user_name')
                ->get();
        } else {
            $return = DB::table('list_tickets')
                ->where(function ($query) use ($id) {
                    $query->where('enviar', 'LIKE', '%' . $id . '%')
                        ->orWhere('enviar', 'LIKE', '%todos%');
                })
                ->orWhere('id_user', $id)
                ->join('app_users', 'list_tickets.id_user', '=', 'app_users.id')
                ->select('list_tickets.*', 'app_users.name as user_name')
                ->get();
        }

        return $return;
    }


    public function editStatusTicket(Request $request)
    {
        $ticket = Tickets::find($request->id_ticket);
        $ticket->estado = $request->status;
        $ticket->save();
        return json_encode('success');
    }


    public function getTicketsSendUser($id)
    {
        $return = Tickets::where('id_user', $id)->get();
        return $return;
    }


    public function deleteTicket($id)
    {
        $agenda = Tickets::find($id);

        if ($agenda) {
            $agenda->delete();
            return json_encode('success');
        } else {
            return json_encode('error: Ticket entry not found');
        }
    }

    public function contactAgent(Request $request)
    {
        Mail::to('contacto@vangoo.mx')->send(new ContactAgentMail($request->all()));
        return response()->json(['message' => 'Correo enviado con éxito'], 200);
    }
}
