<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BahanBaku;
use App\Models\ProdukJadi;
use App\Models\Produksi;
use App\Models\ProductBom;
use App\Models\User;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Bahan Baku
        $bahanBaku = [
            [
                'nama_material' => 'Kulit Sapi Premium',
                'stok' => 150,
                'stok_minimum' => 50,
                'harga_satuan' => 25000,
                'satuan' => 'lembar',
                'supplier' => 'CV. Kulit Indonesia',
                'ref_no' => 'KB001',
                'user_id' => 1
            ],
            [
                'nama_material' => 'Benang Nilon Waxed',
                'stok' => 25,
                'stok_minimum' => 30,
                'harga_satuan' => 15000,
                'satuan' => 'roll',
                'supplier' => 'PT. Benang Maju',
                'ref_no' => 'BN001',
                'user_id' => 1
            ],
            [
                'nama_material' => 'Lem Aica Bond',
                'stok' => 8,
                'stok_minimum' => 10,
                'harga_satuan' => 45000,
                'satuan' => 'kg',
                'supplier' => 'CV. Kimia Leather',
                'ref_no' => 'LM001',
                'user_id' => 1
            ],
            [
                'nama_material' => 'Kancing Logam',
                'stok' => 200,
                'stok_minimum' => 100,
                'harga_satuan' => 500,
                'satuan' => 'pcs',
                'supplier' => 'PT. Aksesoris Tas',
                'ref_no' => 'KC001',
                'user_id' => 1
            ],
            [
                'nama_material' => 'Ritsleting YKK',
                'stok' => 75,
                'stok_minimum' => 50,
                'harga_satuan' => 2500,
                'satuan' => 'pcs',
                'supplier' => 'CV. Ritsleting Berkualitas',
                'ref_no' => 'RZ001',
                'user_id' => 1
            ]
        ];

        foreach ($bahanBaku as $bahan) {
            BahanBaku::create($bahan);
        }

        // Sample Produk Jadi
        $produkJadi = [
            [
                'nama' => 'Tas Kulit Executive',
                'stok' => 45,
                'harga_jual' => 850000,
                'harga_beli' => 600000,
                'deskripsi' => 'Tas kulit premium untuk executive dengan desain elegan',
                'foto' => null
            ],
            [
                'nama' => 'Dompet Kulit Klasik',
                'stok' => 120,
                'harga_jual' => 250000,
                'harga_beli' => 150000,
                'deskripsi' => 'Dompet kulit klasik dengan 8 slot kartu',
                'foto' => null
            ],
            [
                'nama' => 'Sabuk Kulit Premium',
                'stok' => 85,
                'harga_jual' => 180000,
                'harga_beli' => 120000,
                'deskripsi' => 'Sabuk kulit asli dengan kualitas terbaik',
                'foto' => null
            ],
            [
                'nama' => 'Tas Selempang Wanita',
                'stok' => 65,
                'harga_jual' => 350000,
                'harga_beli' => 220000,
                'deskripsi' => 'Tas selempang kulit wanita dengan desain modern',
                'foto' => null
            ],
            [
                'nama' => 'Koper Kulit',
                'stok' => 25,
                'harga_jual' => 1250000,
                'harga_beli' => 850000,
                'deskripsi' => 'Koper kulit premium ukuran sedang',
                'foto' => null
            ]
        ];

        foreach ($produkJadi as $produk) {
            ProdukJadi::create($produk);
        }

        // Sample BOM (Bill of Materials) - Resep untuk setiap produk
        $boms = [
            // Tas Kulit Executive (ID: 1)
            [
                ['produk_jadi_id' => 1, 'bahan_baku_id' => 1, 'quantity_used' => 2.5, 'unit' => 'lembar'], // Kulit Sapi
                ['produk_jadi_id' => 1, 'bahan_baku_id' => 2, 'quantity_used' => 0.3, 'unit' => 'roll'],  // Benang
                ['produk_jadi_id' => 1, 'bahan_baku_id' => 3, 'quantity_used' => 0.1, 'unit' => 'kg'],   // Lem
                ['produk_jadi_id' => 1, 'bahan_baku_id' => 4, 'quantity_used' => 4, 'unit' => 'pcs'],   // Kancing
                ['produk_jadi_id' => 1, 'bahan_baku_id' => 5, 'quantity_used' => 1, 'unit' => 'pcs'],   // Ritsleting
            ],
            // Dompet Kulit Klasik (ID: 2)
            [
                ['produk_jadi_id' => 2, 'bahan_baku_id' => 1, 'quantity_used' => 0.8, 'unit' => 'lembar'], // Kulit Sapi
                ['produk_jadi_id' => 2, 'bahan_baku_id' => 2, 'quantity_used' => 0.1, 'unit' => 'roll'],  // Benang
                ['produk_jadi_id' => 2, 'bahan_baku_id' => 3, 'quantity_used' => 0.05, 'unit' => 'kg'],  // Lem
            ],
            // Sabuk Kulit Premium (ID: 3)
            [
                ['produk_jadi_id' => 3, 'bahan_baku_id' => 1, 'quantity_used' => 1.2, 'unit' => 'lembar'], // Kulit Sapi
                ['produk_jadi_id' => 3, 'bahan_baku_id' => 2, 'quantity_used' => 0.2, 'unit' => 'roll'],  // Benang
                ['produk_jadi_id' => 3, 'bahan_baku_id' => 3, 'quantity_used' => 0.08, 'unit' => 'kg'],  // Lem
                ['produk_jadi_id' => 3, 'bahan_baku_id' => 4, 'quantity_used' => 1, 'unit' => 'pcs'],   // Kancing
            ],
            // Tas Selempang Wanita (ID: 4)
            [
                ['produk_jadi_id' => 4, 'bahan_baku_id' => 1, 'quantity_used' => 1.8, 'unit' => 'lembar'], // Kulit Sapi
                ['produk_jadi_id' => 4, 'bahan_baku_id' => 2, 'quantity_used' => 0.25, 'unit' => 'roll'], // Benang
                ['produk_jadi_id' => 4, 'bahan_baku_id' => 3, 'quantity_used' => 0.08, 'unit' => 'kg'],  // Lem
                ['produk_jadi_id' => 4, 'bahan_baku_id' => 4, 'quantity_used' => 2, 'unit' => 'pcs'],   // Kancing
                ['produk_jadi_id' => 4, 'bahan_baku_id' => 5, 'quantity_used' => 1, 'unit' => 'pcs'],   // Ritsleting
            ],
            // Koper Kulit (ID: 5)
            [
                ['produk_jadi_id' => 5, 'bahan_baku_id' => 1, 'quantity_used' => 4.0, 'unit' => 'lembar'], // Kulit Sapi
                ['produk_jadi_id' => 5, 'bahan_baku_id' => 2, 'quantity_used' => 0.5, 'unit' => 'roll'],  // Benang
                ['produk_jadi_id' => 5, 'bahan_baku_id' => 3, 'quantity_used' => 0.2, 'unit' => 'kg'],   // Lem
                ['produk_jadi_id' => 5, 'bahan_baku_id' => 4, 'quantity_used' => 6, 'unit' => 'pcs'],   // Kancing
                ['produk_jadi_id' => 5, 'bahan_baku_id' => 5, 'quantity_used' => 2, 'unit' => 'pcs'],   // Ritsleting
            ],
        ];

        foreach ($boms as $productBoms) {
            foreach ($productBoms as $bom) {
                ProductBom::create($bom);
            }
        }

        // Sample Produksi (untuk chart)
        $produksiData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $totalProduksi = rand(50, 200); // Random production between 50-200 units

            $produksiData[] = [
                'produk_jadi_id' => rand(1, 5),
                'operator_id' => 2, // Assuming operator with ID 2
                'kode_spk' => 'SPK-' . $date->format('Ym') . '-' . rand(100, 999),
                'jumlah_produksi' => $totalProduksi,
                'status' => 'SELESAI',
                'tanggal_mulai_proses' => $date->startOfMonth(),
                'tanggal_selesai' => $date->endOfMonth(),
                'catatan' => 'Produksi bulan ' . $date->format('F Y'),
                'created_at' => $date,
                'updated_at' => $date
            ];
        }

        foreach ($produksiData as $produksi) {
            Produksi::create($produksi);
        }

        $this->command->info('Sample data berhasil ditambahkan!');
        $this->command->info('Bahan Baku: ' . count($bahanBaku));
        $this->command->info('Produk Jadi: ' . count($produkJadi));
        $this->command->info('BOM (Resep): ' . array_sum(array_map('count', $boms)));
        $this->command->info('Data Produksi: ' . count($produksiData));
    }
}
