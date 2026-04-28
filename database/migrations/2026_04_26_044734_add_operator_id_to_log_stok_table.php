<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOperatorIdToLogStokTable extends Migration
{
    public function up(): void
    {
        Schema::table('log_stok', function (Blueprint $table) {
            // Menambahkan kolom operator_id setelah bahan_baku_id
            $table->unsignedBigInteger('operator_id')->nullable()->after('bahan_baku_id');
        });
    }

    public function down(): void
    {
        Schema::table('log_stok', function (Blueprint $table) {
            $table->dropColumn('operator_id');
        });
    }
}