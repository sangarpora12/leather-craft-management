<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Pengaturan;
use App\Models\Bom;
use Illuminate\Support\Facades\DB;

class ProdukJadiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai proses seeding Produk Jadi...');

        // Ambil margin keuntungan dari pengaturan
        $marginKeuntungan = (float) (Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 30);
        $this->command->info("Margin Keuntungan diatur ke: {$marginKeuntungan}%");

        // Definisikan produk dan resepnya (BOM)
        $products = [
            [
                'nama' => 'Dompet Kulit Klasik - Pull Up Tan',
                'kode' => 'PROD-001',
                'resep' => [
                    ['nama_bahan' => 'Kulit Sapi Pull Up - Tan', 'qty' => 0.7],
                    ['nama_bahan' => 'Benang Nilon Waxed 0.8mm - Brown', 'qty' => 5],
                    ['nama_bahan' => 'Lem Aibon 1kg', 'qty' => 0.02],
                    ['nama_bahan' => 'Cat Pinggir Kulit Giardini - Hitam', 'qty' => 0.01],
                ],
            ],
            [
                'nama' => 'Tas Selempang Kanvas & Kulit',
                'kode' => 'PROD-002',
                'resep' => [
                    ['nama_bahan' => 'Kain Kanvas 10oz - Natural', 'qty' => 1],
                    ['nama_bahan' => 'Kulit Sapi Corrected Grain - Coklat', 'qty' => 0.5],
                    ['nama_bahan' => 'Resleting YKK #5 Coil', 'qty' => 1],
                    ['nama_bahan' => 'Kepala Resleting YKK #5', 'qty' => 1],
                    ['nama_bahan' => 'Ring D 2.5cm - Gold', 'qty' => 2],
                ],
            ],
            [
                'nama' => 'Gantungan Kunci Kulit Vegtan',
                'kode' => 'PROD-003',
                'resep' => [
                    ['nama_bahan' => 'Kulit Vegtan - Natural 1.5mm', 'qty' => 0.2],
                    ['nama_bahan' => 'Hook Gantungan Kunci - Silver', 'qty' => 1],
                    ['nama_bahan' => 'Tokonole Leather Finish - Clear', 'qty' => 0.05],
                ],
            ],
            [
                'nama' => 'Ikat Pinggang Pria - Buckle Silver',
                'kode' => 'PROD-004',
                'resep' => [
                    ['nama_bahan' => 'Kulit Sapi Vegtan 2mm', 'qty' => 1.2],
                    ['nama_bahan' => 'Buckle Sabuk 3.5cm - Silver', 'qty' => 1],
                    ['nama_bahan' => 'Rivet Double Cap 8mm - Gold', 'qty' => 2],
                ],
            ],
            [
                'nama' => 'Clutch Wanita - Kulit Domba Merah',
                'kode' => 'PROD-005',
                'resep' => [
                    ['nama_bahan' => 'Kulit Domba - Merah Maroon', 'qty' => 1.5],
                    ['nama_bahan' => 'Kain Lining Suede - Cream', 'qty' => 0.5],
                    ['nama_bahan' => 'Resleting Besi #8 - Silver', 'qty' => 0.3],
                    ['nama_bahan' => 'Kancing Magnet Tipis 1.4cm', 'qty' => 1],
                ],
            ],
        ];

        $progressBar = $this->command->getOutput()->createProgressBar(count($products));

        foreach ($products as $productData) {
            // Cek apakah produk sudah ada
            $produk = ProdukJadi::where('kode_produk', $productData['kode'])->first();
            $hpp = 0;
            $bahanNotFound = [];

            // Buat atau update produk
            if (!$produk) {
                $produk = new ProdukJadi();
                $produk->kode_produk = $productData['kode'];
            }
            
            $produk->nama_produk = $productData['nama'];
            $produk->stok = 0; // Stok awal diatur ke 0
            $produk->harga_jual = 0; // Nilai awal Harga Jual
            $produk->save(); // Simpan produk untuk mendapatkan ID
            
            // Hapus BOM lama jika ada untuk memastikan resep selalu terupdate
            Bom::where('id_produk', $produk->id)->delete();

            // Buat BOM baru dan hitung total biaya bahan
            $totalBiayaBahan = 0;
            foreach ($productData['resep'] as $item) {
                $bahan = BahanBaku::where('nama_material', $item['nama_bahan'])->first();
                if ($bahan && $bahan->harga_satuan > 0) {
                    $totalBiayaBahan += $bahan->harga_satuan * $item['qty'];
                    Bom::create([
                        'id_produk' => $produk->id,
                        'id_bahan' => $bahan->id,
                        'jumlah_kebutuhan' => $item['qty'],
                        'satuan' => $bahan->satuan, // Menambahkan satuan dari bahan baku
                    ]);
                } else {
                    $bahanNotFound[] = $item['nama_bahan'];
                }
            }

            if (!empty($bahanNotFound)) {
                $this->command->warn("\n[Peringatan] Untuk produk '{$productData['nama']}', bahan berikut tidak ditemukan atau harga 0: " . implode(', ', $bahanNotFound));
                $produk->delete(); // Hapus produk jika resep tidak lengkap
                continue; // Lanjut ke produk berikutnya
            }

            // Update produk dengan harga jual final
            $produk->harga_jual = $totalBiayaBahan * (1 + ($marginKeuntungan / 100));
            $produk->save();

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->info("\nProses seeding 5 Produk Jadi telah selesai.");
    }
}