<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\menu;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResepController extends Controller
{
    /**
     * Menampilkan daftar resep untuk kasir (hanya lihat / read-only).
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $kategori = trim((string) $request->query('kategori', 'semua'));

        $query = menu::with(['reseps.bahanBaku'])
            ->orderBy('nama_menu');

        if ($kategori !== '' && $kategori !== 'semua') {
            $query->where('kategori', $kategori);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nama_menu', 'like', "%{$q}%")
                    ->orWhereHas('reseps.bahanBaku', function ($b) use ($q) {
                        $b->where('nama_bahan', 'like', "%{$q}%");
                    });
            });
        }

        $menus = $query->get();

        $daftarKategori = ['Kopi', 'Non-Kopi', 'Makanan'];

        return view('kasir.resep', compact('menus', 'q', 'kategori', 'daftarKategori'));
    }
}

