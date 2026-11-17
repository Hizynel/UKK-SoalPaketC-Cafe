<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pesanan_bahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanans')->cascadeOnDelete();
            $table->foreignId('id_bahan_baku')->constrained('bahan_bakus');
            $table->decimal('jumlah', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_bahans');
    }
};
