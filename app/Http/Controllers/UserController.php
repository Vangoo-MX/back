<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\support\Facades\Auth;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        return redirect()->route('/', $user)->with('success', 'Usuario registrado correctamente.');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::validate($credentials)) {
            return redirect()->to('/')->withErrors('Datos incorrectos');
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if (!$user || $user->status !== 1) {
            return redirect()->to('/')->withErrors('Usuario no activo');
        }

        Auth::login($user);

        return $user->rol != 1
            ? redirect()->away('https://vangoo.mx')
            //? redirect()->route('admin.index')
            : $this->authenticated($request, $user);
    }

    public function authenticated(Request $request, $user)
    {
        return redirect()->away('https://vangoo.mx');
        //return redirect()->route('admin.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away('https://vangoo.mx');
    }

    public function statusUser($userid, $status)
    {
        try {
            $user = User::findOrFail($userid);

            $user->update(['status' => $status]);

            return redirect()->back();
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
