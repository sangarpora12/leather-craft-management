<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_boms', function (Blueprint $table) {
            $table->id();
            // Kunci Asing ke Produk Jadi
            $table->foreignId('produk_jadi_id')->constrained('produk_jadis')->onDelete('cascade');
            // Kunci Asing ke Bahan Baku
            $table->foreignId('bahan_baku_id')->constrained('bahan_bakus')->onDelete('cascade');
            
            $table->decimal('quantity_used', 10, 2); // Jumlah bahan yang digunakan (misal: 1.5 meter, 5 pcs)
            $table->string('unit')->default('pcs'); // Satuan untuk quantity_used
            
            $table->timestamps();
            
            // Mencegah duplikasi resep (satu produk tidak boleh menggunakan bahan yang sama dua kali)
            $table->unique(['produk_jadi_id', 'bahan_baku_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_boms');
    }
};