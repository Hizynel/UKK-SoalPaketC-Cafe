<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'nomer_telepon' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($cred)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;

            if ($role === 'manajer') return redirect('/manager');
            if ($role === 'kasir') return redirect()->route('kasir.dashboard');

            Auth::logout();
        }

        return back()->withErrors(['login' => 'Username atau password salah.'])
                     ->onlyInput('nomer_telepon');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
