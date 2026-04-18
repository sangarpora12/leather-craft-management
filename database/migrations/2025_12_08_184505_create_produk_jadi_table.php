<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan baris ini di paling atas fungsi up()
        Schema::dropIfExists('produk_jadis'); 
        Schema::dropIfExists('produk_jadi'); // Jaga-jaga kalau namanya beda
    
        Schema::create('produk_jadis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_produk')->unique();
            $table->string('nama_produk');
            $table->decimal('harga_jual', 15, 2);
            $table->text('deskripsi')->nullable();
            $table->integer('stok')->default(0);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_jadis');
    }
};