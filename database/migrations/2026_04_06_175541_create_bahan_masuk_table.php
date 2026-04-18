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
        Schema::create('bahan_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->foreignId('operator_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_material');
            $table->string('kode_material')->nullable();
            $table->decimal('jumlah', 10, 2);
            $table->string('satuan');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->string('supplier')->nullable();
            $table->string('ref_no')->nullable();
            $table->string('kategori')->default('Bahan Utama');
            $table->string('foto_nota')->nullable();
            $table->string('lokasi_gudang')->nullable();
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_masuk');
    }
};