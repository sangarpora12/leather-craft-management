<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BahanBaku;
use App\Models\BahanStokLayer;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BahanBakuMassalSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Memulai proses seeding Bahan Baku Massal...');

        // 1. Ambil data Kategori & Satuan dari tabel Pengaturans
        $kategoriKulit = Pengaturan::where('nama_pengaturan', 'kategori_bahan')->first()->nilai['Kulit Sapi (Genuine)'] ?? 'Kulit Sapi (Genuine)';
        $kategoriAksesoris = Pengaturan::where('nama_pengaturan', 'kategori_bahan')->first()->nilai['Aksesoris Logam'] ?? 'Aksesoris Logam';
        $kategoriPelengkap = Pengaturan::where('nama_pengaturan', 'kategori_bahan')->first()->nilai['Bahan Kimia & Finishing'] ?? 'Bahan Kimia & Finishing';

        $satuanSqft = Pengaturan::where('nama_pengaturan', 'satuan_bahan')->first()->nilai['Feet (ft²)'] ?? 'Feet (ft²)';
        $satuanPcs = Pengaturan::where('nama_pengaturan', 'satuan_bahan')->first()->nilai['pcs'] ?? 'pcs';
        $satuanSet = Pengaturan::where('nama_pengaturan', 'satuan_bahan')->first()->nilai['Set'] ?? 'Set';
        $satuanKg = Pengaturan::where('nama_pengaturan', 'satuan_bahan')->first()->nilai['kg'] ?? 'kg';
        $satuanRoll = Pengaturan::where('nama_pengaturan', 'satuan_bahan')->first()->nilai['Roll'] ?? 'Roll';
        $satuanBotol = Pengaturan::where('nama_pengaturan', 'satuan_bahan')->first()->nilai['Botol'] ?? 'Botol';

        $this->command->info('Data Kategori & Satuan berhasil diambil dari tabel Pengaturans.');

        // 3. Definisikan data bahan baku yang realistis
        $bahanData = [
            // Kategori Kulit
            ['nama' => 'Kulit Sapi Pull Up - Tan', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 28500],
            ['nama' => 'Kulit Sapi Pull Up - Black', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 29000],
            ['nama' => 'Kulit Sapi Vegtan 2mm', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 35000],
            ['nama' => 'Kulit Domba Suede - Brown', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 22000],

            // Kategori Aksesoris
            ['nama' => 'Resleting YKK #5 Coil', 'kategori' => $kategoriAksesoris, 'satuan' => 'Meter', 'harga' => 8500],
            ['nama' => 'Kepala Resleting YKK #5', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 1500],
            ['nama' => 'Ring D 2.5cm - Gold', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 800],
            ['nama' => 'Kancing Magnet Tipis 1.4cm', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanSet, 'harga' => 1200],
            ['nama' => 'Buckle Sabuk 3.5cm - Silver', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 15000],

            // Kategori Pelengkap
            ['nama' => 'Lem Aibon 1kg', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanKg, 'harga' => 65000],
            ['nama' => 'Benang Nilon Waxed 0.8mm - Brown', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanRoll, 'harga' => 25000],
            ['nama' => 'Cat Pinggir Kulit Giardini - Hitam', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanBotol, 'harga' => 85000],
            ['nama' => 'Tokonole Leather Finish - Clear', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanBotol, 'harga' => 70000],

            // Tambahan Baru (Eksotis & Spesifik)
            ['nama' => 'Kulit Sapi Crazy Horse - Dark Brown', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 38000],
            ['nama' => 'Kulit Domba Nappa - Black', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 25000],
            ['nama' => 'Kulit Sapi Emboss Croco - Navy', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 42000],
            ['nama' => 'Solid Brass Buckle 4cm', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 45000],
            ['nama' => 'Rivet Kuningan Bakar 9mm', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 500],
            ['nama' => 'Mink Oil Conditioner', 'kategori' => $kategoriPelengkap, 'satuan' => 'Kaleng', 'harga' => 55000],
            ['nama' => 'Amplas Halus Grid 1000', 'kategori' => $kategoriPelengkap, 'satuan' => 'Lembar', 'harga' => 3000],

            // 50 Data Tambahan Massal
            ['nama' => 'Kulit Kambing Chrome - Hitam', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 18000],
            ['nama' => 'Kulit Sapi Corrected Grain - Coklat', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 20000],
            ['nama' => 'Kulit Ular Python Asli', 'kategori' => $kategoriKulit, 'satuan' => 'Lembar', 'harga' => 150000],
            ['nama' => 'Kain Lining Suede - Cream', 'kategori' => $kategoriPelengkap, 'satuan' => 'Meter', 'harga' => 25000],
            ['nama' => 'Kain Kanvas 10oz - Natural', 'kategori' => $kategoriPelengkap, 'satuan' => 'Meter', 'harga' => 35000],
            ['nama' => 'Benang Serafil 20 - Hitam', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanRoll, 'harga' => 75000],
            ['nama' => 'Benang Katun 1mm - Putih', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanRoll, 'harga' => 15000],
            ['nama' => 'Resleting Besi #8 - Silver', 'kategori' => $kategoriAksesoris, 'satuan' => 'Meter', 'harga' => 12000],
            ['nama' => 'Kepala Resleting #8 - Silver', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 2500],
            ['nama' => 'Magnet Jahit 1.8cm', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanSet, 'harga' => 2000],
            ['nama' => 'Kaki Tas Logam - Gold', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 1500],
            ['nama' => 'Gesper Tas 2cm - Black Nickel', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 5000],
            ['nama' => 'Ring Kotak 3cm - Silver', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 1200],
            ['nama' => 'Busa Ati 3mm', 'kategori' => $kategoriPelengkap, 'satuan' => 'Lembar', 'harga' => 15000],
            ['nama' => 'Busa Angin 5mm', 'kategori' => $kategoriPelengkap, 'satuan' => 'Meter', 'harga' => 10000],
            ['nama' => 'Kertas Pola Coklat', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanRoll, 'harga' => 40000],
            ['nama' => 'Minyak Zaitun (Finishing)', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanBotol, 'harga' => 25000],
            ['nama' => 'Cat Semprot Clear Doff', 'kategori' => $kategoriPelengkap, 'satuan' => 'Kaleng', 'harga' => 35000],
            ['nama' => 'Kulit Ikan Pari - Hitam', 'kategori' => $kategoriKulit, 'satuan' => 'Lembar', 'harga' => 250000],
            ['nama' => 'Kulit Kelinci - Putih', 'kategori' => $kategoriKulit, 'satuan' => 'Lembar', 'harga' => 75000],
            ['nama' => 'Tali Kulit 3mm - Coklat Tua', 'kategori' => $kategoriPelengkap, 'satuan' => 'Meter', 'harga' => 5000],
            ['nama' => 'Gunting Benang', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 8000],
            ['nama' => 'Jarum Jahit Tangan Kulit', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanSet, 'harga' => 15000],
            ['nama' => 'Pembolong Kulit (Hole Punch)', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanSet, 'harga' => 80000],
            ['nama' => 'Lem PVC', 'kategori' => $kategoriPelengkap, 'satuan' => 'Kaleng', 'harga' => 30000],
            ['nama' => 'Thinner A Special', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanBotol, 'harga' => 20000],
            ['nama' => 'Kancing Knop - Silver', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanSet, 'harga' => 1000],
            ['nama' => 'Mata Ayam 5mm - Gold', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 100],
            ['nama' => 'Hook Gantungan Kunci - Silver', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 2500],
            ['nama' => 'Logo Plat Besi Custom', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 15000],
            ['nama' => 'Kulit Pull Up - Hijau Army', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 32000],
            ['nama' => 'Kulit Vegtan - Natural 1.5mm', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 30000],
            ['nama' => 'Kain Drill - Hitam', 'kategori' => $kategoriPelengkap, 'satuan' => 'Meter', 'harga' => 28000],
            ['nama' => 'Benang Polyester No.30 - Navy', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanRoll, 'harga' => 20000],
            ['nama' => 'Resleting AquaProof #5', 'kategori' => $kategoriAksesoris, 'satuan' => 'Meter', 'harga' => 15000],
            ['nama' => 'Ring Jalan 2.5cm - Black Nickel', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 1800],
            ['nama' => 'Spon Eva 2mm', 'kategori' => $kategoriPelengkap, 'satuan' => 'Lembar', 'harga' => 12000],
            ['nama' => 'Beeswax Block (Lilin Lebah)', 'kategori' => $kategoriPelengkap, 'satuan' => 'Blok', 'harga' => 25000],
            ['nama' => 'Dye Kulit Fiebings - Dark Brown', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanBotol, 'harga' => 95000],
            ['nama' => 'Kulit Sapi Milled - Tan', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 27000],
            ['nama' => 'Rivet Double Cap 8mm - Gold', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 300],
            ['nama' => 'Tali Rami 5mm', 'kategori' => $kategoriPelengkap, 'satuan' => 'Meter', 'harga' => 2000],
            ['nama' => 'Penggaris Besi 50cm', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 15000],
            ['nama' => 'Cutter Blade Refill', 'kategori' => $kategoriAksesoris, 'satuan' => 'Set', 'harga' => 10000],
            ['nama' => 'Skiving Tool (Alat Tulis Kulit)', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 60000],
            ['nama' => 'Lem Super (Cyanoacrylate)', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanBotol, 'harga' => 8000],
            ['nama' => 'Kain Microfiber', 'kategori' => $kategoriPelengkap, 'satuan' => $satuanPcs, 'harga' => 12000],
            ['nama' => 'Zipper Puller Kulit - Coklat', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanPcs, 'harga' => 4000],
            ['nama' => 'Kancing Jeans - Tembaga', 'kategori' => $kategoriAksesoris, 'satuan' => $satuanSet, 'harga' => 1500],
            ['nama' => 'Kulit Domba - Merah Maroon', 'kategori' => $kategoriKulit, 'satuan' => $satuanSqft, 'harga' => 23000],
        ];

        $this->command->info('Memulai iterasi dan pembuatan data...');
        $progressBar = $this->command->getOutput()->createProgressBar(count($bahanData));

        // 4. Looping dan buat data
        foreach ($bahanData as $data) {
            // Logika stok acak: 10% habis, 20% di bawah 10, 70% normal
            $chance = rand(1, 100);
            if ($chance <= 10) {
                $stokAwal = 0; // 10% kemungkinan stok habis
            } elseif ($chance <= 30) {
                $stokAwal = rand(1, 9); // 20% kemungkinan stok di bawah 10
            } else {
                $stokAwal = rand(10, 50); // 70% kemungkinan stok normal
            }

            // Buat record di tabel bahan_bakus
            $bahanBaku = BahanBaku::create([
                'kode_material' => BahanBaku::generateKodeMaterial($data['kategori']),
                'nama_material' => $data['nama'],
                'kategori' => $data['kategori'],
                'satuan' => $data['satuan'],
                'stok' => $stokAwal,
                'stok_minimum' => 5, // Default stok minimum
                'harga_satuan' => $data['harga'],
                'supplier' => 'Supplier Demo',
            ]);

            // Hanya buat riwayat jika stok awal lebih dari 0
            if ($stokAwal > 0) {
                BahanStokLayer::create([
                    'bahan_baku_id' => $bahanBaku->id,
                    'tanggal_masuk' => Carbon::now(),
                    'qty_masuk' => $stokAwal,
                    'qty_sisa' => $stokAwal,
                    'harga_beli' => $data['harga'],
                    'sumber_harga' => 'SEEDER-INITIAL',
                ]);
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->command->info('\nProses seeding Bahan Baku Massal telah selesai. '.count($bahanData).' data berhasil dibuat.');
    }
}