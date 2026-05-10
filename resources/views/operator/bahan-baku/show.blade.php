@extends('layouts.operator_app')

@section('content')
<div class="bg-[#0B0E14] min-h-screen pb-24 text-white font-sans">
    <!-- Header -->
    <header class="h-16 bg-[#111111]/80 backdrop-blur-md border-b border-white/5 flex items-center px-4 sticky top-0 z-50 justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('operator.bahan.index') }}" class="w-10 h-10 flex items-center justify-center bg-white/5 rounded-full text-gray-400 hover:text-white transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-sm font-black uppercase tracking-widest text-white">Detail Material</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('operator.bahan.edit', $bahan->id) }}" class="bg-blue-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-tighter shadow-lg shadow-blue-600/20">
                Edit Utama
            </a>
            <button type="button" onclick="if(confirm('Yakin ingin menghapus bahan ini?')){document.getElementById('delete-form-{{ $bahan->id }}').submit();}" class="bg-red-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-tighter shadow-lg shadow-red-600/20">
                Hapus
            </button>
        </div>
    </header>

    <div class="px-4 py-6 space-y-6">
        
        <!-- Hero Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-[#1C222D] to-[#111827] rounded-[2rem] border border-white/5 shadow-2xl p-6">
            <div class="flex flex-col items-center text-center relative z-10">
                <div class="w-32 h-32 bg-[#2D3748] rounded-3xl overflow-hidden border-4 border-white/5 shadow-2xl mb-4">
                    @php
                        $fotoUtama = $bahan->foto;
                        if (!$fotoUtama && isset($varianWarna)) {
                            $varianDenganFoto = $varianWarna->firstWhere('foto', '!=', null);
                            if ($varianDenganFoto) $fotoUtama = $varianDenganFoto->foto;
                        }
                    @endphp
                    @if($fotoUtama)
                        <img src="{{ asset('storage/' . $fotoUtama) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-600">
                            <i class="fa-solid fa-box-open text-4xl"></i>
                        </div>
                    @endif
                </div>
                <h2 class="text-2xl font-black text-white leading-tight mb-1">{{ $bahan->nama_material }}</h2>
                <div class="flex items-center gap-2 mb-4">
                    <span class="px-3 py-1 bg-orange-500/10 text-orange-400 text-[10px] font-black rounded-full border border-orange-500/20 tracking-widest uppercase">{{ $bahan->kode_material }}</span>
                    <span class="px-3 py-1 bg-white/5 text-gray-400 text-[10px] font-bold rounded-full border border-white/5 uppercase">{{ $bahan->kategori }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5 mt-2">
                <div class="text-center">
                    <p class="text-[9px] text-gray-500 uppercase font-black tracking-tighter mb-1">Total Stok</p>
                    <p class="text-xl font-black text-emerald-400">{{ (float)$varianWarna->sum('stok') }} <span class="text-[10px] text-gray-600 font-bold uppercase">{{ $bahan->satuan }}</span></p>
                </div>
                <div class="text-center border-l border-white/5">
                    <p class="text-[9px] text-gray-500 uppercase font-black tracking-tighter mb-1">Batas Aman</p>
                    <p class="text-xl font-black text-blue-400">> {{ (int)($batasStokGlobal ?? $bahan->stok_minimum ?? 5) }}</p>
                </div>
            </div>
        </div>

        <!-- Varian Warna (Hanya Teks & Indikator Merah) -->
        <div>
            <div class="flex items-center justify-between px-1 mb-4">
                <h3 class="text-[11px] font-black text-gray-500 uppercase tracking-[0.2em]">Varian Material</h3>
                <span class="text-[10px] font-bold text-blue-500">{{ $varianWarna->count() }} Pilihan</span>
            </div>
            <div class="flex gap-3 overflow-x-auto pb-4 scrollbar-hide -mx-4 px-4">
                @foreach($varianWarna as $v)
                @php $isLow = $v->stok <= ($batasStokGlobal ?? $bahan->stok_minimum ?? 5); @endphp
                <div class="min-w-[140px] bg-[#1C222D] p-5 rounded-[1.5rem] border {{ $isLow ? 'border-red-500/50 shadow-[0_0_15px_rgba(239,68,68,0.1)]' : 'border-white/5' }} flex flex-col items-center text-center transition-all">
                    <p class="text-xs font-black {{ $isLow ? 'text-red-400' : 'text-white' }} truncate w-full mb-1">{{ $v->warna ?? 'Polos' }}</p>
                    <p class="text-[10px] font-bold {{ $isLow ? 'text-red-500/70' : 'text-gray-500' }} uppercase mb-4">{{ (float)$v->stok }} {{ $bahan->satuan }}</p>
                    <a href="{{ route('operator.bahan.edit-varian', $v->id) }}" class="w-full py-2 {{ $isLow ? 'bg-red-500 text-white' : 'bg-white/5 text-gray-300' }} rounded-xl text-[9px] font-black uppercase transition-all shadow-sm">
                        Edit Warna
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Alur FIFO dengan Filter Tanggal -->
        <div class="bg-[#1C222D] rounded-[1.5rem] p-5 border border-white/5 shadow-xl">
            <div class="flex flex-col gap-4 mb-6">
                <h3 class="text-[11px] font-black text-gray-500 uppercase tracking-[0.2em] flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-purple-500"></i> Alur Masuk (FIFO)
                </h3>
                <!-- Simple Date Filter -->
                <div class="flex gap-2">
                    <div class="flex-1 bg-[#0B0E14] rounded-xl border border-white/5 p-2">
                        <label class="block text-[8px] font-black text-gray-500 uppercase ml-1 mb-1">Mulai</label>
                        <input type="date" id="filterStart" class="bg-transparent text-white text-xs w-full outline-none px-1">
                    </div>
                    <div class="flex-1 bg-[#0B0E14] rounded-xl border border-white/5 p-2">
                        <label class="block text-[8px] font-black text-gray-500 uppercase ml-1 mb-1">Selesai</label>
                        <input type="date" id="filterEnd" class="bg-transparent text-white text-xs w-full outline-none px-1">
                    </div>
                </div>
            </div>

            <div class="space-y-4" id="fifoContainer">
    @foreach($semuaLayer as $layer)
    <div class="fifo-item flex items-start gap-4 relative" data-date="{{ $layer->tanggal_masuk }}">
        <!-- Garis Indikator -->
        <div class="flex flex-col items-center">
            <div class="w-1 h-12 bg-white/5 rounded-full relative">
                <div class="absolute top-0 left-0 w-full h-3/4 bg-purple-500 rounded-full shadow-[0_0_10px_rgba(168,85,247,0.5)]"></div>
            </div>
        </div>

        <div class="flex-1 bg-[#0B0E14]/50 p-3 rounded-2xl border border-white/5">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-[10px] font-black text-white">{{ \Carbon\Carbon::parse($layer->tanggal_masuk)->format('d M Y') }}</p>
                    <p class="text-[9px] text-gray-500 font-bold uppercase mt-0.5">{{ $layer->material->warna ?? '-' }}</p>
                </div>
                <p class="text-xs font-black text-emerald-400">Rp{{ number_format($layer->harga_beli, 0, ',', '.') }}</p>
            </div>
            
            <!-- Grid Angka Stok: MASUK & SISA -->
            <div class="grid grid-cols-2 gap-2 border-t border-white/5 pt-2">
                <div>
                    <p class="text-[8px] text-gray-600 uppercase font-black tracking-tighter">Stok Masuk</p>
                    <p class="text-xs font-bold text-gray-400">{{ (float)$layer->qty_masuk }} <span class="text-[9px] font-normal lowercase">{{ $bahan->satuan }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-[8px] text-gray-600 uppercase font-black tracking-tighter">Sisa Sekarang</p>
                    <p class="text-xs font-black {{ $layer->qty_sisa < 1 ? 'text-red-500' : 'text-orange-400' }}">
                        {{ (float)$layer->qty_sisa }} <span class="text-[9px] font-normal lowercase">{{ $bahan->satuan }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    // Logika Filter Tanggal Sederhana (JS Client Side)
    const startInput = document.getElementById('filterStart');
    const endInput = document.getElementById('filterEnd');
    const items = document.querySelectorAll('.fifo-item');

    function filterFIFO() {
        const start = startInput.value ? new Date(startInput.value) : null;
        const end = endInput.value ? new Date(endInput.value) : null;

        items.forEach(item => {
            const itemDate = new Date(item.getAttribute('data-date'));
            let visible = true;
            if (start && itemDate < start) visible = false;
            if (end && itemDate > end) visible = false;
            item.style.display = visible ? 'flex' : 'none';
        });
    }

    startInput.addEventListener('change', filterFIFO);
    endInput.addEventListener('change', filterFIFO);
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); opacity: 0.5; }
</style>

<!-- Form Hapus -->
<form id="delete-form-{{ $bahan->id }}" action="{{ route('operator.bahan.destroy', $bahan->id) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection