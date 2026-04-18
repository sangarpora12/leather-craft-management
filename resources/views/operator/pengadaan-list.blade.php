@extends('layouts.operator_app')

@section('title', 'Daftar Belanja Material')

@section('content')
<div class="max-w-md mx-auto px-5 pt-6 pb-20">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-black italic uppercase tracking-tighter text-white">BELANJA</h1>
            <p class="text-[10px] text-yellow-500 font-bold uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-truck-fast"></i>
                List Pengadaan Material
            </p>
        </div>
        <a href="{{ route('operator.dashboard') }}" class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center border border-white/10">
            <i class="fas fa-arrow-left text-gray-400 text-sm"></i>
        </a>
    </div>

    <div class="bg-[#1A1C20] rounded-[2rem] p-5 mb-8 border border-white/5 flex justify-between items-center shadow-2xl">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500 text-xs">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div>
                <p class="text-[9px] text-gray-500 uppercase font-black">Total Material</p>
                {{-- Cek apakah variabel ada sebelum dihitung --}}
                <p class="text-sm font-bold text-white">{{ isset($belanjas) ? count($belanjas) : 0 }} Tugas</p>
            </div>
        </div>
        <span class="text-[8px] bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 px-3 py-1 rounded-full font-black uppercase tracking-widest">Pending</span>
    </div>

    <div class="space-y-5">
        @forelse($belanjas as $item)
        <div class="bg-[#1A1C20] rounded-[2.5rem] p-6 border border-white/5 relative overflow-hidden transition active:scale-95 shadow-lg">
            
            @if(isset($item->prioritas) && $item->prioritas == 'tinggi')
                <div class="absolute top-0 right-0 bg-red-600 text-[8px] font-black text-white px-4 py-1 rounded-bl-2xl uppercase tracking-widest">
                    Urgent
                </div>
            @endif

            <div class="flex items-start gap-4 mb-6">
                <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center border border-white/5 text-gray-400">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-white uppercase leading-tight">{{ $item->nama_material ?? 'Material Baru' }}</h3>
                    <p class="text-[10px] text-yellow-500 font-bold mt-1 uppercase tracking-widest">Target: {{ $item->jumlah ?? 0 }} Unit</p>
                </div>
            </div>

            <form action="{{ route('operator.belanja.selesai', $item->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/20 transition">
                    <i class="fas fa-check-circle mr-2"></i>
                    Tandai Sudah Dibeli
                </button>
            </form>
        </div>
        @empty
        <div class="py-24 text-center border-2 border-dashed border-white/5 rounded-[3rem] opacity-20">
            <i class="fas fa-check-double text-4xl mb-4 text-emerald-500"></i>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-white">Semua Belanja Beres</p>
        </div>
        @endforelse
    </div>
</div>
@endsection