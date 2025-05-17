<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\support\Facades\Auth;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        return redirect()->route('/', $user)->with('success', 'Usuario registrado correctamente.');
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
