<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->hasRole('petugas')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                toastr()->info('User anda hanya bisa login di android');

                return redirect()->route('login');
            }

            $request->session()->regenerate();

            return redirect()->intended('/home');
        }

        toastr()->error('Username / Password Tidak Ditemukan');

        return back()->withInput($request->only('username'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
