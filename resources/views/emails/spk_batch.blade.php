<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perintah Kerja Baru - Beberapa Produk</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Halo, {{ $operator_name }}</h2>
    <p>Ada <strong>Perintah Kerja Baru</strong> yang harus Anda kerjakan untuk beberapa produk.</p>

    <div style="border:1px solid #ddd; padding: 12px; border-radius: 8px; margin: 14px 0;">
        <h3 style="margin:0 0 10px 0; color:#333;">Daftar Produk:</h3>
        @foreach($produk_list as $produk)
        <div style="border-bottom:1px solid #eee; padding: 8px 0; margin-bottom: 8px;">
            <p style="margin:0 0 4px 0;"><strong>Kode SPK:</strong> {{ $produk['kode_spk'] }}</p>
            <p style="margin:0 0 4px 0;"><strong>Produk:</strong> {{ $produk['produk_name'] }}</p>
            <p style="margin:0 0 4px 0;"><strong>Target:</strong> {{ $produk['qty'] }} Pcs</p>
            <p style="margin:0;">
                <a href="{{ $produk['url'] }}" style="color: #2563eb; text-decoration: underline;">Buka Detail SPK</a>
            </p>
        </div>
        @endforeach
        <p style="margin:8px 0 0 0;"><strong>Deadline:</strong> {{ $deadline }}</p>
    </div>

    <p>Silakan klik link di atas untuk setiap produk atau gunakan tombol login di bawah untuk masuk ke sistem.</p>

    @if(!empty($login_url))
        <p>
            <a href="{{ $login_url }}" style="background-color: #16a34a; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Buka Halaman Login
            </a>
        </p>
    @endif

    <p style="color:#666; font-size: 12px; margin-top: 18px;">
        Berliano Leather • Production System
    </p>
</body>
</html>
