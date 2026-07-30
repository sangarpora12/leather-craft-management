@extends('layouts.consumer')

@section('title', $product->nama_produk . ' - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <div class="text-sm text-gray-500 mb-4 flex items-center gap-2">
        <a href="{{ route('consumer.catalog') }}" class="hover:text-[#ee4d2d]">Katalog</a>
        <span>/</span>
        <span class="text-gray-700">{{ $product->nama_produk }}</span>
    </div>

    <!-- Product Detail Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
            <!-- Product Image -->
            <div class="bg-gray-50 p-8 flex items-center justify-center">
                @if($product->foto)
                    <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nama_produk }}" class="w-full max-w-md h-auto object-cover rounded-lg">
                @else
                    <div class="w-full max-w-md aspect-square flex items-center justify-center text-gray-300">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="p-6 md:p-8 flex flex-col">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $product->nama_produk }}</h1>
                
                @if($product->sku)
                    <p class="text-sm text-gray-400 mb-4">SKU: {{ $product->sku }}</p>
                @endif

                <!-- Price -->
                <div class="bg-[#fff3f0] rounded-lg p-4 mb-4">
                    <p class="price-tag text-3xl font-bold">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                </div>

                <!-- Stock Status -->
                <div class="flex items-center gap-2 mb-4">
                    @if($product->stok > 0)
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        <span class="text-sm text-green-600 font-medium">Stok tersedia ({{ $product->stok }})</span>
                    @else
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        <span class="text-sm text-red-600 font-medium">Stok habis</span>
                    @endif
                </div>

                <!-- Add to Cart & Buy Now Buttons -->
                <div class="mt-auto">
                    @auth
                        @if(Auth::user()->role === 'customer')
                            <div class="flex flex-col gap-3">
                                <!-- Quantity Selector -->
                                <div>
                                    <label class="text-xs text-gray-500 mb-1 block">Jumlah</label>
                                    <div class="flex items-center border border-gray-300 rounded-lg w-fit">
                                        <button type="button" onclick="decreaseQty()" class="px-3 py-2 text-gray-600 hover:bg-gray-100">-</button>
                                        <input type="number" id="qty-input" value="1" min="1" max="{{ $product->stok }}" class="w-16 text-center py-2 border-x border-gray-300 outline-none text-sm">
                                        <button type="button" onclick="increaseQty()" class="px-3 py-2 text-gray-600 hover:bg-gray-100">+</button>
                                    </div>
                                </div>

                                <!-- Buttons Row -->
                                <div class="flex gap-3">
                                    <form method="POST" action="{{ route('consumer.cart.add', $product->id) }}" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="quantity" id="cart-qty" value="1">
                                        <button type="submit" class="w-full btn-outline bg-white px-4 py-3 rounded-lg font-medium text-sm transition hover:bg-[#ee4d2d] hover:text-white" {{ $product->stok <= 0 ? 'disabled' : '' }}>
                                            🛒 Keranjang
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('consumer.cart.buynow', $product->id) }}" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="quantity" id="buy-qty" value="1">
                                        <button type="submit" class="w-full btn-primary text-white px-4 py-3 rounded-lg font-medium text-sm" {{ $product->stok <= 0 ? 'disabled' : '' }}>
                                            ⚡ Beli Langsung
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('consumer.register') }}" class="block w-full text-center btn-primary text-white px-6 py-3 rounded-lg font-medium">
                                Daftar untuk Membeli
                            </a>
                        @endif
                    @else
                        <a href="{{ route('consumer.login') }}" class="block w-full text-center btn-primary text-white px-6 py-3 rounded-lg font-medium">
                            Login untuk Membeli
                        </a>
                    @endauth
                </div>

                <!-- Info tags -->
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100 text-sm text-gray-500">
                    <span>💳 Transfer Bank</span>
                    <span>📱 QRIS</span>
                    <span>🚚 COD</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-6">
        <a href="{{ route('consumer.catalog') }}" class="inline-flex items-center text-[#ee4d2d] hover:text-[#d63e1f] transition font-medium">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Katalog
        </a>
    </div>
</div>

<script>
    const maxQty = {{ $product->stok }};
    function decreaseQty() {
        const input = document.getElementById('qty-input');
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
            document.getElementById('cart-qty').value = input.value;
            document.getElementById('buy-qty').value = input.value;
        }
    }
    function increaseQty() {
        const input = document.getElementById('qty-input');
        if (parseInt(input.value) < maxQty) {
            input.value = parseInt(input.value) + 1;
            document.getElementById('cart-qty').value = input.value;
            document.getElementById('buy-qty').value = input.value;
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('qty-input');
        input.addEventListener('change', function() {
            let val = parseInt(this.value) || 1;
            if (val < 1) val = 1;
            if (val > maxQty) val = maxQty;
            this.value = val;
            document.getElementById('cart-qty').value = val;
            document.getElementById('buy-qty').value = val;
        });
    });
</script>
@endsection