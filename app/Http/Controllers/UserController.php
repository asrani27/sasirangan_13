<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $data = User::with('roles')->where('username', '!=', 'superadmin')->paginate(15);

        return view('admin.user.index', compact('data'));
    }

    public function reset(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make('123123'),
        ]);

        toastr()->info('Berhasil Direset, Passwordnya: 123123');

        return back();
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|min:6',
        ]);

        $role = Role::where('name', 'petugas')->first();

        $user = new User;
        $user->name = $request->nama;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->save();

        if ($role) {
            $user->roles()->attach($role);
        }

        $user->createToken($request->username)->plainTextToken;

        toastr()->success('Berhasil di simpan, passwordnya: '.$request->password);

        return back();
    }

    public function delete(int|string $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();
        $user->tokens()->delete();
        $user->delete();

        toastr()->success('Berhasil di hapus');

        return back();
    }
}
