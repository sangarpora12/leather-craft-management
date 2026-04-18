<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->string('supplier')->nullable()->after('harga_satuan');
            $table->string('ref_no')->nullable()->after('supplier');
        });
    }

    public function down()
    {
        Schema::table('bahan_bakus', function (Blueprint $table) {
            $table->dropColumn(['supplier', 'ref_no']);
        });
    }
};
