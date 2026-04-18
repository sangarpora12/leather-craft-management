@extends('layouts.admin')

@section('title_header', 'Laporan Harian Operasional')

@section('content')
<div class="space-y-6">
    <!-- Header Laporan -->
    <div class="bg-zinc-900 rounded-xl p-6 border border-zinc-800">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">Laporan Harian Operasional</h1>
                <p class="text-gray-400 mt-1">Tanggal: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                <p class="text-gray-400 text-sm mt-1">Dibuat oleh: {{ Auth::user()->name ?? 'Admin' }}</p>
            </div>
            <div class="text-right">
                <div class="text-gray-400 text-sm">Total Produksi Hari Ini</div>
                <div class="text-3xl font-bold text-orange-500">{{ number_format($totalProduction) }}</div>
                <div class="text-gray-500 text-xs">Unit</div>
            </div>
        </div>
    </div>

    <!-- Bahan Baku Masuk -->
    <div class="bg-zinc-900 rounded-xl border border-zinc-800 overflow-hidden">
        <div class="p-4 border-b border-zinc-800">
            <h3 class="text-lg font-bold text-white">I. Bahan Baku Masuk</h3>
            <p class="text-gray-400 text-sm">Pembelian bahan baku hari ini</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Bahan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Supplier</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase">Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Harga Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($bahanBakuMasuk as $bahan)
                        <tr class="hover:bg-zinc-900/30">
                            <td class="px-6 py-4 text-white font-medium">{{ $bahan->nama_material }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $bahan->supplier }}</td>
                            <td class="px-6 py-4 text-center text-white">{{ floatval($bahan->jumlah) }} {{ $bahan->satuan }}</td>
                            <td class="px-6 py-4 text-right text-gray-300">Rp {{ number_format($bahan->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-orange-500 font-bold">Rp {{ number_format($bahan->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">Tidak ada pembelian bahan baku hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($bahanBakuMasuk->count() > 0)
                <tfoot class="bg-zinc-900/50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-white font-bold">Total Pengeluaran:</td>
                        <td class="px-6 py-3 text-right text-orange-500 font-bold text-lg">Rp {{ number_format($bahanBakuMasuk->sum('total_harga'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Aktivitas Produksi -->
    <div class="bg-zinc-900 rounded-xl border border-zinc-800 overflow-hidden">
        <div class="p-4 border-b border-zinc-800">
            <h3 class="text-lg font-bold text-white">II. Aktivitas Produksi</h3>
            <p class="text-gray-400 text-sm">SPK yang dikerjakan hari ini</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Kode SPK</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Operator</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($productions as $prod)
                        <tr class="hover:bg-zinc-900/30">
                            <td class="px-6 py-4 text-gray-400 font-mono">{{ \Carbon\Carbon::parse($prod->created_at)->format('H:i') }}</td>
                            <td class="px-6 py-4 text-gray-400 font-mono">{{ $prod->kode_spk }}</td>
                            <td class="px-6 py-4 text-white font-medium">{{ $prod->produk_jadi->nama_produk ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $prod->operator->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-500/10 text-green-500 border border-green-500/20">
                                    Selesai
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-white font-bold">{{ $prod->jumlah_produksi }} Pcs</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">Belum ada aktivitas produksi hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Barang Keluar -->
    <div class="bg-zinc-900 rounded-xl border border-zinc-800 overflow-hidden">
        <div class="p-4 border-b border-zinc-800">
            <h3 class="text-lg font-bold text-white">III. Barang Keluar</h3>
            <p class="text-gray-400 text-sm">Produk yang terjual/dikirim hari ini</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Produk</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase">Jumlah Keluar</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase">Total Nilai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($barangKeluar as $keluar)
                        <tr class="hover:bg-zinc-900/30">
                            <td class="px-6 py-4 text-white font-medium">{{ $keluar->produk_jadi->nama_produk ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center text-white">{{ number_format($keluar->total_keluar) }} Pcs</td>
                            <td class="px-6 py-4 text-right text-green-500 font-bold">Rp {{ number_format($keluar->total_nilai, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">Tidak ada barang keluar hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($barangKeluar->count() > 0)
                <tfoot class="bg-zinc-900/50">
                    <tr>
                        <td class="px-6 py-3 text-right text-white font-bold" colspan="2">Total Pemasukan:</td>
                        <td class="px-6 py-3 text-right text-green-500 font-bold text-lg">Rp {{ number_format($barangKeluar->sum('total_nilai'), 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Ringkasan Penggunaan Bahan Baku -->
    <div class="bg-zinc-900 rounded-xl border border-zinc-800 overflow-hidden">
        <div class="p-4 border-b border-zinc-800">
            <h3 class="text-lg font-bold text-white">IV. Ringkasan Penggunaan Bahan Baku</h3>
            <p class="text-gray-400 text-sm">Bahan yang digunakan dalam produksi hari ini</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Bahan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase">Jumlah Digunakan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($materialUsage as $mat)
                        <tr class="hover:bg-zinc-900/30">
                            <td class="px-6 py-4 text-white font-medium">{{ $mat->nama_material }}</td>
                            <td class="px-6 py-4 text-center text-white font-bold">{{ floatval($mat->total_quantity) }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $mat->satuan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">Belum ada penggunaan bahan hari ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center py-4 border-t border-zinc-800">
        <p class="text-gray-600 text-xs">© 2023 Berliano Leather - Laporan Harian Operasional</p>
    </div>
</div>
@endsection
