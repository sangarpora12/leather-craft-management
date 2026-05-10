<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produksi;

class CleanOrphanedProduksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Membersihkan data Produksi yang orphaned...');
        
        // Cari semua produksi dengan produk_jadi_id NULL
        $orphanedProduksi = Produksi::whereNull('produk_jadi_id')->get();
        
        $count = $orphanedProduksi->count();
        $this->command->info("Jumlah data orphaned: $count");
        
        if ($count > 0) {
            // Tampilkan detail data yang akan dihapus
            $this->command->info('Data yang akan dihapus:');
            foreach ($orphanedProduksi as $produksi) {
                $this->command->info("- SPK: {$produksi->kode_spk}, Status: {$produksi->status}, Jumlah: {$produksi->jumlah_produksi} Pcs");
            }
            
            // Hapus semua data orphaned
            $deleted = Produksi::whereNull('produk_jadi_id')->delete();
            
            $this->command->info("✅ Berhasil menghapus $deleted data Produksi orphaned!");
        } else {
            $this->command->info('Tidak ada data orphaned yang perlu dibersihkan.');
        }
        
        // Cek total data setelah pembersihan
        $totalAfter = Produksi::count();
        $this->command->info("Total data Produksi sekarang: $totalAfter");
    }
}