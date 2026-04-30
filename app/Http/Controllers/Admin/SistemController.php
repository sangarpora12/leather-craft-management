<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SistemController extends Controller
{
    /**
     * Menjalankan perintah Artisan untuk membersihkan cache.
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');

        return back()->with('success', 'Cache, view, config, dan route berhasil dibersihkan.');
    }

    /**
     * Melakukan reset data parsial berdasarkan pilihan dari form.
     */
    public function resetData(Request $request)
    {
        $request->validate([
            'tables' => 'required|array',
        ]);

        $tablesToTruncate = $request->input('tables');
        // Daftar tabel transaksi yang aman untuk di-truncate
        $availableTables = [
            'produksi', 'produksi_details', 'penjualan', 'penjualan_details',
            'pembelian', 'pembelian_details', 'bahan_baku_histories', 'stok_opname'
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tablesToTruncate as $table) {
            if (in_array($table, $availableTables) && Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'Data pada tabel yang dipilih berhasil di-reset.');
    }

    /**
     * Melakukan reset total (KIAMAT) pada data transaksi dan master.
     */
    public function superReset()
    {
        $tables = [
            // Transaksi
            'produksi', 'produksi_details', 'penjualan', 'penjualan_details',
            'pembelian', 'pembelian_details', 'bahan_baku_histories', 'stok_opname',
            'absensi', 'gaji', 'kas_histories',
            // Master yang terkait erat dengan transaksi
            'produk_jadi', 'bahan_baku', 'kontak'
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'SUPER RESET (KIAMAT) BERHASIL. Semua data transaksi dan master terkait telah dihapus.');
    }
}