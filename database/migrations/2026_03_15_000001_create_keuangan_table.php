<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini mas!

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('keuangan')) {
            Schema::create('keuangan', function (Blueprint $table) {
                $table->id();
                $table->string('kategori'); 
                $table->decimal('total', 15, 2)->default(0);
                $table->timestamps();
            });

            // Isi data awal kategori aset_bahan
            DB::table('keuangan')->insert([
                'kategori' => 'aset_bahan',
                'total' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('keuangan');
    }
};