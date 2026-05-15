<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Berhasil</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #0B0E14;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #fff;
        }
        .card {
            background: #1C222D;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 20px;
            padding: 50px 40px;
            max-width: 420px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .icon {
            width: 80px;
            height: 80px;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }
        .icon svg {
            width: 40px;
            height: 40px;
            fill: none;
            stroke: #10b981;
            stroke-width: 2;
        }
        h1 {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 8px;
            color: #10b981;
        }
        p {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
        }
        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke-linecap="round" stroke-linejoin="round"/>
                <polyline points="22 4 12 14.01 9 11.01" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <h1>Akun Anda Aktif!</h1>
        <p>Selamat! Akun operator Anda telah berhasil diverifikasi dan aktif.<br>Sekarang Anda bisa menerima instruksi produksi.</p>
        <div class="footer">Berliano Leather &bull; Sistem Informasi Produksi</div>
    </div>
</body>
</html>