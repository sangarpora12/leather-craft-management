<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $nilaiYangDikonversi = ['POLOS', 'N/A', 'NULL', '-', '', 'TIDAK ADA WARNA'];
        
        DB::table('bahan_bakus')
            ->whereNull('warna')
            ->orWhereIn(DB::raw('UPPER(warna)'), $nilaiYangDikonversi)
            ->update(['warna' => 'COKELAT MUDA']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Migrasi ini tidak memiliki proses "down" yang sederhana karena kita tidak tahu
        // nilai asli dari setiap baris yang diubah. Jika diperlukan, pemulihan
        // harus dilakukan dari backup database.
    }
};