<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produksi</title>
    <style>
    @page {
        margin: 20px; /* Mengecilkan margin kertas PDF agar konten tidak melayang */
    }
    body {
        font-family: Arial, sans-serif;
        font-size: 11px; /* Sedikit lebih kecil agar muat banyak */
        margin: 0;
        padding: 0;
    }
    h1 {
        text-align: center;
        font-size: 16px;
        margin: 0 0 10px 0; /* Margin bawah diperkecil */
        padding: 0;
    }
    .header-info {
        text-align: center;
        margin-bottom: 10px; /* Margin bawah diperkecil */
        font-size: 10px;
        color: #333;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 0 !important; /* Paksa margin tabel jadi nol */
        page-break-inside: avoid;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 5px 8px; /* Padding dikurangi biar rapat */
        text-align: left;
    }
    th {
        background-color: #eee !important;
        font-weight: bold;
        font-size: 10px;
    }
    /* Mencegah tabel terpotong paksa */
    tr {
        page-break-inside: avoid;
    }
    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-size: 9px;
        color: #777;
    }
</style>
</head>
<body>
    <h1>LAPORAN PRODUKSI</h1>
    <div class="header-info">
        <p>Periode: {{ $fromDate }} - {{ $toDate }}</p>
        @if(count($filterInfo) > 0)
            <p>{{ implode(' | ', $filterInfo) }}</p>
        @endif
        <p>Dicetak: {{ $tanggal_cetak }}</p>
    </div>

    <h3>Ringkasan Bahan Keluar</h3>
<table>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Bahan</th>
            <th>Digunakan Untuk</th>
            <th>Total Dipakai</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materialUsage as $item)
        <tr>
            <td>{{ $item->kode_material }}</td>
            <td>{{ $item->nama_material }}</td>
            <td>{{ $item->nama_produk }}</td>
            <td class="text-right">{{ number_format($item->jumlah, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4">Tidak ada data bahan keluar</td></tr>
        @endforelse
    </tbody>
</table>

    <div class="footer">
        <p>Sistem Informasi Manajemen Produksi - Berliano Leather</p>
    </div>
</body>
</html>
