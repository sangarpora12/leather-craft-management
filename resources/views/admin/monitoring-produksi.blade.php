@extends('layouts.admin')

@section('title_header', 'Monitoring Proses Produksi')

@section('content')
<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-white">Pantau Status Produksi</h2>
            <p class="text-sm text-gray-500">Monitoring setiap SPK di lantai produksi</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.location.reload()"
                class="px-4 py-2 bg-[#1a1a1a] hover:bg-[#252525] text-gray-300 rounded-lg text-xs font-bold border border-white/10 transition flex items-center gap-2">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <a href="{{ route('admin.produksi.index') }}"
                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-lg">
                <i class="fas fa-plus"></i> Buat SPK
            </a>
        </div>
    </div>

    {{-- ================= STAT CARD ================= --}}
    @php
        $totalSpkAktif = ($spkBaru?->count() ?? 0) + ($spkProses?->count() ?? 0);
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- TOTAL SPK --}}
        <div class="bg-[#0a0a0a] p-6 rounded-2xl border border-white/5">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">
                Total SPK Aktif
            </p>
            <h3 class="text-4xl font-black text-white">{{ $totalSpkAktif }}</h3>
        </div>

        {{-- PRODUK DALAM PROSES --}}
        <div class="bg-[#0a0a0a] p-6 rounded-2xl border border-white/5">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4">
                Produk Dalam Proses
            </p>
            <div class="space-y-2">
                @forelse($spkProses as $spk)
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-300">
                            {{ $spk->produk_jadi->nama_produk ?? '-' }}
                        </span>
                        <span class="font-bold text-white">
                            {{ $spk->jumlah_produksi }} pcs
                        </span>
                    </div>
                @empty
                    <div class="text-gray-600 text-xs italic">Tidak ada proses</div>
                @endforelse
            </div>
        </div>

        {{-- KAPASITAS --}}
        <div class="bg-[#0a0a0a] p-6 rounded-2xl border border-white/5">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">
                Status Produksi
            </p>
            <span class="inline-block px-3 py-1 bg-green-900/30 text-green-400 text-xs rounded">
                Sistem Berjalan Normal
            </span>
        </div>
    </div>

    {{-- ================= KANBAN ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-[500px]">

        {{-- ===== PENDING ===== --}}
        <div>
            <div class="flex justify-between mb-3 border-b border-yellow-500/50 pb-2">
                <h3 class="text-sm font-bold text-white">🟡 Pending</h3>
                <span class="text-[10px] text-gray-400">{{ $spkBaru->count() }} SPK</span>
            </div>

            <div class="space-y-3">
                @forelse($spkBaru as $spk)
                    <div class="bg-[#0a0a0a] p-4 rounded-xl border-l-4 border-yellow-500">
                        <div class="text-[10px] text-gray-500 font-mono mb-1">
                            {{ $spk->kode_spk }}
                        </div>
                        <div class="text-white font-bold text-sm">
                            {{ $spk->produk_jadi->nama_produk ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-400">
                            Qty: {{ $spk->jumlah_produksi }}
                        </div>
                    </div>
                @empty
                    <div class="text-gray-600 text-xs italic text-center py-6">Kosong</div>
                @endforelse
            </div>
        </div>

        {{-- ===== PROSES ===== --}}
        <div>
            <div class="flex justify-between mb-3 border-b border-blue-500/50 pb-2">
                <h3 class="text-sm font-bold text-white">🔵 Proses</h3>
                <span class="text-[10px] text-gray-400">{{ $spkProses->count() }} SPK</span>
            </div>

            <div class="space-y-3">
                @forelse($spkProses as $spk)
                    <div class="bg-[#0a0a0a] p-4 rounded-xl border-l-4 border-blue-500">
                        <div class="text-[10px] text-gray-500 font-mono mb-1">
                            {{ $spk->kode_spk }}
                        </div>
                        <div class="text-white font-bold text-sm">
                            {{ $spk->produk_jadi->nama_produk ?? '-' }}
                        </div>
                        <div class="text-xs text-gray-400">
                            Operator: {{ $spk->operator->name ?? '-' }}
                        </div>
                    </div>
                @empty
                    <div class="text-gray-600 text-xs italic text-center py-6">Tidak ada proses</div>
                @endforelse
            </div>
        </div>

        {{-- ===== SELESAI ===== --}}
        <div>
            <div class="flex justify-between mb-3 border-b border-emerald-500/50 pb-2">
                <h3 class="text-sm font-bold text-white">🟢 Selesai</h3>
                <span class="text-[10px] text-gray-400">{{ $spkSelesai->count() }} SPK</span>
            </div>

            <div class="space-y-3">
                @forelse($spkSelesai as $spk)
                    <div class="bg-[#0a0a0a] p-4 rounded-xl border-l-4 border-emerald-500 opacity-70">
                        <div class="text-[10px] text-gray-500 font-mono mb-1">
                            {{ $spk->kode_spk }}
                        </div>
                        <div class="text-gray-300 line-through">
                            {{ $spk->produk_jadi->nama_produk ?? '-' }}
                        </div>
                        <div class="text-[10px] text-gray-500">
                            {{ $spk->updated_at->diffForHumans() }}
                        </div>
                    </div>
                @empty
                    <div class="text-gray-600 text-xs italic text-center py-6">Belum ada</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
