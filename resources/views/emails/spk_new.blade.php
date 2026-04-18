<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Perintah Kerja Baru</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Halo, {{ $operator_name }}</h2>
    <p>Ada <strong>Perintah Kerja Baru</strong> yang harus Anda kerjakan.</p>

    <div style="border:1px solid #ddd; padding: 12px; border-radius: 8px; margin: 14px 0;">
        <p style="margin:0 0 6px 0;"><strong>Kode SPK:</strong> {{ $kode_spk }}</p>
        <p style="margin:0 0 6px 0;"><strong>Produk:</strong> {{ $produk_name }}</p>
        <p style="margin:0 0 6px 0;"><strong>Target:</strong> {{ $qty }} Pcs</p>
        <p style="margin:0;"><strong>Deadline:</strong> {{ $deadline }}</p>
    </div>

    <p>Silakan login untuk melihat detail SPK dan mulai pengerjaan.</p>

    @if(!empty($url))
        <p>
            <a href="{{ $url }}" style="background-color: #2563eb; color: white; padding: 10px 18px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Buka Detail SPK
            </a>
        </p>
    @endif

    <p style="color:#666; font-size: 12px; margin-top: 18px;">
        Berliano Leather • Production System
    </p>
</body>
</html>
