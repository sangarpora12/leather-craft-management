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
            <div class="flex items-center gap-3">
                <span class="text-gray-400 text-sm font-medium min-w-[40px]">Stok:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-4 py-2 rounded-lg border-0 outline-none cursor-pointer text-sm min-w-[150px]">
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->except('status_stok', 'page'))) }}" {{ !request('status_stok') ? 'selected' : '' }}>Semua</option>
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->query(), ['status_stok' => 'tersedia', 'page' => 1])) }}" {{ request('status_stok') == 'tersedia' ? 'selected' : '' }}>Stok Tersedia</option>
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->query(), ['status_stok' => 'menipis', 'page' => 1])) }}" {{ request('status_stok') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->query(), ['status_stok' => 'habis', 'page' => 1])) }}" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>

            {{-- FILTER KATEGORI --}}
            <div class="flex items-center gap-3">
                <span class="text-gray-400 text-sm font-medium min-w-[50px]">Kategori:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-4 py-2 rounded-lg border-0 outline-none cursor-pointer text-sm min-w-[150px]">
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->except('kategori', 'page'))) }}" {{ !request('kategori') ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($allCategories as $kategori)
                        <option value="{{ route('admin.produk-jadi.index', array_merge(request()->query(), ['kategori' => $kategori, 'page' => 1])) }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>{{ $kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div class="h-8 w-px bg-[#2A3140]"></div>

            {{-- URUTKAN HARGA --}}
            <div class="flex items-center gap-3">
                <span class="text-gray-400 text-sm font-medium min-w-[60px]">Urutkan:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-4 py-2 rounded-lg border-0 outline-none cursor-pointer text-sm min-w-[150px]">
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->except('sort_by', 'page'))) }}" {{ !request('sort_by') ? 'selected' : '' }}>Default</option>
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->query(), ['sort_by' => 'harga_termahal', 'page' => 1])) }}" {{ request('sort_by') == 'harga_termahal' ? 'selected' : '' }}>Termahal</option>
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->query(), ['sort_by' => 'harga_termurah', 'page' => 1])) }}" {{ request('sort_by') == 'harga_termurah' ? 'selected' : '' }}>Termurah</option>
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->query(), ['sort_by' => 'stok_terbanyak', 'page' => 1])) }}" {{ request('sort_by') == 'stok_terbanyak' ? 'selected' : '' }}>Stok Terbanyak</option>
                    <option value="{{ route('admin.produk-jadi.index', array_merge(request()->query(), ['sort_by' => 'stok_terkecil', 'page' => 1])) }}" {{ request('sort_by') == 'stok_terkecil' ? 'selected' : '' }}>Stok Terkecil</option>
                </select>
            </div>

            <div class="ml-auto">
                {{-- Bisa tambahkan tombol tambah produk nanti disini --}}
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

                    {{-- BADGE MENIPIS KIRI ATAS --}}
                    @if($isStokKritis)
                    <div class="absolute top-3 left-3 bg-red-500/90 backdrop-blur-sm px-2 py-1 rounded-md text-[10px] text-white font-bold">
                        ⚠ MENIPIS
                    </div>
                    @endif
                </div>
            </div>

            {{-- BADGE KODE DAN TIPE HARGA --}}
            <div class="px-4 pt-3 flex items-center gap-2">
                <span class="bg-black/70 backdrop-blur-sm px-2.5 py-1 rounded-md text-[10px] text-white font-medium">
                    {{ $item->kode_produk ?? $item->sku ?? '-' }}
                </span>
                @if($item->tipe_harga === 'otomatis' && $item->harga_jual > 0)
                <span class="bg-blue-500/90 backdrop-blur-sm px-2 py-1 rounded-md text-[9px] text-white font-bold">🔵 OTOMATIS</span>
                @else
                <span class="bg-gray-500/90 backdrop-blur-sm px-2 py-1 rounded-md text-[9px] text-white font-bold">⚪ MANUAL</span>
                @endif
            </div>

            {{-- KONTEN BAWAH --}}
            <div class="p-4 pt-2 flex flex-col flex-1">

                {{-- NAMA PRODUK --}}
                <div class="mb-1">
                    <h2 class="text-sm font-bold text-white leading-snug min-h-[40px] line-clamp-2">
                        {{ ucwords($item->nama_produk) }}
                    </h2>
                    <p class="text-gray-500 text-[11px] mt-0.5 line-clamp-2 min-h-[28px]">
                        {{ $item->deskripsi ?? 'Deskripsi produk belum diisi' }}
                    </p>

                    {{-- BADGES KATEGORI, WARNA, MOTIF --}}
                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        @php $kategori = is_string($item->kategori) ? json_decode($item->kategori, true) : $item->kategori; @endphp
                        @if(is_array($kategori) && !empty($kategori))
                            @foreach($kategori as $k)
                                <span class="bg-blue-500/15 text-blue-400 text-[9px] font-semibold px-2 py-0.5 rounded border border-blue-500/25 whitespace-nowrap">{{ $k }}</span>
                            @endforeach
                        @endif

                        @php $warna = is_string($item->warna) ? json_decode($item->warna, true) : $item->warna; @endphp
                        @if(is_array($warna) && !empty($warna))
                            @foreach($warna as $w)
                                <span class="bg-purple-500/15 text-purple-400 text-[9px] font-semibold px-2 py-0.5 rounded border border-purple-500/25 whitespace-nowrap">{{ $w }}</span>
                            @endforeach
                        @endif

                        @php $motif = is_string($item->jenis_motif) ? json_decode($item->jenis_motif, true) : $item->jenis_motif; @endphp
                        @if(is_array($motif) && !empty($motif))
                            @foreach($motif as $m)
                                <span class="bg-amber-500/15 text-amber-400 text-[9px] font-semibold px-2 py-0.5 rounded border border-amber-500/25 whitespace-nowrap">{{ $m }}</span>
                            @endforeach
                        @endif

                        @if((!is_array($kategori) || empty($kategori)) && (!is_array($warna) || empty($warna)) && (!is_array($motif) || empty($motif)))
                            <span class="bg-gray-500/15 text-gray-400 text-[9px] font-semibold px-2 py-0.5 rounded border border-gray-500/25 whitespace-nowrap">Lain-lain</span>
                        @endif
                    </div>
                </div>

                {{-- INFO STOK DAN HARGA --}}
                <div class="space-y-2 my-3">
                    <div class="flex justify-between items-center">
                        <p class="text-gray-500 text-[10px] uppercase tracking-wider">Stok</p>
                        <p class="text-white text-sm font-bold {{ $isStokKritis ? 'text-red-400' : '' }}">{{ $item->stok }}</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-gray-500 text-[10px] uppercase tracking-wider">Harga Jual</p>
                        <p class="text-[#22C55E] text-sm font-bold">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="flex items-center gap-2 mt-auto">
                    <a href="{{ route('admin.produk-jadi.edit', $item->id) }}"
                       class="w-10 h-9 border border-zinc-600 rounded-lg flex items-center justify-center text-gray-300 hover:bg-zinc-700 hover:text-white transition-colors shrink-0"
                       title="Edit Produk">
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