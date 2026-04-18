<!DOCTYPE html>
<html>
<head>
    <title>Undangan Operator</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Halo, {{ $name }}</h2>
    <p>Admin telah mendaftarkan email ini sebagai <strong>Operator Produksi</strong> di Berliano Leather.</p>
    
    <p>Untuk mengaktifkan akun Anda dan mulai menerima pekerjaan, silakan klik tombol di bawah ini sebagai tanda persetujuan:</p>
    
    <p>
        <a href="{{ $link }}" style="background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            SAYA SETUJU & AKTIFKAN AKUN
        </a>
    </p>

    <p>Jika ini bukan Anda, abaikan email ini.</p>
</body>
</html>