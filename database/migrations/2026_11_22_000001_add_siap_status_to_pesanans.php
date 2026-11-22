<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pesanans') || ! Schema::hasColumn('pesanans', 'status_pesanan')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `pesanans` MODIFY `status_pesanan` ENUM('baru','diproses','siap','selesai','dibatalkan') NOT NULL DEFAULT 'baru'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pesanans') || ! Schema::hasColumn('pesanans', 'status_pesanan')) {
            return;
        }

        if (DB::table('pesanans')->where('status_pesanan', 'siap')->exists()) {
            throw new RuntimeException('Cannot remove siap while orders with that status exist.');
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE `pesanans` MODIFY `status_pesanan` ENUM('baru','diproses','selesai','dibatalkan') NOT NULL DEFAULT 'baru'");
        }
    }
};
