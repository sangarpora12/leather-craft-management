<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produksi;
use App\Models\Pengadaan;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use Carbon\Carbon;

class AdvancedSimulationSeeder extends Seeder
{
    public function run()
    {
        $produk = ProdukJadi::all();
        $bahan = BahanBaku::all();

        if ($produk->isEmpty() || $bahan->isEmpty()) {
            $this->command->error('Isi dulu data Produk dan Bahan Baku di database!');
            return;
        }

        $this->command->info('Memulai simulasi data untuk 6 bulan terakhir...');

        for ($i = 180; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);
            
            // 1. Simulasi Produksi
            $jumlahTransaksiProduksi = rand(0, 2);
            for ($j = 0; $j < $jumlahTransaksiProduksi; $j++) {
                $p = $produk->random();
                Produksi::create([
                    // Pakai 'kode_spk' jika ada, jika error hapus baris ini
                    'kode_spk' => 'SPK-' . $tanggal->format('YmdHis') . $j, 
                    'produk_jadi_id' => $p->id,
                    'jumlah_produksi' => rand(5, 30),
                    'status' => 'SELESAI',
                    'created_at' => $tanggal,
                ]);
            }

            // 2. Simulasi Pengadaan (Tanpa kolom kode_pengadaan agar aman)
            if (rand(1, 10) > 8) { 
                $b = $bahan->random();
                Pengadaan::create([
                    'bahan_baku_id' => $b->id,
                    'jumlah' => rand(50, 150),
                    'status' => 'SELESAI',
                    'created_at' => $tanggal,
                ]);
            }
        }

        $this->command->info('Simulasi selesai! Data sudah masuk ke database.');
    }
}