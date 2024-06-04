<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\support\Facades\Session;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Str;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use App\Mail\BePartnerContactMail;

class UserController extends Controller
{
    public function getNameUser($id)
    {
        $user = User::selectRaw('name')->where('id', $id)->get();
        return json_encode($user);
    }

    public function getAllInfoUser($id)
    {
        $user = DB::table('app_users')->join('app_roles', 'app_users.rol', '=', 'app_roles.id')->where('app_users.id', $id)->select('app_users.id', 'app_users.name', 'app_users.email', 'app_users.tel', 'app_users.contact_preference', 'app_users.contact_schedule', 'app_users.biography', 'app_users.profile_image', 'app_users.created_at', 'app_roles.title')->get();
        return $user;
    }


    public function register(RegisterRequest $request)
    {

        $user = User::create($request->validated());
        return redirect()->route('/', $user);
    }


    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::validate($credentials)) {
            return redirect()->to('/')->withErrors('Datos incorrectos');
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if ($user && $user->status == 1) {
            Auth::login($user);
            if ($user->rol != 1) {
                return redirect()->away('https://vangoo.mx');
            }
            return $this->authenticated($request, $user);
        } else {
            return redirect()->to('/')->withErrors('Usuario no activo');
        }
    }


    public function authenticated(Request $request, $user)
    {
        if (Auth::user()->rol != 1 || Auth::user()->rol != 2) {
            return redirect('https://vangoo.mx');
        } else {
            return redirect('https://vangoo.mx');
        }
    }

    public function logout()
    {
        //Session::flush();
        Auth::logout();

        return redirect('https://vangoo.mx');
    }

    /* endpoints frontend */

    public function checkAuthEP()
    {
        if (Auth::check()) {
            return json_encode(Auth::user());
        } else {
            return json_encode('error');
        }
    }

    public function loginEP(LoginRequest $request)
    {
        if (Auth::check()) {
            return json_encode('alreadylogin');
        }
        $credentials = $request->validated();

        if (!Auth::validate($credentials)) {
            return json_encode('error');
        }
        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        Auth::login($user);

        if ($user && $user->status == 1) {
            Auth::login($user);
            return json_encode(Auth::user());
        } else {
            return json_encode('inactiveuser');
        }
    }


    public function logoutEP()
    {
        //Session::flush();
        Auth::logout();

        return json_encode('success');
    }

    public function registerEP(RegisterRequest $request)
    {

        $user = User::create($request->validated());
        return json_encode('success');
    }

    public function updateUserEP(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->email = $request->email;
        $user->tel = $request->tel;
        $user->contact_preference = $request->contact_preference;
        $user->contact_schedule = $request->contact_schedule;
        $user->biography = $request->biography;
        $user->save();

        return json_encode('success');
    }

    public function updateUserEPp2(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($request->hasFile('profile_image')) {
                $userId = $request->id;
                $filename = $userId . "." . $request->profile_image->extension();
                $request->profile_image->storeAs('public/img/users', $filename);
                $user->profile_image = $filename;
            }

            $user->name = $request->name;
            $user->save();

            return json_encode('success');
        } catch (Exception $e) {
            return json_encode('error: ' . $e);
        }
    }

    public function statusUser($userid, $status)
    {
        try {
            $user = User::findOrFail($userid);

            $user->status = $status;
            $user->save();

            return redirect()->back();
        } catch (Exception $e) {
            return json_encode('error: ' . $e);
        }
    }

    public function bepartnerEP(Request $request)
    {
        Mail::to('bepartner@vangoo.mx')->send(new BePartnerContactMail($request->all()));
        return response()->json(['message' => 'Correo enviado con éxito'], 200);
    }

    public function mailTest()
    {
        $formData = [
            'name' => 'prueba',
            'email' => 'contacto@vangoo.mx',
            'tel' => '12345',
            'asunto' => 'hola prueba',
            'mensaje' => 'hola esta es una prueba',
            'id_user' => 1
        ];

        Mail::to('contacto@vangoo.mx')->send(new BePartnerContactMail($formData));

        return json_encode(1);
    }
}
