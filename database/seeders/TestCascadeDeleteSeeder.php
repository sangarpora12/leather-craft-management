<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProdukJadi;
use App\Models\Produksi;

class TestCascadeDeleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil produk pertama
        $produk = ProdukJadi::first();
        
        if (!$produk) {
            $this->command->info('Tidak ada produk untuk di-test');
            return;
        }
        
        $this->command->info('Produk ID: ' . $produk->id . ' - ' . $produk->nama_produk);
        
        // Buat perintah produksi untuk produk ini
        $produksi = Produksi::create([
            'kode_spk' => 'TEST-CASCADE-001',
            'produk_jadi_id' => $produk->id,
            'jumlah_produksi' => 10,
            'status' => 'PROSES',
            'tanggal_mulai' => now(),
            'tanggal_target' => now()->addDays(7),
        ]);
        
        $this->command->info('Produksi ID: ' . $produksi->id . ' dibuat untuk produk ID: ' . $produk->id);
        
        // Hitung jumlah produksi sebelum delete
        $countBefore = Produksi::count();
        $this->command->info('Jumlah produksi sebelum delete: ' . $countBefore);
        
        // Hapus produk
        $this->command->info('Menghapus produk ID: ' . $produk->id);
        $produk->delete();
        
        // Hitung jumlah produksi setelah delete
        $countAfter = Produksi::count();
        $this->command->info('Jumlah produksi setelah delete: ' . $countAfter);
        
        // Cek apakah cascade delete berhasil
        $produksiDeleted = Produksi::find($produksi->id) === null;
        
        if ($produksiDeleted) {
            $this->command->info('✅ CASCADE DELETE BERHASIL! Produksi ikut terhapus.');
        } else {
            $this->command->info('❌ CASCADE DELETE GAGAL! Produksi masih ada.');
        }
    }
}