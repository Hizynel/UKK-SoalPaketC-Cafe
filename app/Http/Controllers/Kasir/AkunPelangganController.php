<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AkunPelangganController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $pelanggan = User::where('role', 'pelanggan')
            ->when($q, fn ($x) => $x->where(fn ($w) => $w
                ->where('name', 'like', "%$q%")
                ->orWhere('nomer_telepon', 'like', "%$q%")))
            ->orderBy('name')
            ->get();

        return view('kasir.akun', compact('pelanggan', 'q'));
    }

    public function reset(User $user)
    {
        abort_unless($user->role === 'pelanggan', 404);

        $sementara = strtoupper(Str::random(8));

        $user->update([
            'password' => Hash::make($sementara),
            'must_change_password' => true,
        ]);

        return redirect()->route('kasir.akun')
            ->with('reset', ['nama' => $user->name, 'password' => $sementara]);
    }
}