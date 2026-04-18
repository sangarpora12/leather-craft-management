<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Lengkap Operasional</title>
    <style>
        /* PAKSA LANDSCAPE LANGSUNG DARI CSS AGAR TIDAK KEPOTONG */
        @page { size: A4 landscape; margin: 30px; }
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 22px; color: #000; }
        .header p { margin: 5px 0 0 0; font-size: 12px; }
        
        .section-title { font-size: 13px; font-weight: bold; margin-top: 15px; margin-bottom: 5px; color: #000; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #999; padding: 6px; text-align: left; }
        table.data-table th { background-color: #f3f4f6; color: #000; font-weight: bold; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* TABEL KEUANGAN KHUSUS DOMPDF ANTI GAGAL */
        table.finance-table { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid; }
        table.finance-table td { padding: 0; border: none; vertical-align: top; }
        
        .box { border: 1px solid #888; padding: 15px; text-align: center; border-radius: 5px; }
        .box-profit { border: 2px solid #000; padding: 15px; text-align: center; border-radius: 5px; background-color: #f3f4f6; }
        
        .box-label { font-size: 9px; color: #555; }
        .box-value { font-size: 12px; font-weight: bold; margin-top: 2px; color: #000; }
        .box-profit-value { font-size: 18px; font-weight: bold; margin-top: 5px; color: #000; }
    </style>
</head>
<body>

    <div class="header">
        <h2>BERLIANO LEATHER</h2>
        <p>Laporan Lengkap Operasional | Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    </div>

    <div class="section-title">1. Output Produksi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Operator</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Biaya/Unit</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productions as $prod)
            <tr>
                <td>{{ \Carbon\Carbon::parse($prod->tanggal)->format('d M Y') }}</td>
                <td>{{ $prod->nama_produk ?? 'N/A' }}</td>
                <td>{{ $prod->operators ?? 'N/A' }}</td>
                <td class="text-right">{{ $prod->total_selesai ?? 0 }}</td>
                <td class="text-right">Rp {{ number_format($prod->harga_jual ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format(($prod->total_selesai ?? 0) * ($prod->harga_jual ?? 0), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">2. Riwayat Penjualan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Produk</th>
                <th class="text-right">Terjual</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesHistory as $sale)
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->tanggal)->format('d M Y') }}</td>
                <td>{{ $sale->nama_produk }}</td>
                <td class="text-right">{{ $sale->total_terjual }}</td>
                <td class="text-right">Rp {{ number_format($sale->harga_jual, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sale->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">3. Penggunaan Bahan Baku</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Material</th>
                <th class="text-right">Jumlah</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materialUsage as $sup => $materials)
                @foreach($materials as $mat)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($mat->tanggal)->format('d M Y') }}</td>
                    <td>{{ $mat->nama_material }}</td>
                    <td class="text-right">{{ floatval($mat->total_quantity ?? 0) }}</td>
                    <td class="text-center">{{ $mat->satuan }}</td>
                    <td class="text-right">Rp {{ number_format($mat->total_cost ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">4. Pembelian Bahan Baku</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Material</th>
                <th class="text-right">Jumlah</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materialPurchases as $purchase)
            <tr>
                <td>{{ \Carbon\Carbon::parse($purchase->tanggal)->format('d M Y') }}</td>
                <td>{{ $purchase->nama_material }}</td>
                <td class="text-right">{{ floatval($purchase->jumlah_masuk) }}</td>
                <td class="text-center">{{ $purchase->satuan }}</td>
                <td class="text-right">Rp {{ number_format($purchase->total_biaya ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">5. Ringkasan Keuangan</div>
    <table class="finance-table">
        <tr>
            <td style="width: 32%;">
                <div class="box">
                    <div class="box-label">Total Pemasukan (Penjualan)</div>
                    <div class="box-value">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 2%;"></td>
            <td style="width: 32%;">
                <div class="box">
                    <div class="box-label">Total Pengeluaran (Bahan Baku)</div>
                    <div class="box-value">Rp {{ number_format($totalExpense ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 2%;"></td>
            <td style="width: 32%;">
                <div class="box-profit">
                    <div class="box-label" style="color: #000; font-weight: bold;">Keuntungan Bersih</div>
                    <div class="box-profit-value">Rp {{ number_format($totalProfit ?? 0, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>