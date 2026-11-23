<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\menu;
use App\Models\bahan_baku;
use App\Models\pesanan;
use App\Models\detail_pesanan;
use App\Models\pesanan_bahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CafeApiController extends Controller
{
    // ==========================================
    // 1. AUTENTIKASI (POIN 12)
    // ==========================================

    public function login(Request $request)
    {
        $request->validate([
            'nomer_telepon' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('nomer_telepon', $request->nomer_telepon)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        $token = $user->createToken('cafe_apk_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nomer_telepon' => $user->nomer_telepon,
                'role' => $user->role,
                'must_change_password' => (bool) $user->must_change_password,
            ],
            'mustChange' => (bool) $user->must_change_password,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nomer_telepon' => 'required|string|min:10|unique:users,nomer_telepon',
            'password' => 'required|string|min:8',
        ], [
            'nomer_telepon.unique' => 'Nomor HP sudah terdaftar. Silakan masuk.',
            'nomer_telepon.min' => 'Nomor HP minimal 10 digit, tanpa huruf.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'nomer_telepon' => $request->nomer_telepon,
            'password' => Hash::make($request->password),
            'role' => 'pelanggan',
            'must_change_password' => false,
        ]);

        $token = $user->createToken('cafe_apk_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran akun berhasil.',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nomer_telepon' => $user->nomer_telepon,
                'role' => $user->role,
                'must_change_password' => false,
            ],
        ], 201);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'nullable|string',
            'new_password' => 'required|string|min:8',
            'is_forced' => 'nullable|boolean',
        ], [
            'new_password.min' => 'Password baru minimal 8 karakter.',
        ]);

        /** @var User $user */
        $user = $request->user();

        // Jika ganti password biasa, periksa password lama
        if (! $request->is_forced && ! $user->must_change_password) {
            if (! $request->old_password || ! Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai.',
                ], 422);
            }
        }

        $user->password = Hash::make($request->new_password);
        $user->must_change_password = false;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }

    public function resetCustomerPassword(Request $request)
    {
        $request->validate([
            'nomer_telepon' => 'required|string|exists:users,nomer_telepon',
        ]);

        $customer = User::where('nomer_telepon', $request->nomer_telepon)->first();

        // Generate password sementara 8 karakter
        $tempPassword = strtoupper(Str::random(8));

        $customer->password = Hash::make($tempPassword);
        $customer->must_change_password = true;
        $customer->save();

        return response()->json([
            'success' => true,
            'temp_password' => $tempPassword,
            'message' => "Password sementara untuk {$customer->name} berhasil dibuat.",
        ]);
    }

    public function getCustomers()
    {
        $customers = User::where('role', 'pelanggan')
            ->select('id', 'name', 'nomer_telepon', 'must_change_password', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    // ==========================================
    // 2. MENU & STOK (POIN 13)
    // ==========================================

    public function getMenus()
    {
        $menus = menu::with(['reseps.bahanBaku'])
            ->where('is_active', true)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'nama' => $m->nama_menu,
                    'harga' => (float) $m->harga,
                    'kategori' => strtolower($m->kategori),
                    'status' => $m->status_stok === 'Tersedia' ? 'tersedia' : 'habis',
                    'resep' => $m->reseps->map(fn ($r) => [
                        'bahanId' => $r->id_bahan_baku,
                        'jumlah' => (float) $r->takaran,
                    ]),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $menus,
        ]);
    }

    public function getBahanBaku()
    {
        $bahan = bahan_baku::select('id', 'kode_bahan', 'nama_bahan', 'stock', 'satuan')
            ->orderBy('id')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'nama' => $b->nama_bahan,
                'stok' => (float) $b->stock,
                'satuan' => $b->satuan,
            ]);

        return response()->json([
            'success' => true,
            'data' => $bahan,
        ]);
    }

    // ==========================================
    // 3. PESANAN (POIN 13 & 14)
    // ==========================================

    public function createOrder(Request $request)
    {
        $request->validate([
            'nomor_meja' => 'required|integer|min:1|max:10',
            'metode_pesan' => 'required|in:tunai,qris',
            'items' => 'required|array|min:1',
            'items.*.menuId' => 'required|exists:menus,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        /** @var User $user */
        $user = $request->user();

        // Poin 13: Hanya boleh satu pesanan berstatus Baru pada satu waktu per pelanggan
        if ($user->role === 'pelanggan') {
            $hasActive = pesanan::where('id_pelanggan', $user->id)
                ->where('status_pesanan', 'baru')
                ->exists();

            if ($hasActive) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda masih memiliki pesanan berstatus "Baru". Tunggu diproses atau batalkan sebelum membuat pesanan baru.',
                ], 422);
            }
        }

        return DB::transaction(function () use ($request, $user) {
            $items = $request->items;

            // 1. Hitung kebutuhan bahan baku total dan periksa stok
            $neededStock = [];
            $menuModels = [];

            foreach ($items as $item) {
                $menu = menu::with('reseps.bahanBaku')->findOrFail($item['menuId']);
                $menuModels[$item['menuId']] = $menu;

                foreach ($menu->reseps as $resep) {
                    $bId = $resep->id_bahan_baku;
                    $needed = (float) $resep->takaran * (int) $item['qty'];
                    $neededStock[$bId] = ($neededStock[$bId] ?? 0) + $needed;
                }
            }

            foreach ($neededStock as $bahanId => $totalNeed) {
                $bahan = bahan_baku::lockForUpdate()->find($bahanId);
                if (! $bahan || (float) $bahan->stock < $totalNeed) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok {$bahan?->nama_bahan} tidak mencukupi (Tersedia: {$bahan?->stock} {$bahan?->satuan}).",
                    ], 422);
                }
            }

            // 2. Hitung total biaya
            $subtotal = 0;
            $detailData = [];

            foreach ($items as $item) {
                $menu = $menuModels[$item['menuId']];
                $lineSubtotal = (float) $menu->harga * (int) $item['qty'];
                $subtotal += $lineSubtotal;

                $detailData[] = [
                    'id_menu' => $menu->id,
                    'nama_menu' => $menu->nama_menu,
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $menu->harga,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $service = round($subtotal * 0.05);
            $pajak = round($subtotal * 0.1);
            $totalHarga = $subtotal + $service + $pajak;

            // Kode unik 8 digit
            $kodeUnik = strtoupper(Str::random(8));

            // Buat Pesanan
            $pesanan = pesanan::create([
                'id_kasir' => $user->role === 'kasir' ? $user->id : null,
                'id_pelanggan' => $user->role === 'pelanggan' ? $user->id : null,
                'tanggal_pesanan' => now(),
                'nama_pelanggan' => $request->nama_pelanggan ?? $user->name,
                'nomor_meja' => $request->nomor_meja,
                'kode_unik' => $kodeUnik,
                'status_pesanan' => 'baru',
                'metode_pesan' => strtolower($request->metode_pesan),
                'subtotal' => $subtotal,
                'service' => $service,
                'pajak' => $pajak,
                'total_harga' => $totalHarga,
            ]);

            // Simpan detail pesanan & potong stok
            foreach ($detailData as $detail) {
                $pesanan->detailPesanan()->create($detail);
            }

            foreach ($neededStock as $bahanId => $totalNeed) {
                $bahan = bahan_baku::find($bahanId);
                $bahan->decrement('stock', $totalNeed);

                // Catat bahan yang terpakai untuk pesanan ini agar mudah direstore saat dibatalkan
                pesanan_bahan::create([
                    'id_pesanan' => $pesanan->id,
                    'id_bahan_baku' => $bahanId,
                    'jumlah' => $totalNeed,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data' => $this->formatOrder($pesanan->fresh(['detailPesanan', 'pelanggan'])),
            ], 201);
        });
    }

    // Poin 14: Pelanggan memantau status dan struk pesanannya sendiri;
    // Pesanan orang lain tidak bisa dibuka
    public function getMyOrders(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $orders = pesanan::with(['detailPesanan', 'pelanggan'])
            ->where('id_pelanggan', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($p) => $this->formatOrder($p));

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    // Kasir: Mengambil seluruh pesanan kafe
    public function getAllOrders()
    {
        $orders = pesanan::with(['detailPesanan', 'pelanggan'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($p) => $this->formatOrder($p));

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    // Kasir / Pelanggan: Ubah status pesanan (Baru -> Diproses -> Selesai / Dibatalkan)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:baru,diproses,selesai,dibatalkan',
        ]);

        /** @var User $user */
        $user = $request->user();
        $pesanan = pesanan::with('bahans')->findOrFail($id);

        // Jika pelanggan ingin membatalkan, hanya boleh jika pesanannya sendiri & status masih 'baru'
        if ($user->role === 'pelanggan') {
            if ($pesanan->id_pelanggan !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
            }
            if ($request->status !== 'dibatalkan' || $pesanan->status_pesanan !== 'baru') {
                return response()->json(['success' => false, 'message' => 'Hanya bisa membatalkan pesanan baru.'], 422);
            }
        }

        DB::transaction(function () use ($pesanan, $request) {
            $prevStatus = $pesanan->status_pesanan;
            $newStatus = $request->status;

            // Jika status baru dibatalkan, kembalikan stok bahan otomatis (Poin 13)
            if ($prevStatus === 'baru' && $newStatus === 'dibatalkan') {
                foreach ($pesanan->bahans as $pb) {
                    $bahan = bahan_baku::find($pb->id_bahan_baku);
                    if ($bahan) {
                        $bahan->increment('stock', $pb->jumlah);
                    }
                }
            }

            $pesanan->status_pesanan = $newStatus;
            $pesanan->save();
        });

        return response()->json([
            'success' => true,
            'message' => "Status pesanan diubah menjadi {$request->status}.",
            'data' => $this->formatOrder($pesanan->fresh(['detailPesanan', 'pelanggan'])),
        ]);
    }

    private function formatOrder(pesanan $p)
    {
        // Capitalize status format: baru -> Baru
        $statusMap = [
            'baru' => 'Baru',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        return [
            'id' => $p->id,
            'kode' => $p->kode_unik,
            'pelangganHp' => $p->pelanggan?->nomer_telepon ?? '',
            'pelangganNama' => $p->nama_pelanggan,
            'meja' => "Meja {$p->nomor_meja}",
            'metodePembayaran' => strtoupper($p->metode_pesan),
            'items' => $p->detailPesanan->map(fn ($d) => [
                'menuId' => $d->id_menu,
                'nama' => $d->nama_menu,
                'qty' => $d->jumlah,
                'harga' => (float) $d->harga_satuan,
            ]),
            'subtotal' => (float) $p->subtotal,
            'serviceCharge' => (float) $p->service,
            'pajak' => (float) $p->pajak,
            'total' => (float) $p->total_harga,
            'status' => $statusMap[$p->status_pesanan] ?? 'Baru',
            'createdAt' => $p->created_at->diffForHumans(),
        ];
    }
}

