<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ubah kolom sketsa dari string (single path) ke text (JSON array).
     * Migrasi data lama: wrap path string ke dalam array JSON.
     */
    public function up(): void
    {
        // Ubah tipe kolom ke text agar muat multiple paths dalam JSON
        Schema::table('produk_jadis', function (Blueprint $table) {
            $table->text('sketsa')->nullable()->change();
        });

        // Migrasi data lama: string path → JSON array
        $products = DB::table('produk_jadis')->whereNotNull('sketsa')->get();
        foreach ($products as $product) {
            $value = trim($product->sketsa);
            if (empty($value)) continue;

            // Cek apakah sudah berupa JSON array
            $decoded = json_decode($value, true);
            if (!is_array($decoded)) {
                // Data lama berupa string path, wrap ke array JSON
                DB::table('produk_jadis')->where('id', $product->id)->update([
                    'sketsa' => json_encode([$value])
                ]);
            }
        }
    }

    /**
     * Rollback: ubah kembali ke string (ambil elemen pertama array jika ada).
     */
    public function down(): void
    {
        $products = DB::table('produk_jadis')->whereNotNull('sketsa')->get();
        foreach ($products as $product) {
            $decoded = json_decode($product->sketsa, true);
            if (is_array($decoded) && count($decoded) > 0) {
                DB::table('produk_jadis')->where('id', $product->id)->update([
                    'sketsa' => $decoded[0]
                ]);
            }
        }

        Schema::table('produk_jadis', function (Blueprint $table) {
            $table->string('sketsa')->nullable()->change();
        });
    }
};
