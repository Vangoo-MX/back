<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;
use Illuminate\Support\Str;

class UserApiController extends Controller
{
    public function getInfoUser($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }

        $userData = $user->toArray();

        return response()->json($userData);
    }

    public function getInfoUserByUuid($uuid): JsonResponse
    {
        $user = User::where('uuid', $uuid)->first();

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }

        return response()->json($user->toArray());
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'success',
            'redirect' => 'https://www.vangoo.mx/'
        ]);
    }

    public function checkAuth()
    {
        if (Auth::check()) {
            return json_encode(Auth::user());
        }

        return json_encode('error');
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
                if ($user->profile_image) {
                    Storage::delete("public/img/users/{$user->profile_image}");
                }

                $filename = Str::slug($user->id) . '_' . time() . '.' . $request->profile_image->extension();

                $request->profile_image->storeAs('public/img/users', $filename);
                $updateData['profile_image'] = $filename;
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
