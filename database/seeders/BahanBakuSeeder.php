<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;
use App\Models\BahanStokLayer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BahanBakuSeeder extends Seeder
{
    public function run()
    {
        // 1. Ambil ID Admin atau Operator untuk penginput
        $user = User::where('role', 'admin')->first() ?? User::first();

        // 2. Data Bahan Baku
        $bahan = BahanBaku::create([
            'kode_material' => 'BHN-0001',
            'nama_material' => 'Kulit Sapi Nappa Black',
            'kategori'      => 'Kulit',
            'satuan'        => 'Feet',
            'stok'          => 50.5, // Contoh input desimal
            'harga_satuan'  => 35000,
            'is_inventory'  => true,
        ]);

        // 3. PENTING: Isi tabel Layer FIFO agar harga beli tidak kosong
        BahanStokLayer::create([
            'bahan_baku_id' => $bahan->id,
            'tanggal_masuk' => now(),
            'qty_masuk'     => 50.5,
            'qty_sisa'      => 50.5,
            'harga_beli'    => 35000,
            'sumber_harga'  => 'Seeder',
        ]);

        // 4. PENTING: Isi tabel Log Stok agar penginput tidak kosong di laporan
        DB::table('log_stok')->insert([
            'bahan_baku_id'   => $bahan->id,
            'operator_id'     => $user->id, // Nama penginput diambil dari sini
            'jenis_transaksi' => 'INPUT_AWAL',
            'jumlah'          => 50.5,
            'keterangan'      => 'Input awal via Seeder',
            'created_at'      => now(),
        ]);
    }
}