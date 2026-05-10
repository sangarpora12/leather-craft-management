@extends('layouts.operator_app')

@section('content')
<div class="w-full max-w-md mx-auto bg-[#0B0E14] text-white font-normal" style="font-family: 'Poppins', sans-serif;">
    
    <!-- Header dengan Tombol Kembali -->
    @include('components.operator-header', ['title' => 'Rincian Belanja', 'url' => route('operator.belanja.index')])

    <div class="p-5">
        <!-- Info Utama Bahan -->
        <div class="bg-[#1C222D] border border-white/5 rounded-xl p-5 mb-5">
            <div class="flex gap-4 items-center">
                @if($firstItem->bahan && $firstItem->bahan->foto)
                    <img src="{{ asset('storage/' . $firstItem->bahan->foto) }}" alt="{{ $nama_material_decoded }}" class="w-16 h-16 rounded-lg object-cover border border-white/5">
                @else
                    <div class="w-16 h-16 rounded-lg bg-[#0B0E14] flex items-center justify-center border border-dashed border-white/10">
                        <i class="fa-solid fa-image text-gray-600 text-2xl"></i>
                    </div>
                @endif
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-white">{{ $nama_material_decoded }}</h2>
                    <p class="text-sm text-gray-400">
                        Total: <span class="font-bold text-blue-400">{{ (float)$totalJumlah }}</span>
                        @if($totalReceived > 0)
                            | Sudah dibeli: <span class="font-bold text-green-400">{{ (float)$totalReceived }}</span>
                            | Sisa: <span class="font-bold text-orange-400">{{ (float)$totalSisa }}</span>
                        @endif
                        {{ $firstItem->bahan->satuan }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Daftar Rincian Permintaan -->
        <div class="space-y-3">
            <h3 class="text-sm text-gray-500 font-normal uppercase px-2">Daftar Permintaan ({{ $items->count() }})</h3>
            @foreach($items as $item)
            <div class="bg-[#1C222D] border border-white/5 rounded-xl p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-base font-medium text-white">
                            <span class="font-bold text-blue-400">{{ (float)$item->jumlah }}</span> {{ $item->bahan->satuan }}
                            @if($item->bahan->warna)
                                - Warna: <span class="font-semibold">{{ $item->bahan->warna }}</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fa-regular fa-calendar-alt mr-1"></i>
                            Butuh sebelum: {{ \Carbon\Carbon::parse($item->tanggal_dibutuhkan)->isoFormat('dddd, D MMMM YYYY') }}
                        </p>
                    </div>
                    @if($item->prioritas == 'URGENT')
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-500/20 text-red-300 flex-shrink-0">
                            URGENT
                        </span>
                    @endif
                </div>
                @if($item->catatan)
                <div class="mt-3 pt-3 border-t border-white/10">
                    <p class="text-xs text-gray-500 font-normal uppercase">Catatan dari Admin</p>
                    <p class="text-sm italic text-gray-300 mt-1">"{{ $item->catatan }}"</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection