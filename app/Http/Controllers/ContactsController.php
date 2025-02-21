<?php

namespace App\Http\Controllers;

use App\Events\NotaAdministradorActualizada;
use App\Events\NotaVendedorActualizada;
use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\AgendaDocs;
use App\Models\Tickets;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class ContactsController extends Controller
{
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
            $agendaDocs = AgendaDocs::findOrFail($idDocs);
            $agendaDocs->nota_vendedor = trim($note);
            if ($agendaDocs->isDirty('nota_vendedor')) {
                $agendaDocs->save();

                $agenda = $agendaDocs->agendaRelation;
                if ($agenda) {
                    $agenda->mensaje_leido = false;
                    $agenda->save();
                }

                event(new NotaVendedorActualizada($agendaDocs));
            }
            return json_encode($agendaDocs);
        } catch (Exception $e) {
            return json_encode('error: ' . $e);
        }
    }

    public function saveContactDocs(Request $request)
    {
        try {
            $agendaDocs = AgendaDocs::firstOrNew(['id_agenda' => $request->id]);
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

            $agendaDocs->fill($docsData)->save();

            if ($agendaDocs->wasChanged('nota_admin')) {
                event(new NotaAdministradorActualizada($agendaDocs));
            }

            return redirect()->route('admin.contacts');
        } catch (Exception $e) {
            return json_encode($e->getMessage());
        }
    }

    public function marcarComoLeido($id_agenda)
    {
        $agenda = Agenda::findOrFail($id_agenda);
        $agenda->mensaje_leido = true;
        $agenda->save();

        return redirect()->route('admin.contacts');
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
}
