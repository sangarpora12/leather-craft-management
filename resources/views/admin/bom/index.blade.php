@extends('layouts.admin')

@section('page_title', 'Bill of Materials')

@section('content')
<div class="p-2">
    {{-- Header --}}
    {{-- Header --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 border-b border-white/10 pb-6 gap-4">
        <div>
            <p class="text-gray-400 text-sm">Resep bahan baku untuk setiap unit produk jadi.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <!-- Form membungkus Search dan Filter -->
            <form action="{{ route('admin.bom.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full">
                <!-- Kotak Pencarian -->
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Resep Produk..." class="bg-[#1A1A1A] border border-white/10 rounded-lg py-2.5 px-4 pl-10 text-sm focus:outline-none focus:border-orange-500 w-full transition text-white">
                    <span class="absolute left-3 top-3 text-gray-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                </div>

                <!-- Dropdown Filter Status -->
                <select name="status" onchange="this.form.submit()" class="bg-[#1A1A1A] border border-white/10 rounded-lg py-2.5 px-4 text-sm focus:outline-none focus:border-orange-500 transition text-white cursor-pointer w-full sm:w-auto outline-none">
                    <option value="">Semua Status</option>
                    <option value="sudah_ada" {{ request('status') == 'sudah_ada' ? 'selected' : '' }}>Sudah Ada Resep</option>
                    <option value="belum_ada" {{ request('status') == 'belum_ada' ? 'selected' : '' }}>Belum Ada Resep</option>
                </select>
            </form>

            <!-- Tombol Buat Produk Baru -->
            <a href="{{ route('admin.produk-jadi.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold px-4 py-2.5 rounded-lg transition whitespace-nowrap flex items-center justify-center shrink-0">
                Buat Produk Baru
            </a>
        </div>
    </div>

    {{-- Grid Produk --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($products as $product)
        <div class="bg-[#1C222D] rounded-xl border border-[#2A3140]/30 overflow-hidden group hover:border-[#FF6B00]/50 transition-all flex flex-col shadow-lg">
            <div class="h-40 bg-[#161B26] relative overflow-hidden flex items-center justify-center">
                @if($product->foto)
                    <img src="{{ asset('storage/' . $product->foto) }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                @else
                    <div class="text-gray-600 flex flex-col items-center">
                        <svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs">No Image</span>
                    </div>
                @endif
                
                <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10 font-mono">
                    {{ $product->kode_produk }}
                </span>

                @php
                    // Hitung jumlah bahan yang valid saja
                    $validBomsCount = $product->boms->filter(fn($b) => $b->bahanBaku)->count();
                @endphp

                @if($validBomsCount == 0)
                    <span class="absolute bottom-3 left-3 bg-red-500/80 backdrop-blur-md px-2 py-1 rounded text-[10px] text-white font-bold border border-red-500/20">
                        Belum Ada Resep
                    </span>
                @endif
            </div>
            
            <div class="p-5 flex-1 flex flex-col">
                 <h3 class="font-bold text-white text-lg leading-tight mb-1 truncate">
                     {{ ucwords(str_replace(['tali pingang', 'Tali pingang'], 'Tali Pinggang', $product->nama_produk)) }}
                 </h3>
                 <p class="text-xs text-[#94A3B8] mb-4 leading-relaxed">
                     {{ $product->deskripsi ? Str::limit($product->deskripsi, 50) : 'Tidak ada deskripsi' }}
                 </p>

                <div class="flex justify-between items-center bg-[#161B26] p-3 rounded-lg border border-[#2A3140]/20 mb-4 mt-auto">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                        <p class="text-sm font-bold {{ $validBomsCount > 0 ? 'text-blue-400' : 'text-gray-600' }}">
                            {{ $validBomsCount }} Item
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                    <p class="text-sm font-bold text-[#22C55E]">
                            Rp {{ number_format($product->boms->sum(function($b) {
                                return $b->jumlah_kebutuhan * ($b->bahanBaku->harga_satuan ?? 0);
                            }), 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    <a href="{{ route('admin.bom.edit', $product->id) }}" class="w-full py-2.5 bg-[#FF6B00] hover:bg-[#FF8A33] rounded-lg text-xs font-bold text-white flex items-center justify-center gap-2 transition shadow-md">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        {{ $validBomsCount == 0 ? 'Buat Resep' : 'Edit Resep' }}
                    </a>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('admin.produk-jadi.edit', $product->id) }}" class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z"></path></svg>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('admin.produk-jadi.copy', $product->id) }}" method="POST" class="w-full" onsubmit="return confirm('Anda yakin ingin menyalin produk ini?')">
                            @csrf
                            <button type="submit" class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>Salin</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full p-12 text-center text-gray-500 border border-dashed border-white/10 rounded-xl">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p>Belum ada produk yang terdaftar.</p>
            <a href="{{ route('admin.produk-jadi.create') }}" class="text-orange-500 hover:underline text-sm mt-2 inline-block">Tambah Produk Dulu</a>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection