<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->enum('status_pesanan', ['baru', 'diproses', 'selesai', 'dibatalkan'])
                ->default('baru')
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::table('pesanans')->where('status_pesanan', 'dibatalkan')->exists()) {
            throw new RuntimeException('Cannot remove dibatalkan while cancelled orders exist.');
        }

        Schema::table('pesanans', function (Blueprint $table) {
            $table->enum('status_pesanan', ['baru', 'diproses', 'selesai'])
                ->default('baru')
                ->change();
        });
    }
};
