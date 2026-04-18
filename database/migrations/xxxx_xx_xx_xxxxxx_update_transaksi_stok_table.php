
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransaksiStokTable extends Migration
{
    public function up()
    {
        Schema::table('transaksi_stok', function (Blueprint $table) {
            $table->text('keterangan')->change();
        });
    }

    public function down()
    {
        Schema::table('transaksi_stok', function (Blueprint $table) {
            $table->string('keterangan', 255)->change();
        });
    }
}