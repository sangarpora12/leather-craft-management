<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$produk = DB::table('produk_jadis')->where('nama_produk', 'like', '%Sandal%Kulit%Casual%')->first();
if (!$produk) {
    $produk = DB::table('produk_jadis')->where('nama_produk', 'like', '%Sandal%')->first();
}
if ($produk) {
    echo "PRODUK: {$produk->nama_produk} (ID: {$produk->id})\n";
    echo "=======================\n\n";
    
    $boms = DB::table('bom')->where('id_produk', $produk->id)->get();
    echo "Jumlah BOM: " . count($boms) . "\n\n";
    
    $maxCandidates = [];
    foreach ($boms as $bom) {
        $bahan = DB::table('bahan_bakus')->find($bom->id_bahan);
        if ($bahan) {
            $totalStok = DB::table('bahan_bakus')->where('kode_material', $bahan->kode_material)->sum('stok');
            $maxForThis = (int)($totalStok / $bom->jumlah_kebutuhan);
            $maxCandidates[] = $maxForThis;
            
            $status = $bahan->is_inventory ? 'INVENTORY' : 'PELENGKAP';
            echo "[$status] {$bahan->nama_material} (kode:{$bahan->kode_material}, ID:{$bahan->id})\n";
            echo "  Butuh/unit: {$bom->jumlah_kebutuhan}\n";
            echo "  Stok varian ini: {$bahan->stok}\n";
            echo "  Total stok semua varian: $totalStok\n";
            echo "  Max produksi bahan ini: $maxForThis\n\n";
        } else {
            echo "[ERROR] BOM ID:{$bom->id} -> BAHAN TIDAK ADA (id_bahan:{$bom->id_bahan})\n\n";
        }
    }
    
    $bottleneck = !empty($maxCandidates) ? min($maxCandidates) : 999;
    echo "=======================\n";
    echo "BOTTLENECK (max produksi): $bottleneck\n";
    echo "IS_READY: " . ($bottleneck > 0 ? 'true' : 'false') . "\n";
} else {
    echo "Produk Sandal tidak ditemukan\n";
    
    // Cari produk terdekat
    $all = DB::table('produk_jadis')->select('id', 'nama_produk')->get();
    foreach ($all as $p) {
        if (stripos($p->nama_produk, 'sandal') !== false || stripos($p->nama_produk, 'casual') !== false || stripos($p->nama_produk, 'selop') !== false) {
            echo "  Mirip: {$p->nama_produk} (ID:{$p->id})\n";
        }
    }
}