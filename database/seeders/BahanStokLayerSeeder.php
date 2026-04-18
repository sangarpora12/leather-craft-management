<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BahanBaku;
use App\Models\BahanStokLayer;
use Carbon\Carbon;

class BahanStokLayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Fungsi ini untuk migrasi stok lama ke tabel layer.
     * Cari semua data di bahan_bakus yang stok > 0,
     * lalu buat satu record default di bahan_stok_layers.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key check untuk keamanan
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Kosongkan tabel layer terlebih dahulu (jika ingin fresh start)
        // Atau bisa skip baris ini jika ingin preserve existing layers
        // BahanStokLayer::truncate();
        
        $count = 0;
        $today = Carbon::now()->toDateString();
        
        // Ambil semua bahan baku yang memiliki stok > 0
        $bahanBakus = BahanBaku::where('stok', '>', 0)->get();
        
        echo "Menemukan {$bahanBakus->count()} bahan baku dengan stok > 0\n";
        
        foreach ($bahanBakus as $bahan) {
            // Cek apakah sudah ada layer untuk bahan ini
            $existingLayer = BahanStokLayer::where('bahan_baku_id', $bahan->id)->first();
            
            if ($existingLayer) {
                echo "Bahan {$bahan->nama_material} sudah punya layer, skip...\n";
                continue;
            }
            
            // Ambil harga_beli dari harga_satuan master
            // Jika tidak ada, coba ambil dari supplier katalog harga
            $hargaBeli = (float) $bahan->harga_satuan ?? 0;
            
            // Jika harga_satuan kosong, coba cari dari harga katalog supplier
            if ($hargaBeli == 0 && !empty($bahan->supplier)) {
                // Coba cari harga dari tabel supplier_katalog_harga jika ada
                // Untuk saat ini, set 0 jika tidak ada harga
                $hargaBeli = 0;
            }
            
            // Buat layer baru
            BahanStokLayer::create([
                'bahan_baku_id' => $bahan->id,
                'tanggal_masuk' => $today, // Tanggal hari ini sebagai default
                'qty_masuk' => (float) $bahan->stok,
                'qty_sisa' => (float) $bahan->stok,
                'harga_beli' => $hargaBeli,
                'sumber_harga' => !empty($bahan->supplier) ? $bahan->supplier : 'Migrasi Stok Awal',
            ]);
            
            $count++;
            echo "Berhasil migrasi: {$bahan->nama_material} - Stok: {$bahan->stok}, Harga: " . number_format($hargaBeli, 2) . "\n";
        }
        
        // Aktifkan kembali foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        echo "\n=== MIGRASI SELESAI ===\n";
        echo "Total bahan yang dimigrasikan: {$count}\n";
        echo "Anda bisa menjalankan: php artisan db:seed --class=BahanStokLayerSeeder\n";
    }
}
