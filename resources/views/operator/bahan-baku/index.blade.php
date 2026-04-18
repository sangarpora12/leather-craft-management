@extends('layouts.operator_app')

@section('content')
<div class="w-full max-w-md mx-auto bg-gray-900 text-white font-normal" style="font-family: 'Poppins', sans-serif;">
    <!-- Header -->
    @include('components.operator-header', ['title' => 'BAHAN BAKU'])

    <!-- Stats -->
    <div class="px-5 py-4">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-500"></i>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-normal uppercase">Total SKU</span>
                        <div class="text-lg font-normal text-white">{{ $totalSKU }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500 font-normal uppercase">Kritis</span>
                    <div class="text-lg font-normal text-red-400">{{ $totalKritis }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="px-5 pb-4">
        <form method="GET" action="{{ route('operator.bahan.index') }}" class="mb-4">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari bahan baku..." class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none pr-10">
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Bahan Baku List -->
    <div class="px-5 pb-4">
        @forelse($bahanBakus as $bahan)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 mb-4">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <div class="text-base font-normal text-white mb-1">{{ $bahan->nama_material }}</div>
                    <div class="text-sm text-gray-500 font-normal mb-2">{{ $bahan->kode_material }}</div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded font-normal">{{ $bahan->kategori }}</span>
                        <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded font-normal">{{ $bahan->satuan }}</span>
                    </div>
                    <div class="text-xs text-gray-400 font-normal">
                        Harga: Rp {{ number_format($bahan->harga_satuan, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-normal {{ $bahan->stok <= $bahan->stok_minimum ? 'text-red-400' : 'text-green-400' }}">
                        {{ $bahan->stok }}
                    </div>
                    <div class="text-xs text-gray-500 font-normal uppercase">Stok</div>
                    @if($bahan->stok <= $bahan->stok_minimum)
                    <div class="text-xs text-red-400 font-normal mt-1">Kritis</div>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('operator.bahan.edit', $bahan->id) }}" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white py-2 rounded-lg text-sm font-normal uppercase text-center">
                    Edit
                </a>
                <a href="{{ route('operator.bahan.tambah') }}" class="flex-1 bg-green-600 hover:bg-green-500 text-white py-2 rounded-lg text-sm font-normal uppercase text-center">
                    Tambah Stok
                </a>
            </div>
        </div>
        @empty
        <div class="py-12 border-2 border-dashed border-gray-700 rounded-xl text-center">
            <span class="text-sm text-gray-500 font-normal">Belum ada bahan baku</span>
        </div>
        @endforelse

        @if($bahanBakus->hasPages())
            <div class="mt-6">
                {{ $bahanBakus->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
