<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        return redirect()->route('admin.users')
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

    public function update(UserRequest $request, User $user)
    {
        $validatedData = $request->validated();

        $updateData = Arr::except($validatedData, ['profile_image']);

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::delete('public/img/users/' . $user->profile_image);
            }

            $filename = Str::slug($user->name) . '-' . $user->id . '.' . $request->profile_image->extension();

            $request->profile_image->storeAs('public/img/users', $filename);

            $updateData['profile_image'] = $filename;
        }

        $user->update($updateData);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function destroy(User $user)
    {
        if ($user->profile_image) {
            Storage::delete('public/img/users/' . $user->profile_image);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente');
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
