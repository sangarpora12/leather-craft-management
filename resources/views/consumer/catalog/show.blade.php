@extends('layouts.consumer')

@section('title', $product->nama_produk . ' - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Breadcrumb -->
    <div class="text-sm text-[#8B4513]/60 mb-4 flex items-center gap-2">
        <a href="{{ route('consumer.catalog') }}" class="hover:text-[#8B4513]">Katalog</a>
        <span>/</span>
        <span class="text-[#3E2723] font-medium">{{ $product->nama_produk }}</span>
    </div>

    <!-- Product Detail Card -->
    <div class="leather-card rounded-lg shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
            <!-- Product Image -->
            <div class="bg-[#F5F0E1] p-8 flex items-center justify-center">
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
                <h1 class="text-2xl font-bold text-[#3E2723] mb-2 font-display">{{ $product->nama_produk }}</h1>
                
                @if($product->sku)
                    <p class="text-sm text-[#8B4513]/40 mb-4">SKU: {{ $product->sku }}</p>
                @endif

                <!-- Price -->
                <div class="bg-[#F5F0E1] border border-[#8B4513]/10 rounded-lg p-4 mb-4">
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
                                <button type="submit" class="w-full btn-outline bg-white px-4 py-3 rounded-lg font-medium text-sm transition" {{ $product->stok <= 0 ? 'disabled' : '' }}>
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
                </div>

                <!-- Product Description -->
                @if($product->deskripsi)
                    <div class="mt-4 pt-4 border-t border-[#8B4513]/10">
                        <h3 class="text-sm font-bold text-[#3E2723] mb-2">Deskripsi Produk</h3>
                        <p class="text-sm text-[#8B4513]/70 leading-relaxed">{{ $product->deskripsi }}</p>
                    </div>
                @endif

                <!-- Product Specifications -->
                <div class="mt-4 pt-4 border-t border-[#8B4513]/10">
                    <h3 class="text-sm font-bold text-[#3E2723] mb-2">Spesifikasi</h3>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        @if($product->panjang || $product->lebar || $product->tinggi)
                            <div class="flex items-center gap-2 text-[#8B4513]/70">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                <span>{{ $product->panjang }} x {{ $product->lebar }} x {{ $product->tinggi }} cm</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2 text-[#8B4513]/70">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            <span>Kategori: {{ is_array($product->kategori) ? implode(', ', $product->kategori) : $product->kategori }}</span>
                        </div>
                        @if($product->warna && is_array($product->warna) && count($product->warna) > 0)
                            <div class="flex items-center gap-2 text-[#8B4513]/70">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                <span>{{ implode(', ', $product->warna) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info tags -->
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-[#8B4513]/10 text-sm text-[#8B4513]/60">
                    <span class="flex items-center gap-1">💳 Transfer Bank</span>
                    <span class="flex items-center gap-1">📱 QRIS</span>
                    <span class="flex items-center gap-1">🚚 COD</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    @php
        $testimonials = $product->testimonials()->where('is_approved', true)->latest()->limit(3)->get();
    @endphp
    
    @if($testimonials->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-[#3E2723] mb-4 font-display">Apa Kata Pelanggan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($testimonials as $testimonial)
                    <div class="leather-card rounded-lg p-4 shadow-sm">
                        <div class="flex items-center gap-1 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $testimonial->rating)
                                    <svg class="w-4 h-4 text-[#C5A059]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.46a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.46a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endif
                            @endfor
                        </div>
                        <p class="text-sm text-[#8B4513]/80 mb-3 italic">"{{ $testimonial->ulasan }}"</p>
                        <p class="text-xs font-bold text-[#3E2723]">- {{ $testimonial->nama_pelanggan }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Related Products -->
    @php
        $relatedProducts = \App\Models\ProdukJadi::where('kategori', $product->kategori)
            ->where('id', '!=', $product->id)
            ->where('stok', '>', 0)
            ->inRandomOrder()
            ->limit(4)
            ->get();
    @endphp
    
    @if($relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-[#3E2723] mb-4 font-display">Produk Sejenis</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($relatedProducts as $related)
                    <div class="product-card leather-card rounded-lg overflow-hidden shadow-sm hover:shadow-md cursor-pointer" onclick="window.location.href='{{ route('consumer.catalog.show', $related->id) }}'">
                        <div class="relative aspect-square bg-[#F5F0E1]">
                            @if($related->foto)
                                <img src="{{ asset('storage/' . $related->foto) }}" alt="{{ $related->nama_produk }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[#8B4513]/20">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="text-sm font-medium text-[#3E2723] line-clamp-2 mb-1 leading-tight">{{ $related->nama_produk }}</h3>
                            <p class="price-tag font-bold text-sm">Rp {{ number_format($related->harga_jual, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Back Button -->
    <div class="mt-8">
        <a href="{{ route('consumer.catalog') }}" class="inline-flex items-center text-[#8B4513] hover:text-[#5C3317] transition font-medium">
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