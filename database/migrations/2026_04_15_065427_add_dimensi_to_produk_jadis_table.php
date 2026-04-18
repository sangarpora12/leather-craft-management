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
            $table->decimal('panjang', 8, 2)->nullable();
            $table->decimal('lebar', 8, 2)->nullable();
            $table->decimal('tinggi', 8, 2)->nullable();
            $table->text('detail_pola')->nullable();
            $table->string('sketsa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_jadis', function (Blueprint $table) {
            $table->dropColumn(['panjang', 'lebar', 'tinggi', 'detail_pola', 'sketsa']);
        });
    }
};