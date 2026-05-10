<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengadaans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengadaans', 'warna')) {
                $table->string('warna')->nullable()->after('bahan_baku_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengadaans', function (Blueprint $table) {
            if (Schema::hasColumn('pengadaans', 'warna')) {
                $table->dropColumn('warna');
            }
        });
    }
};