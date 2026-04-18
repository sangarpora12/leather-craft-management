<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bahan_bakus') && !Schema::hasColumn('bahan_bakus', 'user_id')) {
            Schema::table('bahan_bakus', function (Blueprint $table) {
                // Menambahkan kolom user_id sebagai foreign key ke tabel users
                // nullable() agar data lama tidak error
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('bahan_bakus') && Schema::hasColumn('bahan_bakus', 'user_id')) {
            Schema::table('bahan_bakus', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }
    }
};