<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Helpers\WarnaHelper;

return new class extends Migration
{
    /**
     * Backfill kode_warna untuk data warna yang sudah ada sebelumnya (NULL).
     */
    public function up(): void
    {
        $warnas = DB::table('warnas')->whereNull('kode_warna')->get();

        foreach ($warnas as $warna) {
            $hex = WarnaHelper::detectColor($warna->nama_warna);
            DB::table('warnas')
                ->where('id', $warna->id)
                ->update(['kode_warna' => $hex]);
        }
    }

    /**
     * Reverse the migrations — tidak perlu undo.
     */
    public function down(): void
    {
        // Tidak ada yang perlu di-reverse
    }
};
