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
        Schema::table('bahan_bakus', function (Blueprint $table) {
            // Hapus unique constraint pada kode_material
            // karena dalam konsep Master-Variant, satu kode_material
            // bisa dipakai oleh banyak baris (varian warna berbeda)
            $table->dropUnique(['kode_material']);
            
            // Tambah index biasa (bukan unique) untuk performa query
            $table->index('kode_material');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropIndex(['kode_material']);
            $table->unique('kode_material');
        });
    }
};