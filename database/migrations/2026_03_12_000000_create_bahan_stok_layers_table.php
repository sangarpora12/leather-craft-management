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
        Schema::create('bahan_stok_layers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bahan_baku_id');
            $table->date('tanggal_masuk');
            $table->decimal('qty_masuk', 15, 2);
            $table->decimal('qty_sisa', 15, 2);
            $table->decimal('harga_beli', 15, 2);
            $table->string('sumber_harga')->nullable();
            $table->timestamps();
            
            $table->foreign('bahan_baku_id')->references('id')->on('bahan_bakus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_stok_layers');
    }
};
