<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Models\Developments;
use App\Models\DevelopmentsHighlights;
use App\Models\DevelopmentsApartments;
use App\Models\Agenda;
use App\Models\Estados;
use App\Models\Municipios;
use App\Models\DevelopmentsHorizontalApartments;
use App\Models\DevelopmentsHorizontalHighlights;
use App\Models\DevelopmentsHorizontals;
use App\Models\Lots;
use App\Models\LotsHighlights;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.admin');
    }

    //index
    public function index()
    {
        $data = [
            'activePropertiesCount' => Properties::count(),
            'pendingPropertiesCount' => PropertiesQueue::where('status_aproved', '!=', 2)->count(),
            'activeDevelopmentsCount' => Developments::count(),
            'properties' => Properties::all(),
        ];

        return view('admin.index', $data);
    }

    //users

    public function contacts(Request $request)
    {
        $selectedUserID = $request->input('user_id', Auth::id());

        $agenda = Agenda::join('app_users', 'list_agenda.id_user', '=', 'app_users.id')
            ->where('list_agenda.id_user', $selectedUserID)
            ->select('list_agenda.*', 'app_users.name as user_name')
            ->get();

        $users = User::whereIn('rol', [1, 2, 3, 4])->pluck('name', 'id');

        return view('admin.contacts', compact('agenda', 'users', 'selectedUserID'));
    }

    //developments

    public function editdev($id)
    {
        $municipios = Municipios::where('id_estado', 19)->get();
        $dev = Developments::findOrFail($id);
        $app = DevelopmentsApartments::where('id_development', $id)->get();

        return response()->view('admin.editdev', compact('municipios', 'dev', 'app'))
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
            ]);
    }

    public function highlightsdev()
    {
        $devshl = DevelopmentsHighlights::all();
        $estados = Estados::all();
        $municipios = Municipios::all();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightsdev', compact('devshl', 'estados', 'municipios', 'municipiosh'));
    }

    //development horizontals

    public function developmentsHorizontal(Request $request)
    {
        $selectedMode = $request->input('mode', 'all');

        $desarrollos = $selectedMode === 'all'
            ? DevelopmentsHorizontals::all()
            : DevelopmentsHorizontals::where('mode', $selectedMode)->get();

        return view('admin.developmentsHorizontal', compact('desarrollos', 'selectedMode'));
    }

    public function createdevHorizontal()
    {
        $municipios = Municipios::where('id_estado', 19)->get();

        return view('admin.createdevHorizontal', compact('municipios'));
    }

    public function editdevHorizontal($id)
    {
        $municipios = Municipios::where('id_estado', 19)->get();
        $dev = DevelopmentsHorizontals::findOrFail($id);
        $app = DevelopmentsHorizontalApartments::where('id_development', $id)->get();

        return response()->view('admin.editdevHorizontal', compact('municipios', 'dev', 'app'))
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
            ]);
    }

    public function highlightsdevHorizontal()
    {
        $devshl = DevelopmentsHorizontalHighlights::all();
        $estados = Estados::all();
        $municipios = Municipios::all();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightsdevHorizontal', compact('devshl', 'estados', 'municipios', 'municipiosh'));
    }

    //lots
    public function lots()
    {
        $lots = Lots::all();

        return view('admin.lots', compact('lots'));
    }

    public function createLot()
    {
        $municipios = Municipios::where('id_estado', 19)->get();

        return view('admin.createlot', compact('municipios'));
    }

    public function editLotPage($id)
    {
        $municipios = Municipios::where('id_estado', 19)->get();
        $lot = Lots::findOrFail($id);

        return response()
            ->view('admin.editlot', compact('municipios', 'lot'))
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
            ]);
    }

    public function highlightsLot()
    {
        $lotshl = LotsHighlights::all();
        $estados = Estados::all();
        $municipios = Municipios::all();
        $municipiosh = Municipios::where('highlight', 1)->get();

        return view('admin.highlightslots', compact('lotshl', 'estados', 'municipios', 'municipiosh'));
    }

    //various
    public function files()
    {
        return view('admin.files');
    }

    public function statistics()
    {
        return view('admin.statistics');
    }

    public function settingsinfo()
    {
        return view('admin.settingsinfo');
    }

    public function email_confirm()
    {
        return view('emails.confirm');
    }

    public function email_template()
    {
        return view('emails.template');
    }
}
