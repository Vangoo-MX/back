<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->rol->value !== 1) {
            return redirect()->route('user.login.view')->withErrors('No tienes permiso para acceder a esta página.');
        }
        return $next($request);
    }
}
