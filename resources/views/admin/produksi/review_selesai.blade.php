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

<div class="container-fluid">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.produksi.index') }}" class="p-2 hover:bg-white/5 rounded-xl transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h4 class="text-xl font-bold bg-gradient-to-r from-green-400 to-emerald-500 bg-clip-text text-transparent">✅ Produksi Selesai</h4>
            </div>
            <p class="text-sm text-gray-400">
                Kode SPK: <strong class="text-amber-400 font-mono">{{ $produksi->kode_spk }}</strong>
            </p>
        </div>
    </div>

    {{-- STATUS BADGE --}}
    <div class="mb-5">
        <div class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-green-500/10 border border-green-500/30">
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
            <span class="font-bold text-lg text-green-400">SELESAI</span>
        </div>
    </div>

    <div class="row g-4">

        {{-- KIRI --}}
        <div class="col-md-6">

            {{-- FOTO BUKTI PENGERJAAN --}}
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-5 shadow-2xl shadow-black/50 mb-4">
                <h5 class="font-bold text-white mb-4">📸 Foto Hasil Pengerjaan</h5>
                @if($produksi->foto_bukti)
                <div class="grid grid-cols-2 gap-3">
                    @foreach(json_decode($produksi->foto_bukti) as $foto)
                    <div class="rounded-xl overflow-hidden border border-green-500/30">
                        <img src="{{ asset('storage/' . $foto) }}" alt="Foto Bukti" class="w-full h-40 object-cover">
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4 text-gray-500">
                    <i class="fa-solid fa-camera-slash text-2xl mb-2 opacity-50"></i>
                    <p class="text-sm">Tidak ada foto bukti yang diupload</p>
                </div>
                @endif
            </div>

            {{-- FOTO REFERENSI & SKETSA --}}
            @if($produksi->product?->foto)
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-5 shadow-2xl shadow-black/50">
                <h5 class="font-bold text-white mb-4">📷 Foto Referensi Produk</h5>
                <div class="rounded-xl overflow-hidden border border-white/20">
                    <img src="{{ asset('storage/' . $produksi->product->foto) }}" alt="Foto Referensi" class="w-full h-auto">
                </div>
            </div>
            @endif

        </div>

        {{-- KANAN --}}
        <div class="col-md-6">

            {{-- INFORMASI UTAMA --}}
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-5 shadow-2xl shadow-black/50 mb-4">
                <h5 class="font-bold text-white mb-4">📋 Detail Produksi</h5>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <div class="text-gray-400">Nama Produk</div>
                        <div class="font-bold text-white">{{ $produksi->product?->nama_produk ?? '-' }}</div>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-400">Operator</div>
                        <div class="font-bold text-white">{{ $produksi->operator?->name ?? '-' }}</div>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-400">Total Produksi</div>
                        <div class="font-bold text-emerald-400">{{ (float)$produksi->jumlah_produksi }} Pcs</div>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-400">Selesai Pada</div>
                        <div class="font-bold text-white">{{ $produksi->tanggal_selesai?->format('d F Y H:i') }}</div>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-400">Tanggal Target</div>
                        <div class="font-bold text-white">{{ $produksi->tanggal_target?->format('d F Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- DIMENSI --}}
            @if($produksi->product?->panjang || $produksi->product?->lebar || $produksi->product?->tinggi)
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-5 shadow-2xl shadow-black/50 mb-4">
                <h5 class="font-bold text-white mb-4">📏 Dimensi Produk</h5>
                <div class="grid grid-cols-3 gap-2">
                    @if($produksi->product?->panjang)
                    <div class="bg-cyan-500/10 border border-cyan-500/30 rounded-xl p-3 text-center">
                        <div class="text-xs text-cyan-400 mb-1">PANJANG</div>
                        <div class="font-bold text-white">{{ (float)$produksi->product->panjang }} cm</div>
                    </div>
                    @endif
                    @if($produksi->product?->lebar)
                    <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-3 text-center">
                        <div class="text-xs text-emerald-400 mb-1">LEBAR</div>
                        <div class="font-bold text-white">{{ (float)$produksi->product->lebar }} cm</div>
                    </div>
                    @endif
                    @if($produksi->product?->tinggi)
                    <div class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-3 text-center">
                        <div class="text-xs text-rose-400 mb-1">TINGGI</div>
                        <div class="font-bold text-white">{{ (float)$produksi->product->tinggi }} cm</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- CATATAN --}}
            @if($produksi->catatan)
            <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4 border-l-4 border-amber-500">
                <div class="text-sm font-bold text-amber-400 mb-2">Catatan</div>
                <p class="text-amber-300">{{ $produksi->catatan }}</p>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection