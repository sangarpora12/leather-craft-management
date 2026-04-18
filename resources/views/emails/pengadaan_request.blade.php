<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Request Pengadaan Bahan</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px;">
    <h2>Stok Kritis! Request Pengadaan</h2>

    <p>Halo, {{ $operator_name }}</p>
    <p>Produksi <strong>{{ $kode_spk }}</strong> membutuhkan pengadaan bahan berikut:</p>

    <div style="border:1px solid #ddd; padding: 12px; border-radius: 8px; margin: 14px 0;">
        <p style="margin:0 0 6px 0;"><strong>Bahan:</strong> {{ $bahan_name }}</p>
        <p style="margin:0 0 6px 0;"><strong>Butuh Tambahan:</strong> {{ $kurang }} {{ $satuan }}</p>
        <p style="margin:0;"><strong>Stok Saat Ini:</strong> {{ $stok }} {{ $satuan }}</p>
    </div>

    <p>Mohon segera lakukan pembelian agar produksi dapat dilanjutkan.</p>

    <p style="color:#666; font-size: 12px; margin-top: 18px;">
        Berliano Leather • Procurement System
    </p>
</body>
</html>
