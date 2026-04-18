@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.produksi.index') }}" class="text-xs text-gray-500 hover:text-orange-400 flex items-center gap-1 mb-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar SPK
            </a>
            <h1 class="text-xl font-bold text-white">Review Hasil Produksi</h1>
            <p class="text-xs text-gray-500 mt-1">Detail lengkap hasil produksi & analisis finansial</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                ✓ SELESAI
            </span>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-[#20232A] border border-[#2D3139] text-gray-300 hover:text-white hover:border-gray-500 text-xs font-bold flex items-center gap-2 transition">
                <i class="fa-solid fa-print"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Foto Komparasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Foto Produk (Katalog) -->
        <div class="glass-card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-image text-blue-400 text-xs"></i>
                </div>
                Foto Produk (Katalog)
            </h3>
            <div class="aspect-video rounded-xl overflow-hidden bg-[#111] border border-white/10">
                @if(!empty($produksi->product->foto))
                    <img src="{{ asset('storage/' . $produksi->product->foto) }}" class="w-full h-full object-cover" alt="Produk Katalog">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-600">
                        <div class="text-center">
                            <i class="fa-solid fa-image text-4xl mb-2 opacity-30"></i>
                            <p class="text-xs">Tidak ada foto katalog</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Foto Bukti (Hasil Operator) -->
        <div class="glass-card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-camera text-emerald-400 text-xs"></i>
                </div>
                Foto Bukti (Hasil Produksi)
            </h3>
            <div class="aspect-video rounded-xl overflow-hidden bg-[#111] border border-white/10">
                @if(!empty($produksi->foto_bukti))
                    <img src="{{ asset('storage/' . $produksi->foto_bukti) }}" class="w-full h-full object-cover" alt="Foto Bukti Produksi">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-600">
                        <div class="text-center">
                            <i class="fa-solid fa-camera text-4xl mb-2 opacity-30"></i>
                            <p class="text-xs">Tidak ada foto bukti</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Info SPK & Produk -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Detail SPK -->
        <div class="glass-card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-orange-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-file-text text-orange-400 text-xs"></i>
                </div>
                Informasi SPK
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Kode SPK</span>
                    <span class="text-xs font-mono font-bold text-orange-300">{{ $produksi->kode_spk }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Operator</span>
                    <span class="text-xs font-bold text-white">{{ $produksi->operator->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Target Selesai</span>
                    <span class="text-xs text-white">{{ $produksi->tanggal_target ? \Carbon\Carbon::parse($produksi->tanggal_target)->format('d M Y') : '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Tanggal Selesai</span>
                    <span class="text-xs text-emerald-300">{{ $produksi->tanggal_selesai ? \Carbon\Carbon::parse($produksi->tanggal_selesai)->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Detail Produk -->
        <div class="glass-card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-box text-purple-400 text-xs"></i>
                </div>
                Detail Produk
            </h3>
            <div class="space-y-3">
                <div>
                    <span class="text-xs text-gray-500 block mb-1">Nama Produk</span>
                    <span class="text-sm font-bold text-white">{{ $produksi->product->nama_produk ?? '-' }}</span>
                </div>
                <div class="flex gap-2">
                    @if($produksi->product->warna)
                        <span class="text-xs px-2 py-1 rounded bg-white/5 border border-white/10 text-gray-400">{{ $produksi->product->warna }}</span>
                    @endif
                    @if($produksi->product->jenis_motif)
                        <span class="text-xs px-2 py-1 rounded bg-white/5 border border-white/10 text-gray-400">{{ $produksi->product->jenis_motif }}</span>
                    @endif
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Target Produksi</span>
                    <span class="text-xs font-bold text-white">{{ number_format($produksi->jumlah_produksi, 0, ',', '.') }} Pcs</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Realisasi</span>
                    <span class="text-xs font-bold text-emerald-300">{{ number_format($produksi->jumlah_selesai, 0, ',', '.') }} Pcs</span>
                </div>
            </div>
        </div>

        <!-- Catatan Admin -->
        <div class="glass-card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-list text-amber-400 text-xs"></i>
                </div>
                Catatan Admin
            </h3>
            @if($produksi->keterangan || $produksi->catatan)
                <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20">
                    <p class="text-xs text-amber-100/90 leading-relaxed">
                        {{ $produksi->keterangan ?? $produksi->catatan }}
                    </p>
                </div>
            @else
                <div class="p-3 rounded-xl bg-white/5 border border-white/10 text-center">
                    <p class="text-xs text-gray-500">Tidak ada catatan</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Realisasi Bahan -->
    <div class="glass-card rounded-2xl p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 flex items-center justify-center">
                <i class="fa-solid fa-list-check text-cyan-400 text-xs"></i>
            </div>
            Realisasi Penggunaan Bahan
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Material</th>
                        <th class="text-right py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Kebutuhan/Unit</th>
                        <th class="text-right py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Total Dipakai</th>
                        <th class="text-right py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Harga Satuan</th>
                        <th class="text-right py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($realisasiBahan as $bahan)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="py-3 px-3 text-white font-medium">{{ $bahan['nama_material'] }}</td>
                        <td class="py-3 px-3 text-right text-gray-400">{{ number_format($bahan['kebutuhan_per_unit'], 2, ',', '.') }} {{ $bahan['satuan'] }}</td>
                        <td class="py-3 px-3 text-right text-emerald-300 font-bold">{{ number_format($bahan['total_dipakai'], 2, ',', '.') }} {{ $bahan['satuan'] }}</td>
                        <td class="py-3 px-3 text-right text-gray-400">Rp {{ number_format($bahan['harga_satuan'], 0, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right text-orange-300 font-bold">Rp {{ number_format($bahan['total_biaya'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t border-white/10">
                        <td colspan="4" class="py-3 px-3 text-right text-gray-500 font-bold uppercase">Total HPP</td>
                        <td class="py-3 px-3 text-right text-orange-300 font-bold text-sm">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Analisis Finansial -->
    <div class="glass-card rounded-2xl p-5">
        <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                <i class="fa-solid fa-chart-line text-green-400 text-xs"></i>
            </div>
            Analisis Finansial
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Harga Jual -->
            <div class="rounded-xl p-4 bg-blue-500/10 border border-blue-500/20">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-blue-300/70 uppercase font-bold">Harga Jual / Unit</span>
                    <i class="fa-solid fa-tag text-blue-400 text-xs"></i>
                </div>
                <div class="text-lg font-bold text-blue-300">Rp {{ number_format($hargaJual, 0, ',', '.') }}</div>
                <div class="text-[10px] text-blue-300/50 mt-1">× {{ number_format($produksi->jumlah_produksi, 0, ',', '.') }} unit</div>
            </div>

            <!-- Total Penjualan -->
            <div class="rounded-xl p-4 bg-purple-500/10 border border-purple-500/20">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-purple-300/70 uppercase font-bold">Total Penjualan</span>
                    <i class="fa-solid fa-coins text-purple-400 text-xs"></i>
                </div>
                <div class="text-lg font-bold text-purple-300">Rp {{ number_format($totalJual, 0, ',', '.') }}</div>
                <div class="text-[10px] text-purple-300/50 mt-1">Revenue kotor</div>
            </div>

            <!-- Margin -->
            <div class="rounded-xl p-4 {{ $margin > 0 ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-red-500/10 border border-red-500/20' }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs {{ $margin > 0 ? 'text-emerald-300/70' : 'text-red-300/70' }} uppercase font-bold">Margin Keuntungan</span>
                    <i class="fa-solid fa-trending-{{ $margin > 0 ? 'up' : 'down' }} {{ $margin > 0 ? 'text-emerald-400' : 'text-red-400' }} text-xs"></i>
                </div>
                <div class="text-lg font-bold {{ $margin > 0 ? 'text-emerald-300' : 'text-red-300' }}">
                    {{ $margin > 0 ? '+' : '' }}Rp {{ number_format($margin, 0, ',', '.') }}
                </div>
                <div class="text-[10px] {{ $margin > 0 ? 'text-emerald-300/50' : 'text-red-300/50' }} mt-1">
                    {{ number_format($marginPercent, 1, ',', '.') }}% dari penjualan
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="mt-4 p-4 rounded-xl bg-white/5 border border-white/10">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-xs text-gray-500 block mb-1">Total Biaya Produksi (HPP)</span>
                    <span class="text-sm font-bold text-orange-300">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block mb-1">Laba per Unit</span>
                    <span class="text-sm font-bold {{ ($hargaJual * $produksi->jumlah_produksi - $totalHpp) > 0 ? 'text-emerald-300' : 'text-red-300' }}">
                        Rp {{ number_format($margin / max($produksi->jumlah_produksi, 1), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.glass-card {
    background: rgba(30, 30, 30, 0.6);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

@media print {
    body { background: white !important; color: black !important; }
    .glass-card {
        background: white !important;
        border: 1px solid #ddd !important;
        backdrop-filter: none !important;
    }
    .text-gray-300, .text-gray-400, .text-gray-500 { color: #333 !important; }
    .text-white { color: black !important; }
    .bg-\[\\#111316\], .bg-\[\\#0D1117\] { background: white !important; }
}
</style>
@endsection