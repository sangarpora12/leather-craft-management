@extends('layouts.consumer')

@section('title', 'Katalog Produk - Berliano Leather')

@section('content')
    <!-- Hero Banner Carousel -->
    @if(!request('search') && !request('kategori'))
        <div x-data="{ 
            currentSlide: 0,
            slides: [
                { image: '{{ asset('produk/Dompet Kulit Premium.jpg') }}', title: 'Dompet Kulit Premium', subtitle: 'Dompet kulit asli berkualitas tinggi dengan desain elegan, awet, dan stylish untuk gaya sehari-hari.' },
                { image: '{{ asset('produk/Tas Ransel Kulit.jpg') }}', title: 'Tas Ransel Kulit Handmade', subtitle: 'Tas ransel kulit buatan tangan artisan profesional, kapasitas luas dengan desain modern untuk traveling dan daily use.' },
                { image: '{{ asset('produk/Sepatu Kulit Formal.jpg') }}', title: 'Sepatu Kulit Formal Eksklusif', subtitle: 'Sepatu kulit formal impor dengan jahitan presisi, nyaman dipakai untuk acara kantor maupun acara formal lainnya.' }
            ],
            init() {
                setInterval(() => {
                    this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                }, 2000);
            }
        }" class="relative rounded-2xl overflow-hidden mb-6 shadow-xl">
            <!-- Slides -->
            <template x-for="(slide, index) in slides" :key="index">
                <div x-show="currentSlide === index" 
                     x-transition:enter="transition-opacity duration-700 ease-in-out"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity duration-700 ease-in-out"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0">
                    <div class="absolute inset-0 bg-cover bg-center transition-all duration-1000" :style="`background-image: url('${slide.image}')`"></div>
                    <div class="absolute inset-0 bg-black/60"></div>
                </div>
            </template>
            
            <!-- Content -->
            <div class="relative px-6 py-8 md:px-12 md:py-14 flex flex-col items-center text-center">
                <span class="badge-gold text-xs font-bold px-4 py-1.5 rounded-full mb-3 tracking-widest uppercase">Premium Leather Collection</span>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-3 font-display italic" x-text="slides[currentSlide].title">Kulit Asli Pilihan Terbaik</h1>
                <p class="text-white/80 text-sm md:text-base max-w-2xl mb-5" x-text="slides[currentSlide].subtitle">Dibuat dengan keahlian artisan berpengalaman, setiap produk kulit Berliano menawarkan kualitas, gaya, dan ketahanan premium.</p>

                <!-- Floating Search Bar -->
                <form method="GET" action="{{ route('consumer.catalog') }}" class="w-full max-w-2xl mb-5">
                    <div class="flex items-center bg-white/95 backdrop-blur rounded-full shadow-lg overflow-hidden">
                        <input type="text" name="search" placeholder="Cari dompet, tas, sepatu, aksesoris kulit..." value="{{ request('search') }}"
                            class="flex-1 px-5 py-3.5 rounded-l-full border-0 outline-none text-sm text-[#3E2723] placeholder-[#8B4513]/50 bg-transparent">
                        <button type="submit" class="bg-[#8B4513] hover:bg-[#5C3317] text-white px-5 py-3.5 rounded-r-full transition font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="hidden sm:inline text-sm">Cari</span>
                        </button>
                    </div>
                </form>

                <!-- Quick Category Pills - REMOVED per user request, only in filter below -->
                
                <!-- Slide Indicators -->
                <div class="flex items-center gap-2 mt-4">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="currentSlide = index" 
                                :class="currentSlide === index ? 'bg-[#C5A059] w-6' : 'bg-white/50 w-2'" 
                                class="h-2 rounded-full transition-all duration-300 hover:bg-white/80">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    @endif

    <!-- Category Filter -->
    <div class="leather-card rounded-lg shadow-sm p-3 mb-4 overflow-x-auto scrollbar-hide">
        <div class="flex gap-2 min-w-max">
            <a href="{{ route('consumer.catalog') }}" 
               class="category-pill px-4 py-1.5 rounded-full text-sm font-medium {{ !request('kategori') ? 'active' : 'bg-[#F5F0E1] text-[#8B4513]' }}">
                Semua
            </a>
            @php $seenKategoris = []; @endphp
            @foreach($kategoris as $kategori)
                @php
                    $kategoriArray = is_string($kategori) ? json_decode($kategori, true) : $kategori;
                    $kategoriName = is_array($kategoriArray) ? ($kategoriArray[0] ?? $kategori) : $kategori;
                    if (in_array($kategoriName, $seenKategoris)) continue;
                    $seenKategoris[] = $kategoriName;
                @endphp
                <a href="{{ route('consumer.catalog', ['kategori' => $kategoriName]) }}" 
                   class="category-pill px-4 py-1.5 rounded-full text-sm font-medium whitespace-nowrap {{ request('kategori') == $kategoriName ? 'active' : 'bg-[#F5F0E1] text-[#8B4513]' }}">
                    {{ $kategoriName }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Sort & Filter Bar -->
    <div class="leather-card rounded-lg shadow-sm p-3 mb-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div class="text-sm text-[#8B4513]/70">
            Menampilkan <span class="font-bold text-[#8B4513]">{{ $products->total() }}</span> produk
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-[#8B4513]/70">Urutkan:</span>
            <select onchange="window.location.href=this.value" class="text-sm border border-[#8B4513]/20 rounded-lg px-3 py-1.5 focus:outline-none focus:border-[#A0522D] bg-white text-[#5C3317]">
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
                <div class="product-card leather-card rounded-lg overflow-hidden shadow-sm hover:shadow-md cursor-pointer" onclick="window.location.href='{{ route('consumer.catalog.show', $product->id) }}'">
                    <!-- Product Image -->
                    <div class="relative aspect-square bg-[#F5F0E1]">
                        @if($product->foto)
                            <img src="{{ asset('storage/' . $product->foto) }}" alt="{{ $product->nama_produk }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-[#8B4513]/20">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        @if($product->stok <= 0)
                            <div class="absolute inset-0 bg-[#3E2723]/60 flex items-center justify-center">
                                <span class="text-white font-bold text-lg bg-[#8B4513] px-4 py-1 rounded">HABIS</span>
                            </div>
                        @endif
                        @if($product->stok > 0)
                            <div class="absolute top-2 right-2 badge-gold text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">PREMIUM</div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="p-2.5 space-y-1">
                        <h3 class="text-sm font-medium text-[#3E2723] line-clamp-2 leading-tight">{{ $product->nama_produk }}</h3>
                        <p class="price-tag font-bold text-base">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                        @php
                            $kategoriList = is_array($product->kategori) ? $product->kategori : (is_string($product->kategori) ? json_decode($product->kategori, true) ?? [$product->kategori] : []);
                            $motifList = is_array($product->jenis_motif) ? $product->jenis_motif : (is_string($product->jenis_motif) ? json_decode($product->jenis_motif, true) ?? [$product->jenis_motif] : []);
                            $kategoriText = !empty($kategoriList) ? $kategoriList[0] : 'Umum';
                            $motifText = !empty($motifList) ? $motifList[0] : 'Polos';
                            $totalSold = \App\Models\ProdukKeluar::where('produk_jadi_id', $product->id)->sum('jumlah_keluar');
                        @endphp
                        <p class="text-[10px] text-gray-400 leading-tight truncate">Kategori: {{ $kategoriText }} | Motif: {{ $motifText }}</p>
                        <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                            <span class="text-[10px] text-gray-400"><i class="fa-solid fa-box-open mr-0.5"></i> Stok: {{ $product->stok }}</span>
                            <span class="text-[10px] text-gray-400"><i class="fa-solid fa-fire mr-0.5 text-orange-400"></i> {{ $totalSold }} Terjual</span>
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
        <div class="text-center py-16 leather-card rounded-lg shadow-sm">
            <svg class="w-20 h-20 mx-auto text-[#8B4513]/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-[#8B4513]/50 text-lg">Tidak ada produk yang ditemukan</p>
            <a href="{{ route('consumer.catalog') }}" class="inline-block mt-4 btn-primary text-white px-6 py-2 rounded-lg text-sm">Lihat Semua Produk</a>
        </div>
    @endif
@endsection