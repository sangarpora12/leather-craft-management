<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\BahanBaku;
use App\Models\ProdukJadi;
use App\Models\User;

class GrafikSimulatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Memulai Grafik Simulator Seeder...');

        // Hapus data lama agar tidak duplikat
        DB::table('log_stok')->whereDate('created_at', Carbon::today())->delete();
        DB::table('produksis')->whereDate('tanggal_selesai', Carbon::today())->delete();

        $bahanBakus = BahanBaku::pluck('id')->toArray();
        $produkJadis = ProdukJadi::pluck('id')->toArray();
        $operator = User::where('role', 'operator')->first();

        if (empty($bahanBakus) || empty($produkJadis) || !$operator) {
            $this->command->error('Pastikan ada data master (Bahan Baku, Produk Jadi, User Operator) sebelum menjalankan seeder ini.');
            return;
        }

        // 1. Seed 'log_stok' (Bahan Baku)
        $logStokData = [];
        for ($i = 0; $i < 50; $i++) {
            $jenis = (rand(0, 1) == 1) ? 'MASUK' : 'KELUAR';
            $logStokData[] = [
                'bahan_baku_id' => $bahanBakus[array_rand($bahanBakus)],
                'jenis_transaksi' => $jenis,
                'jumlah' => rand(5, 50),
                'satuan' => 'PCS',
                'keterangan' => 'Seeder ' . $jenis,
                'user_id' => $operator->id,
                'created_at' => Carbon::now()->subHours(rand(0, 10))->subMinutes(rand(0, 59)),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('log_stok')->insert($logStokData);
        $this->command->info('-> 50 data log_stok (MASUK/KELUAR) berhasil dibuat untuk hari ini.');

        // 2. Seed 'produksis' (Produk Jadi Selesai)
        $produksiData = [];
        for ($i = 0; $i < 10; $i++) {
            $produkJadiId = $produkJadis[array_rand($produkJadis)];
            $jumlah = rand(10, 30);
            $produksiData[] = [
                'kode_spk' => 'SPK-SEEDER-' . uniqid(),
                'produk_jadi_id' => $produkJadiId,
                'jumlah_produksi' => $jumlah,
                'jumlah_selesai' => $jumlah,
                'status' => 'SELESAI',
                'operator_id' => $operator->id,
                'tanggal_mulai' => Carbon::now()->subHours(rand(11, 12)),
                'tanggal_target' => Carbon::now()->addDay(),
                'tanggal_selesai' => Carbon::now()->subHours(rand(0, 10))->subMinutes(rand(0, 59)),
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ];
        }
        DB::table('produksis')->insert($produksiData);
        $this->command->info('-> 10 data produksi (SELESAI) berhasil dibuat untuk hari ini.');

        $this->command->info('Seeder selesai. Grafik dashboard sekarang seharusnya memiliki data.');
    }
}