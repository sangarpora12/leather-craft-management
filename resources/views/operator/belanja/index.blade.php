@extends('layouts.operator_app')

@section('content')
<div class="w-full max-w-md mx-auto bg-[#0B0E14] text-white font-normal" style="font-family: 'Poppins', sans-serif;">
    <!-- Header -->
    @include('components.operator-header', ['title' => 'Daftar Belanja Bahan'])

    <!-- Filter (jika diperlukan, bisa ditambahkan kembali) -->

    <!-- Stats -->
    <div class="px-5 py-3">
        <div class="bg-[#1C222D] border border-white/5 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-normal uppercase">Total Jenis Bahan</span>
                        <div class="text-lg font-normal text-white">{{ $groupedPengadaan->count() }} Jenis</div>
                    </div>
                </div>
                <span class="text-xs bg-green-500/20 text-green-500 border border-green-500/30 px-3 py-1 rounded-full font-normal uppercase">Pending</span>
            </div>
        </div>
    </div>

    <!-- Daftar Belanja Ter-grup -->
    <div class="px-5 pb-4">
        @forelse($groupedPengadaan as $namaMaterial => $items)
        @php
            $firstItem = $items->first();
            $totalJumlah = $items->sum('jumlah');
            $totalReceived = $items->sum('received');
            $totalSisa = $totalJumlah - $totalReceived;
            $deadlineTerdekat = $items->min('tanggal_dibutuhkan');
            $isUrgent = $items->contains('prioritas', 'URGENT');
            $isOverdue = \Carbon\Carbon::parse($deadlineTerdekat)->isPast();
        @endphp
        <div classa="rounded-xl p-5 mb-4 {{ $isOverdue ? 'bg-red-500/10 border border-red-500/40' : 'bg-[#1C222D] border border-white/5' }}">
            <div class="flex gap-4 items-start mb-4">
                <!-- FOTO BAHAN -->
                <div class="flex-shrink-0">
                    @if($firstItem->bahan && $firstItem->bahan->foto)
                        <img src="{{ asset('storage/' . $firstItem->bahan->foto) }}" class="w-14 h-14 rounded-xl object-cover border border-white/5">
                    @else
                        <div class="w-14 h-14 rounded-xl bg-[#1C222D] flex items-center justify-center border border-white/5">
                            <i class="fa-solid fa-box text-gray-500 text-xl"></i>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="text-base font-normal text-white">{{ $namaMaterial }}</div>
                    <div class="text-sm text-gray-500 font-normal">
                        {{ $items->count() }} permintaan berbeda
                    </div>
                    @if($isUrgent)
                    <div class="mt-2">
                        <span class="bg-red-500/20 text-red-400 text-xs font-bold px-2 py-1 rounded-full">Ada Prioritas Urgent</span>
                    </div>
                    @endif
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs text-gray-500 font-normal">Total Beli</span>
                    <div class="text-lg font-normal text-blue-400">{{ (float)$totalJumlah }} <span class="text-xs">{{ $firstItem->bahan->satuan ?? 'pcs' }}</span></div>
                    @if($totalReceived > 0)
                    <div class="text-xs mt-1">
                        <span class="text-green-400">Sudah: {{ (float)$totalReceived }}</span> | 
                        <span class="text-orange-400 font-bold">Sisa: {{ (float)$totalSisa }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 {{ $isOverdue ? 'text-red-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-xs font-normal {{ $isOverdue ? 'text-red-400 font-bold' : 'text-gray-400' }}">Deadline Terdekat: {{ \Carbon\Carbon::parse($deadlineTerdekat)->format('d M Y') }}</span>
                </div>
                <a href="{{ route('operator.belanja.show', ['nama_material' => urlencode($namaMaterial)]) }}" class="text-xs px-3 py-2 rounded-lg font-normal bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500/30">
                    Lihat Rincian
                </a>
            </div>
        </div>
        @empty
        <div class="py-12 border-2 border-dashed border-white/5 rounded-xl text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm text-gray-500 font-normal">Keranjang belanja Anda kosong. Semua tugas sudah selesai!</span>
        </div>
        @endforelse
    </div>
</div>
@endsection