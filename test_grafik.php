<?php
require_once 'vendor/autoload.php';

// Setup Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Test query grafik
$now = Carbon::now()->timezone('Asia/Jakarta');
$today = $now->copy()->startOfDay();

$labels = [];
$bMasuk = [];
$bKeluar = [];

for ($h = 0; $h <= 23; $h += 2) {
    $time = (clone $today)->addHours($h);
    
    if ($time->lte($now)) {
        $labels[] = $time->format('H:00');
        $start = (clone $time)->startOfHour(); 
        $end = (clone $time)->endOfHour();

        $bMasuk[] = DB::table('log_stok')
            ->whereBetween('created_at', [$start, $end])
            ->where('jenis_transaksi', 'INPUT MANUAL OPERATOR')
            ->sum('jumlah');

        $bKeluar[] = DB::table('log_stok')
            ->whereBetween('created_at', [$start, $end])
            ->where('jenis_transaksi', 'PENGELUARAN_PRODUKSI')
            ->sum('jumlah');
    }
}

echo "Labels: " . json_encode($labels) . "\n";
echo "Bahan Masuk: " . json_encode($bMasuk) . "\n";
echo "Bahan Keluar: " . json_encode($bKeluar) . "\n";