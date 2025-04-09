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
    public function getNameUser($id)
    {
        $user = User::where('id', $id)->value('name');
        return response()->json(['name' => $user]);
    }

    public function getAllInfoUser($id)
    {
        // if (!Auth::check() || Auth::user()->id != $id) {
        //     return response()->json(['error' => 'No autorizado.'], 403);
        // }

        $user = DB::table('app_users')
            ->join('app_roles', 'app_users.rol', '=', 'app_roles.id')
            ->where('app_users.id', $id)
            ->select([
                'app_users.id',
                'app_users.name',
                'app_users.email',
                'app_users.tel',
                'app_users.contact_preference',
                'app_users.contact_schedule',
                'app_users.biography',
                'app_users.profile_image',
                'app_users.created_at',
                'app_roles.title',
            ])
            ->first();

        return response()->json($user);
    }


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

    /* endpoints frontend */

    public function checkAuthEP()
    {
        if (Auth::check()) {
            return json_encode(Auth::user());
        }

        return json_encode('error');
    }

    public function loginEP(LoginRequest $request)
    {
        if (Auth::check()) {
            return response()->json(['status' => 'already_logged_in']);
        }

        $credentials = $request->validated();

        if (!Auth::validate($credentials)) {
            return response()->json(['status' => 'invalid_credentials']);
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if ($user && $user->status == 1) {
            Auth::login($user);
            return response()->json(Auth::user());
        }

        return response()->json(['status' => 'inactive_user']);
    }


    public function logoutEP(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['status' => 'success']);
    }

    public function registerEP(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        return response()->json(['status' => 'success']);
    }

    public function updateUserEP(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->update($request->only([
            'email',
            'tel',
            'contact_preference',
            'contact_schedule',
            'biography'
        ]));

        return response()->json(['status' => 'success']);
    }

    public function updateUserEPp2(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($request->hasFile('profile_image')) {
                $filename = $request->id . '.' . $request->profile_image->extension();
                $request->profile_image->storeAs('public/img/users', $filename);
                $user->profile_image = $filename;
            }

            $user->update([
                'name' => $request->name
            ]);

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
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
