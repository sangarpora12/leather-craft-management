<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BahanBaku;

BahanBaku::create([
    'kode_material' => 'TEST-' . time(),
    'nama_material' => 'Test Bahan',
    'kategori' => 'Kulit',
    'satuan' => 'Lembar',
    'stok' => 10,
    'stok_minimum' => 5,
    'harga_satuan' => 10000,
]);

echo "Data inserted";
