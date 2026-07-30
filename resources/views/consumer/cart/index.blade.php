@extends('layouts.consumer')

@section('title', 'Keranjang Belanja - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Keranjang Belanja</h1>

    @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-lg shadow-sm p-4 flex items-center gap-4">
                        <!-- Product Image -->
                        <div class="w-24 h-24 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center">
                            @if($item->produkJadi->foto)
                                <img src="{{ asset('storage/' . $item->produkJadi->foto) }}" alt="{{ $item->produkJadi->nama_produk }}" 
                                    class="w-full h-full object-cover rounded-lg">
                            @else
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $item->produkJadi->nama_produk }}</h3>
                            <p class="text-sm text-gray-500 mb-2">Stok tersedia: {{ $item->produkJadi->stok }}</p>
                            <p class="text-lg font-bold text-amber-700">
                                Rp {{ number_format($item->produkJadi->harga_jual, 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('consumer.cart.update', $item->id) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->produkJadi->stok }}"
                                    class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                                <button type="submit" class="bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200 transition text-sm">
                                    Update
                                </button>
                            </form>

                            <form method="POST" action="{{ route('consumer.cart.remove', $item->id) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-2" onclick="return confirm('Hapus produk dari keranjang?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Ringkasan</h3>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Total Item</span>
                            <span>{{ $cartItems->sum('quantity') }} item</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('consumer.checkout') }}" class="block w-full bg-amber-700 text-white text-center py-3 rounded-lg hover:bg-amber-800 transition font-medium mb-2">
                        Checkout
                    </a>

                    <form method="POST" action="{{ route('consumer.cart.clear') }}" class="inline w-full">
                        @csrf
                        <button type="submit" class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition text-sm"
                            onclick="return confirm('Kosongkan keranjang?')">
                            Kosongkan Keranjang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow-sm">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-gray-500 text-lg mb-4">Keranjang belanja Anda kosong.</p>
            <a href="{{ route('consumer.catalog') }}" class="inline-block bg-amber-700 text-white px-6 py-2 rounded-lg hover:bg-amber-800 transition">
                Belanja Sekarang
            </a>
        </div>
    @endif
</div>
@endsection

