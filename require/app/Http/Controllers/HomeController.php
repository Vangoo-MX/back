<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\User;
use Illuminate\support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke(){

        if(Auth::check()){
            return redirect('overview/home');
        }


        return view('home');
    }
}
