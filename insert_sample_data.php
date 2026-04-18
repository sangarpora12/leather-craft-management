<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use App\Models\BahanBaku;
use App\Models\ProdukJadi;
use App\Models\Produksi;
use Carbon\Carbon;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

echo "=== INSERTING SAMPLE DATA FOR ACCURATE CHARTS ===\n\n";

try {
    // Clear existing data in correct order (respecting foreign keys)
    \App\Models\Produksi::query()->delete();
    \App\Models\ProductBom::query()->delete();
    \App\Models\ProdukJadi::query()->delete();
    \App\Models\BahanBaku::query()->delete();

    // Insert Bahan Baku
    $bahanBaku = [
        [
            'nama_material' => 'Kulit Sapi Premium',
            'stok' => 150,
            'stok_minimum' => 50,
            'harga_satuan' => 25000,
            'satuan' => 'lembar',
            'supplier' => 'CV. Kulit Indonesia',
            'deskripsi' => 'Kulit sapi asli berkualitas tinggi untuk tas premium',
            'user_id' => 1
        ],
        [
            'nama_material' => 'Benang Nilon Waxed',
            'stok' => 25,
            'stok_minimum' => 30,
            'harga_satuan' => 15000,
            'satuan' => 'roll',
            'supplier' => 'PT. Benang Maju',
            'deskripsi' => 'Benang nilon wax untuk jahitan kuat',
            'user_id' => 1
        ],
        [
            'nama_material' => 'Lem Aica Bond',
            'stok' => 8,
            'stok_minimum' => 10,
            'harga_satuan' => 45000,
            'satuan' => 'kg',
            'supplier' => 'CV. Kimia Leather',
            'deskripsi' => 'Lem khusus untuk kulit dengan daya rekat tinggi',
            'user_id' => 1
        ],
        [
            'nama_material' => 'Kancing Logam',
            'stok' => 200,
            'stok_minimum' => 100,
            'harga_satuan' => 500,
            'satuan' => 'pcs',
            'supplier' => 'PT. Aksesoris Tas',
            'deskripsi' => 'Kancing logam anti karat untuk tas kulit',
            'user_id' => 1
        ],
        [
            'nama_material' => 'Ritsleting YKK',
            'stok' => 75,
            'stok_minimum' => 50,
            'harga_satuan' => 2500,
            'satuan' => 'pcs',
            'supplier' => 'CV. Ritsleting Berkualitas',
            'deskripsi' => 'Ritsleting YKK original dengan kualitas terbaik',
            'user_id' => 1
        ]
    ];

    foreach ($bahanBaku as $bahan) {
        BahanBaku::create($bahan);
    }
    echo "✅ Inserted " . count($bahanBaku) . " bahan baku records\n";

    // Insert Produk Jadi
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
    echo "✅ Inserted " . count($produkJadi) . " produk jadi records\n";

    // Insert Produksi data for charts
    $produksiData = [];
    for ($i = 5; $i >= 0; $i--) {
        $date = Carbon::now()->subMonths($i);
        $totalProduksi = rand(50, 200);

        $produksiData[] = [
            'produk_jadi_id' => rand(1, 5),
            'operator_id' => 2,
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
    echo "✅ Inserted " . count($produksiData) . " produksi records\n";

    echo "\n=== SAMPLE DATA INSERTED SUCCESSFULLY ===\n";
    echo "Now the dashboard charts should show accurate data!\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
