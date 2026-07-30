<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('produk_jadi_id')->constrained('produk_jadis')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();

            $table->unique(['user_id', 'produk_jadi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};