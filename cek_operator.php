<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'berliano_leather',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Cek data bahan masuk
echo "=== CEK DATA BAHAN MASUK ===\n";
$totalMasuk = DB::table('log_stok')
    ->where(function($q) {
        $q->where('log_stok.jenis_transaksi', 'like', '%MASUK%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%TAMBAH%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%PENGADAAN%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%RESTOCK%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%INPUT%');
    })
    ->count();

echo "Total data bahan masuk: " . $totalMasuk . "\n";

$withOperator = DB::table('log_stok')
    ->where(function($q) {
        $q->where('log_stok.jenis_transaksi', 'like', '%MASUK%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%TAMBAH%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%PENGADAAN%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%RESTOCK%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%INPUT%');
    })
    ->whereNotNull('operator_id')
    ->count();

echo "Dengan operator_id: " . $withOperator . "\n";
echo "Tanpa operator_id: " . ($totalMasuk - $withOperator) . "\n";

// Cek sample data dengan operator
echo "\n=== SAMPLE DATA DENGAN OPERATOR ===\n";
$sample = DB::table('log_stok')
    ->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
    ->leftJoin('users', 'log_stok.operator_id', '=', 'users.id')
    ->where(function($q) {
        $q->where('log_stok.jenis_transaksi', 'like', '%MASUK%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%TAMBAH%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%PENGADAAN%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%RESTOCK%')
          ->orWhere('log_stok.jenis_transaksi', 'like', '%INPUT%');
    })
    ->select('log_stok.id', 'bahan_bakus.nama_material', 'log_stok.jenis_transaksi', 'log_stok.operator_id', 'users.name')
    ->limit(5)
    ->get();

foreach ($sample as $row) {
    echo "ID: " . $row->id . ", Material: " . $row->nama_material . ", Transaksi: " . $row->jenis_transaksi . ", Operator: " . ($row->name ?? 'N/A') . "\n";
}

echo "\n=== CEK DATA PRODUKSI (BAHAN KELUAR) ===\n";
// Cek data produksi untuk bahan keluar
$totalProduksi = DB::table('produksis')
    ->where('status', 'SELESAI')
    ->count();

echo "Total data produksi SELESAI: " . $totalProduksi . "\n";

$withOperatorProd = DB::table('produksis')
    ->where('status', 'SELESAI')
    ->whereNotNull('operator_id')
    ->count();

echo "Dengan operator_id: " . $withOperatorProd . "\n";
echo "Tanpa operator_id: " . ($totalProduksi - $withOperatorProd) . "\n";

echo "\n=== SELESAI ===\n";