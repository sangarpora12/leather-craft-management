<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operator - Berliano Leather</title>
    <style>
        * { font-family: Arial, Helvetica, sans-serif; }
        body { margin: 20px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 12px; color: #666; }
        .summary { margin: 15px 0; padding: 10px; background: #f5f5f5; border-radius: 5px; }
        .summary-row { display: flex; justify-content: space-between; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-produk { background-color: #dbeafe; color: #1e40af; }
        .badge-bahan { background-color: #dcfce7; color: #166534; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">BERLIANO LEATHER</div>
        <div class="subtitle">LAPORAN OPERATOR</div>
        <div class="subtitle">Periode: {{ $fromDate }} s/d {{ $toDate }}</div>
        <div class="subtitle">Operator: {{ $user->name }}</div>
    </div>

    <div class="summary">
        <div style="font-weight: bold; margin-bottom: 8px;">RINGKASAN</div>
        <div class="summary-row">
            <span>✅ Total Produk Selesai:</span>
            <span><strong>{{ $totalProduk }} pcs</strong></span>
        </div>
        <div class="summary-row">
            <span>✅ Total Bahan Diterima:</span>
            <span><strong>{{ $totalBahan }} unit</strong></span>
        </div>
        <div class="summary-row">
            <span>📋 Kategori:</span>
            <span>{{ $kategori == 'semua' ? 'Semua (Produk + Bahan)' : ($kategori == 'produk' ? 'Hanya Produk Jadi' : 'Hanya Bahan Baku') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="60">JENIS</th>
                <th>NAMA ITEM</th>
                <th width="80">TOTAL</th>
                <th width="80">TRANSAKSI</th>
                <th width="120">TANGGAL TERAKHIR</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riwayat as $item)
            <tr>
                <td>
                    <span class="badge {{ $item->jenis == 'produk' ? 'badge-produk' : 'badge-bahan' }}">
                        {{ $item->jenis == 'produk' ? 'PRODUK' : 'BAHAN' }}
                    </span>
                </td>
                <td>{{ $item->jenis == 'produk' ? ucwords($item->product->nama_produk ?? 'Produk') : ucwords($item->nama) }}</td>
                <td>{{ (int) $item->total_qty }} {{ $item->jenis == 'produk' ? 'pcs' : ($item->satuan ?? 'unit') }}</td>
                <td>{{ $item->jumlah_transaksi }}x</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_terakhir)->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>