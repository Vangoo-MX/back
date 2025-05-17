<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('admin.users.index')->with('users', $users);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        User::create($request->safe()->only([
            'name',
            'email',
            'password',
            'rol',
            'tel'
        ]));

        return redirect()->route('users.index')
            ->with('success', __('Usuario creado exitosamente'));
    }

    public function show(User $user)
    {
        return view('admin.users.show')->with('user', $user);
    }

    public function edit(User $user)
    {
        return view('admin.users.edit')
            ->with([
                'user' => $user,
            ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'password' => 'nullable|min:8|confirmed',
            'password_confirmation' => 'sometimes|required_with:password'
        ]);

        $updateData = $request->only([
            'name',
            'tel',
            'biography',
            'email',
            'rol',
            'contact_preference',
            'contact_schedule',
        ]);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::delete('public/img/users/' . $user->profile_image);
            }

            $filename = Str::slug($user->name) . '-' . $user->id . '.' . $request->profile_image->extension();

            $request->profile_image->storeAs('public/img/users', $filename);

            $updateData['profile_image'] = $filename;
        }

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $user->update($updateData);

        return redirect()->route('users.show', $user)
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        if ($user->profile_image) {
            Storage::delete('public/img/users/' . $user->profile_image);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }

    public function statusUser(User $user, Request $request)
    {
        try {
            $user->update(['status' => $request->status]);

            return redirect()->back()
                ->with('success', 'Estado del usuario actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }
}
