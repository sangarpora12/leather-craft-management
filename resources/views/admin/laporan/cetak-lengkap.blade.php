<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Lengkap - {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</title>
    <style>
        @page { size: A4 landscape; margin: 1.5cm; }
        * { box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11pt; 
            color: black;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid black;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .header p {
            font-size: 12px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        thead {
            display: table-header-group;
        }
        th {
            border-bottom: 2px solid #000;
            padding: 8px;
            text-align: left;
            background-color: #f0f0f0;
            font-weight: bold;
        }
        td {
            border-bottom: 1px solid #d1d5db;
            padding: 6px 8px;
            text-align: left;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .font-bold { font-weight: bold !important; }
        
        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .summary-table td {
            border: 1px solid #d1d5db;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
            vertical-align: top;
        }
        .summary-table .highlight {
            border: 2px solid #000;
            background-color: #f9fafb;
        }
        .summary-table p {
            margin: 0;
            font-size: 12px;
            color: #555;
        }
        .summary-table .amount {
            margin: 8px 0 0 0;
            font-size: 16px;
            font-weight: bold;
        }
        .summary-table .highlight p {
            font-size: 13px;
            font-weight: bold;
            color: #000;
        }
        .summary-table .highlight .amount {
            font-size: 18px;
        }
        
        h3 {
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>BERLIANO LEATHER</h2>
        <p>Laporan Lengkap Operasional | Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    </div>

    <!-- Ringkasan Keuangan - PALING ATAS -->
    <h3 style="margin-top: 0;">Ringkasan Keuangan</h3>
    <table class="summary-table">
        <tr>
            <td>
                <p>Total Pemasukan (Penjualan)</p>
                <p class="amount">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</p>
            </td>
            <td>
                <p>Total Pengeluaran (Bahan Baku)</p>
                <p class="amount">Rp {{ number_format($totalPurchaseCost ?? 0, 0, ',', '.') }}</p>
            </td>
            <td class="highlight">
                <p>Keuntungan Bersih</p>
                <p class="amount">Rp {{ number_format(($totalIncome ?? 0) - ($totalPurchaseCost ?? 0), 0, ',', '.') }}</p>
            </td>
        </tr>
    </table>

    <!-- Output Produksi -->
    @if($mode == 'lengkap' || $mode == 'produksi')
    <h3>1. Output Produksi</h3>
    <table>
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
                <td class="text-right">{{ $prod->total_selesai ?? 0 }} Pcs</td>
                <td class="text-right">Rp {{ number_format($prod->harga_jual ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format(($prod->total_selesai ?? 0) * ($prod->harga_jual ?? 0), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="no-data">Tidak ada data produksi</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- Riwayat Penjualan -->
    @if($mode == 'lengkap' || $mode == 'penjualan')
    <h3>2. Riwayat Penjualan / Barang Keluar</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal Keluar</th>
                <th>Nama Produk</th>
                <th class="text-right">Total Terjual</th>
                <th class="text-right">Harga Jual</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesHistory as $sale)
            <tr>
                <td>{{ \Carbon\Carbon::parse($sale->tanggal)->format('d M Y') }}</td>
                <td>{{ $sale->nama_produk }}</td>
                <td class="text-right">{{ number_format($sale->total_terjual) }} Pcs</td>
                <td class="text-right">Rp {{ number_format($sale->harga_jual, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($sale->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="no-data">Tidak ada data penjualan</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- Penggunaan Bahan Baku -->
    @if($mode == 'lengkap' || $mode == 'bahan')
    <h3>3. Penggunaan Bahan Baku</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Material</th>
                <th class="text-right">Jumlah Terpakai</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materialUsage as $supplier => $materials)
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
            <tr><td colspan="5" class="no-data">Tidak ada data penggunaan bahan baku</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <!-- Pembelian Bahan Baku -->
    @if($mode == 'lengkap' || $mode == 'pembelian')
    <h3>4. Pembelian / Bahan Baku Masuk</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal Masuk</th>
                <th>Nama Material</th>
                <th class="text-right">Jumlah Masuk</th>
                <th class="text-center">Satuan</th>
                <th class="text-right">Total Biaya</th>
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
            <tr><td colspan="5" class="no-data">Tidak ada data pembelian</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif
</body>
</html>
