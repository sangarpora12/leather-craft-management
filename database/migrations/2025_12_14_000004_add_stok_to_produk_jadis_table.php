<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('produk_jadis')) {
            Schema::table('produk_jadis', function (Blueprint $table) {
                if (!Schema::hasColumn('produk_jadis', 'stok')) {
                    // Menambahkan kolom stok jika belum ada
                    $table->integer('stok')->default(0)->after('harga_jual');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('produk_jadis') && Schema::hasColumn('produk_jadis', 'stok')) {
            Schema::table('produk_jadis', function (Blueprint $table) {
                $table->dropColumn('stok');
            });
        }
    }
};