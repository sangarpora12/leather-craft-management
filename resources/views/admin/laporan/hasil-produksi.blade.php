@extends('layouts.admin')

@section('title_header', 'Laporan Hasil Produksi')

@section('content')
<div class="space-y-6">
    <!-- Header Laporan -->
    <div class="bg-zinc-900 rounded-xl p-6 border border-zinc-800">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">Laporan Hasil Produksi</h1>
                <p class="text-gray-400 mt-1">Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
                <p class="text-gray-400 text-sm mt-1">Dibuat oleh: {{ Auth::user()->name ?? 'Admin' }}</p>
            </div>
            <div class="text-right">
                <div class="text-gray-400 text-sm">Total Produksi</div>
                <div class="text-3xl font-bold text-orange-500">{{ number_format($data->sum('jumlah_produksi')) }}</div>
                <div class="text-gray-500 text-xs">Unit</div>
            </div>
        </div>
    </div>

    <!-- Daftar Produksi -->
    <div class="bg-zinc-900 rounded-xl border border-zinc-800 overflow-hidden">
        <div class="p-4 border-b border-zinc-800">
            <h3 class="text-lg font-bold text-white">Detail Produksi</h3>
            <p class="text-gray-400 text-sm">SPK yang telah selesai dikerjakan</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-neutral-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Kode SPK</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Produk Jadi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Operator</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase">Target</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase">Realisasi</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase">Tanggal Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($data as $d)
                        <tr class="hover:bg-zinc-900/30">
                            <td class="px-6 py-4 text-gray-400 font-mono font-bold">{{ $d->kode_spk }}</td>
                            <td class="px-6 py-4 text-white font-medium">{{ $d->produk_jadi->nama_produk ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-300">{{ $d->operator->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center text-gray-400">{{ $d->jumlah_produksi }}</td>
                            <td class="px-6 py-4 text-center text-white font-bold">{{ $d->jumlah_produksi }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-500/10 text-green-500 border border-green-500/20">
                                    100%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400">{{ $d->tanggal_selesai ? \Carbon\Carbon::parse($d->tanggal_selesai)->format('d/m/Y H:i') : 'N/A' }}</td>
                        </tr>

                        <!-- BOM Details for this production -->
                        @if($d->bom && $d->bom->count() > 0)
                            <tr class="bg-zinc-900/20">
                                <td colspan="7" class="px-6 py-2">
                                    <div class="text-xs text-gray-500 mb-1">Konsumsi Bahan Baku:</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                        @foreach($d->bom as $bom_item)
                                            <div class="bg-zinc-800/50 rounded px-2 py-1 text-xs">
                                                <span class="text-gray-300">{{ $bom_item->bahan_baku->nama_material ?? 'N/A' }}</span>
                                                <span class="text-orange-400 font-bold ml-1">{{ floatval($bom_item->jumlah_kebutuhan * $d->jumlah_produksi) }} {{ $bom_item->bahan_baku->satuan ?? '' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 italic">Belum ada data produksi dalam periode ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-900 rounded-lg p-6 border border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total SPK Selesai</p>
                    <p class="text-2xl font-bold text-white">{{ $data->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-zinc-900 rounded-lg p-6 border border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Total Output</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($data->sum('jumlah_produksi')) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-zinc-900 rounded-lg p-6 border border-zinc-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm">Rata-rata per SPK</p>
                    <p class="text-2xl font-bold text-white">{{ $data->count() > 0 ? number_format($data->sum('jumlah_produksi') / $data->count(), 1) : 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-center py-4 border-t border-zinc-800">
        <p class="text-gray-600 text-xs">© 2023 Berliano Leather - Laporan Hasil Produksi</p>
    </div>
</div>
@endsection
