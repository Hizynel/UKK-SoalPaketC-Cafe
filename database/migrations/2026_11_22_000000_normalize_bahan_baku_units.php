<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const UNITS = [
        'g', 'gr', 'gram', 'grams',
        'ml', 'mililiter', 'milliliter', 'millilitre',
        'pcs', 'psc',
        'kg', 'kilogram', 'kilograms',
        'l', 'lt', 'liter', 'litre', 'liters', 'litres',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('bahan_bakus') || ! Schema::hasColumn('bahan_bakus', 'satuan')) {
            return;
        }

        $this->assertSupportedUnits();

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `bahan_bakus` MODIFY `satuan` ENUM('g','gr','gram','grams','ml','mililiter','milliliter','millilitre','pcs','psc','kg','kilogram','kilograms','l','lt','liter','litre','liters','litres') NOT NULL");
        }

        DB::statement("
            UPDATE bahan_bakus
            SET
                stock = CASE
                    WHEN LOWER(TRIM(satuan)) IN ('kg', 'kilogram', 'kilograms', 'l', 'lt', 'liter', 'litre', 'liters', 'litres') THEN stock * 1000
                    ELSE stock
                END,
                harga_terakhir = CASE
                    WHEN LOWER(TRIM(satuan)) IN ('kg', 'kilogram', 'kilograms', 'l', 'lt', 'liter', 'litre', 'liters', 'litres') THEN harga_terakhir / 1000
                    ELSE harga_terakhir
                END,
                satuan = CASE
                    WHEN LOWER(TRIM(satuan)) IN ('g', 'gr', 'gram', 'grams', 'kg', 'kilogram', 'kilograms') THEN 'g'
                    WHEN LOWER(TRIM(satuan)) IN ('ml', 'mililiter', 'milliliter', 'millilitre', 'l', 'lt', 'liter', 'litre', 'liters', 'litres') THEN 'ml'
                    WHEN LOWER(TRIM(satuan)) IN ('pcs', 'psc') THEN 'pcs'
                    ELSE LOWER(TRIM(satuan))
                END
        ");

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `bahan_bakus` MODIFY `satuan` ENUM('g','ml','pcs') NOT NULL");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('bahan_bakus') || ! Schema::hasColumn('bahan_bakus', 'satuan')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `bahan_bakus` MODIFY `satuan` ENUM('g','ml','pcs','psc') NOT NULL");
        }

        DB::table('bahan_bakus')->where('satuan', 'pcs')->update(['satuan' => 'psc']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `bahan_bakus` MODIFY `satuan` ENUM('g','ml','psc') NOT NULL");
        }
    }

    private function assertSupportedUnits(): void
    {
        $supported = self::UNITS;
        $unsupported = DB::table('bahan_bakus')
            ->distinct()
            ->pluck('satuan')
            ->filter(fn ($unit) => ! in_array(mb_strtolower(trim($unit)), $supported, true))
            ->values()
            ->all();

        if ($unsupported !== []) {
            throw new RuntimeException(
                'Satuan bahan baku tidak dikenal; perbaiki sebelum migrasi: '.implode(', ', $unsupported)
            );
        }
    }
};
