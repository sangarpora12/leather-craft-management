<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Produksi - {{ $from->format('d M Y') }} sampai {{ $to->format('d M Y') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            font-size: 11px;
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
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .bom-details {
            background-color: #f9f9f9;
            border-top: 1px solid #ccc;
        }

        .bom-title {
            font-size: 9px;
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }

        .bom-item {
            display: inline-block;
            background: #e8e8e8;
            padding: 2px 4px;
            margin: 1px;
            border-radius: 2px;
            font-size: 8px;
        }

        .summary-grid {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            padding: 10px;
            border: 1px solid #000;
            width: 33.33%;
        }

        .summary-cell h3 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #666;
        }

        .summary-cell .value {
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 20px;
        }

        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Hasil Produksi</h1>
        <p>Periode: {{ $from->format('d M Y') }} - {{ $to->format('d M Y') }}</p>
        <p>Dibuat pada: {{ \Carbon\Carbon::now()->format('d M Y H:i') }}</p>
        <p>Berliano Leather Production System</p>
    </div>

    <!-- Production Table -->
    <table>
        <thead>
            <tr>
                <th width="12%">Kode SPK</th>
                <th width="20%">Produk Jadi</th>
                <th width="15%">Operator</th>
                <th width="10%" class="text-center">Target</th>
                <th width="10%" class="text-center">Realisasi</th>
                <th width="8%" class="text-center">Status</th>
                <th width="15%">Tanggal Selesai</th>
                <th width="10%" class="text-center">BOM</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $d)
                <tr>
                    <td>{{ $d->kode_spk }}</td>
                    <td>{{ $d->produk_jadi->nama_produk ?? 'N/A' }}</td>
                    <td>{{ $d->operator->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $d->jumlah_produksi }}</td>
                    <td class="text-center">{{ $d->jumlah_produksi }}</td>
                    <td class="text-center">
                        <span style="color: #10b981; font-weight: bold;">✓</span>
                    </td>
                    <td>{{ $d->tanggal_selesai ? \Carbon\Carbon::parse($d->tanggal_selesai)->format('d/m/Y H:i') : 'N/A' }}</td>
                    <td class="text-center">
                        @if($d->bom && $d->bom->count() > 0)
                            {{ $d->bom->count() }}
                        @else
                            -
                        @endif
                    </td>
                </tr>

                <!-- BOM Details Row -->
                @if($d->bom && $d->bom->count() > 0)
                    <tr class="bom-details">
                        <td colspan="8">
                            <div class="bom-title">Konsumsi Bahan Baku:</div>
                            @foreach($d->bom as $bom_item)
                                <span class="bom-item">
                                    {{ $bom_item->bahan_baku->nama_material ?? 'N/A' }}:
                                    {{ floatval($bom_item->jumlah_kebutuhan * $d->jumlah_produksi) }}
                                    {{ $bom_item->bahan_baku->satuan ?? '' }}
                                </span>
                            @endforeach
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada data produksi dalam periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Statistics -->
    <div class="summary-grid">
        <div class="summary-row">
            <div class="summary-cell">
                <h3>Total SPK Selesai</h3>
                <div class="value">{{ $data->count() }}</div>
            </div>
            <div class="summary-cell">
                <h3>Total Output</h3>
                <div class="value">{{ number_format($data->sum('jumlah_produksi')) }}</div>
            </div>
            <div class="summary-cell">
                <h3>Rata-rata per SPK</h3>
                <div class="value">{{ $data->count() > 0 ? number_format($data->sum('jumlah_produksi') / $data->count(), 1) : 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Berliano Leather</p>
        <p>© {{ date('Y') }} Berliano Leather - All rights reserved</p>
    </div>
</body>
</html>
