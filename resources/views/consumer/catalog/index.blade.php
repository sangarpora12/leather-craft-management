@extends('layouts.consumer')

@section('title', 'Katalog Produk - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Katalog Produk Kulit</h1>
        <p class="text-gray-600">Temukan produk kulit berkualitas dari Berliano Leather</p>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('consumer.catalog') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
            </div>
            <div>
                <select name="kategori" class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kategori)
                        @php
                            $kategoriArray = is_string($kategori) ? json_decode($kategori, true) : $kategori;
                            $kategoriName = is_array($kategoriArray) ? ($kategoriArray[0] ?? $kategori) : $kategori;
                        @endphp
                        <option value="{{ $kategoriName }}" {{ request('kategori') == $kategoriName ? 'selected' : '' }}>
                            {{ $kategoriName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-amber-700 text-white px-6 py-2 rounded-lg hover:bg-amber-800 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
                    <!-- Product Image -->
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        @if($product->foto)
                            <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nama_produk }}" 
                                class="w-full h-full object-cover">
                        @else
                            <div class="text-gray-400 text-center p-4">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Tidak ada foto</p>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 mb-1">{{ $product->nama_produk }}</h3>
                        <p class="text-sm text-gray-500 mb-2">Stok: {{ $product->stok }}</p>
                        <p class="text-lg font-bold text-amber-700 mb-3">
                            Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                        </p>

                        <div class="flex gap-2">
                            <a href="{{ route('consumer.catalog.show', $product->id) }}" 
                                class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-sm">
                                Detail
                            </a>
                            @auth
                                <form method="POST" action="{{ route('consumer.cart.add', $product->id) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" 
                                        class="w-full bg-amber-700 text-white px-4 py-2 rounded-lg hover:bg-amber-800 transition text-sm"
                                        {{ $product->stok <= 0 ? 'disabled' : '' }}>
                                        {{ $product->stok > 0 ? 'Tambah' : 'Habis' }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('consumer.login') }}" 
                                    class="flex-1 text-center bg-amber-700 text-white px-4 py-2 rounded-lg hover:bg-amber-800 transition text-sm">
                                    {{ $product->stok > 0 ? 'Login' : 'Habis' }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-gray-500 text-lg">Tidak ada produk yang tersedia.</p>
        </div>
    @endif
</div>
@endsection

