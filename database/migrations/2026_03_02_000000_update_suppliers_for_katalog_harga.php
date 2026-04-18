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
        // Update suppliers table to become Katalog Harga
        Schema::table('suppliers', function (Blueprint $table) {
            // Rename or add columns for price catalog
            // First, rename existing columns to match new structure
            $table->renameColumn('nama_supplier', 'nama_toko');
            $table->renameColumn('no_telepon', 'kontak');
            
            // Add new columns for price tracking
            $table->string('nama_bahan')->nullable()->after('kontak');
            $table->decimal('harga', 10, 2)->nullable()->after('nama_bahan');
            $table->string('satuan')->nullable()->after('harga');
            
            // Drop alamat as it's not needed for price catalog
            $table->dropColumn('alamat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('nama_toko', 'nama_supplier');
            $table->renameColumn('kontak', 'no_telepon');
            $table->dropColumn(['nama_bahan', 'harga', 'satuan']);
            $table->text('alamat')->nullable();
        });
    }
};
