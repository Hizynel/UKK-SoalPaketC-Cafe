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
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            $table->integer(kode_menu);
            $table->foreignId('id_menu')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('id_bahan_baku')->constrained('bahan_bakus');
            $table->decimal('takaran','10','2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
