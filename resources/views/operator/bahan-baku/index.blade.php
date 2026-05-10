@extends('layouts.operator_app')

@section('content')
<div class="w-full max-w-md mx-auto bg-[#0B0E14] text-white min-h-screen">
    <!-- Header -->
    @include('components.operator-header', ['title' => 'BAHAN BAKU'])

    <!-- Stats -->
    <div class="px-4 py-3">
        <div class="bg-[#1C222D] border border-white/5 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-500"></i>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase">Total SKU</span>
                        <div class="text-lg font-medium text-white">{{ $totalSKU }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500 uppercase">Kritis</span>
                    <div class="text-lg font-medium text-red-400">{{ $totalKritis }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="px-4 pb-4">
        <form method="GET" action="{{ route('operator.bahan.index') }}" class="space-y-3">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / kode bahan..." class="w-full bg-[#1C222D] border border-white/5 rounded-2xl px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none pr-10">
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <div class="flex overflow-x-auto gap-2 pb-1 scrollbar-hide">
                <select name="kategori" class="bg-[#1C222D] border border-white/5 rounded-lg px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none min-w-[100px]">
                    <option value="" {{ !request('kategori') ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($kategories as $k)
                        <option value="{{ $k->nama_kategori }}" {{ request('kategori') == $k->nama_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
                
                <select name="satuan" class="bg-[#1C222D] border border-white/5 rounded-lg px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none min-w-[90px]">
                    <option value="" {{ !request('satuan') ? 'selected' : '' }}>Semua Satuan</option>
                    @foreach($satuans as $s)
                        <option value="{{ $s->nama_satuan }}" {{ request('satuan') == $s->nama_satuan ? 'selected' : '' }}>{{ $s->nama_satuan }}</option>
                    @endforeach
                </select>
                
                <select name="sort" class="bg-[#1C222D] border border-white/5 rounded-lg px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none min-w-[120px]">
                    <option value="terbaru" {{ !request('sort') || request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="stok_terendah" {{ request('sort') == 'stok_terendah' ? 'selected' : '' }}>Stok Rendah</option>
                    <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama A-Z</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2 rounded-lg text-sm font-medium">
                Terapkan Filter
            </button>
        </form>
    </div>

    <!-- Bahan Baku List -->
    <div class="px-4 pb-24">
        @forelse($bahanBakus as $bahan)
        <div class="rounded-xl mb-3 overflow-hidden relative @if($bahan->stok <= $bahan->stok_minimum) bg-red-500/10 border border-red-500/40 @else bg-[#1C222D] border border-white/5 @endif">
            <!-- ICON EDIT POJOK KANAN ATAS -->
            <div class="absolute top-3 right-3 flex gap-1 z-10">
                <a href="{{ route('operator.bahan.edit', $bahan->id) }}" 
                   onclick="event.stopPropagation();"
                   class="text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg p-1.5 transition touch-manipulation">
                    <i class="fas fa-pencil-alt"></i>
                </a>
                @if(isset($bahan->jenis_bahan) && $bahan->jenis_bahan == 0)
                    {{-- Tombol hapus untuk bahan pelengkap --}}
                    <form action="{{ route('operator.bahan.destroy', $bahan->id) }}" method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus bahan pelengkap ini?');"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="event.stopPropagation();"
                                class="text-gray-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg p-1.5 transition touch-manipulation">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                @endif
            </div>
        
            <!-- SELURUH KARTU ADALAH LINK DETAIL -->
            <a href="{{ route('operator.bahan.show', $bahan->id) }}" class="block p-4 hover:bg-white/5 transition">
                <div class="flex items-center gap-3">
                    <!-- ✅ FOTO BAHAN KIRI -->
                    <div class="w-14 h-14 overflow-hidden rounded-xl bg-gray-700 border border-white/5 flex-shrink-0">
                        @if($bahan->foto)
                            <img src="{{ asset('storage/' . $bahan->foto) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-box text-gray-500 text-xl"></i>
                            </div>
                        @endif
                    </div>

                    <!-- ✅ INFORMASI TENGAH -->
                    <div class="flex-1 min-w-0 pr-10">
                        <div class="text-sm font-medium text-white mb-0.5 truncate">{{ $bahan->nama_material }}</div>
                        <div class="text-xs text-gray-400 mb-1">{{ $bahan->kode_material }}</div>
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 bg-gray-700 text-gray-300 text-[10px] rounded">{{ $bahan->kategori }}</span>
                            @if(isset($bahan->jenis_bahan) && $bahan->jenis_bahan == 0)
                                {{-- Bahan Pelengkap --}}
                                <span class="px-1.5 py-0.5 bg-yellow-600/20 text-yellow-400 text-[10px] rounded border border-yellow-600/30">
                                    Pelengkap
                                </span>
                            @else
                                {{-- Bahan Inventory --}}
                                @php
        $stokTampil = $bahan->total_stok ?? $bahan->stok;
        // Gunakan batas stok global seperti admin (fallback ke 5)
        $batasKritis = $batasStokGlobal ?? 5;
        // Format cerdas: 150.50 -> 150.5, 150.00 -> 150
        $formatStokIdx = function($val) {
            $val = (float) $val;
            if ($val == (int) $val) return number_format($val, 0);
            return rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.');
        };
    @endphp
    <span class="text-xs font-bold {{ $stokTampil <= $batasKritis ? 'text-red-400' : 'text-green-400' }}">
        {{ $formatStokIdx($stokTampil) }} <span class="font-normal text-gray-400">{{ strtolower($bahan->satuan) }}</span>
    </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="py-12 border-2 border-dashed border-white/5 rounded-xl text-center">
            <span class="text-sm text-gray-500">Belum ada bahan baku</span>
        </div>
        @endforelse

        @if($bahanBakus->hasPages())
            <div class="mt-4">
                {{ $bahanBakus->links() }}
            </div>
        @endif
    </div>
</div>

<style>
/* Sembunyikan scrollbar tapi tetap bisa scroll */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endsection