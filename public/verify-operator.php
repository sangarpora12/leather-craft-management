<?php
// Masukkan koneksi database Laravel kamu manual di sini karena ini file PHP murni
$host = '127.0.0.1';
$db   = 'nama_database_kamu'; // Ganti dengan nama database skripsi kamu
$user = 'root';
$pass = ''; // Ganti jika ada passwordnya

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    // Ambil ID dari URL (?id=8)
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id) {
        // Cek apakah user id tersebut ada
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        if ($user) {
            // Update status: Aktifkan dan verifikasi email
            $update = $pdo->prepare("UPDATE users SET email_verified_at = NOW(), is_active = 1 WHERE id = ?");
            $update->execute([$id]);

            $status = "success";
            $message = "Verifikasi Berhasil!";
            $subtext = "Akun " . $user['name'] . " sudah aktif. Silakan login.";
        } else {
            $status = "error";
            $message = "Verifikasi Gagal";
            $subtext = "User tidak ditemukan di sistem.";
        }
    } else {
        $status = "error";
        $message = "Verifikasi Gagal";
        $subtext = "ID verifikasi tidak valid.";
    }
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Akun - Berliano Leather</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-3xl shadow-xl max-w-sm w-full text-center">
        <div class="mb-6">
            @if($status == 'success')
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-4xl">
                    <i class="fas fa-check"></i>
                </div>
            @else
                <div class="w-20 h-20 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto text-4xl">
                    <i class="fas fa-times"></i>
                </div>
            @endif
        </div>

        <h1 class="text-2xl font-bold mb-2"><?php echo $message; ?></h1>
        <p class="text-gray-500 mb-8"><?php echo $subtext; ?></p>

        <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition inline-block w-full">
            Ke Halaman Login
        </a>
    </div>
</body>
</html>