<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('admin.index');
        }

        return view('home');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::validate($credentials)) {
            return redirect()->route('user.login.view')
                ->withErrors('Datos incorrectos');
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if (!$user || $user->status !== 1) {
            return redirect()->route('user.login.view')
                ->withErrors('Usuario no activo');
        }

        Auth::login($user);

        return $user->rol->value != 1
            ? redirect()->away('https://vangoo.mx')
            //? redirect()->route('admin.index')
            : $this->authenticated($request, $user);
    }

    public function authenticated(Request $request, $user)
    {
        //return redirect()->away('https://vangoo.mx');
        return redirect()->route('admin.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away('https://vangoo.mx');
    }
}
