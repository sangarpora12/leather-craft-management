<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== GROUP BY KODE MATERIAL (INDEX) ===\n";
$groups = DB::select("SELECT kode_material, MAX(id) as id, nama_material, SUM(stok) as total, COUNT(*) as jml_varian FROM bahan_bakus GROUP BY kode_material, nama_material ORDER BY kode_material");
foreach ($groups as $g) {
    printf("id=%d | %-14s | %-50s | jml_varian=%d | total=%.2f\n", $g->id, $g->kode_material, $g->nama_material, $g->jml_varian, $g->total);
}

echo "\n=== PER ID, CEK VARIAN DENGAN NAMA MATERIAL SAMA ===\n";
$all = DB::select("SELECT id, kode_material, nama_material, warna, stok FROM bahan_bakus ORDER BY kode_material, warna, id");
foreach ($all as $a) {
    $totalVarian = DB::select("SELECT SUM(stok) as total FROM bahan_bakus WHERE nama_material = ?", [$a->nama_material])[0]->total;
    $jmlVarian = DB::select("SELECT COUNT(*) as jml FROM bahan_bakus WHERE nama_material = ?", [$a->nama_material])[0]->jml;
    printf("id=%-2d | %-14s | %-50s | warna=%-15s | stok=%.2f | total_varian_sama=%.2f (dari %d baris)\n", 
        $a->id, $a->kode_material, $a->nama_material, $a->warna ?? '-', $a->stok, $totalVarian, $jmlVarian);
}