<?php

namespace App\Services;

use App\Models\bahan_baku;
use Illuminate\Support\Facades\Http;

class SupplierPriceService
{
    public function sync(): array
    {
        $url = config('services.supplier.url');
        $apiKey = config('services.supplier.api_key');

        if (empty($url) || empty($apiKey)) {
            return $this->gagal('Konfigurasi SUPPLIER_URL atau SUPPLIER_API_KEY belum diatur di file .env.');
        }

        try {
            $response = Http::withHeaders(['X-API-Key' => $apiKey])
                ->timeout(15)
                ->get($url);
        } catch (\Throwable $e) {
            return $this->gagal('Supplier tidak dapat dihubungi: ' . $e->getMessage());
        }

        if (! $response->successful()) {
            return $this->gagal('Supplier menolak permintaan (status ' . $response->status() . ').');
        }

        $json = $response->json();
        if (! is_array($json['data'] ?? null)) {
            return $this->gagal('Format respons Supplier tidak dikenali.');
        }

        $tanggal = $json['diperbarui'] ?? now()->toDateString();
        $jumlah = 0;

        foreach ($json['data'] as $item) {
            $nama = mb_strtolower(trim($item['nama'] ?? ''));
            $satuan = strtolower(trim($item['satuan_dasar'] ?? ''));

            $bahan = bahan_baku::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$nama])->first();

            if (! $bahan || strtolower(trim($bahan->satuan)) !== $satuan) {
                continue;
            }

            $bahan->update([
                'harga_terakhir' => $item['harga_per_satuan'],
                'harga_diperbaharui' => $tanggal,
            ]);
            $jumlah++;
        }

        return ['ok' => true, 'message' => "Harga {$jumlah} bahan diperbarui (data Supplier tanggal {$tanggal})."];
    }

    public function syncSingle(bahan_baku $bahan): array
    {
        $url = config('services.supplier.url');
        $apiKey = config('services.supplier.api_key');

        if (empty($url) || empty($apiKey)) {
            return $this->gagal('Konfigurasi SUPPLIER_URL atau SUPPLIER_API_KEY belum diatur di file .env.');
        }

        try {
            $response = Http::withHeaders(['X-API-Key' => $apiKey])
                ->timeout(15)
                ->get($url);
        } catch (\Throwable $e) {
            return $this->gagal('Supplier tidak dapat dihubungi: ' . $e->getMessage());
        }

        if (! $response->successful()) {
            return $this->gagal('Supplier menolak permintaan (status ' . $response->status() . ').');
        }

        $json = $response->json();
        if (! is_array($json['data'] ?? null)) {
            return $this->gagal('Format respons Supplier tidak dikenali.');
        }

        $tanggal = $json['diperbarui'] ?? now()->toDateString();
        $targetNama = mb_strtolower(trim($bahan->nama_bahan));
        $targetSatuan = strtolower(trim($bahan->satuan));

        foreach ($json['data'] as $item) {
            $nama = mb_strtolower(trim($item['nama'] ?? ''));
            $satuan = strtolower(trim($item['satuan_dasar'] ?? ''));

            if ($nama === $targetNama && $satuan === $targetSatuan) {
                $bahan->update([
                    'harga_terakhir' => $item['harga_per_satuan'],
                    'harga_diperbaharui' => $tanggal,
                ]);

                return ['ok' => true, 'message' => "Harga untuk {$bahan->nama_bahan} berhasil diperbarui menjadi Rp " . number_format($item['harga_per_satuan'], 2, ',', '.') . " (data per {$tanggal})."];
            }
        }

        return $this->gagal("Bahan '{$bahan->nama_bahan}' ({$bahan->satuan}) tidak ditemukan di API Supplier.");
    }

    private function gagal(string $alasan): array
    {
        return ['ok' => false, 'message' => $alasan . ' Memakai harga terakhir yang tersimpan.'];
    }
}
