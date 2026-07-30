@extends('layouts.consumer')

@section('title', 'Katalog Produk - Berliano Leather')

@section('content')
    <!-- Category Filter -->
    <div class="bg-white rounded-lg shadow-sm p-3 mb-4 overflow-x-auto">
        <div class="flex gap-2 min-w-max">
            <a href="{{ route('consumer.catalog') }}" 
               class="px-4 py-1.5 rounded-full text-sm font-medium {{ !request('kategori') ? 'bg-[#ee4d2d] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            @foreach($kategoris as $kategori)
                @php
                    $kategoriArray = is_string($kategori) ? json_decode($kategori, true) : $kategori;
                    $kategoriName = is_array($kategoriArray) ? ($kategoriArray[0] ?? $kategori) : $kategori;
                @endphp
                <a href="{{ route('consumer.catalog', ['kategori' => $kategoriName]) }}" 
                   class="px-4 py-1.5 rounded-full text-sm font-medium whitespace-nowrap {{ request('kategori') == $kategoriName ? 'bg-[#ee4d2d] text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $kategoriName }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Sort & Filter Bar -->
    <div class="bg-white rounded-lg shadow-sm p-3 mb-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div class="text-sm text-gray-500">
            Menampilkan <span class="font-medium text-gray-700">{{ $products->total() }}</span> produk
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Urutkan:</span>
            <select onchange="window.location.href=this.value" class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:border-[#ee4d2d]">
                <option value="{{ route('consumer.catalog', ['sort' => 'terbaru']) }}" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="{{ route('consumer.catalog', ['sort' => 'termurah']) }}" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Termurah</option>
                <option value="{{ route('consumer.catalog', ['sort' => 'termahal']) }}" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Termahal</option>
                <option value="{{ route('consumer.catalog', ['sort' => 'nama']) }}" {{ request('sort') == 'nama' ? 'selected' : '' }}>Nama A-Z</option>
            </select>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            @foreach($products as $product)
                <div class="product-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md cursor-pointer" onclick="window.location.href='{{ route('consumer.catalog.show', $product->id) }}'">
                    <!-- Product Image -->
                    <div class="relative aspect-square bg-gray-50">
                        @if($product->foto)
                            <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nama_produk }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        @if($product->stok <= 0)
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                <span class="text-white font-bold text-lg bg-red-500 px-4 py-1 rounded">HABIS</span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-2.5">
                        <h3 class="text-sm font-medium text-gray-800 line-clamp-2 mb-1 leading-tight">{{ $product->nama_produk }}</h3>
                        <p class="price-tag font-bold text-base">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                        
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-400">{{ $product->stok }} terjual</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $products->withQueryString()->links('pagination::tailwind') }}
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-lg shadow-sm">
            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-gray-400 text-lg">Tidak ada produk yang ditemukan</p>
            <a href="{{ route('consumer.catalog') }}" class="inline-block mt-4 btn-primary text-white px-6 py-2 rounded-lg text-sm">Lihat Semua Produk</a>
        </div>
    @endif
@endsection