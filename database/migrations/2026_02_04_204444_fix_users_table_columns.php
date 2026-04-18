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
    Schema::table('users', function (Blueprint $table) {
        // Jika kolom 'name' belum ada, kita buat
        if (!Schema::hasColumn('users', 'name')) {
            $table->string('name')->nullable()->after('id');
        }
        
        // Hapus kolom 'nama' lama jika ada (opsional, biar rapi)
        // if (Schema::hasColumn('users', 'nama')) {
        //    $table->dropColumn('nama');
        // }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('name');
    });
}
};
