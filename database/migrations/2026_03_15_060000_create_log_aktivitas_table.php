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
        Schema::create('log_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('modul'); // Contoh: 'Bahan Baku', 'Pengaturan Keuangan'
            $table->text('aktivitas'); // Deskripsi lengkap aksi
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            // Index untuk performa pencarian
            $table->index('modul');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_aktivitas');
    }
};