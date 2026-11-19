<?php

namespace App\Services;

use App\Models\bahan_baku;
use App\Models\menu;
use App\Models\resep;
use Illuminate\Support\Facades\DB;

class CsvImportService
{
    /**
     * Import data dari file path atau raw CSV content.
     */
    public function import(string $contentOrPath): array
    {
        $content = file_exists($contentOrPath)
            ? file_get_contents($contentOrPath)
            : $contentOrPath;

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return [
                'ok' => false,
                'message' => 'File CSV kosong atau tidak terbaca.',
            ];
        }

        $currentSection = null;
        $bahanCount = 0;
        $menuCount = 0;
        $resepCount = 0;

        DB::beginTransaction();

        try {
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                $row = str_getcsv($line, ',', '"', '\\');
                $firstCol = strtolower(trim($row[0] ?? ''));

                // Deteksi Header Seksi
                if ($firstCol === 'kode' && isset($row[1]) && strtolower(trim($row[1])) === 'nama_bahan') {
                    $currentSection = 'bahan';
                    continue;
                }

                if ($firstCol === 'kode' && isset($row[1]) && strtolower(trim($row[1])) === 'nama_menu') {
                    $currentSection = 'menu';
                    continue;
                }

                if ($firstCol === 'kode_menu' && isset($row[1]) && strtolower(trim($row[1])) === 'nama_bahan') {
                    $currentSection = 'resep';
                    continue;
                }

                // Jika belum ada header eksplisit, coba deteksi dari pola kode
                if ($currentSection === null) {
                    if (str_starts_with(strtoupper($row[0] ?? ''), 'BHN-')) {
                        $currentSection = 'bahan';
                    } elseif (str_starts_with(strtoupper($row[0] ?? ''), 'MNU-')) {
                        // Bedakan Menu vs Resep berdasarkan jumlah kolom atau tipe data
                        if (count($row) === 4 && is_numeric($row[3] ?? null)) {
                            $currentSection = 'menu';
                        } else {
                            $currentSection = 'resep';
                        }
                    }
                }

                // Parsing baris sesuai seksi aktif
                if ($currentSection === 'bahan') {
                    // Format: kode,nama_bahan,stok_awal,satuan,harga_per_satuan
                    if (count($row) >= 5 && str_starts_with(strtoupper($row[0]), 'BHN-')) {
                        $this->importBahanRow($row);
                        $bahanCount++;
                    }
                } elseif ($currentSection === 'menu') {
                    // Format: kode,nama_menu,kategori,harga_jual
                    if (count($row) >= 4 && str_starts_with(strtoupper($row[0]), 'MNU-')) {
                        $this->importMenuRow($row);
                        $menuCount++;
                    }
                } elseif ($currentSection === 'resep') {
                    // Format: kode_menu,nama_bahan,takaran,satuan
                    if (count($row) >= 3 && str_starts_with(strtoupper($row[0]), 'MNU-')) {
                        $this->importResepRow($row);
                        $resepCount++;
                    }
                }
            }

            DB::commit();

            return [
                'ok' => true,
                'bahan_count' => $bahanCount,
                'menu_count' => $menuCount,
                'resep_count' => $resepCount,
                'message' => "Berhasil mengimpor {$bahanCount} bahan baku, {$menuCount} menu, dan {$resepCount} komposisi resep.",
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'ok' => false,
                'message' => 'Gagal mengimpor CSV: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Import 1 baris bahan baku
     */
    private function importBahanRow(array $row): void
    {
        $kode = trim($row[0]);
        $nama = trim($row[1]);
        $stokRaw = (float) trim($row[2]);
        $satuanRaw = strtolower(trim($row[3]));
        $hargaRaw = (float) trim($row[4]);

        $satuan = $satuanRaw;
        $stock = $stokRaw;
        $harga = $hargaRaw;

        // Konversi satuan besar (kg, liter) ke satuan dasar resep (g, ml)
        if ($satuanRaw === 'kg') {
            $satuan = 'g';
            $stock = $stokRaw * 1000;
            $harga = $hargaRaw / 1000;
        } elseif ($satuanRaw === 'liter' || $satuanRaw === 'l') {
            $satuan = 'ml';
            $stock = $stokRaw * 1000;
            $harga = $hargaRaw / 1000;
        }

        $bahan = bahan_baku::where('kode_bahan', $kode)
            ->orWhereRaw('LOWER(TRIM(nama_bahan)) = ?', [mb_strtolower($nama)])
            ->first();

        if (! $bahan) {
            $bahan = new bahan_baku();
        }

        $bahan->kode_bahan = $kode;
        $bahan->nama_bahan = $nama;
        $bahan->stock = $stock;
        $bahan->batas_minimun = 0;
        $bahan->satuan = $satuan;
        $bahan->harga_terakhir = $harga;
        $bahan->harga_diperbaharui = now()->toDateString();
        $bahan->save();
    }

    /**
     * Import 1 baris menu
     */
    private function importMenuRow(array $row): void
    {
        $kode = trim($row[0]);
        $nama = trim($row[1]);
        $kategori = trim($row[2]);
        $harga = (float) trim($row[3]);

        $menu = menu::where('kode_menu', $kode)
            ->orWhereRaw('LOWER(TRIM(nama_menu)) = ?', [mb_strtolower($nama)])
            ->first();

        if (! $menu) {
            $menu = new menu();
            $menu->status = 'tersedia';
        }

        $menu->kode_menu = $kode;
        $menu->nama_menu = $nama;
        $menu->kategori = $kategori;
        $menu->harga = $harga;
        $menu->save();
    }

    /**
     * Import 1 baris resep
     */
    private function importResepRow(array $row): void
    {
        $kodeMenu = trim($row[0]);
        $namaBahan = trim($row[1]);
        $takaran = (float) trim($row[2]);
        $satuanRecipe = trim($row[3] ?? '');

        // Cari menu berdasarkan kode_menu
        $menu = menu::where('kode_menu', $kodeMenu)
            ->orWhereRaw('LOWER(TRIM(nama_menu)) = ?', [mb_strtolower($kodeMenu)])
            ->first();

        if (! $menu) {
            return;
        }

        // Cari bahan baku berdasarkan nama (dengan normalisasi variasi ejaan)
        $namaSearch = mb_strtolower($namaBahan);
        if ($namaSearch === 'biji kopi arabica') {
            $namaSearch = 'biji kopi arabika';
        }

        $bahan = bahan_baku::whereRaw('LOWER(TRIM(nama_bahan)) = ?', [$namaSearch])->first();

        // Jika bahan baku belum ada (contoh: Susu oat, Sirup vanila), buatkan otomatis
        if (! $bahan) {
            $bahan = bahan_baku::create([
                'nama_bahan' => $namaBahan,
                'satuan' => $satuanRecipe ?: 'g',
                'stock' => 0,
                'harga_terakhir' => 0,
                'harga_diperbaharui' => now()->toDateString(),
            ]);
        }

        $bahan->batas_minimun = 0;
        $bahan->save();

        resep::updateOrCreate(
            [
                'id_menu' => $menu->id,
                'id_bahan_baku' => $bahan->id,
            ],
            [
                'kode_menu' => $menu->kode_menu ?? $kodeMenu,
                'takaran' => $takaran,
            ]
        );
    }

    /**
     * Import data bawaan dari database/data/kafe_data.csv
     */
    public function importDefault(): array
    {
        $defaultPath = database_path('data/kafe_data.csv');
        if (! file_exists($defaultPath)) {
            return [
                'ok' => false,
                'message' => "File template {$defaultPath} tidak ditemukan.",
            ];
        }

        return $this->import($defaultPath);
    }
}
