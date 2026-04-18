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
        Schema::create('produk_keluars', function (Blueprint $table) {
            $table->id();
            
            // Kolom untuk Foreign Key ke produk jadi
            $table->foreignId('produk_jadi_id')->constrained('produk_jadis')->onDelete('cascade');
            
            // Detail Transaksi
            $table->integer('jumlah_keluar');
            $table->enum('alasan', ['Penjualan', 'Rusak', 'Sample', 'Lain-lain']);
            $table->text('catatan')->nullable();
            $table->date('tanggal_keluar');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_keluars');
    }
};