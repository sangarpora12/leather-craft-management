@extends('layouts.admin')

@section('page_title', 'Laporan & Mutasi')

@section('content')
@php 
// Get Pengaturan for Kop Surat
$namaPerusahaan = \App\Models\Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather';
$alamat = \App\Models\Pengaturan::where('nama_pengaturan', 'alamat')->first()?->nilai ?? '';
$noTelepon = \App\Models\Pengaturan::where('nama_pengaturan', 'no_telepon')->first()?->nilai ?? '';
$email = \App\Models\Pengaturan::where('nama_pengaturan', 'email')->first()?->nilai ?? '';
$logo = \App\Models\Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai ?? '';

// Get mode from controller (passed as $mode) - default to 'lengkap'
if (!isset($mode) || empty($mode)) {
    $mode = 'lengkap';
}
@endphp

<style>
@media print {
    * { overflow: visible !important; height: auto !important; max-height: none !important; }
    .sidebar, nav, .no-print, .print\:hidden, .hidden { display: none !important; }
    .print\:flex { display: flex !important; }
    .print\:block { display: block !important; }
    .print\:grid { display: grid !important; }
    .print\:table { display: table !important; }
    html, body { background-color: white !important; color: black !important; font-size: 10pt !important; overflow: visible !important; height: auto !important; }
    .container, .bg-zinc-900, .bg-neutral-900, .main-content, .card { background-color: white !important; color: black !important; padding: 0 !important; margin: 0 !important; overflow: visible !important; height: auto !important; box-shadow: none !important; border-radius: 0 !important; }
    .overflow-auto, .overflow-x-auto, .rounded-xl, .shadow-lg { overflow: visible !important; height: auto !important; }
    .text-white, .text-gray-300, .text-gray-400 { color: black !important; }
    .print-header-container { display: flex !important; visibility: visible !important; }
    table { border: 1px solid #333 !important; width: 100% !important; font-size: 9pt !important; page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    thead { display: table-header-group; }
    tbody { display: table-row-group; }
    th, td { border: 1px solid #333 !important; color: black !important; padding: 4px 8px !important; font-size: 9pt !important; }
    th { background-color: #f5f5f5 !important; font-weight: bold !important; }
    .summary-card { border: 1px solid #ddd !important; page-break-inside: avoid; }
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
    .print-grid-cols-4 { display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 10px !important; }
    .print-grid-cols-3 { display: grid !important; grid-template-columns: repeat(3, 1fr) !important; gap: 10px !important; }
    .mb-6, .summary-card { page-break-inside: avoid !important; }
    .print-break-before { page-break-before: always; }
    .hidden.print\:block { display: block !important; }
}
</style>

<div class="container mx-auto px-4 py-6 max-w-7xl">
    <div class="bg-zinc-900 rounded-xl shadow-lg overflow-hidden">
        <div class="bg-zinc-900/50 border-b border-zinc-800 p-4">
            <h1 class="text-xl font-bold text-white">Laporan Operasional</h1>
            <p class="text-gray-400 text-xs mt-1">Ringkasan aktivitas produksi dan gudang.</p>
        </div>

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
                    <a href="{{ route('admin.laporan.export-excel', request()->query()) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-bold">Excel</a>
                </div>
            </form>
        </div>
        <div class="hidden print:block" style="display: none;">
            <div class="print-header-container" style="text-align: center; margin-bottom: 20px; flex-direction: column;">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">{{ $namaPerusahaan }}</div>
                <div style="font-size: 12px; margin-bottom: 10px;">
                    @if($alamat){{ $alamat }}@endif
                    @if($noTelepon) | Telp: {{ $noTelepon }}@endif
                </div>
                
                <div style="font-size: 14px; font-weight: bold; border-top: 1px solid #333; border-bottom: 1px solid #333; padding: 5px 0; margin-bottom: 5px;">
                    LAPORAN OPERASIONAL GUDANG & PRODUKSI<br>
                    <span style="font-size: 11px; font-weight: normal;">Periode: {{ \Carbon\Carbon::parse($fromDate ?? now())->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($toDate ?? now())->translatedFormat('d F Y') }}</span>
                </div>
                <div style="font-size: 10px; text-align: right; font-style: italic;">
                    Dicetak pada: {{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
                </div>
            </div>
        </div>

        <div class="p-4">
            
            <div class="mb-6">
                <h3 class="text-white text-sm font-bold mb-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ringkasan Keuangan
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 print-grid-cols-3">
                    <div class="summary-card bg-gradient-to-br from-red-900/30 to-red-800/20 rounded-lg p-4 border border-red-700/50">
                        <p class="text-red-300 text-xs font-semibold">Total Pengeluaran (Produksi)</p>
                        <p class="text-red-400 text-xl font-bold">Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</p>
                        <p class="text-red-300/60 text-xs mt-1">Biaya bahan & upah SPK selesai</p>
                    </div>
                    <div class="summary-card bg-gradient-to-br from-green-900/30 to-green-800/20 rounded-lg p-4 border border-green-700/50">
                        <p class="text-green-300 text-xs font-semibold">Total Pemasukan (Penjualan)</p>
                        <p class="text-green-400 text-xl font-bold">Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</p>
                        <p class="text-green-300/60 text-xs mt-1">Nilai produk terjual keluar</p>
                    </div>
                    <div class="summary-card bg-gradient-to-br from-blue-900/30 to-blue-800/20 rounded-lg p-4 border border-blue-700/50">
                        <p class="text-blue-300 text-xs font-semibold">Estimasi Laba Kotor</p>
                        <p class="text-blue-400 text-xl font-bold {{ ($estimasiLaba ?? 0) < 0 ? 'text-red-400' : 'text-blue-400' }}">Rp {{ number_format($estimasiLaba ?? 0, 0, ',', '.') }}</p>
                        <p class="text-blue-300/60 text-xs mt-1">Selisih pemasukan - pengeluaran</p>
                    </div>
                </div>
            </div>

            @if($mode == 'produk' || $mode == 'lengkap')
            <div class="mb-2">
                <h3 class="text-white text-sm font-bold uppercase tracking-wider">📦 PRODUK</h3>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Total produk jadi</p>
                    <p class="text-white text-xl font-bold">{{ number_format($totalOutput ?? 0) }}</p>
                </div>
                <div class="bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Produksi belum selesai</p>
                    <p class="text-white text-xl font-bold">{{ number_format($produksiPending ?? 0) }}</p>
                </div>
                <div class="bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Produk Keluar</p>
                    <p class="text-white text-xl font-bold">{{ number_format($barangKeluar ?? 0) }}</p>
                </div>
                <div class="bg-red-900/20 rounded-lg p-4 border border-red-800">
                    <p class="text-red-400 text-xs font-bold uppercase">Produk Kritis</p>
                    <p class="text-red-400 text-xl font-bold">{{ $produkKritisCount ?? 0 }}</p>
                    <p class="text-red-300/60 text-[10px]">Stok hampir habis</p>
                </div>
            </div>
            @endif

            @if($mode == 'bahan' || $mode == 'lengkap')
            <div class="mb-2">
                <h3 class="text-white text-sm font-bold uppercase tracking-wider">📦 BAHAN</h3>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 print-grid-cols-4">
                <div class="summary-card bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Total Bahan Masuk</p>
                    <p class="text-green-400 text-xl font-bold">+{{ number_format($totalBahanMasuk ?? 0) }}</p>
                </div>
                <div class="summary-card bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Total Bahan Keluar</p>
                    <p class="text-red-400 text-xl font-bold">-{{ number_format($totalBahanKeluar ?? 0) }}</p>
                </div>
                <div class="summary-card bg-zinc-900/50 rounded-lg p-4 border border-zinc-800">
                    <p class="text-gray-400 text-xs">Total bahan di gudang</p>
                    <p class="text-white text-xl font-bold">{{ number_format($stokTersedia ?? 0) }}</p>
                </div>
                <div class="summary-card bg-amber-900/20 rounded-lg p-4 border border-amber-800">
                    <p class="text-amber-400 text-xs font-bold uppercase">Bahan Kritis</p>
                    <p class="text-amber-400 text-xl font-bold">{{ $bahanKritisCount ?? 0 }}</p>
                    <p class="text-amber-300/60 text-[10px]">Segera Restock</p>
                </div>
            </div>
            @endif

            @if($mode == 'lengkap')
            <div class="mb-2">
                <h3 class="text-white text-sm font-bold uppercase tracking-wider">💰 METRIK KEUANGAN (ERP)</h3>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="summary-card bg-gradient-to-br from-purple-900/30 to-purple-800/20 rounded-lg p-4 border border-purple-700/50">
                    <p class="text-purple-300 text-xs font-semibold">Estimasi Nilai Aset</p>
                    <p class="text-purple-400 text-lg font-bold">Rp {{ number_format($estimasiNilaiAset ?? 0, 0, ',', '.') }}</p>
                    <p class="text-purple-300/60 text-xs mt-1">Stok gudang (Bahan + Produk)</p>
                </div>
                <div class="summary-card bg-gradient-to-br from-emerald-900/30 to-emerald-800/20 rounded-lg p-4 border border-emerald-700/50">
                    <p class="text-emerald-300 text-xs font-semibold">Laba Bersih</p>
                    <p class="text-emerald-400 text-lg font-bold {{ ($labaBersih ?? 0) < 0 ? 'text-red-400' : '' }}">Rp {{ number_format($labaBersih ?? 0, 0, ',', '.') }}</p>
                    <p class="text-emerald-300/60 text-xs mt-1">Setelah biaya operasional</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800">
                    <div class="px-4 py-3 border-b border-zinc-800">
                        <h3 class="text-white text-sm font-bold">🏆 Top 3 Produk Terlaris</h3>
                    </div>
                    <div class="p-4">
                        @forelse($topProdukTerlaris as $produk)
                        <div class="flex items-center justify-between py-3 border-b border-zinc-800 last:border-0">
                            <div>
                                <p class="text-white text-sm font-bold">{{ $produk->nama_produk }}</p>
                                <p class="text-gray-500 text-xs">{{ $produk->kode_produk }}</p>
                            </div>
                            <span class="text-green-400 text-sm font-bold">{{ number_format($produk->total_terjual) }} pcs</span>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm text-center py-4">Belum ada data penjualan</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800">
                    <div class="px-4 py-3 border-b border-zinc-800">
                        <h3 class="text-white text-sm font-bold">🔥 Bahan Paling Cepat Habis</h3>
                    </div>
                    <div class="p-4">
                        @forelse($bahanCepatHabis as $bahan)
                        <div class="flex items-center justify-between py-3 border-b border-zinc-800 last:border-0">
                            <div>
                                    <p class="text-white text-sm font-bold">{{ $bahan->nama_material }}</p>
                                    <p class="text-gray-500 text-xs">{{ $bahan->kode_material }}
                                        @if(isset($bahan->warna) && $bahan->warna && $bahan->warna !== 'Tidak Ada Warna')
                                            <span class="text-blue-400 ml-1">[{{ $bahan->warna }}]</span>
                                        @endif
                                    </p>
                            </div>
                            <span class="text-red-400 text-sm font-bold">-{{ number_format($bahan->total_dipakai) }} {{ $bahan->satuan }}</span>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm text-center py-4">Belum ada data pemakaian</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            
            @if($mode == 'produk' || $mode == 'lengkap')
            <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800 mb-6">
                <div class="px-4 py-3 border-b border-zinc-800">
                    <h3 class="text-white text-sm font-bold">Ringkasan Output Produksi (Produk Selesai)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full print:table">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Kode</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Produk</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Operator</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Harga Satuan</th>
                                <th class="px-3 py-2 text-center text-xs font-bold text-gray-500">Qty (SPK)</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($productions as $prod)
                            <tr>
                                <td class="px-3 py-2 text-xs text-gray-400">{{ $prod->kode_produk ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs font-bold text-white">{{ $prod->nama_produk }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300">{{ $prod->operator_name ?? 'Tidak diketahui' }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300 text-right">Rp {{ number_format($prod->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-xs text-green-400 text-center font-bold">
                                    +{{ number_format($prod->jumlah_selesai) }} pcs
                                    <br><span class="text-gray-500 text-[10px] font-normal">({{ $prod->jumlah_spk }} SPK)</span>
                                </td>
                                <td class="px-3 py-2 text-xs text-emerald-400 text-right font-bold">Rp {{ number_format($prod->jumlah_selesai * $prod->harga_jual, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500 text-xs">Belum ada data produksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800 mb-6">
                <div class="px-4 py-3 border-b border-zinc-800">
                    <h3 class="text-white text-sm font-bold">Ringkasan Produk Keluar (Penjualan & Pemakaian)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full print:table">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Tanggal</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Kode</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Produk</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Keterangan</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Harga Satuan</th>
                                <th class="px-3 py-2 text-center text-xs font-bold text-gray-500">Jumlah</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Total (Rp)</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($produkKeluars as $keluar)
                            <tr>
                                <td class="px-3 py-2 text-xs text-gray-400 whitespace-nowrap">
                                    {{ isset($keluar->tanggal_terbaru) ? \Carbon\Carbon::parse($keluar->tanggal_terbaru)->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-400">{{ $keluar->kode_produk ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs font-bold text-white">{{ $keluar->nama_produk }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300">{{ $keluar->alasan }}</td>
                                <td class="px-3 py-2 text-xs text-gray-300 text-right">Rp {{ number_format($keluar->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-xs text-red-400 text-center font-bold">-{{ number_format($keluar->jumlah) }} pcs</td>
                                <td class="px-3 py-2 text-xs text-amber-400 text-right font-bold">Rp {{ number_format($keluar->jumlah * $keluar->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-xs text-gray-500">{{ $keluar->operator_name ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="px-3 py-4 text-center text-gray-500 text-xs">Belum ada data produk keluar</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($mode == 'bahan' || $mode == 'lengkap')
            <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800 mb-6 print-break-before">
                <div class="px-4 py-3 border-b border-zinc-800">
                    <h3 class="text-white text-sm font-bold">Ringkasan Bahan Masuk (Pengadaan)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full print:table">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Kode</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Nama Bahan</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Harga Satuan</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Total Nilai</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Jumlah Masuk</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Operator / User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($pengadaans as $bahan)
                            <tr>
                                <td class="px-3 py-2 text-xs text-gray-400">{{ $bahan->kode_material ?? '-' }}</td>
                                <td class="px-3 py-2 text-xs font-bold text-white">
                                    {{ $bahan->nama_material }}
                                    @if(!empty($bahan->warna) && strtolower($bahan->warna) !== 'tidak ada warna')
                                        <span class="text-blue-400 ml-1">[{{ $bahan->warna }}]</span>
                                    @else
                                        <span class="text-gray-500 ml-1">[Tidak Ada Warna]</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-xs text-gray-300 text-right">Rp {{ number_format($bahan->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-xs text-amber-400 text-right font-bold">Rp {{ number_format(($bahan->harga_satuan ?? 0) * $bahan->jumlah, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-xs text-green-400 text-right font-bold">+{{ rtrim(rtrim(number_format($bahan->jumlah, 2, ',', '.'), '0'), ',') }} {{ $bahan->satuan }}</td>
                                <td class="px-3 py-2 text-xs text-gray-500">{{ $bahan->operator_name ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500 text-xs">Belum ada data bahan masuk</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="bg-zinc-900/50 rounded-lg overflow-hidden border border-zinc-800 mb-6">
                <div class="px-4 py-3 border-b border-zinc-800">
                    <h3 class="text-white text-sm font-bold">Ringkasan Bahan Keluar (Pemakaian untuk SPK)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full print:table">
                        <thead class="bg-neutral-900">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Kode</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Bahan</th>
                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500">Keterangan</th> 
                                <th class="px-3 py-2 text-center text-xs font-bold text-gray-500">Satuan</th>
                                <th class="px-3 py-2 text-right text-xs font-bold text-gray-500">Total Dipakai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @forelse($materialUsage as $bahan)
                                @if(empty($bahan->deleted_at) && ($bahan->status ?? '') !== 'dihapus' && ($bahan->status ?? '') !== 'deleted')
                                <tr>
                                    <td class="px-3 py-2 text-xs text-gray-400">{{ $bahan->kode_material ?? '-' }}</td>
                                    <td class="px-3 py-2 text-xs font-bold text-white">
                                        {{ $bahan->nama_material }}
                                        @if(!empty($bahan->warna) && strtolower($bahan->warna) !== 'tidak ada warna')
                                            <span class="text-blue-400 ml-1">[{{ $bahan->warna }}]</span>
                                        @else
                                            <span class="text-gray-500 ml-1">[Tidak Ada Warna]</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-400">
                                        Dipakai untuk: 
                                        <span class="text-white font-semibold">
                                            {{ $bahan->nama_produk ?? ($bahan->kode_spk ?? 'SPK Tidak Diketahui') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-300 text-center">{{ $bahan->satuan ?? '-' }}</td>
                                    <td class="px-3 py-2 text-xs text-red-400 text-right font-bold">-{{ rtrim(rtrim(number_format($bahan->jumlah, 2, ',', '.'), '0'), ',') }}</td>
                                </tr>
                                @endif
                            @empty
                            <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500 text-xs">Belum ada data bahan keluar</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.onbeforeprint = function() { 
    var printHeader = document.querySelector('.print-header-container');
    if (printHeader) { printHeader.parentElement.style.display = 'block'; }
};
window.onafterprint = function() { 
    var printHeader = document.querySelector('.print-header-container');
    if (printHeader) { printHeader.parentElement.style.display = 'none'; }
};
</script>
@endsection