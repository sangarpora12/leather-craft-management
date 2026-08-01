<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProdukJadi;

echo "Total Produk: " . ProdukJadi::count() . PHP_EOL;
echo "Stok > 0: " . ProdukJadi::where('stok', '>', 0)->count() . PHP_EOL;
echo "Stok = 0: " . ProdukJadi::where('stok', '=', 0)->count() . PHP_EOL;

echo PHP_EOL . "Sample products:" . PHP_EOL;
foreach (ProdukJadi::limit(5)->get() as $p) {
    echo "- {$p->nama_produk} | Stok: {$p->stok}" . PHP_EOL;
}