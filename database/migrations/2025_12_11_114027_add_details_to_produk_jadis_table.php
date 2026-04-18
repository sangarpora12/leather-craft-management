<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produk_jadis', function (Blueprint $table) {
            $table->string('warna')->nullable()->after('nama_produk');
            $table->string('jenis_motif')->nullable()->after('warna');
        });
    }

    public function down()
    {
        Schema::table('produk_jadis', function (Blueprint $table) {
            $table->dropColumn(['warna', 'jenis_motif']);
        });
    }
};