<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ambil kode_material yang punya foto
$rows = DB::table('bahan_bakus')
    ->select('kode_material', DB::raw('MAX(foto) as foto_ada'))
    ->whereNotNull('foto')
    ->where('foto', '!=', '')
    ->groupBy('kode_material')
    ->get();

$updated = 0;
foreach ($rows as $r) {
    if ($r->foto_ada) {
        $count = DB::table('bahan_bakus')
            ->where('kode_material', $r->kode_material)
            ->where(function($q) { 
                $q->whereNull('foto')->orWhere('foto', ''); 
            })
            ->update(['foto' => $r->foto_ada]);
        $updated += $count;
        echo "Kode {$r->kode_material}: update {$count} rows dengan foto {$r->foto_ada}\n";
    }
}

echo "\n✅ Total {$updated} bahan diupdate dengan foto dari kode_material yang sama.\n";