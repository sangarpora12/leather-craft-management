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
        Schema::table('produk_jadis', function (Blueprint $table) {
            $table->string('tipe_harga')->default('manual')->after('harga_jual');
            // 'manual' = harga diinput langsung oleh admin
            // 'otomatis' = harga dihitung otomatis berdasarkan BOM + Margin + PPN
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_jadis', function (Blueprint $table) {
            $table->dropColumn('tipe_harga');
        });
    }
};