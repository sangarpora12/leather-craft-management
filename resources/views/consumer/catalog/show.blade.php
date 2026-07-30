@extends('layouts.consumer')

@section('title', $product->nama_produk . ' - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6">
            <!-- Product Image -->
            <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
                @if($product->foto)
                    <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nama_produk }}" 
                        class="w-full h-full object-cover rounded-lg">
                @else
                    <div class="text-gray-400 text-center p-8">
                        <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-lg">Tidak ada foto produk</p>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->nama_produk }}</h1>
                
                <div class="mb-4">
                    <span class="text-3xl font-bold text-amber-700">
                        Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                    </span>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center">
                        <span class="text-gray-600 w-32">Kategori:</span>
                        <span class="font-medium">
                            @php
                                $kategori = is_string($product->kategori) ? json_decode($product->kategori, true) : $product->kategori;
                                echo is_array($kategori) ? implode(', ', $kategori) : ($product->kategori ?? 'N/A');
                            @endphp
                        </span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-600 w-32">Stok Tersedia:</span>
                        <span class="font-medium {{ $product->stok > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $product->stok }} item
                        </span>
                    </div>
                    @if($product->deskripsi)
                        <div>
                            <span class="text-gray-600 block mb-1">Deskripsi:</span>
                            <p class="text-gray-700">{{ $product->deskripsi }}</p>
                        </div>
                    @endif
                </div>

                @if($product->stok > 0)
                    @auth
                        <form method="POST" action="{{ route('consumer.cart.add', $product->id) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stok }}"
                                    class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                            <button type="submit" class="w-full bg-amber-700 text-white py-3 px-6 rounded-lg hover:bg-amber-800 transition font-medium">
                                Tambah ke Keranjang
                            </button>
                        </form>
                    @else
                        <div class="space-y-3">
                            <p class="text-sm text-gray-600 bg-amber-50 border border-amber-200 rounded-lg p-3">
                                <strong>Wajib daftar/login</strong> untuk membeli produk ini.
                            </p>
                            <a href="{{ route('consumer.login') }}" class="block w-full bg-amber-700 text-white text-center py-3 px-6 rounded-lg hover:bg-amber-800 transition font-medium">
                                Login untuk Beli
                            </a>
                            <a href="{{ route('consumer.register') }}" class="block w-full bg-gray-100 text-gray-700 text-center py-2 px-6 rounded-lg hover:bg-gray-200 transition font-medium text-sm">
                                Belum punya akun? Daftar
                            </a>
                        </div>
                    @endauth
                @else
                    <button disabled class="w-full bg-gray-300 text-gray-500 py-3 px-6 rounded-lg cursor-not-allowed">
                        Stok Habis
                    </button>
                @endif

                <div class="mt-6">
                    <a href="{{ route('consumer.catalog') }}" class="text-amber-700 hover:text-amber-800 font-medium">
                        &larr; Kembali ke Katalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

