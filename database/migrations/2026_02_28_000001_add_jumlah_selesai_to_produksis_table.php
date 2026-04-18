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
            // Track how many units have been completed in partial production
            if (!Schema::hasColumn('produksis', 'jumlah_selesai')) {
                $table->integer('jumlah_selesai')->default(0)->after('jumlah_produksi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produksis', function (Blueprint $table) {
            $table->dropColumn('jumlah_selesai');
        });
    }
};
