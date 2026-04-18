<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produksi;
use App\Models\Pengadaan;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    public function run()
    {
        // Ambil data pertama agar tidak error
        $produk = ProdukJadi::first();
        $bahan = BahanBaku::first();

        if (!$produk || !$bahan) {
            $this->command->info('Isi dulu minimal 1 Produk dan 1 Bahan Baku di database!');
            return;
        }

        for ($i = 0; $i < 7; $i++) {
            $tanggal = Carbon::now()->subDays($i);

            // Data Produksi (Batang Merah/Keluar)
            Produksi::create([
                'kode_spk' => 'SPK-DUMMY-' . $i,
                'produk_jadi_id' => $produk->id,
                'jumlah_produksi' => rand(10, 50),
                'status' => 'PROSES',
                'created_at' => $tanggal,
            ]);

            // Data Pengadaan (Batang Hijau/Masuk)
            Pengadaan::create([
                'kode_pengadaan' => 'PO-DUMMY-' . $i,
                'bahan_baku_id' => $bahan->id,
                'jumlah' => rand(30, 80),
                'status' => 'SELESAI',
                'created_at' => $tanggal,
            ]);
        }
        $this->command->info('Data Grafik Berhasil Diisi!');
    }
}