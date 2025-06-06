<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Properties;
use App\Models\PropertiesQueue;
use App\Models\Developments;
use App\Models\Agenda;


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
