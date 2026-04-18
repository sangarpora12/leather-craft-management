<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .header-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #999;
            text-align: center;
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

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th class="text-right">Target</th>
                <th class="text-right">Selesai</th>
                <th class="text-right">Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productions as $index => $prod)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($prod->tanggal)->format('d M Y') }}</td>
                <td>{{ $prod->nama_produk ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($prod->total_target ?? 0) }}</td>
                <td class="text-right">{{ number_format($prod->total_capaian ?? 0) }}</td>
                <td class="text-right">Rp {{ number_format(($prod->total_capaian ?? 0) * ($prod->harga_jual ?? 0), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data produksi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Informasi Manajemen Produksi - Berliano Leather</p>
    </div>
</body>
</html>
