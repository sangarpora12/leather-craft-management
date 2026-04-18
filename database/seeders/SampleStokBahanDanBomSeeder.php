<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleStokBahanDanBomSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder ini mengisi:
        // - 20 bahan baku (lengkap: supplier + ref_no/nota + stok + harga)
        // - 5 produk jadi
        // - BOM untuk tiap produk (4 bahan per produk)
        //
        // Dibuat idempotent via kode unik (kode_material/kode_produk).

        DB::transaction(function () {
            $now = now();

            // --- 1) BAHAN BAKU ---
            $suppliers = [
                'CV Kulit Jaya',
                'PT Aksesoris Nusantara',
                'UD Kimia Makmur',
                'Toko Packaging Sentosa',
            ];

            $kategories = [
                'Bahan Utama',
                'Aksesoris',
                'Kimia',
                'Packaging',
            ];

            $bahanList = [
                // nama, kategori, satuan
                ['Kulit Sapi Full Grain 1.4mm', 'Bahan Utama', 'Lembar'],
                ['Kulit Sapi Pull-Up 1.2mm', 'Bahan Utama', 'Lembar'],
                ['Kulit Kambing Lining 0.8mm', 'Bahan Utama', 'Lembar'],
                ['Kain Suede Microfiber', 'Bahan Utama', 'Meter'],
                ['Kain Kanvas 12oz', 'Bahan Utama', 'Meter'],
                ['Benang Nylon 20s', 'Aksesoris', 'Roll'],
                ['Benang Polyester 30s', 'Aksesoris', 'Roll'],
                ['Resleting YKK #5', 'Aksesoris', 'Pcs'],
                ['Resleting YKK #3', 'Aksesoris', 'Pcs'],
                ['Magnet Button 18mm', 'Aksesoris', 'Pcs'],
                ['Ring D 25mm', 'Aksesoris', 'Pcs'],
                ['Buckle 25mm', 'Aksesoris', 'Pcs'],
                ['Lem Kuning (Contact Adhesive)', 'Kimia', 'Liter'],
                ['Lem Putih (PVAc)', 'Kimia', 'Liter'],
                ['Edge Paint Hitam', 'Kimia', 'Liter'],
                ['Edge Paint Coklat', 'Kimia', 'Liter'],
                ['Kertas Pola A3', 'Packaging', 'Pcs'],
                ['Dus Kraft Medium', 'Packaging', 'Box'],
                ['Plastic OPP 20x30', 'Packaging', 'Pcs'],
                ['Hangtag Berliano', 'Packaging', 'Pcs'],
            ];

            $bahanIds = [];
            foreach ($bahanList as $idx => [$nama, $kat, $satuan]) {
                $kode = 'SMP-BHN-' . str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT);

                $supplier = $suppliers[$idx % count($suppliers)];
                $refNo = 'NOTA-' . $now->format('ymd') . '-' . str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT);

                // stok & harga dibuat bervariasi tapi masuk akal
                $stok = match ($satuan) {
                    'Lembar' => random_int(5, 40),
                    'Meter' => random_int(10, 200),
                    'Roll' => random_int(5, 30),
                    'Liter' => random_int(5, 50),
                    'Box' => random_int(5, 40),
                    default => random_int(20, 300),
                };

                $harga = match ($kat) {
                    'Bahan Utama' => random_int(150000, 650000),
                    'Aksesoris' => random_int(2000, 35000),
                    'Kimia' => random_int(45000, 250000),
                    'Packaging' => random_int(500, 25000),
                    default => random_int(1000, 100000),
                };

                $stokMinimum = random_int(5, 25);

                DB::table('bahan_bakus')->updateOrInsert(
                    ['kode_material' => $kode],
                    [
                        'user_id' => 1,
                        'nama_material' => $nama,
                        'kategori' => $kat,
                        'stok' => $stok,
                        'reserved' => 0,
                        'satuan' => $satuan,
                        'harga_satuan' => $harga,
                        'supplier' => $supplier,
                        'ref_no' => $refNo,
                        'stok_minimum' => $stokMinimum,
                        'status' => 'aktif',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $id = (int) DB::table('bahan_bakus')->where('kode_material', $kode)->value('id');
                $bahanIds[] = $id;
            }

            // --- 2) PRODUK JADI ---
            $produkList = [
                ['Sling Bag "Nusa"', 'Tas', 'Coklat', 'Polos'],
                ['Wallet "Sagara"', 'Dompet', 'Hitam', 'Polos'],
                ['Tote Bag "Arunika"', 'Tas', 'Tan', 'Textured'],
                ['Card Holder "Raya"', 'Aksesoris', 'Hitam', 'Polos'],
                ['Belt "Garuda"', 'Aksesoris', 'Coklat', 'Textured'],
            ];

            $produkIds = [];
            foreach ($produkList as $idx => [$nama, $kat, $warna, $motif]) {
                $kode = 'SMP-PRD-' . str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT);

                DB::table('produk_jadis')->updateOrInsert(
                    ['kode_produk' => $kode],
                    [
                        'nama_produk' => $nama,
                        'kategori' => $kat,
                        'warna' => $warna,
                        'jenis_motif' => $motif,
                        'harga_jual' => random_int(150000, 1200000),
                        'deskripsi' => 'Sample produk untuk testing BOM & produksi.',
                        'stok' => random_int(0, 50),
                        'stok_minimum' => random_int(2, 10),
                        'foto' => null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );

                $id = (int) DB::table('produk_jadis')->where('kode_produk', $kode)->value('id');
                $produkIds[] = $id;
            }

            // --- 3) BOM ---
            // Tabel BOM di project ini adalah `bom` (bukan `boms`).
            // Kolom: id_produk, id_bahan, jumlah_kebutuhan, satuan
            foreach ($produkIds as $pIdx => $produkId) {
                // pilih 4 bahan berbeda untuk tiap produk
                $selected = collect($bahanIds)->shuffle()->take(4)->values();

                foreach ($selected as $bahanId) {
                    $satuanBahan = (string) DB::table('bahan_bakus')->where('id', $bahanId)->value('satuan');
                    $jumlah = match ($satuanBahan) {
                        'Lembar' => 1,
                        'Meter' => (float) number_format(mt_rand(10, 80) / 10, 1, '.', ''), // 1.0 - 8.0
                        'Roll' => 1,
                        'Liter' => (float) number_format(mt_rand(1, 20) / 10, 1, '.', ''), // 0.1 - 2.0
                        default => random_int(1, 10),
                    };

                    // Hindari duplikat: unique by (id_produk, id_bahan)
                    DB::table('bom')->updateOrInsert(
                        ['id_produk' => $produkId, 'id_bahan' => $bahanId],
                        [
                            'jumlah_kebutuhan' => $jumlah,
                            'satuan' => $satuanBahan,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    );
                }
            }
        });
    }
}
