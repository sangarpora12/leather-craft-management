@extends('layouts.admin')

@section('page_title', 'Produk Jadi')

@section('content')
<div class="min-h-screen bg-[#0B0E14] px-6 py-8">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Master Data Produk Jadi</h1>
            <p class="text-gray-400 text-sm">Katalog produk, manajemen stok, dan harga pokok produksi.</p>
        </div>

        <form method="GET" action="{{ route('admin.produk-jadi.index') }}" class="w-full md:w-auto">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama, SKU..."
                   class="w-full md:w-72 bg-neutral-900 border border-neutral-800 rounded-full px-5 py-3 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:border-orange-500 transition-colors">
        </form>
    </div>

    {{-- FILTER BAR AREA --}}
    <div class="bg-[#1C222D] rounded-xl p-4 mb-8">
        <div class="flex flex-wrap items-center gap-4">
            
            {{-- FILTER KATEGORI --}}
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">Kategori:</span>
                <div class="flex gap-1">
                    <a href="{{ route('admin.produk-jadi.index', array_filter(['search' => request('search'), 'stok' => request('stok'), 'sort' => request('sort')])) }}"
                       class="px-3 py-1.5 rounded-lg text-sm transition-colors whitespace-nowrap
                       {{ !request('kategori') ? 'bg-[#2A3140] text-white' : 'bg-transparent text-gray-400 hover:text-white' }}">
                        Semua
                    </a>
                    
                    @foreach($kategoriList ?? [] as $kategori)
                    <a href="{{ route('admin.produk-jadi.index', array_filter(['kategori' => $kategori, 'search' => request('search'), 'stok' => request('stok'), 'sort' => request('sort')])) }}"
                       class="px-3 py-1.5 rounded-lg text-sm transition-colors whitespace-nowrap
                       {{ request('kategori') == $kategori ? 'bg-[#2A3140] text-white' : 'bg-transparent text-gray-400 hover:text-white' }}">
                        {{ ucwords($kategori) }}
                    </a>
                    @endforeach
                </div>
            </div>

            <div class="h-6 w-px bg-[#2A3140]"></div>

            {{-- FILTER STOK --}}
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">Stok:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-3 py-1.5 rounded-lg border-0 outline-none cursor-pointer">
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'sort' => request('sort')])) }}" {{ !request('stok') ? 'selected' : '' }}>Semua</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'stok' => 'tersedia', 'sort' => request('sort')])) }}" {{ request('stok') == 'tersedia' ? 'selected' : '' }}>Stok Tersedia</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'stok' => 'menipis', 'sort' => request('sort')])) }}" {{ request('stok') == 'menipis' ? 'selected' : '' }}>Stok Menipis (< 10)</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'stok' => 'habis', 'sort' => request('sort')])) }}" {{ request('stok') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>

            <div class="h-6 w-px bg-[#2A3140]"></div>

            {{-- URUTKAN HARGA --}}
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">Urutkan:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-3 py-1.5 rounded-lg border-0 outline-none cursor-pointer">
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'stok' => request('stok')])) }}" {{ !request('sort') ? 'selected' : '' }}>Default</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'stok' => request('stok'), 'sort' => 'termahal'])) }}" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Termahal</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'stok' => request('stok'), 'sort' => 'termurah'])) }}" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Termurah</option>
                </select>
            </div>

        </div>
    </div>

    {{-- GRID PRODUK - TANPA ABSOLUTE APAPUN --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    @forelse($produk_jadi as $item)

        {{-- KARTU PRODUK PERSIS FIGMA --}}
        <div class="bg-[#1C222D] rounded-2xl border-0 overflow-hidden flex flex-col hover:translate-y-[-2px] transition-all duration-300 shadow-xl">

            {{-- FOTO ATAS 192px --}}
            <div class="aspect-square w-full overflow-hidden">
                @if($item->foto)
                <img src="{{ asset('storage/'.$item->foto) }}" 
                     class="w-full h-full object-cover" 
                     alt="{{ $item->nama_produk }}">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fa-solid fa-image text-neutral-600 text-4xl"></i>
                </div>
                @endif
                
                {{-- BADGE SKU --}}
                <div class="absolute top-4 left-3 bg-black/60 px-2 py-1 rounded text-[10px] text-white">
                    {{ $item->kode_produk ?? $item->sku ?? '-' }}
                </div>
            </div>

            {{-- KONTEN BAWAH 208px --}}
            <div class="p-5 flex flex-col flex-1">
                
                {{-- NAMA & WARNA --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            {{ ucwords($item->nama_produk) }}
                        </h3>
                        @if($item->tipe_harga === 'otomatis' && $item->harga_jual > 0)
                        <span class="bg-green-500/10 text-green-500 text-[9px] font-bold px-2 py-1 rounded border border-green-500/20">BOM: OK</span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-xs mt-1">
                        {{ $item->deskripsi ? Str::limit($item->deskripsi, 50) : 'Deskripsi produk belum diisi' }}
                    </p>
                </div>

                {{-- STOK --}}
                <div class="flex justify-between items-center pb-2 mb-2 border-b border-neutral-800">
                    <span class="text-gray-500 text-xs">Stok</span>
                    <span class="text-white text-xs font-bold">{{ $item->stok }}</span>
                </div>

                {{-- HARGA --}}
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-400 text-sm">Harga Jual</span>
                    <span class="font-bold text-[#22C55E] text-lg">
                        Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                    </span>
                </div>

                {{-- TOMBOL --}}
                <div class="flex gap-3 mt-auto">
                    <a href="{{ route('admin.produk-jadi.edit', $item->id) }}"
                       class="w-16 h-9 border border-zinc-800 rounded-lg flex items-center justify-center text-gray-300 text-xs font-bold hover:bg-zinc-800 transition-colors">
                        <i class="fa-solid fa-pen mr-1"></i> Edit
                    </a>

                    <a href="{{ route('admin.produk-jadi.show', $item->id) }}"
                       class="flex-1 h-9 bg-[#FF6B00] hover:bg-[#FF8A33] rounded-lg text-white text-sm font-semibold text-center flex items-center justify-center transition-colors">
                        Lihat Detail
                    </a>
                </div>

            </div>
        </div>

    @empty
        <div class="col-span-full text-center py-20">
            <i class="fa-solid fa-box-open text-neutral-600 text-5xl mb-4"></i>
            <p class="text-gray-500">Produk tidak ditemukan</p>
        </div>
    @endforelse

    </div>

</div>
@endsection