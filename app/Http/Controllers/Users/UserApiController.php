<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use Illuminate\Support\Facades\Log;

class UserApiController extends Controller
{
    public function getInfoUser($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }

        $userData = $user->toArray();
        $userData['rol_title'] = $user->rol->title();

        return response()->json($userData);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        return response()->json(['status' => 'success']);
    }

    public function checkAuth()
    {
        Log::info('Auth check:', ['check' => Auth::check(), 'user' => Auth::user()]);
        if (Auth::check()) {
            return json_encode(Auth::user());
        }

        return json_encode('error');
    }

    public function forceCheck()
    {
        if (Auth::check()) {
            return response()
                ->json(Auth::user())
                ->header('Cache-Control', 'no-store, must-revalidate')
                ->header('Pragma', 'no-cache');
        }
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    public function updateUser(Request $request): JsonResponse
    {
        try {
            $user = User::findOrFail($request->id);

            $updateData = $request->only([
                'email',
                'tel',
                'name',
                'contact_preference',
                'contact_schedule',
                'biography'
            ]);

            if ($request->hasFile('profile_image')) {
                $extension = $request->profile_image->extension();
                $filename = "{$user->id}.{$extension}";

                $request->profile_image->storeAs('public/img/users', $filename);
                $updateData['profile_image'] = $filename;

                if ($user->profile_image) {
                    Storage::delete("public/img/users/{$user->profile_image}");
                }
            }

            $user->update($updateData);

            return response()->json(['status' => 'success']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error actualizando los datos del usuario'
            ], 500);
        }
    }
}
