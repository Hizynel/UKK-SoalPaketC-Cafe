<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\menu;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = menu::query()
            ->with('reseps.bahanBaku')
            ->orderBy('nama_menu')
            ->get(['id', 'nama_menu', 'kategori', 'harga', 'status']);

        return view('kasir.menu', compact('menus'));
    }
}
