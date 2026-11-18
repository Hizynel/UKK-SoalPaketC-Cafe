<?php

namespace App\Http\Controllers\Kasir;

use App\Exceptions\PesananDitolak;
use App\Http\Controllers\Controller;
use App\Models\menu;
use App\Models\pesanan;
use App\Services\PesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PesananController extends Controller
{
    public function input(): View
    {
        $menus = menu::where('status', 'tersedia')
            ->whereHas('reseps')
            ->orderBy('nama_menu')
            ->get();

        return view('kasir.input', compact('menus'));
    }

    public function store(Request $request, PesananService $service): RedirectResponse
    {
        $data = $request->validate([
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'nomor_meja' => ['required', 'integer', 'between:1,10'],
            'metode' => ['required', 'in:Tunai,QRIS'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_menu' => ['required', 'integer', 'exists:menus,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $order = $service->buat(
                $data['items'],
                $data['nama_pelanggan'],
                (int) $data['nomor_meja'],
                $data['metode'],
                auth()->user(),
            );
        } catch (PesananDitolak $e) {
            $messages = array_filter([$e->getMessage(), ...$e->daftar]);

            return back()->withInput()->withErrors([
                'items' => implode(' ', $messages),
            ]);
        }

        return redirect()->route('kasir.input')->with('createdOrder', [
            'kode' => $order->kode_unik,
            'nama' => $order->nama_pelanggan,
            'total' => $order->total_harga,
        ]);
    }

    public function dashboard(): View
    {
        $antrean = pesanan::where('status_pesanan', 'baru')
            ->latest('tanggal_pesanan')
            ->get();

        return view('kasir.dashboard', [
            'antrean' => $antrean,
            'baruCount' => $antrean->count(),
            'diprosesCount' => pesanan::where('status_pesanan', 'diproses')->count(),
            'selesaiHariIniCount' => pesanan::where('status_pesanan', 'selesai')
                ->whereDate('tanggal_pesanan', today())
                ->count(),
        ]);
    }

    public function data(): View
    {
        return view('kasir.data', [
            'pesanan' => pesanan::latest('tanggal_pesanan')->get(),
        ]);
    }

    public function proses(pesanan $pesanan, PesananService $service): RedirectResponse
    {
        return $this->ubahStatus($pesanan, 'diproses', $service);
    }

    public function selesai(pesanan $pesanan, PesananService $service): RedirectResponse
    {
        return $this->ubahStatus($pesanan, 'selesai', $service);
    }

    public function batal(pesanan $pesanan, PesananService $service): RedirectResponse
    {
        try {
            $service->batalkan($pesanan, auth()->user());
        } catch (PesananDitolak $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function struk(pesanan $pesanan): View
    {
        $pesanan->load('detailPesanan.menu');

        return view('kasir.struk', compact('pesanan'));
    }

    private function ubahStatus(
        pesanan $pesanan,
        string $status,
        PesananService $service
    ): RedirectResponse {
        try {
            $service->ubahStatus($pesanan, $status, auth()->user());
        } catch (PesananDitolak $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
