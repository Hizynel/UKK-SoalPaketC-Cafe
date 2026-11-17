<?php

namespace App\Services;

use App\Exceptions\PesananDitolak;
use App\Models\bahan_baku;
use App\Models\detail_pesanan;
use App\Models\menu;
use App\Models\pesanan;
use App\Models\pesanan_bahan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PesananService
{
    public static function hitungBiaya(float $subtotal): array
    {
        $service = round($subtotal * 0.05, 2);
        $pajak = round(($subtotal + $service) * 0.10, 2);

        return [
            'subtotal' => $subtotal,
            'service'  => $service,
            'pajak'    => $pajak,
            'total'    => $subtotal + $service + $pajak,
        ];
    }

    public static function rapikan(array $items): array
    {
        $out = [];
        foreach ($items as $it) {
            $id = (int) ($it['id_menu'] ?? 0);
            $jml = (int) ($it['jumlah'] ?? 0);
            if ($id > 0 && $jml > 0) {
                $out[$id] = ($out[$id] ?? 0) + $jml;
            }
        }
        return $out;
    }

    public function cekStok(array $items): array
    {
        $porsi = self::rapikan($items);
        if (! $porsi) {
            return [];
        }

        try {
            $menus = $this->muatMenu($porsi);
        } catch (PesananDitolak $e) {
            return [$e->getMessage()];
        }

        $butuh = $this->kebutuhan($porsi, $menus);
        $bahans = bahan_baku::whereIn('id', array_keys($butuh))->get()->keyBy('id');

        return $this->kurang($butuh, $bahans);
    }

    public function buat(array $items, string $nama, int $meja, string $metode, ?User $kasir = null, ?User $pelanggan = null): pesanan
    {
        abort_unless($kasir?->role === 'kasir' || $pelanggan?->role === 'pelanggan', 403);

        if ($meja < 1 || $meja > 10) {
            throw new PesananDitolak('Nomor meja harus 1 sampai 10.');
        }
        if (! in_array($metode, ['Tunai', 'QRIS'], true)) {
            throw new PesananDitolak('Metode bayar harus Tunai atau QRIS.');
        }

        $porsi = self::rapikan($items);
        if (! $porsi) {
            throw new PesananDitolak('Pilih minimal satu menu.');
        }

        return DB::transaction(function () use ($porsi, $nama, $meja, $metode, $kasir, $pelanggan) {
            if ($pelanggan) {
                User::whereKey($pelanggan->id)->lockForUpdate()->first();

                if (pesanan::where('id_pelanggan', $pelanggan->id)->where('status_pesanan', 'Baru')->exists()) {
                    throw new PesananDitolak('Anda masih punya pesanan berstatus Baru.');
                }
            }

            $menus = $this->muatMenu($porsi);

            $butuh = $this->kebutuhan($porsi, $menus);
            ksort($butuh);
            $bahans = bahan_baku::whereIn('id', array_keys($butuh))
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            $kurang = $this->kurang($butuh, $bahans);
            if ($kurang) {
                throw new PesananDitolak('Stok bahan tidak cukup.', $kurang);
            }

            $subtotal = 0;
            foreach ($porsi as $idMenu => $jml) {
                $subtotal += $menus[$idMenu]->harga * $jml;
            }
            $b = self::hitungBiaya((float) $subtotal);

            $p = pesanan::create([
                'kode_unik'       => $this->kodeBaru(),
                'id_pelanggan'    => $pelanggan?->id,
                'id_kasir'        => $kasir?->id,
                'tanggal_pesanan' => now(),
                'nama_pelanggan'  => $pelanggan?->name ?? $nama,
                'nomor_meja'      => $meja,
                'metode_pesan'    => $metode,
                'status_pesanan'  => 'Baru',
                'subtotal'        => $b['subtotal'],
                'service'         => $b['service'],
                'pajak'           => $b['pajak'],
                'total_harga'     => $b['total'],
            ]);

            foreach ($porsi as $idMenu => $jml) {
                detail_pesanan::create([
                    'id_pesanan'   => $p->id,
                    'id_menu'      => $idMenu,
                    'nama_menu'    => $menus[$idMenu]->nama_menu,
                    'jumlah'       => $jml,
                    'harga_satuan' => $menus[$idMenu]->harga,
                ]);
            }

            foreach ($butuh as $idBahan => $jml) {
                pesanan_bahan::create(['id_pesanan' => $p->id, 'id_bahan_baku' => $idBahan, 'jumlah' => $jml]);
                $bahans[$idBahan]->decrement('stock', $jml);
            }

            return $p;
        });
    }

    public function ubahStatus(pesanan $p, string $target, User $aktor): void
    {
        abort_unless($aktor->role === 'kasir', 403);

        DB::transaction(function () use ($p, $target) {
            $p = pesanan::whereKey($p->id)->lockForUpdate()->firstOrFail();
            $berikut = ['Baru' => 'Diproses', 'Diproses' => 'Selesai'][$p->status_pesanan] ?? null;

            if ($berikut === null || $berikut !== $target) {
                throw new PesananDitolak("Status {$p->status_pesanan} tidak bisa langsung menjadi {$target}.");
            }

            $p->update(['status_pesanan' => $target]);
        });
    }

    public function batalkan(pesanan $p, User $aktor): void
    {
        abort_unless($aktor->role === 'kasir', 403);

        DB::transaction(function () use ($p) {
            $p = pesanan::whereKey($p->id)->lockForUpdate()->firstOrFail();

            if ($p->status_pesanan !== 'Baru') {
                throw new PesananDitolak('Pesanan hanya bisa dibatalkan saat berstatus Baru.');
            }

            foreach ($p->bahans()->orderBy('id_bahan_baku')->get() as $b) {
                bahan_baku::whereKey($b->id_bahan_baku)->increment('stock', $b->jumlah);
            }

            $p->update(['status_pesanan' => 'Dibatalkan']);
        });
    }

    private function muatMenu(array $porsi)
    {
        $menus = menu::with('reseps')->whereIn('id', array_keys($porsi))->get()->keyBy('id');

        foreach (array_keys($porsi) as $id) {
            $m = $menus[$id] ?? throw new PesananDitolak('Menu tidak ditemukan.');

            if (! $m->aktif) {
                throw new PesananDitolak("Menu {$m->nama_menu} sedang nonaktif.");
            }
            if ($m->reseps->isEmpty()) {
                throw new PesananDitolak("Menu {$m->nama_menu} belum punya resep.");
            }
        }

        return $menus;
    }

    private function kebutuhan(array $porsi, $menus): array
    {
        $butuh = [];
        foreach ($porsi as $idMenu => $jml) {
            foreach ($menus[$idMenu]->reseps as $r) {
                $butuh[$r->id_bahan_baku] = round(($butuh[$r->id_bahan_baku] ?? 0) + $r->takaran * $jml, 2);
            }
        }
        return $butuh;
    }

    private function kurang(array $butuh, $bahans): array
    {
        $daftar = [];
        foreach ($butuh as $idBahan => $perlu) {
            $b = $bahans[$idBahan];
            if ((float) $b->stock < $perlu) {
                $daftar[] = "{$b->nama_bahan}: butuh " . (float) $perlu . " {$b->satuan}, tersedia " . (float) $b->stock . " {$b->satuan}";
            }
        }
        return $daftar;
    }

    private function kodeBaru(): string
    {
        do {
            $kode = strtoupper(Str::random(8));
        } while (pesanan::where('kode_unik', $kode)->exists());

        return $kode;
    }
}