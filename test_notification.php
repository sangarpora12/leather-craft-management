<?php

/**
 * Script Testing Notifikasi Advanced
 * 
 * Cara penggunaan:
 * 1. php test_notification.php admin      # Test notifikasi scan nota ke admin
 * 2. php test_notification.php operator   # Test notifikasi pengadaan/produksi ke operator
 * 3. php test_notification.php all        # Test semua notifikasi
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Pengadaan;
use App\Models\Produksi;
use App\Models\ProdukJadi;
use App\Notifications\ScanNotaNotification;
use App\Notifications\PengadaanMaterialNotification;
use App\Notifications\PerintahProduksiNotification;
use Illuminate\Support\Facades\Notification;

echo "\n";
echo "========================================\n";
echo "  TESTING NOTIFIKASI ADVANCED\n";
echo "========================================\n";
echo "\n";

// Test fungsi
function testScanNotaNotification() {
    echo "📸 Testing ScanNotaNotification...\n";
    
    $admins = User::where('role', 'admin')->get();
    if ($admins->count() === 0) {
        echo "   ❌ Tidak ada user admin di database.\n";
        return;
    }
    
    $scanData = [
        'id' => 1,
        'bahan_id' => 1,
        'nama_bahan' => 'Kulit Sapi A',
        'items' => [
            [
                'nama' => 'Kulit Sapi A',
                'jumlah' => 50,
                'satuan' => 'Lembar',
                'harga' => 150000,
            ],
            [
                'nama' => 'Kulit Kambing',
                'jumlah' => 30,
                'satuan' => 'Lembar',
                'harga' => 120000,
            ],
        ],
        'total_harga' => 7500000,
    ];
    
    $notification = new ScanNotaNotification($scanData, 'Budi Operator', 'notas/test_scan.jpg');
    
    foreach ($admins as $admin) {
        try {
            $admin->notify($notification);
            echo "   ✅ Notifikasi dikirim ke admin: {$admin->email}\n";
        } catch (\Exception $e) {
            echo "   ❌ Gagal kirim notifikasi ke admin {$admin->email}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "   📧 Cek database 'notifications' untuk melihat notifikasi yang tersimpan.\n";
    echo "\n";
}

function testPengadaanMaterialNotification() {
    echo "📦 Testing PengadaanMaterialNotification...\n";
    
    $operators = User::where('role', 'operator')->get();
    if ($operators->count() === 0) {
        echo "   ❌ Tidak ada user operator di database.\n";
        return;
    }
    
    $pengadaan = Pengadaan::first();
    if (!$pengadaan) {
        echo "   ⚠️  Tidak ada data pengadaan. Membuat data dummy...\n";
        
        // Coba buat data dummy
        $pengadaan = Pengadaan::create([
            'no_referensi' => 'TEST-' . date('YmdHis'),
            'bahan_baku_id' => 1,
            'jumlah' => 50,
            'prioritas' => 'BIASA',
            'tanggal_dibutuhkan' => date('Y-m-d', strtotime('+7 days')),
            'operator_id' => $operators->first()->id,
            'catatan' => 'Test pengadaan untuk testing notifikasi',
            'status' => 'PENDING',
            'admin_id' => 1,
        ]);
        
        echo "   ✅ Data pengadaan dummy dibuat: {$pengadaan->no_referensi}\n";
    }
    
    $notification = new PengadaanMaterialNotification($pengadaan, 'Test Admin');
    
    foreach ($operators as $operator) {
        try {
            $operator->notify($notification);
            echo "   ✅ Notifikasi dikirim ke operator: {$operator->email}\n";
        } catch (\Exception $e) {
            echo "   ❌ Gagal kirim notifikasi ke operator {$operator->email}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "   📧 Cek database 'notifications' untuk melihat notifikasi yang tersimpan.\n";
    echo "\n";
}

function testPerintahProduksiNotification() {
    echo "🏭 Testing PerintahProduksiNotification...\n";
    
    $operators = User::where('role', 'operator')->get();
    if ($operators->count() === 0) {
        echo "   ❌ Tidak ada user operator di database.\n";
        return;
    }
    
    $produksi = Produksi::first();
    if (!$produksi) {
        echo "   ⚠️  Tidak ada data produksi. Testing dilewati.\n";
        echo "\n";
        return;
    }
    
    $notification = new PerintahProduksiNotification($produksi, 'Test Admin');
    
    foreach ($operators as $operator) {
        try {
            $operator->notify($notification);
            echo "   ✅ Notifikasi dikirim ke operator: {$operator->email}\n";
        } catch (\Exception $e) {
            echo "   ❌ Gagal kirim notifikasi ke operator {$operator->email}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "   📧 Cek database 'notifications' untuk melihat notifikasi yang tersimpan.\n";
    echo "\n";
}

// Jalankan test berdasarkan argumen
$testType = $argv[1] ?? 'all';

switch ($testType) {
    case 'admin':
        testScanNotaNotification();
        break;
    case 'operator':
        testPengadaanMaterialNotification();
        testPerintahProduksiNotification();
        break;
    case 'all':
    default:
        testScanNotaNotification();
        testPengadaanMaterialNotification();
        testPerintahProduksiNotification();
        break;
}

echo "========================================\n";
echo "  TESTING SELESAI!\n";
echo "========================================\n";
echo "\n";
echo "📝 Catatan:\n";
echo "   - Notifikasi database tersimpan di tabel 'notifications'\n";
echo "   - Email akan terkirim jika konfigurasi SMTP di .env sudah benar\n";
echo "   - Cek storage/logs/laravel.log jika ada error\n";
echo "\n";