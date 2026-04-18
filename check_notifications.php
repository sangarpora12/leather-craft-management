<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Notifications\DatabaseNotification;

echo "\n";
echo "========================================\n";
echo "  CEK NOTIFIKASI DI DATABASE\n";
echo "========================================\n";
echo "\n";

$notifications = DatabaseNotification::orderBy('created_at', 'desc')->limit(10)->get();

if ($notifications->count() === 0) {
    echo "❌ Tidak ada notifikasi di database.\n";
} else {
    echo "✅ Ditemukan {$notifications->count()} notifikasi:\n";
    echo "\n";
    
    foreach ($notifications as $n) {
        $data = $n->data;
        $judul = $data['judul'] ?? 'No Title';
        $type = $data['type'] ?? $n->type;
        $created = $n->created_at->format('d M Y H:i:s');
        
        echo "ID: {$n->id}\n";
        echo "Judul: {$judul}\n";
        echo "Type: {$type}\n";
        echo "Created: {$created}\n";
        echo "Read At: " . ($n->read_at ? $n->read_at->format('d M Y H:i:s') : 'Not Read') . "\n";
        echo "----------------------------------------\n";
    }
}

echo "\n";
echo "========================================\n";
echo "  CEK EMAIL LOG\n";
echo "========================================\n";
echo "\n";

// Cek log Laravel
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    $emailLines = [];
    
    // Cari baris yang mengandung email
    $lines = explode("\n", $logContent);
    foreach ($lines as $line) {
        if (stripos($line, 'email') !== false || stripos($line, 'mail') !== false || stripos($line, 'smtp') !== false) {
            $emailLines[] = $line;
        }
    }
    
    if (count($emailLines) > 0) {
        echo "✅ Ditemukan " . count($emailLines) . " baris terkait email di log:\n";
        echo "\n";
        foreach (array_slice($emailLines, -5) as $line) {
            echo $line . "\n";
        }
    } else {
        echo "❌ Tidak ada log email ditemukan.\n";
    }
} else {
    echo "❌ File log tidak ditemukan.\n";
}

echo "\n";
echo "========================================\n";
echo "  CEK KONFIGURASI EMAIL\n";
echo "========================================\n";
echo "\n";

// Cek konfigurasi email
$mailConfig = config('mail');
echo "Mail Mailer: " . ($mailConfig['default'] ?? 'Not Set') . "\n";
echo "Mail Host: " . ($mailConfig['mailers']['smtp']['host'] ?? 'Not Set') . "\n";
echo "Mail Port: " . ($mailConfig['mailers']['smtp']['port'] ?? 'Not Set') . "\n";
echo "Mail Encryption: " . ($mailConfig['mailers']['smtp']['encryption'] ?? 'Not Set') . "\n";
echo "Mail Username: " . ($mailConfig['mailers']['smtp']['username'] ?? 'Not Set') . "\n";

echo "\n";
echo "========================================\n";
echo "  TESTING EMAIL SEDERHANA\n";
echo "========================================\n";
echo "\n";

try {
    \Illuminate\Support\Facades\Mail::raw('Test email dari sistem notifikasi', function ($message) {
        $message->to('zulk61934@gmail.com')
                ->subject('Test Email - Sistem Notifikasi');
    });
    
    echo "✅ Email test berhasil dikirim ke zulk61934@gmail.com\n";
} catch (\Exception $e) {
    echo "❌ Gagal kirim email test: " . $e->getMessage() . "\n";
}

echo "\n";
echo "========================================\n";
echo "  PENGECEKAN SELESAI\n";
echo "========================================\n";
echo "\n";