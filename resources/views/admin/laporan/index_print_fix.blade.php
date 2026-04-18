@extends('layouts.admin')

@section('content')
@php 
// Get Pengaturan for Kop Surat
$namaPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather';
$alamat = \App\Models\Pengaturan::where('nama_pengaturan', 'alamat')->first()?->nilai ?? '';
$noTelepon = \App\Models\Pengaturan::where('nama_pengaturan', 'no_telepon')->first()?->nilai ?? '';
$email = \App\Models\Pengaturan::where('nama_pengaturan', 'email')->first()?->nilai ?? '';
$logo = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai ?? '';

// Get mode from controller (passed as $mode)
$mode = $mode ?? 'lengk' ?? 'lengkap';

// Combine bahan data for mode bahan - include both pengadaans (incoming) and materialUsage (outgoing)
$bahanItems = collect([]);
if(isset($pengadaans) && $pengadaans->count() > 0) {
    $bahanItems = $bahanItems->concat($pengadaans);
}
if(isset($materialUsage) && $materialUsage->count() > 0) {
    $bahanItems = $bahanItems->concat($materialUsage);
}
$bahanItems = $bahanItems->sortByDesc('tanggal')->values();
@endphp

<style>
@media print {
    /* Reset all overflow restrictions */
    html, body, .main-content, .card, .overflow-auto, .overflow-x-auto {
        height: auto !important;
        overflow: visible !important;
        display: block !important;
    }
    
    /* Hide sidebar and nav elements */
    .sidebar, nav, .no-print, .print\:hidden {
        display: none !important;
    }
    
    /* Force print block for elements */
    .hidden { display: none !important; }
    .print\:flex { display: flex !important; }
    .print\:block { display: block !important; }
    .print\:grid { display: grid !important; }
    .print\:table { display: table !important; }
    
    /* Body and container reset */
    body { 
        background-color: white !important; 
        color: black !important;
        font-size: 10pt !important;
    }
    .container, .bg-zinc-900, .bg-neutral-900 { 
        background-color: white !important; 
        color: black !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Text colors */
    .text-white, .text-gray-300, .text-gray-400 { 
        color: black !important; 
    }
    
    /* Print header styles */
    .print-header-container {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 2px solid #000;
        margin-bottom: 20px;
    }
    
    /* Table styles */
    table { 
        border: 1px solid #333 !important; 
        width: 100% !important;
        font-size: 9pt !important;
    }
    th, td { 
        border: 1px solid #333 !important; 
        color: black !important;
        padding: 4px 8px !important;
        font-size: 9pt !important;
    }
    th { 
        background-color: #f5f5f5 !important; 
        font-weight: bold !important;
    }
    
    /* Card/Summary styles */
    .summary-card { 
        border: 1px solid #ddd !important; 
        page-break-inside: avoid;
    }
    
    /* Financial Cards Print Styles - Light backgrounds */
    .bg-gradient-to-br { background: none !important; }
    .from-red-900\/30, .from-red-800\/20 { background-color: #fef2f2 !important; border-color: #fecaca !important; }
    .from-green-900\/30, .from-green-800\/20 { background-color: #f0fdf4 !important; border-color: #bbf7d0 !important; }
    .from-blue-900\/30, .from-blue-800\/20 { background-color: #eff6ff !important; border-color: #bfdbfe !important; }
    .text-red-300, .text-red-400 { color: #dc2626 !important; }
    .text-green-300, .text-green-400 { color: #16a34a !important; }
    .text-blue-300, .text-blue-400 { color: #2563eb !important; }
    .text-red-300\/60 { color: #991b1b !important; }
    .text-green-300\/60 { color: #166534 !important; }
    .text-blue-300\/60 { color: #1e40af !important; }
    .text-yellow-400 { color: #ca8a04 !important; }
    
    /* Grid for print */
    .print-grid-cols-3 {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px !important;
    }
    
    /* Avoid page breaks inside cards */
    .mb-6, .summary-card {
        page-break-inside: avoid !important;
    }
    
    /* Print break after */
    .print-break-after { page-break-after: always; }
}
</style>

<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="bg-zinc-900 rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-zinc-900/50 border-b border-zinc-800 p-4">
            <h1 class="text-xl font-bold text-white">Laporan Operasional</h1>
            <p class="text-gray-400 text-xs mt-1">Ringkasan aktivitas produksi dan gudang.</p>
        </div>

        <!-- Filter Form -->
        <div class="bg-zinc-900 p-3 border-b border-zinc-800 no-print">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-wrap gap-2 items-end">
                <div>
                    <label class="block text-gray-400 text-xs font-semibold mb-1">Mode Laporan</label>
                    <select name="mode" class="bg-neutral-900 border border-zinc-700 rounded px-2 py-1.5 text-white text-xs" onchange="this.form.submit()">
                        <option value="lengkap" {{ $mode == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                        <option value="produk" {{ $mode == 'produk' ? 'selected' : '' }}>Produk</option>
                        <option value="bahan" {{ $mode == 'bahan' ? 'selected' : '' }}>Bahan</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <div>
                        <label class="block text-gray-400 text-xs font-semibold mb-1">Dari</label>
                        <input type="date" name="from" value="{{ $fromDate ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="bg-neutral-900 border border-zinc-700 rounded px-2 py-1.5 text-white text-xs">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-semibold mb-1">Sampai</label>
                        <input type="date" name="to" value="{{ $toDate ?? \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" class="bg-neutral-900 border border-zinc-700 rounded px-2 py-1.5 text-white text-xs">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded text-xs font-bold">Terapkan</button>
                    <button type="button" onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-bold">Cetak</button>
                </div>
            </form>
        </div>

        <!-- Print Header (Kop Surat) - Hidden on screen, shown only when printing -->
        <div class="hidden print:block" style="display: none;">
            <div class="print-header-container">
                <div>
                    @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" style="max-height: 80px; max-width: 150px;">
                    @else
                    <div style="width: 150px; height: 80px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #999;">Logo</div>
                    @endif
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 24px; font-weight: bold;">{{ $namaPerusahaan }}</div>
                    <div style="font-size: 12px;">
                        @if($alamat){{ $alamat }}<br>@endif
                        @if($noTelepon)Telp: {{ $noTelepon }}@endif
                        @if($email) | Email: {{ $email }}@endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4">
            <!-- RINGKASAN STATISTIK (untuk mode bahan) -->
            @if($mode == 'bahan' || $mode == 'lengkap')
            <div class="grid grid-cols-3 gap-4 mb-6 print-grid-cols-3">
                <!-- Card: Total Bahan Masuk -->
                <div class="summary-card bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Total Bahan Masuk</p>
                    <p class="text-green-400 text-xl font-bold">+{{ number_format($totalBahanMasuk ?? 0) }}</p>
                </div>
                <!-- Card: Total Bahan Keluar -->
                <div class="summary-card bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Total Bahan Keluar</p>
                    <p class="text-red-400 text-xl font-bold">-{{ number_format($totalBahanKeluar ?? 0) }}</p>
                </div>
                <!-- Card: Stok Tersedia -->
                <div class="summary-card bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Stok Tersedia</p>
                    <p class="text-white text-xl font-bold">{{ number_format($stokTersedia ?? 0) }}</p>
                </div>
            </div>
            @endif

            <!-- Summary Cards (Lengkap mode only) -->
            @if($mode == 'lengkap')
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Total Output</p>
                    <p class="text-white text-xl font-bold">{{ number_format($totalOutput) }}</p>
                </div>
                <div class="bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Produksi Pending</p>
                    <p class="text-white text-xl font-bold">{{ number_format($produksiPending) }}</p>
                </div>
                <div class="bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Barang Keluar</p>
                    <p class="text-white text-xl font-bold">{{ number_format($barangKeluar) }}</p>
                </div>
                <div class="bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Stok Kritis</p>
                    <p class="text-white text-xl font-bold">{{ $stokKritisCount }}</p>
                </div>
            </div>
            @endif

            <!-- RINGKASAN KEUANGAN (Financial Summary) - Visible in all modes -->
            <div class="mb-6 print-break-after">
                <h3 class="text-white text-sm font-bold mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ringkasan Keuangan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print-grid-cols-3">
                    <!-- Total Pengeluaran -->
                    <div class="summary-card bg-gradient-to-br from-red-900/30 to-red-800/20 rounded-lg p-4 border border-red-700/50">
                        <p class="text-red-300 text-xs font-semibold">Total Pengeluaran (Produksi)</p>
                        <p class="text-red-400 text-xl font-bold">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</p>
                        <p class="text-red-300/60 text-xs mt-1">Biaya bahan & upah SPK selesai</p>
                    </div>
                    <!-- Total Pemasukan -->
                    <div class="summary-card bg-gradient-to-br from-green-900/30 to-green-800/20 rounded-lg p-4 border border-green-700/50">
                        <p class="text-green-300 text-xs font-semibold">Total Pemasukan (Penjualan)</p>
                        <p class="text-green-400 text-xl font-bold">Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</p>
                        <p class="text-green-300/60 text-xs mt-1">Nilai produk terjual keluar</p>
                    </div>
                    <!-- Estimasi Laba -->
                    <div class="summary-card bg-gradient-to-br from-blue-900/30 to-blue-800/20 rounded-lg p-4 border border-blue-700/50">
                        <p class="text-blue-300 text-xs font-semibold">Estimasi Laba Kotor</p>
                        <p class="text-blue-400 text-xl font-bold {{ ($estimasiLaba ?? 0) < 0 ? 'text-red-400' : 'text-blue-400' }}">Rp {{ number_format($estimasiLaba ?? 0, 0, ',', '.') }}</p>
                        <p class="text-blue-300/60 text-xs mt-1">Selisih pemasukan - pengeluaran</p>
                    </div>
                </div>
            </div>

            <!-- TABEL PRODUK (mode produk atau lengkap) -->
            @if($mode == 'produk' || $mode == 'lengkap')
            <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800 mb-6">
                <div class="px-4 py-3 border-b border-zinc-800">
                    <h3 class="text-white text-sm font-bold">Output Produksi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full print:table">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Tanggal</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Produk</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Jenis</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Jumlah</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Nilai (Rp)</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($productions as $prod)
                            <tr>
                                <td class="px-3 py-2 text-xs text-white">{{ \Carbon\Carbon::parse($prod->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-xs font-bold text-white">{{ $prod->nama_produk }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300">{{ $prod->operator_name ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs"><span class="bg-blue-500/20 text-blue-400 px-2 rounded">Produksi</span></td>
                                <td class="px-3 py-2 text-xs text-white text-right">+{{ number_format($prod->jumlah_selesai) }}</td>
                                <td class="px-3 py-2 text-xs text-emerald-400 text-right">Rp {{ number_format($prod->jumlah_selesai * $prod->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300">{{ $prod->kode_spk ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="px-3 py-4 text-center text-gray-500 text-xs">Belum ada data produksi</td></tr>
                            @endforelse
                            @foreach($produkKeluars as $keluar)
                            <tr>
                                <td class="px-3 py-2 text-xs text-white">{{ \Carbon\Carbon::parse($keluar->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-xs font-bold text-white">{{ $keluar->nama_produk }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300">{{ $keluar->operator_name ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs"><span class="bg-green-500/20 text-green-400 px-2 rounded">Keluar</span></td>
                                <td class="px-3 py-2 text-xs text-red-400 text-right">-{{ number_format($keluar->jumlah) }}</td>
                                <td class="px-3 py-2 text-xs text-emerald-400 text-right">Rp {{ number_format($keluar->jumlah * $keluar->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300">{{ $keluar->kode ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- TABEL BAHAN (mode bahan atau lengkap) - Now shows both IN and OUT -->
            @if($mode == 'bahan' || $mode == 'lengkap')
            <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800 mb-6">
                <div class="px-4 py-3 border-b border-zinc-800">
                    <h3 class="text-white text-sm font-bold">Riwayat Stok Bahan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full print:table">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Tanggal</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Bahan</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Jenis</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Jumlah</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($bahanItems as $bahan)
                            <tr>
                                <td class="px-3 py-2 text-xs text-white">{{ \Carbon\Carbon::parse($bahan->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-xs font-bold text-white">{{ $bahan->nama_material ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300">{{ $bahan->operator_name ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs">
                                    @if(($bahan->jenis_mutasi ?? '') == 'Masuk')
                                    <span class="bg-green-500/20 text-green-400 px-2 rounded">Bahan Baru</span>
                                    @else
                                    <span class="bg-orange-500/20 text-orange-400 px-2 rounded">Dipakai</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-xs font-bold text-right {{ ($bahan->jenis_mutasi ?? '') == 'Masuk' ? 'text-green-400' : 'text-red-400' }}">
                                    {{ ($bahan->jenis_mutasi ?? '') == 'Masuk' ? '+' : '-' }}{{ floatval($bahan->jumlah) }}
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-300">
                                    @if(($bahan->jenis_mutasi ?? '') == 'Masuk')
                                        {{ isset($bahan->kode) && $bahan->kode ? 'PO: ' . $bahan->kode : 'Penambahan Stok' }}
                                    @else
                                        {{ isset($bahan->kode_spk) && $bahan->kode_spk ? 'SPK: ' . $bahan->kode_spk : (isset($bahan->kode) && $bahan->kode ? 'SPK: ' . $bahan->kode : 'Dipakai Produksi') }}
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500 text-xs">Belum ada data bahan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
window.onbeforeprint = function() { 
    // Force show print header
    var printHeader = document.querySelector('.print-header-container');
    if (printHeader) {
        printHeader.parentElement.style.display = 'block';
    }
};
window.onafterprint = function() { 
    var printHeader = document.querySelector('.print-header-container');
    if (printHeader) {
        printHeader.parentElement.style.display = 'none';
    }
};
</script>
@endsection
