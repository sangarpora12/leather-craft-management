@extends('layouts.admin')

@section('page_title', 'Monitoring Proses')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <p class="text-xs text-gray-500">Pantau proses produksi yang sedang berjalan secara real-time.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[10px] font-bold text-emerald-500 uppercase">Sistem Aktif</span>
        </div>
    </div>

    {{-- Grid Monitoring --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($activeProcesses as $proc)
        <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl hover:border-red-500/30 transition-all group">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-white/5 rounded-xl group-hover:bg-red-600/10 transition">
                    <i class="fas fa-microchip text-gray-400 group-hover:text-red-500"></i>
                </div>
                <span class="px-2 py-1 rounded text-[9px] font-bold uppercase tracking-wider {{ $proc->status == 'PROSES' ? 'bg-blue-500/10 text-blue-400' : 'bg-yellow-500/10 text-yellow-400' }}">
                    {{ $proc->status }}
                </span>
            </div>
            
            <div class="mb-6">
                <h3 class="text-white font-bold text-lg leading-tight">{{ $proc->produkJadi->nama ?? 'Produk' }}</h3>
                <p class="text-gray-500 text-[10px] mt-1 font-mono uppercase">{{ $proc->no_spk }}</p>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-end">
                    <div class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">Progress</div>
                    <div class="text-sm font-black text-white">
                        {{ $proc->jumlah_target > 0 ? round(($proc->jumlah_selesai / $proc->jumlah_target) * 100) : 0 }}%
                    </div>
                </div>
                
                <div class="w-full bg-white/5 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-600 to-orange-500 h-full rounded-full transition-all duration-1000" 
                         style="width: {{ $proc->jumlah_target > 0 ? ($proc->jumlah_selesai / $proc->jumlah_target) * 100 : 0 }}%">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-white/5">
                    <div>
                        <p class="text-[9px] text-gray-600 uppercase font-bold">Target</p>
                        <p class="text-sm text-gray-300 font-bold">{{ $proc->jumlah_target }} <span class="text-[10px] font-normal">Pcs</span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] text-gray-600 uppercase font-bold">Selesai</p>
                        <p class="text-sm text-emerald-500 font-bold">{{ $proc->jumlah_selesai }} <span class="text-[10px] font-normal">Pcs</span></p>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 flex flex-col items-center justify-center bg-[#0a0a0a] border border-dashed border-white/10 rounded-3xl">
            <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-tv text-2xl text-gray-700"></i>
            </div>
            <p class="text-gray-500 font-medium">Belum ada aktivitas produksi hari ini.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection