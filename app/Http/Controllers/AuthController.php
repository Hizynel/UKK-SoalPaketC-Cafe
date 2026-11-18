<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->arahkan(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt(['nomer_telepon' => $data['username'], 'password' => $data['password']])) {
            $role = Auth::user()->role;

            if (in_array($role, ['kasir', 'manajer'])) {
                $request->session()->regenerate();
                return $this->arahkan($role);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return back()->withErrors(['login' => 'Username atau password salah.'])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function arahkan(string $role)
    {
        return $role === 'manajer' ? redirect('/manager') : redirect()->route('kasir.dashboard');
    }
}