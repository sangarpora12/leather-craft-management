@extends('layouts.admin')

@section('title', 'Review Produksi Selesai: ' . $produksi->kode_spk)
@section('title_header', 'Review Produksi Selesai')

@section('content')

@if(session('notif_success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '{{ session('notif_success') }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });
});
</script>
@endif

@php
    $rsKat = is_array($produksi->produk_jadi->kategori) ? $produksi->produk_jadi->kategori : (json_decode($produksi->produk_jadi->kategori, true) ?: [$produksi->produk_jadi->kategori ?: '-']);
    $rsWar = is_array($produksi->produk_jadi->warna)    ? $produksi->produk_jadi->warna    : (json_decode($produksi->produk_jadi->warna, true)    ?: [$produksi->produk_jadi->warna    ?: '-']);
    $rsMot = is_array($produksi->produk_jadi->jenis_motif) ? $produksi->produk_jadi->jenis_motif : (json_decode($produksi->produk_jadi->jenis_motif, true) ?: [$produksi->produk_jadi->jenis_motif ?: '-']);
    $rsSketsas = $produksi->produk_jadi->sketsa ?? [];
    $rsFotoBukti = [];
    if (!empty($produksi->foto_bukti)) {
        $decoded = json_decode($produksi->foto_bukti, true);
        $rsFotoBukti = is_array($decoded) ? $decoded : [$produksi->foto_bukti];
    }
@endphp

<div class="max-w-6xl mx-auto animate-fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.produksi.index') }}" class="text-xs text-gray-500 hover:text-orange-400 flex items-center gap-1 mb-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar SPK
            </a>
            <h1 class="text-xl font-bold text-white">Review Hasil Produksi</h1>
            <p class="text-xs text-gray-500 mt-1">Produksi telah selesai dikerjakan oleh operator</p>
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

    <!-- Progress Realisasi -->
    <div class="glass-card rounded-2xl p-5 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-gray-300 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-emerald-400 text-xs"></i>
                </div>
                Realisasi Produksi
            </h3>
            <span class="text-lg font-bold text-emerald-300">100%</span>
        </div>
        <div class="w-full bg-white/10 rounded-full h-3 overflow-hidden">
            <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: 100%"></div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
            <span>Target: <strong class="text-white">{{ number_format($produksi->jumlah_produksi, 0, ',', '.') }} Pcs</strong></span>
            <span>Selesai: <strong class="text-emerald-300">{{ number_format($produksi->jumlah_selesai, 0, ',', '.') }} Pcs</strong></span>
        </div>
    </div>

    <!-- Foto Komparasi -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Foto Hasil Pengerjaan -->
        <div class="glass-card rounded-2xl p-5">
            <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                    <i class="fa-solid fa-camera text-emerald-400 text-xs"></i>
                </div>
                Foto Hasil Pengerjaan
            </h3>
            @if(count($rsFotoBukti) > 0)
                <div class="grid grid-cols-2 gap-3">
                    @foreach($rsFotoBukti as $fotoPath)
                    <div class="aspect-video rounded-xl overflow-hidden bg-[#111] border border-emerald-500/20">
                        <img src="{{ asset('storage/' . $fotoPath) }}" class="w-full h-full object-cover hover:scale-105 transition-transform" alt="Foto Bukti">
                    </div>
                    @endforeach
                </div>
            @else
                <div class="aspect-video rounded-xl overflow-hidden bg-[#111] border border-white/10 flex items-center justify-center text-gray-600">
                    <div class="text-center">
                        <i class="fa-solid fa-camera text-4xl mb-2 opacity-30"></i>
                        <p class="text-xs">Tidak ada foto bukti yang diupload</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Foto Referensi & Sketsa -->
        <div class="space-y-4">
            <!-- Foto Referensi -->
            @if($produksi->product?->foto)
            <div class="glass-card rounded-2xl p-5">
                <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-image text-blue-400 text-xs"></i>
                    </div>
                    Foto Referensi Produk
                </h3>
                <div class="aspect-video rounded-xl overflow-hidden bg-[#111] border border-white/10">
                    <img src="{{ asset('storage/' . $produksi->product->foto) }}" class="w-full h-full object-cover" alt="Foto Referensi">
                </div>
            </div>
            @endif

            <!-- Sketsa Produk -->
            @if(count($rsSketsas) > 0)
            <div class="glass-card rounded-2xl p-5">
                <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-orange-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-ruler-combined text-orange-400 text-xs"></i>
                    </div>
                    Sketsa Produk
                </h3>
                <div class="grid grid-cols-4 gap-2">
                    @foreach($rsSketsas as $skPath)
                    <div class="aspect-square rounded-lg overflow-hidden border border-white/10">
                        <img src="{{ asset('storage/' . $skPath) }}" class="w-full h-full object-cover hover:scale-110 transition-transform" alt="Sketsa">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Info Grid -->
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
                    <span class="text-xs text-gray-500">Kode Produk</span>
                    <span class="text-xs font-mono text-amber-400">{{ $produksi->product?->kode_produk ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Operator</span>
                    <span class="text-xs font-bold text-white">{{ $produksi->operator?->name ?? '-' }}</span>
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
                    <span class="text-sm font-bold text-white">{{ $produksi->product?->nama_produk ?? '-' }}</span>
                </div>

                <!-- Kategori Badge -->
                <div>
                    <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider block mb-1">Kategori</span>
                    <div class="flex flex-wrap gap-1">
                        @foreach($rsKat as $item)
                        <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full capitalize" style="background:rgba(59,130,246,0.2);color:#93c5fd;border:1px solid rgba(59,130,246,0.3)">{{ $item }}</span>
                        @endforeach
                    </div>
                </div>

                <!-- Warna & Motif Badge -->
                <div class="flex gap-3">
                    <div class="flex-1">
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider block mb-1">Warna</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach($rsWar as $item)
                            <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full capitalize" style="background:rgba(236,72,153,0.2);color:#f9a8d4;border:1px solid rgba(236,72,153,0.3)">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider block mb-1">Motif</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach($rsMot as $item)
                            <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded-full capitalize" style="background:rgba(168,85,247,0.2);color:#d8b4fe;border:1px solid rgba(168,85,247,0.3)">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Dimensi -->
                @if($produksi->product?->panjang || $produksi->product?->lebar || $produksi->product?->tinggi)
                <div class="pt-2 border-t border-white/5">
                    <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider block mb-1">📐 Dimensi</span>
                    <div class="flex gap-2">
                        @if($produksi->product->panjang)
                        <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-blue-500/10 text-blue-300">P {{ (int)$produksi->product->panjang }} cm</span>
                        @endif
                        @if($produksi->product->lebar)
                        <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-green-500/10 text-green-300">L {{ (int)$produksi->product->lebar }} cm</span>
                        @endif
                        @if($produksi->product->tinggi)
                        <span class="text-[10px] font-bold px-2 py-1 rounded-lg bg-red-500/10 text-red-300">T {{ (int)$produksi->product->tinggi }} cm</span>
                        @endif
                    </div>
                </div>
                @endif

                <div class="flex justify-between items-center pt-2 border-t border-white/5">
                    <span class="text-xs text-gray-500">Target Produksi</span>
                    <span class="text-xs font-bold text-white">{{ number_format($produksi->jumlah_produksi, 0, ',', '.') }} Pcs</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Realisasi</span>
                    <span class="text-xs font-bold text-emerald-300">{{ number_format($produksi->jumlah_selesai, 0, ',', '.') }} Pcs</span>
                </div>
            </div>
        </div>

        <!-- Catatan & Detail Pola -->
        <div class="space-y-4">
            <!-- Detail Pola -->
            @if($produksi->product?->detail_pola)
            <div class="glass-card rounded-2xl p-5">
                <h3 class="text-sm font-bold text-gray-300 mb-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-900/40 flex items-center justify-center">
                        <i class="fa-solid fa-scissors text-blue-400 text-xs"></i>
                    </div>
                    Detail Pola / Catatan Potong
                </h3>
                <p class="text-xs text-gray-300 leading-relaxed">{{ $produksi->product->detail_pola }}</p>
            </div>
            @endif

            <!-- Deskripsi Produk -->
            @if($produksi->product?->deskripsi)
            <div class="glass-card rounded-2xl p-5">
                <h3 class="text-sm font-bold text-gray-300 mb-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center">
                        <i class="fa-solid fa-book text-gray-400 text-xs"></i>
                    </div>
                    Deskripsi Produk
                </h3>
                <p class="text-xs text-gray-300 leading-relaxed">{{ $produksi->product->deskripsi }}</p>
            </div>
            @endif

            <!-- Catatan Admin -->
            <div class="glass-card rounded-2xl p-5">
                <h3 class="text-sm font-bold text-gray-300 mb-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-list text-amber-400 text-xs"></i>
                    </div>
                    Catatan
                </h3>
                @if($produksi->keterangan || $produksi->catatan)
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20">
                        <p class="text-xs text-amber-100/90 leading-relaxed">{{ $produksi->keterangan ?? $produksi->catatan }}</p>
                    </div>
                @else
                    <div class="p-3 rounded-xl bg-white/5 border border-white/10 text-center">
                        <p class="text-xs text-gray-500">Tidak ada catatan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- BOM / Bahan Baku -->
    @if($produksi->produk_jadi && $produksi->produk_jadi->boms->isNotEmpty())
    <div class="glass-card rounded-2xl p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-300 mb-4 flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-cyan-500/20 flex items-center justify-center">
                <i class="fa-solid fa-list-check text-cyan-400 text-xs"></i>
            </div>
            Bahan Baku yang Digunakan
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Material</th>
                        <th class="text-right py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Kebutuhan / Unit</th>
                        <th class="text-right py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Total Kebutuhan</th>
                        <th class="text-right py-3 px-3 text-gray-500 font-semibold uppercase tracking-wider">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produksi->produk_jadi->boms as $bom)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="py-3 px-3 text-white font-medium">{{ $bom->bahanBaku?->nama_material ?? '-' }}</td>
                        <td class="py-3 px-3 text-right text-gray-400">{{ $bom->jumlah_kebutuhan }}</td>
                        <td class="py-3 px-3 text-right text-emerald-300 font-bold">{{ number_format($bom->jumlah_kebutuhan * $produksi->jumlah_produksi, 2, ',', '.') }}</td>
                        <td class="py-3 px-3 text-right text-gray-400">{{ $bom->bahanBaku?->satuan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
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
}
</style>
@endsection
