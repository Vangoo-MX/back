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

    public function email_confirm()
    {
        return view('emails.confirm');
    }

    public function email_template()
    {
        return view('emails.template');
    }
}
