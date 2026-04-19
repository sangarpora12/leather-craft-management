@extends('layouts.operator_app')

@section('title', 'Detail Bahan Baku')

@section('content')
<div class="w-full max-w-md mx-auto bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="py-4 sticky top-0 z-40 flex items-center gap-4 bg-gray-900/95 backdrop-blur border-b border-gray-700 px-5">
        <a href="{{ route('operator.bahan.index') }}" class="w-8 h-8 flex items-center justify-center bg-gray-800 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-white">Detail Bahan</h1>
            <p class="text-xs text-gray-400 font-mono">{{ $bahan->kode_material }}</p>
        </div>
        
        <a href="{{ route('operator.bahan.edit', $bahan->id) }}" class="ml-auto bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition">
            <i class="fas fa-pencil-alt mr-1.5"></i>Edit
        </a>
    </header>

    <main class="pb-32 pt-6 px-5">
        <!-- ✅ FOTO MASTER BAHAN -->
        <div class="mb-6">
            <div class="mx-auto w-48 h-48 overflow-hidden rounded-2xl border-4 border-gray-700 shadow-2xl bg-gray-700">
                @if($bahan->foto)
                    <img src="{{ asset('storage/' . $bahan->foto) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fa-solid fa-box text-gray-500 text-5xl"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- ✅ INFORMASI UTAMA -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-4">
            <h3 class="text-xl font-bold text-white mb-1">{{ $bahan->nama_material }}</h3>
            <div class="text-xs text-gray-400 mb-4">{{ $bahan->kode_material }}</div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Kategori</div>
                    <div class="bg-gray-700 rounded-lg px-3 py-2 text-sm">{{ $bahan->kategori }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Satuan</div>
                    <div class="bg-gray-700 rounded-lg px-3 py-2 text-sm">{{ $bahan->satuan }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Harga Beli</div>
                    <div class="bg-gray-700 rounded-lg px-3 py-2 text-sm font-mono">Rp {{ number_format($bahan->harga_satuan, 0, ',', '.') }}</div>
                </div>
                <div class="space-y-1">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Stok Saat Ini</div>
                    <div class="bg-gray-700 rounded-lg px-3 py-2 text-sm font-bold {{ $bahan->stok <= $bahan->stok_minimum ? 'text-red-400' : 'text-green-400' }}">
                        {{ (int)$bahan->stok }} {{ strtolower($bahan->satuan) }}
                    </div>
                </div>
            </div>
            
            @if($bahan->supplier)
            <div class="mt-4 pt-4 border-t border-gray-700">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1 mb-1">Supplier</div>
                <div class="text-sm text-gray-300">{{ $bahan->supplier ?? '-' }}</div>
            </div>
            @endif
        </div>

        <!-- ✅ RIWAYAT FIFO STOK -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
            <h4 class="font-bold text-white mb-3">Riwayat Stok</h4>
            
            @if(isset($riwayatStok) && $riwayatStok->count())
            <div class="space-y-2">
                @foreach($riwayatStok as $riwayat)
                @php
                    $isMasuk = str_contains(strtolower($riwayat->jenis_transaksi), 'tambah') || str_contains(strtolower($riwayat->jenis_transaksi), 'masuk') || str_contains(strtolower($riwayat->jenis_transaksi), 'restock');
                @endphp
                <div class="flex items-center justify-between bg-gray-700/50 rounded-lg px-3 py-2.5">
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium">{{ $riwayat->jenis_transaksi }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ date('d M Y H:i', strtotime($riwayat->created_at)) }}</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-bold {{ $isMasuk ? 'text-green-400' : 'text-red-400' }}">
                            {{ $isMasuk ? '+' : '-' }}{{ (int)$riwayat->jumlah }}
                        </div>
                        @if(isset($riwayat->harga_beli) && $riwayat->harga_beli > 0)
                        <div class="text-xs text-gray-500">Rp {{ number_format($riwayat->harga_beli, 0, ',', '.') }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-6 text-gray-500 text-sm">
                Belum ada riwayat pergerakan stok
            </div>
            @endif
        </div>
    </main>
</div>
@endsection