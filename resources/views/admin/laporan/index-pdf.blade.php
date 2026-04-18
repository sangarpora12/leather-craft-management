<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produksi (Ringkasan) - {{ $fromDate }} s/d {{ $toDate }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 6px 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .meta {
            margin: 10px 0 16px 0;
            font-size: 10px;
        }

        .meta .row {
            display: table;
            width: 100%;
        }

        .meta .cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 9px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        @page {
            margin: 1cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Produksi (Ringkasan)</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
        <p>Dibuat pada: {{ $tanggal_cetak }}</p>
        <p>Berliano Leather Production System</p>
    </div>

    <div class="meta">
        <div class="row">
            <div class="cell">
                <strong>Total Target:</strong> {{ number_format($totalTarget) }} pcs<br>
                <strong>Total Selesai:</strong> {{ number_format($totalCapaian) }} pcs
            </div>
            <div class="cell text-right">
                <strong>Total Nilai Output:</strong><br>
                Rp {{ number_format($totalNilai, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th>Nama Produk</th>
                <th width="12%">Target</th>
                <th width="12%">Selesai</th>
                <th width="18%">Nilai Output (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groupedData as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $row->nama_produk }}</td>
                    <td class="text-right">{{ number_format($row->total_target) }}</td>
                    <td class="text-right">{{ number_format($row->total_capaian) }}</td>
                    <td class="text-right">{{ number_format($row->nilai_output, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data produksi pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        @if(($groupedData ?? collect())->count() > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format($totalTarget) }}</td>
                    <td class="text-right">{{ number_format($totalCapaian) }}</td>
                    <td class="text-right">{{ number_format($totalNilai, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Berliano Leather</p>
        <p>© {{ date('Y') }} Berliano Leather - All rights reserved</p>
    </div>
</body>
</html>
