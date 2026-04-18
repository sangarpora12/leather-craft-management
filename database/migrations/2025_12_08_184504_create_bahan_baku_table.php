<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan_bakus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_material')->unique();
            $table->string('nama_material');
            $table->string('kategori');
            $table->integer('stok')->default(0);
            $table->string('satuan');
            $table->decimal('harga_satuan', 15, 2);
            $table->integer('stok_minimum')->default(10);
            $table->enum('status', ['Aman', 'Menipis', 'Habis'])->default('Aman');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan_bakus');
    }
};