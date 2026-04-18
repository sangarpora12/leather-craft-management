<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bom', function (Blueprint $table) {
            $table->id();
            
            // Kolom Foreign Key
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_bahan');
            
            $table->integer('jumlah_kebutuhan'); // Berapa banyak bahan yang dipakai
            $table->string('satuan'); // Pcs, Cm, Lembar
            $table->timestamps();

            // RELASI KE PRODUK JADI (Perbaikan Disini)
            // Kita arahkan ke tabel 'produk_jadis' dan kolom 'id'
            $table->foreign('id_produk')
                  ->references('id')
                  ->on('produk_jadis')
                  ->onDelete('cascade');

            // RELASI KE BAHAN BAKU
            // Kita arahkan ke tabel 'bahan_bakus' dan kolom 'id'
            $table->foreign('id_bahan')
                  ->references('id')
                  ->on('bahan_bakus')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bom');
    }
};