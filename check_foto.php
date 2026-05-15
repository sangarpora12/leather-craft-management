<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('bahan_bakus')->select('id', 'nama_material', 'kode_material', 'warna', 'foto')->get();
echo "Total bahan: " . count($rows) . "\n";
echo "Bahan dengan foto: " . DB::table('bahan_bakus')->whereNotNull('foto')->where('foto', '!=', '')->count() . "\n\n";
foreach ($rows as $r) {
    $f = $r->foto ? 'YA' : 'TIDAK';
    echo "[$f] ID:{$r->id} | {$r->kode_material} | {$r->nama_material} | Warna:{$r->warna} | Foto:{$r->foto}\n";
}