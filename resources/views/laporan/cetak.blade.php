<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Lengkap - {{ $periode_label }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
            background: white;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .company-address {
            font-size: 11px;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-align: center;
        }

        .report-period {
            font-size: 12px;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 25px 0 10px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .summary-table {
            margin: 20px 0;
        }

        .summary-table th {
            background-color: #e0e0e0;
        }

        .total-row {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .supplier-section {
            margin-bottom: 20px;
        }

        .supplier-header {
            background-color: #e8e8e8;
            padding: 8px;
            font-weight: bold;
            margin-bottom: 10px;
            border: 1px solid #ccc;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 20px;
        }

        @page {
            margin: 1cm;
        }

        @media print {
            body { margin: 0; padding: 15px; }
        }
    </style>
</head>
<body>
    <!-- Company Header -->
    <div class="header">
        <div class="company-name">BERLIANO LEATHER</div>
        <div class="company-address">Jl. Industri Kulit No. 123, Jakarta</div>
        <div class="company-address">Telp: (021) 12345678 | Email: info@berliano-leather.com</div>
    </div>

    <!-- Report Title -->
    <div class="report-title">{{ $periode_label }}</div>
    <div class="report-period">Dibuat pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</div>

    @if($mode == 'lengkap' || $mode == 'produksi')
    <!-- Production Output -->
    <div class="section-title">Output Produksi</div>
    <table>
        <thead>
            <tr>
                <th width="15%">Kode SPK</th>
                <th width="25%">Produk Jadi</th>
                <th width="20%">Operator</th>
                <th width="15%" class="text-center">Jumlah Selesai</th>
                <th width="15%" class="text-center">Biaya/Unit</th>
                <th width="10%" class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productions as $prod)
                <tr>
                    <td>{{ $prod->kode_spk }}</td>
                    <td>{{ $prod->produk_jadi->nama_produk ?? 'N/A' }}</td>
                    <td>{{ $prod->operator->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $prod->jumlah_produksi }}</td>
                    <td class="text-right">Rp {{ number_format($prod->produk_jadi->harga_jual ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format(($prod->jumlah_produksi * ($prod->produk_jadi->harga_jual ?? 0)), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data produksi</td>
                </tr>
            @endforelse
            @if($productions->count() > 0)
                <tr class="total-row">
                    <td colspan="3"><strong>Total Produksi</strong></td>
                    <td class="text-center"><strong>{{ $totalProduction }}</strong></td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>
    @endif

    @if($mode == 'lengkap' || $mode == 'bahan')
    <!-- Material Usage -->
    <div class="section-title">Penggunaan Bahan Baku (Per Supplier)</div>
    @forelse($materialUsage as $supplier => $materials)
        <div class="supplier-section">
            <div class="supplier-header">
                Supplier: {{ $supplier }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th width="40%">Nama Bahan</th>
                        <th width="20%" class="text-center">Jumlah</th>
                        <th width="20%" class="text-center">Satuan</th>
                        <th width="20%" class="text-right">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $mat)
                        <tr>
                            <td>{{ $mat->nama_material }}</td>
                            <td class="text-center">{{ number_format($mat->total_quantity, 2) }}</td>
                            <td class="text-center">{{ $mat->satuan }}</td>
                            <td class="text-right">Rp {{ number_format($mat->total_cost, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3"><strong>Subtotal Supplier</strong></td>
                        <td class="text-right"><strong>Rp {{ number_format($materials->sum('total_cost'), 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <p>Belum ada data penggunaan bahan baku</p>
    @endforelse

    @if($totalMaterialCost > 0)
        <div style="text-align: right; margin-top: 20px; font-size: 12px; font-weight: bold;">
            Total Biaya Bahan Baku: Rp {{ number_format($totalMaterialCost, 0, ',', '.') }}
        </div>
    @endif
    @endif

    <!-- Financial Summary -->
    <div class="section-title">Ringkasan Keuangan</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th width="33%">Keterangan</th>
                <th width="33%" class="text-center">Nilai</th>
                <th width="34%" class="text-center">Persentase</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Pemasukan</td>
                <td class="text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                <td class="text-center">{{ $totalIncome + $totalExpense > 0 ? round(($totalIncome / ($totalIncome + $totalExpense)) * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                <td class="text-center">{{ $totalIncome + $totalExpense > 0 ? round(($totalExpense / ($totalIncome + $totalExpense)) * 100, 1) : 0 }}%</td>
            </tr>
            <tr class="total-row">
                <td><strong>Keuntungan Bersih</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalProfit, 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ $totalIncome > 0 ? round(($totalProfit / $totalIncome) * 100, 1) : 0 }}%</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Berliano Leather</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
        <p>© {{ date('Y') }} Berliano Leather - All rights reserved</p>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
