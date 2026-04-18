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
        Schema::table('satuans', function (Blueprint $table) {
            $table->string('tipe')->after('nama_satuan'); // 'bahan' atau 'produk'
            $table->dropColumn('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('satuans', function (Blueprint $table) {
            $table->string('keterangan')->nullable();
            $table->dropColumn('tipe');
        });
    }
};