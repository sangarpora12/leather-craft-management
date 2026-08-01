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
        if (!Schema::hasTable('produk_keluars')) {
            Schema::create('produk_keluars', function (Blueprint $table) {
                $table->id();
                $table->foreignId('produk_jadi_id')->constrained('produk_jadis')->onDelete('cascade');
                $table->integer('jumlah_keluar');
                $table->string('alasan', 255)->nullable();
                $table->text('catatan')->nullable();
                $table->date('tanggal_keluar');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_keluars');
    }
};