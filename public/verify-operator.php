<?php
// Fungsi untuk membaca file .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
    return $env;
}

// Load konfigurasi dari .env
$rootPath = dirname(__DIR__) . '/.env';
$env = loadEnv($rootPath);
$operatorUrl = $env['OPERATOR_URL'] ?? 'http://127.0.0.1:8000';

// Koneksi database
$host = 'localhost';
$dbname = $env['DB_DATABASE'] ?? 'berliano_leather';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['token']) && !empty(trim($_GET['token']))) {
        $token = trim($_GET['token']);

        $stmt = $conn->prepare("SELECT id, is_verified FROM operators WHERE verification_token = :token");
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        $operator = $stmt->fetch();

        if ($operator) {
            if ($operator['is_verified'] == 1) {
                $status = 'sudah_terverifikasi';
                $message = 'Akun Anda sudah terverifikasi sebelumnya.';
            } else {
                $updateStmt = $conn->prepare("UPDATE operators SET is_verified = 1, verification_token = NULL, verified_at = NOW() WHERE id = :id");
                $updateStmt->bindParam(':id', $operator['id']);
                
                if ($updateStmt->execute()) {
                    $status = 'berhasil';
                    $message = 'Selamat! Akun Anda telah berhasil diaktifkan.';
                } else {
                    $status = 'gagal';
                    $message = 'Gagal mengaktifkan akun, silakan coba lagi.';
                }
            }
        } else {
            $status = 'gagal';
            $message = 'Token verifikasi tidak valid atau sudah kadaluarsa.';
        }
    } else {
        $status = 'gagal';
        $message = 'Token verifikasi tidak ditemukan.';
    }

} catch (PDOException $e) {
    $status = 'gagal';
    $message = 'Terjadi kesalahan sistem: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun - Berliano Leather</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .verification-card {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #28a745;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
        }
        .success-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        .error-icon {
            width: 80px;
            height: 80px;
            background-color: #dc3545;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
        }
        .error-icon svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-login {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        .btn-login:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <?php if ($status == 'berhasil' || $status == 'sudah_terverifikasi'): ?>
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
            </div>
            <h1>Akun Berhasil Diaktifkan!</h1>
            <p><?= $message ?> Silakan login untuk mulai mengakses sistem Berliano Leather.</p>
        <?php else: ?>
            <div class="error-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
            </div>
            <h1>Verifikasi Gagal</h1>
            <p><?= $message ?></p>
        <?php endif; ?>
        <a href="<?= $operatorUrl ?>/login" class="btn-login">Ke Halaman Login</a>
    </div>
</body>
</html>