<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('produk_jadis')) {
            return;
        }

        Schema::table('produk_jadis', function (Blueprint $table) {
            if (!Schema::hasColumn('produk_jadis', 'stok_minimum')) {
                $table->integer('stok_minimum')->default(10)->after('stok');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('produk_jadis') && Schema::hasColumn('produk_jadis', 'stok_minimum')) {
            Schema::table('produk_jadis', function (Blueprint $table) {
                $table->dropColumn('stok_minimum');
            });
        }
    }
};
