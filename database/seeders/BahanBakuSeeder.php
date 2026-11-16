<?php

namespace Database\Seeders;

use App\Models\bahan_baku;
use Illuminate\Database\Seeder;

class BahanBakuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Biji kopi arabika', 'g', 185.4],
            ['Biji kopi robusta', 'g', 116.4],
            ['Susu UHT', 'ml', 18.54],
            ['Gula pasir', 'g', 16.48],
            ['Gula aren cair', 'ml', 45.0],
            ['Sirup karamel', 'ml', 87.55],
            ['Cokelat bubuk', 'g', 99.75],
            ['Matcha bubuk', 'g', 432.6],
            ['Teh celup', 'pcs', 824.0],
            ['Lemon', 'pcs', 3000.0],
            ['Es batu', 'g', 4.12],
            ['Roti tawar', 'pcs', 1500.0],
            ['Keju slice', 'pcs', 2625.0],
            ['Mentega', 'g', 63.0],
            ['Kentang beku', 'g', 39.14],
            ['Gelas 12 oz', 'pcs', 436.5],
            ['Gelas 16 oz', 'pcs', 550.0],
            ['Tutup gelas', 'pcs', 157.5],
            ['Sedotan', 'pcs', 103.0],
            ['Whipped cream', 'ml', 72.1],
        ];

        foreach ($data as [$nama, $satuan, $harga]) {
            $bahan = bahan_baku::firstOrNew(['nama_bahan' => $nama]);

            if (! $bahan->exists) {
                $bahan->stock = 100;
                $bahan->batas_minimun = 50;
            }

            $bahan->satuan = $satuan;
            $bahan->harga_terakhir = $harga;
            $bahan->harga_diperbaharui = '2026-11-01';
            $bahan->save();
        }
    }
}