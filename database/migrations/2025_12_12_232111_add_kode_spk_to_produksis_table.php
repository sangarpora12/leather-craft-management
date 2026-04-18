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
        // Pastikan tabel 'produksis' ada sebelum mencoba menambah kolom
        if (Schema::hasTable('produksis')) {
            Schema::table('produksis', function (Blueprint $table) {
                // Tambahkan kolom 'kode_spk' yang unik (untuk identifikasi SPK)
                // Kita letakkan setelah kolom 'id'
                $table->string('kode_spk', 50)->unique()->after('id'); 
                
                // Opsional: Tambahkan kolom lain jika Anda yakin hilang,
                // seperti foreign key atau kolom penting lainnya yang digunakan di Controller.
                /*
                $table->unsignedBigInteger('produk_jadi_id')->change(); 
                $table->string('status', 20)->default('PROSES')->change();
                */
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pastikan tabel ada sebelum mencoba menghapus kolom
        if (Schema::hasTable('produksis')) {
            Schema::table('produksis', function (Blueprint $table) {
                // Hapus kolom 'kode_spk'
                if (Schema::hasColumn('produksis', 'kode_spk')) {
                    $table->dropColumn('kode_spk');
                }
            });
        }
    }
};