<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GantiPassController extends Controller
{
    public function index(): View
    {
        return view('admin.gantipass');
    }

    public function change(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'min:6'],
            'password2' => ['required', 'same:password'],
        ], [
            'password2.same' => 'Password Konfirmasi Tidak Sama',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        toastr()->success('Password Berhasil Di Ubah');

        return back();
    }
}
