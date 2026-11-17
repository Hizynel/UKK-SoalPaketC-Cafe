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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kasir')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('id_pelanggan')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('tanggal_pesanan');
            $table->string('nama_pelanggan');
            $table->integer('nomor_meja');
            $table->string('kode_unik', 8)->unique();
            $table->enum('status_pesanan', ['baru', 'diproses', 'selesai', 'dibatalkan']) ->default('baru');
            $table->enum('metode_pesan', ['tunai', 'qris']);
            $table->decimal('subtotal', 12 , 2);
            $table->decimal('service', 12 , 2);
            $table->decimal('pajak', 12 , 2);
            $table->decimal('total_harga', 12 , 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
