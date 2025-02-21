<?php

namespace App\Http\Controllers;

use Illuminate\support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke()
    {

        if (Auth::check()) {
            return redirect('overview/home');
        }


        return view('home');
    }
}
