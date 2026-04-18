@extends('layouts.admin')

@section('title_header', 'Monitoring Produksi')

@section('content')
<div class="space-y-6">
    {{-- Header Monitoring --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-white">Monitoring Proses</h2>
            <p class="text-xs text-gray-500 mt-1">Status produksi yang sedang berjalan saat ini.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">Sistem Aktif</span>
        </div>
    </div>

    {{-- Daftar Proses --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($activeProcesses as $proc)
        <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl hover:border-red-500/30 transition-all group relative overflow-hidden">
            {{-- Status Badge --}}
            <div class="absolute top-4 right-4">
                <span class="px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest {{ $proc->status == 'PROSES' ? 'bg-blue-500/10 text-blue-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                    {{ $proc->status }}
                </span>
            </div>

            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-gray-400 group-hover:bg-red-600/10 group-hover:text-red-500 transition">
                    <i class="fas fa-microchip text-xl"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-base leading-tight">{{ $proc->produkJadi->nama ?? 'Produk' }}</h3>
                    <p class="text-gray-600 text-[10px] font-mono mt-0.5">{{ $proc->no_spk }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-end">
                    <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Progress</span>
                    <span class="text-sm font-black text-white">
                        {{ $proc->jumlah_target > 0 ? round(($proc->jumlah_selesai / $proc->jumlah_target) * 100) : 0 }}%
                    </span>
                </div>
                
                {{-- Progress Bar --}}
                <div class="w-full bg-white/5 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-600 to-red-400 h-full rounded-full transition-all duration-1000" 
                         style="width: {{ $proc->jumlah_target > 0 ? ($proc->jumlah_selesai / $proc->jumlah_target) * 100 : 0 }}%">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                    <div>
                        <p class="text-[9px] text-gray-600 uppercase font-bold">Target</p>
                        <p class="text-sm text-gray-300 font-bold">{{ $proc->jumlah_target }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] text-gray-600 uppercase font-bold">Selesai</p>
                        <p class="text-sm text-emerald-500 font-bold">{{ $proc->jumlah_selesai }}</p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 flex flex-col items-center justify-center bg-[#0a0a0a] border border-dashed border-white/10 rounded-3xl opacity-50">
            <i class="fas fa-tv text-4xl mb-4 text-gray-700"></i>
            <p class="text-gray-500 text-sm">Belum ada proses produksi yang aktif.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection