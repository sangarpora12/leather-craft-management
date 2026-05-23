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
                    <p class="text-xl font-black text-emerald-400">{{ (float)$varianWarna->sum('total_stok_warna') }} <span class="text-[10px] text-gray-600 font-bold uppercase">{{ $bahan->satuan }}</span></p>
                </div>
                <div class="text-center border-l border-white/5">
                    <p class="text-[9px] text-gray-500 uppercase font-black tracking-tighter mb-1">Batas Aman</p>
                    <p class="text-xl font-black text-blue-400">> {{ (int)($batasStokGlobal ?? $bahan->stok_minimum ?? 5) }}</p>
                </div>
            </div>
        </div>

        <!-- Varian Warna (Logika dari Admin) -->
        @if(isset($varianWarna) && $varianWarna->count() > 0)
        <div>
            <div class="flex items-center justify-between px-1 mb-4">
                <h3 class="text-[11px] font-black text-gray-500 uppercase tracking-[0.2em]">Varian Material</h3>
                <span class="text-[10px] font-bold text-blue-500">{{ $varianWarna->count() }} Pilihan</span>
            </div>
            <div class="bg-[#1C222D] p-4 rounded-[1.5rem] border border-white/5 shadow-xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[10px] text-gray-500 uppercase tracking-widest">
                            <tr>
                                <th class="px-4 py-3">Warna</th>
                                <th class="px-4 py-3 text-right">Stok</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($varianWarna as $v)
                            @php
                                // Logika isLow disesuaikan dari admin
                                $isLow = $v->total_stok_warna <= ($bahan->stok_minimum ?? 5);
                            @endphp
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-5 h-5 rounded-full inline-block flex-shrink-0 border border-white/10" style="background-color: {{ $v->color->kode_hex ?? (App\Helpers\WarnaHelper::detectColor($v->warna ?? '')) }};"></span>
                                        <span class="text-white font-medium text-sm">{{ $v->warna ?? 'Tidak Ada Warna' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-bold {{ $isLow ? 'text-red-400' : 'text-orange-400' }}">
                                    {{ (float) $v->total_stok_warna }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('operator.bahan.edit-varian', $v->id) }}" class="inline-block bg-gray-700/50 hover:bg-blue-600 text-white p-2 rounded-lg transition duration-200" title="Edit Varian">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Ringkasan Per Harga (Logika dari Admin) -->
        @if(isset($ringkasanPerHarga) && $ringkasanPerHarga->count() > 0)
        <div class="bg-[#1C222D] rounded-[1.5rem] p-5 border border-white/5 shadow-xl">
            <h3 class="text-[11px] font-black text-gray-500 uppercase tracking-[0.2em] flex items-center gap-2 mb-6">
                <i class="fa-solid fa-chart-simple text-emerald-400"></i> Ringkasan Per Harga
            </h3>
            <div class="space-y-3">
                @foreach($ringkasanPerHarga as $r)
                @if(round((float) $r->total_sisa, 2) > 0)
                <div class="flex items-center justify-between p-3 rounded-2xl {{ $loop->first ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-[#0B0E14]/50 border border-white/5' }}">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-white font-bold">Rp {{ number_format($r->harga_beli, 0, ',', '.') }}</span>
                            @if($loop->first)
                                <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[9px] font-bold rounded border border-emerald-500/30 uppercase">Terakhir</span>
                            @endif
                        </div>
                        <div>
                            <span class="px-2 py-1 bg-white/10 text-gray-300 text-[10px] font-medium rounded border border-white/10 uppercase tracking-tight">
                                {{ $r->warna ?? 'Tidak Ada Warna' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-right">
                        <div>
                            <p class="text-[8px] text-gray-500 uppercase font-black tracking-tighter">Masuk</p>
                            <p class="text-sm font-bold text-gray-400">{{ (float) $r->total_masuk }}</p>
                        </div>
                        <div class="w-px h-6 bg-white/10"></div>
                        <div>
                            <p class="text-[8px] text-gray-500 uppercase font-black tracking-tighter">Sisa</p>
                            <p class="text-sm font-black text-orange-400">{{ (float) $r->total_sisa }}</p>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif


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