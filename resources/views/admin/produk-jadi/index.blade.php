@extends('layouts.admin')

@section('page_title', 'Produk Jadi')

@section('content')
<div class="space-y-6 animate-fade-in font-sans">

{{-- HEADER --}}
<div class="flex justify-between items-center mb-8">
    <div>
        <p class="text-gray-400 text-sm">
            Katalog produk, manajemen stok, dan harga pokok produksi.
        </p>
    </div>

    <form method="GET" action="{{ route('admin.produk-jadi.index') }}">
        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Cari nama, SKU..."
               class="bg-[#111] border border-gray-800 rounded-full px-5 py-2 text-sm w-64 focus:outline-none focus:border-orange-500">
    </form>
</div>


{{-- FILTER --}}
<div class="space-y-4 mb-8">

    {{-- Baris Kategori (tab horizontal/scroll) --}}
    <div class="flex items-center gap-3">
        <span class="text-gray-400 text-sm shrink-0">Kategori:</span>

        <div class="flex gap-2 overflow-x-auto custom-scrollbar py-1">
            <a href="{{ route('admin.produk-jadi.index', array_filter(['search' => request('search'), 'warna' => request('warna')])) }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold border transition whitespace-nowrap
               {{ !request('kategori') ? 'bg-[#2D3139] border-[#2D3139] text-white' : 'bg-[#111] border-gray-800 text-gray-300 hover:bg-[#20232A]' }}">
                Semua
            </a>

            @foreach($kategoriList ?? [] as $kategori)
                <a href="{{ route('admin.produk-jadi.index', array_filter(['kategori' => $kategori, 'search' => request('search'), 'warna' => request('warna')])) }}"
                   class="px-4 py-2 rounded-xl text-sm font-semibold border transition whitespace-nowrap
                   {{ request('kategori') == $kategori ? 'bg-[#2D3139] border-[#2D3139] text-white' : 'bg-[#111] border-gray-800 text-gray-300 hover:bg-[#20232A]' }}">
                    {{ $kategori }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Baris Warna (dropdown terpisah agar tidak tabrakan) --}}
    <form method="GET" action="{{ route('admin.produk-jadi.index') }}" class="flex flex-col md:flex-row gap-3 md:items-center">
        {{-- keep query lain --}}
        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        <input type="hidden" name="search" value="{{ request('search') }}">

        <div class="flex items-center gap-3">
            <span class="text-gray-400 text-sm shrink-0">Warna:</span>
            <div class="flex items-center bg-[#111] border border-gray-800 rounded-xl px-4 py-2.5">
                <select name="warna" onchange="this.form.submit()" class="bg-transparent border-none outline-none text-sm font-semibold text-gray-200 w-56 cursor-pointer appearance-none">
                    <option value="" class="bg-[#111]">Semua Warna</option>
                    @foreach($warnaList ?? [] as $warna)
                        <option value="{{ $warna }}" class="bg-[#111]" {{ request('warna') == $warna ? 'selected' : '' }}>{{ $warna }}</option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down text-gray-500 text-[10px] ml-2 pointer-events-none"></i>
            </div>
        </div>

        @if(request('warna'))
            <a href="{{ route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search')])) }}"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-[#2D3139] text-white hover:bg-gray-700 transition w-fit">
                Reset Warna
            </a>
        @endif
    </form>

</div>


{{-- GRID PRODUK --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

@forelse($produk_jadi as $item)

<div class="bg-[#111] rounded-2xl border border-gray-800 overflow-hidden hover:border-orange-500 transition">

{{-- IMAGE --}}
<div class="relative h-48">

@if($item->foto)
<img src="{{ asset('storage/'.$item->foto) }}" class="w-full h-full object-cover" alt="{{ $item->nama_produk }}">
@else
<div class="w-full h-full bg-gray-900 flex items-center justify-center">
    <i class="fa-solid fa-image text-gray-700 text-4xl"></i>
</div>
@endif

@if($item->sketsa)
<div class="absolute top-2 right-2 w-12 h-12 bg-black/50 backdrop-blur-sm rounded-md border border-white/10 p-1 overflow-hidden">
    <img src="{{ asset('storage/'.$item->sketsa) }}" class="w-full h-full object-contain" alt="Sketsa">
</div>
@endif

<div class="absolute top-3 left-3 bg-black/70 px-2 py-1 text-xs rounded">
{{ $item->kode_produk }}
</div>

</div>


{{-- BODY --}}
<div class="p-5">

<h3 class="font-semibold text-lg">
{{ $item->nama_produk }}
</h3>

 <div class="flex flex-wrap gap-1 mb-4">
     <span class="px-2 py-0.5 bg-gray-700 rounded text-xs text-gray-200 text-capitalize">
         {{ is_array(json_decode($item->warna, true)) ? implode(', ', json_decode($item->warna, true)) : ($item->warna ?? '-') }}
     </span>
 </div>
 <div class="flex flex-wrap gap-1 mb-2">
     <span class="px-2 py-0.5 bg-blue-900/50 rounded text-xs text-blue-200 text-capitalize">
         {{ is_array(json_decode($item->kategori, true)) ? implode(', ', json_decode($item->kategori, true)) : ($item->kategori ?? '-') }}
     </span>
 </div>

@if($item->panjang || $item->lebar || $item->tinggi)
<div class="mb-4">
    <p class="text-xs text-gray-500 font-semibold">Dimensi:</p>
    <p class="text-gray-400 text-sm">
        {{ $item->panjang + 0 }} x {{ $item->lebar + 0 }} x {{ $item->tinggi + 0 }} cm
    </p>
</div>
@endif

@if($item->deskripsi)
<div class="mb-4">
    <p class="text-xs text-gray-500 font-semibold">Deskripsi:</p>
    <p class="text-gray-400 text-sm leading-relaxed">
        {{ Str::limit($item->deskripsi, 100) }}
    </p>
</div>
@endif

@if($item->keterangan)
<div class="mb-4 p-3 bg-gray-900/50 border border-gray-800 rounded-lg">
    <p class="text-xs text-yellow-400/80 font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-info"></i>
        Catatan Internal:
    </p>
    <p class="text-gray-400 text-sm mt-1">
        {{ Str::limit($item->keterangan, 100) }}
    </p>
</div>
@endif



<div class="flex justify-between text-sm mb-2">
<span class="text-gray-400">Stok</span>
<span>{{ $item->stok }}</span>
</div>

<div class="flex justify-between text-sm mb-5">
<span class="text-gray-400">Harga Jual</span>
<span class="font-semibold {{ $item->tipe_harga === 'otomatis' ? 'text-green-500' : 'text-orange-500' }}">
@if($item->tipe_harga === 'otomatis' && $item->harga_jual == 0)
<span class="text-xs text-gray-500 block">Belum ada BOM</span>
Rp 0
@else
Rp {{ number_format($item->harga_jual,0,',','.') }}
@endif
</span>
</div>


<div class="flex gap-2">

    <a href="{{ route('admin.produk-jadi.edit', $item->id) }}"
       class="flex-1 border border-gray-700 py-2 rounded-lg text-sm hover:bg-gray-800 text-center">
        Edit
    </a>

    <form action="{{ route('admin.produk-jadi.copy', $item->id) }}" method="POST" class="flex-shrink-0">
        @csrf
        <button type="submit"
                class="w-10 h-10 border border-gray-700 rounded-lg text-sm hover:bg-gray-800 text-center"
                title="Duplikat Produk">
            <i class="fa-solid fa-copy"></i>
        </button>
    </form>

    <a href="{{ route('admin.produk-jadi.show', $item->id) }}"
       class="flex-1 bg-orange-600 hover:bg-orange-500 py-2 rounded-lg text-sm text-center">
        Lihat Detail →
    </a>

</div>

</div>
</div>

@empty

<div class="col-span-full text-center py-20 text-gray-500">
Produk tidak ditemukan
</div>

@endforelse

</div>

</div>
@endsection