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
        Schema::table('produksis', function (Blueprint $table) {
            // Drop foreign key yang lama jika ada
            if (Schema::hasColumn('produksis', 'produk_jadi_id')) {
                // Drop foreign key constraint yang lama
                $table->dropForeign(['produk_jadi_id']);
                
                // Tambahkan foreign key baru dengan cascade delete
                $table->foreign('produk_jadi_id')
                    ->references('id')
                    ->on('produk_jadis')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksis', function (Blueprint $table) {
            // Kembalikan ke set null jika rollback
            if (Schema::hasColumn('produksis', 'produk_jadi_id')) {
                $table->dropForeign(['produk_jadi_id']);
                
                $table->foreign('produk_jadi_id')
                    ->references('id')
                    ->on('produk_jadis')
                    ->onDelete('set null');
            }
        });
    }
};