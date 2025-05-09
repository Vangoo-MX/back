<?php

namespace App\Http\Controllers;

use Illuminate\support\Facades\Auth;

class HomeController extends Controller
{
    public function __invoke()
    {

        if (Auth::check()) {
            return redirect()->route('admin.index');
        }

        return redirect()->away('https://vangoo.mx');
    }
}
