<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_stok', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('id_bahan')->nullable(); 
            $table->unsignedBigInteger('id_produk')->nullable();

            $table->string('jenis_transaksi');
            $table->integer('jumlah');
            $table->date('tanggal');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            // Relasi Bahan Baku
            $table->foreign('id_bahan')
                  ->references('id')
                  ->on('bahan_bakus')
                  ->onDelete('cascade');

            // Relasi Produk Jadi (Ke 'produk_jadis')
            $table->foreign('id_produk')
                  ->references('id')
                  ->on('produk_jadis') // <--- PAKAI S
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_stok');
    }
};