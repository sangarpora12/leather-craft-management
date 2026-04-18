@extends('layouts.admin')

@section('css')
<style>
    @media print {
        @page { size: A4; margin: 1cm; }
        
        body { 
            background: white !important; 
            font-size: 12px;
        }
        
        /* Hide elements */
        .no-print, 
        .print-button, 
        button, 
        a, 
        .sidebar,
        .navbar,
        nav,
        header,
        footer,
        .filter-form { 
            display: none !important; 
        }
        
        /* Show print content */
        .print-area { 
            display: block !important; 
        }
        
        /* Container styles */
        .container { 
            max-width: 100% !important; 
            padding: 0 !important; 
            margin: 0 !important; 
        }
        
        /* Card styles */
        .bg-zinc-800 { 
            background: white !important; 
            border: 1px solid #000 !important; 
            box-shadow: none !important; 
            margin-bottom: 10px;
        }
        
        /* Text colors */
        .text-white, 
        .text-gray-300, 
        .text-gray-400, 
        .text-gray-500 { 
            color: #000 !important; 
        }
        
        .text-emerald-400 { color: #000 !important; }
        .text-yellow-400 { color: #000 !important; }
        .text-red-400 { color: #000 !important; }
        .text-blue-400 { color: #000 !important; }
        
        /* Round and shadow */
        .rounded-lg, .rounded-xl { 
            border-radius: 0 !important; 
            box-shadow: none !important; 
        }
        
        .card { 
            page-break-inside: avoid; 
        }
        
        /* Table styles */
        table { 
            width: 100% !important; 
            border-collapse: collapse !important; 
            font-size: 10px;
        }
        
        th, td { 
            border: 1px solid #000 !important; 
            color: #000 !important; 
            padding: 5px !important;
        }
        
        th { 
            background: #eee !important; 
        }
        
        /* Overflow */
        .overflow-x-auto { 
            overflow: visible !important; 
        }
        
        /* Hide shadows */
        .shadow-lg, .shadow { 
            box-shadow: none !important; 
        }
        
        /* Print only section */
        .print-only { 
            display: table !important; 
            margin-top: 30px;
        }
        
        /* Title */
        h1 { font-size: 18px !important; }
        h3 { font-size: 14px !important; }
    }
    
    .print-area { display: block; }
    .print-only { display: none; }
    .hidden { display: none; }
</style>
@endsection

@section('content')
<!-- Main Content - Visible on Screen and Print -->
<div class="container mx-auto px-4 py-6 max-w-7xl print-area">
    
    <!-- Header with Title -->
    <div style="margin-bottom: 20px;">
        <h1 class="text-2xl font-bold text-white" style="color: #000 !important;">Laporan Produksi</h1>
        <p class="text-gray-400 text-sm mt-1" style="color: #666 !important;">Periode: {{ $fromDate }} - {{ $toDate }}</p>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" class="flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors print-button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Cetak
        </button>
        <a href="{{ route('admin.laporan.exportPdf', ['produk_id' => $produkId ?? '', 'bahan_id' => $bahanId ?? '', 'periode' => $periode ?? 'bulanan', 'tanggal' => $tanggal ?? date('Y-m-d'), 'bulan' => $bulan ?? date('m'), 'tahun' => $tahun ?? date('Y')]) }}" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Export PDF
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 mb-6" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px;">
        <div class="bg-zinc-800 rounded-lg p-4 card" style="border: 1px solid #000; padding: 10px;">
            <p style="font-size: 10px; color: #666;">Total Output</p>
            <p style="font-size: 18px; font-weight: bold; color: #000;">{{ number_format($totalOutput) }}</p>
            <p style="font-size: 10px; color: #666;">Pcs</p>
        </div>
        <div class="bg-zinc-800 rounded-lg p-4 card" style="border: 1px solid #000; padding: 10px;">
            <p style="font-size: 10px; color: #666;">Total Omzet</p>
            <p style="font-size: 18px; font-weight: bold; color: #000;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <div class="bg-zinc-800 rounded-lg p-4 card" style="border: 1px solid #000; padding: 10px;">
            <p style="font-size: 10px; color: #666;">Total Biaya</p>
            <p style="font-size: 18px; font-weight: bold; color: #000;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
        <div class="bg-zinc-800 rounded-lg p-4 card" style="border: 1px solid #000; padding: 10px;">
            <p style="font-size: 10px; color: #666;">Pending</p>
            <p style="font-size: 18px; font-weight: bold; color: #000;">{{ number_format($produksiPending) }}</p>
            <p style="font-size: 10px; color: #666;">Pcs</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-zinc-800 rounded-lg overflow-hidden card" style="border: 1px solid #000;">
        <div class="p-4 border-b border-zinc-700" style="border-bottom: 1px solid #000;">
            <h3 class="text-white text-sm font-bold" style="color: #000; font-weight: bold;">Detail Produksi</h3>
            <p class="text-gray-500 text-xs" style="color: #666;">{{ $fromDate }} - {{ $toDate }}</p>
        </div>
        <div style="overflow-x: auto;">
            <table class="w-full text-sm" style="width: 100%; border-collapse: collapse;">
                <thead class="bg-zinc-900" style="background: #eee;">
                    <tr>
                        <th style="text-align: left; padding: 8px; border: 1px solid #000;">No</th>
                        <th style="text-align: left; padding: 8px; border: 1px solid #000;">Tanggal</th>
                        <th style="text-align: left; padding: 8px; border: 1px solid #000;">Nama Produk</th>
                        <th style="text-align: right; padding: 8px; border: 1px solid #000;">Target</th>
                        <th style="text-align: right; padding: 8px; border: 1px solid #000;">Tercapai</th>
                        <th style="text-align: right; padding: 8px; border: 1px solid #000;">Nilai Output (Rp)</th>
                        <th style="text-align: center; padding: 8px; border: 1px solid #000;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productions as $index => $prod)
                    <tr style="border-bottom: 1px solid #ccc;">
                        <td style="padding: 8px; border: 1px solid #000;">{{ $index + 1 }}</td>
                        <td style="padding: 8px; border: 1px solid #000;">{{ \Carbon\Carbon::parse($prod->tanggal)->format('d/m/Y') }}</td>
                        <td style="padding: 8px; border: 1px solid #000; font-weight: bold;">{{ $prod->nama_produk }}</td>
                        <td style="text-align: right; padding: 8px; border: 1px solid #000;">{{ number_format($prod->total_target) }}</td>
                        <td style="text-align: right; padding: 8px; border: 1px solid #000; font-weight: bold;">{{ number_format($prod->total_capaian) }}</td>
                        <td style="text-align: right; padding: 8px; border: 1px solid #000;">Rp {{ number_format($prod->total_capaian * $prod->harga_jual, 0, ',', '.') }}</td>
                        <td style="text-align: center; padding: 8px; border: 1px solid #000;">Selesai</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center; border: 1px solid #000;">Tidak ada data produksi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Tanda Tangan - Print Only -->
    <div class="print-only" style="display: none; margin-top: 40px;">
        <table class="w-full text-sm" style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding-top: 40px; text-align: center; width: 50%;">
                    <p style="margin-bottom: 60px; font-weight: bold;">Dibuat Oleh:</p>
                    <p>{{ Auth::user()->name ?? 'Administrator' }}</p>
                </td>
                <td style="border: none; padding-top: 40px; text-align: center; width: 50%;">
                    <p style="margin-bottom: 60px; font-weight: bold;">Mengetahui:</p>
                    <p>Pimpinan</p>
                </td>
            </tr>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleFields(val) {
    document.getElementById('tgl-field').classList.toggle('hidden', val !== 'harian');
    document.getElementById('bln-field').classList.toggle('hidden', val !== 'bulanan');
    document.getElementById('thn-field').classList.toggle('hidden', val !== 'bulanan');
}
var chartLabels = <?php echo json_encode($chartLabels); ?>;
var chartData = <?php echo json_encode($chartData); ?>;
var ctx = document.getElementById('productionChart');
if(ctx) {
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: { labels: chartLabels, datasets: [{ label: 'Produksi', data: chartData, borderColor: '#F97316', backgroundColor: 'rgba(249, 115, 22, 0.1)', tension: 0.4, fill: true }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: {} } }
    });
}
</script>
@endsection
