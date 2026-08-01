@extends('layouts.consumer')

@section('title', 'Keranjang Belanja - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <div class="text-sm text-[#8B4513]/60 mb-4 flex items-center gap-2">
        <a href="{{ route('consumer.catalog') }}" class="hover:text-[#8B4513]">Katalog</a>
        <span>/</span>
        <span class="text-[#3E2723] font-medium">Keranjang Belanja</span>
    </div>

    <h1 class="text-3xl font-bold text-[#3E2723] mb-2 font-display">Keranjang Belanja</h1>
    <p class="text-[#8B4513]/50 text-sm mb-8">Periksa produk pilihan Anda sebelum checkout</p>

    @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Cart Header -->
                <div class="leather-card rounded-lg shadow-sm p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#8B4513] rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-[#3E2723] font-display text-lg">{{ $cartItems->count() }} Produk di Keranjang</h2>
                            <p class="text-xs text-[#8B4513]/50">{{ $cartItems->sum('quantity') }} total item</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('consumer.cart.clear') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 hover:underline transition flex items-center gap-1"
                            onclick="return confirm('Kosongkan keranjang?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Kosongkan
                        </button>
                    </form>
                </div>

                @foreach($cartItems as $item)
                    <div class="leather-card rounded-lg shadow-sm overflow-hidden transition hover:shadow-md">
                        <div class="flex flex-col sm:flex-row">
                            <!-- Product Image -->
                            <div class="w-full sm:w-32 h-32 bg-[#F5F0E1] flex-shrink-0 flex items-center justify-center">
                                @if($item->produkJadi->foto)
                                    <img src="{{ asset('storage/' . $item->produkJadi->foto) }}" alt="{{ $item->produkJadi->nama_produk }}" 
                                        class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12 text-[#8B4513]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @endif
                            </div>

                            <!-- Product Info & Controls -->
                            <div class="flex-1 p-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <!-- Info -->
                                <div class="flex-1 w-full">
                                    <a href="{{ route('consumer.catalog.show', $item->produkJadi->id) }}" class="font-semibold text-[#3E2723] mb-1 hover:text-[#8B4513] transition block font-display">
                                        {{ $item->produkJadi->nama_produk }}
                                    </a>
                                    <div class="flex items-center gap-3 mb-2">
                                        @if($item->produkJadi->stok > 0)
                                            <span class="flex items-center gap-1 text-xs text-green-600">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                                Stok tersedia ({{ $item->produkJadi->stok }})
                                            </span>
                                        @else
                                            <span class="flex items-center gap-1 text-xs text-red-600">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                                Stok habis
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-lg font-bold price-tag font-display">
                                        Rp {{ number_format($item->produkJadi->harga_jual, 0, ',', '.') }}
                                    </p>
                                </div>

                                <!-- Quantity & Subtotal -->
                                <div class="flex flex-row sm:flex-col items-center sm:items-end gap-3 w-full sm:w-auto">
                                    <!-- Quantity Selector with +/- -->
                                    <form method="POST" action="{{ route('consumer.cart.update', $item->id) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex items-center border border-[#8B4513]/20 rounded-lg overflow-hidden bg-white">
                                            <button type="button" onclick="this.parentNode.querySelector('input').stepDown(); this.parentNode.querySelector('input').dispatchEvent(new Event('change'));" 
                                                class="px-3 py-1.5 text-[#8B4513] hover:bg-[#8B4513] hover:text-white transition border-r border-[#8B4513]/10">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                            </button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->produkJadi->stok }}"
                                                class="w-12 text-center py-1.5 border-0 outline-none text-sm font-medium text-[#3E2723] bg-transparent"
                                                onchange="this.form.submit()">
                                            <button type="button" onclick="this.parentNode.querySelector('input').stepUp(); this.parentNode.querySelector('input').dispatchEvent(new Event('change'));" 
                                                class="px-3 py-1.5 text-[#8B4513] hover:bg-[#8B4513] hover:text-white transition border-l border-[#8B4513]/10">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Subtotal & Remove -->
                                    <div class="flex items-center gap-3">
                                        <div class="text-right">
                                            <p class="text-xs text-[#8B4513]/50">Subtotal</p>
                                            <p class="font-bold text-[#3E2723] text-sm">Rp {{ number_format($item->produkJadi->harga_jual * $item->quantity, 0, ',', '.') }}</p>
                                        </div>
                                        <form method="POST" action="{{ route('consumer.cart.remove', $item->id) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition" 
                                                onclick="return confirm('Hapus produk dari keranjang?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Continue Shopping -->
                <a href="{{ route('consumer.catalog') }}" class="inline-flex items-center gap-2 text-[#8B4513] hover:text-[#5C3317] transition font-medium text-sm mt-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Lanjut Belanja
                </a>
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="leather-card rounded-lg shadow-sm p-6 sticky top-24">
                    <h3 class="text-xl font-bold text-[#3E2723] mb-4 font-display flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        Ringkasan Belanja
                    </h3>
                    
                    <div class="space-y-3 mb-4 pb-4 border-b border-[#8B4513]/10">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-[#8B4513]/70 line-clamp-1 flex-1 mr-2">{{ $item->produkJadi->nama_produk }} <span class="text-[#8B4513]/40">x{{ $item->quantity }}</span></span>
                                <span class="font-medium text-[#3E2723] whitespace-nowrap">Rp {{ number_format($item->produkJadi->harga_jual * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 mb-4 pb-4 border-b border-[#8B4513]/10">
                        <div class="flex justify-between text-sm text-[#8B4513]/70">
                            <span>Total Item</span>
                            <span>{{ $cartItems->sum('quantity') }} item</span>
                        </div>
                        <div class="flex justify-between text-sm text-[#8B4513]/70">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-[#8B4513]/70">
                            <span>Ongkos Kirim</span>
                            <span class="text-green-600 font-medium">Gratis</span>
                        </div>
                    </div>

                    <div class="flex justify-between text-xl font-bold text-[#3E2723] mb-4">
                        <span class="font-display">Total</span>
                        <span class="price-tag font-display">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <a href="{{ route('consumer.checkout') }}" class="block w-full btn-primary text-white text-center py-3 rounded-lg transition font-medium mb-3 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Checkout Sekarang
                    </a>

                    <!-- Trust Badges -->
                    <div class="space-y-2 mt-4 pt-4 border-t border-[#8B4513]/10">
                        <div class="flex items-center gap-2 text-xs text-[#8B4513]/60">
                            <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            <span>100% Kulit Asli Bergaransi</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-[#8B4513]/60">
                            <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Transfer Bank • QRIS • COD</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-[#8B4513]/60">
                            <svg class="w-4 h-4 text-[#C5A059]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            <span>Pengiriman Cepat & Aman</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16 leather-card rounded-lg shadow-sm">
            <div class="w-24 h-24 bg-[#F5F0E1] rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-[#8B4513]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#3E2723] mb-2 font-display">Keranjang Belanja Kosong</h3>
            <p class="text-[#8B4513]/50 mb-6 max-w-sm mx-auto">Belum ada produk kulit premium di keranjang Anda. Yuk jelajahi koleksi kulit asli pilihan terbaik kami!</p>
            <a href="{{ route('consumer.catalog') }}" class="inline-flex items-center gap-2 btn-primary text-white px-6 py-3 rounded-lg transition font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Mulai Belanja
            </a>
        </div>
    @endif
</div>
@endsection