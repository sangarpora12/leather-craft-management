@extends('layouts.admin')

@section('page_title', 'Produk Jadi')

@section('content')
<div class="min-h-screen bg-[#0B0E14] px-6 py-4">

    {{-- SEARCH BAR MODERN --}}
    <form method="GET" action="{{ route('admin.produk-jadi.index') }}" class="mb-6">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-gray-500"></i>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari nama produk, kode SKU, deskripsi..."
                   class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 pl-12 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:border-orange-500 transition-all">
        </div>
    </form>

    {{-- FILTER BAR AREA --}}
    <div class="bg-[#1C222D] rounded-xl p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            

            {{-- FILTER STOK --}}
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">Stok:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-3 py-1.5 rounded-lg border-0 outline-none cursor-pointer">
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'sort_by' => request('sort_by')])) }}" {{ !request('status_stok') ? 'selected' : '' }}>Semua</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => 'tersedia', 'sort_by' => request('sort_by')])) }}" {{ request('status_stok') == 'tersedia' ? 'selected' : '' }}>Stok Tersedia</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => 'menipis', 'sort_by' => request('sort_by')])) }}" {{ request('status_stok') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => 'habis', 'sort_by' => request('sort_by')])) }}" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>

            <div class="h-6 w-px bg-[#2A3140]"></div>

            {{-- URUTKAN HARGA --}}
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">Urutkan:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-3 py-1.5 rounded-lg border-0 outline-none cursor-pointer">
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok')])) }}" {{ !request('sort_by') ? 'selected' : '' }}>Default</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok'), 'sort_by' => 'harga_termahal'])) }}" {{ request('sort_by') == 'harga_termahal' ? 'selected' : '' }}>Termahal</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok'), 'sort_by' => 'harga_termurah'])) }}" {{ request('sort_by') == 'harga_termurah' ? 'selected' : '' }}>Termurah</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok'), 'sort_by' => 'stok_terbanyak'])) }}" {{ request('sort_by') == 'stok_terbanyak' ? 'selected' : '' }}>Stok Terbanyak</option>
                    <option value="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok'), 'sort_by' => 'stok_terkecil'])) }}" {{ request('sort_by') == 'stok_terkecil' ? 'selected' : '' }}>Stok Terkecil</option>
                </select>
            </div>

        </div>
    </div>

    {{-- GRID PRODUK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    @forelse($produk_jadi as $item)
        @php
            $stokMinProduk = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_produk')->value('nilai') ?? 2);
            $isStokKritis = $item->stok <= $stokMinProduk;
        @endphp

        {{-- KARTU PRODUK --}}
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 overflow-hidden flex flex-col hover:translate-y-[-2px] transition-all duration-300 shadow-xl">

            {{-- FOTO AREA DENGAN BACKGROUND TERANG --}}
            <div class="p-3 pb-0">
                <div class="aspect-square w-full bg-[#F5F5F5] rounded-xl overflow-hidden relative">
                    @if($item->foto)
                    <img src="{{ asset('storage/'.$item->foto) }}"
                         class="w-full h-full object-cover"
                         alt="{{ $item->nama_produk }}">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fa-solid fa-image text-neutral-400 text-4xl"></i>
                    </div>
                    @endif

                    {{-- BADGE KODE KANAN ATAS --}}
                    <div class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm px-2.5 py-1 rounded-md text-[10px] text-white font-medium">
                        {{ $item->kode_produk ?? $item->sku ?? '-' }}
                    </div>

                    {{-- BADGE MENIPIS KIRI ATAS --}}
                    @if($isStokKritis)
                    <div class="absolute top-3 left-3 bg-red-500/90 backdrop-blur-sm px-2 py-1 rounded-md text-[10px] text-white font-bold">
                        ⚠ MENIPIS
                    </div>
                    @endif
                </div>
            </div>

            {{-- KONTEN BAWAH --}}
            <div class="p-4 flex flex-col flex-1">

                {{-- NAMA & BOM BADGE --}}
                <div class="mb-1">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-base font-bold text-white truncate">
                            {{ ucwords($item->nama_produk) }}
                        </h3>
                        @if($item->tipe_harga === 'otomatis' && $item->harga_jual > 0)
                        <span class="bg-green-500/15 text-green-400 text-[9px] font-bold px-2 py-0.5 rounded border border-green-500/25 whitespace-nowrap">BOM: OK</span>
                        @endif
                    </div>
                    <p class="text-gray-500 text-[11px] mt-0.5 truncate">
                        {{ $item->deskripsi ? Str::limit($item->deskripsi, 40) : 'Deskripsi produk belum diisi' }}
                    </p>
                </div>

                {{-- INFO GRID 2 KOLOM --}}
                <div class="grid grid-cols-2 gap-3 my-3">
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase tracking-wider">Stok</p>
                        <p class="text-white text-sm font-bold mt-0.5 {{ $isStokKritis ? 'text-red-400' : '' }}">{{ $item->stok }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase tracking-wider">Harga Jual</p>
                        <p class="text-[#22C55E] text-sm font-bold mt-0.5">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="flex items-center gap-2 mt-auto">
                    <a href="{{ route('admin.produk-jadi.edit', $item->id) }}"
                       class="w-10 h-9 border border-zinc-600 rounded-lg flex items-center justify-center text-gray-300 hover:bg-zinc-700 hover:text-white transition-colors shrink-0"
                       title="Edit">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>

                    <a href="{{ route('admin.produk-jadi.show', $item->id) }}"
                       class="flex-1 h-9 bg-[#FF6B00] hover:bg-[#E55D00] rounded-lg text-white text-xs font-semibold flex items-center justify-center transition-colors">
                        Lihat Detail <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
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
