@extends('layouts.admin')

@section('title', 'Detail Produk')

@section('content')
<div class="max-w-6xl mx-auto">
{{-- JUDUL HALAMAN --}}
    {{-- Header --}}
    <div class="mb-6">
    <h1 class="text-2xl font-bold text-white">Detail Produk</h1>
    <p class="text-xs text-gray-500">Informasi lengkap produk & pengelolaan stok</p>
</div>
    <div class="flex justify-between items-center mb-8 border-b border-white/10 pb-6">
        <div>
            <h2 class="text-2xl font-bold text-white">{{ $product->nama_produk }}</h2>
        </div>
        <div class="flex gap-3" x-data="{ showModal:false }">

    <a href="{{ route('admin.produk-jadi.edit', $product->id) }}"
       class="bg-[#202020] hover:bg-white hover:text-black text-gray-300 text-xs font-bold px-4 py-2.5 rounded-lg transition border border-white/10">
        Edit Data
    </a>

    <a href="{{ route('admin.bom.create', $product->id) }}"
       class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold px-4 py-2.5 rounded-lg transition">
        Kelola Resep (BOM)
    </a>
</div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Kartu Informasi Produk --}}
        <div class="lg:col-span-2 bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 relative">
            <div class="flex gap-6">
                <div class="w-48 h-48 bg-[#222] rounded-xl flex-shrink-0 flex items-center justify-center overflow-hidden border border-white/5">
                    @if($product->foto)
                        <img src="{{ asset('storage/' . $product->foto) }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-16 h-16 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    @endif
                </div>

                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-1">{{ $product->nama_produk }}</h1>
                            <p class="text-sm text-gray-500 mb-4">
                                SKU: <span class="font-mono text-orange-400">{{ $product->kode_produk }}</span> • 
                                Warna: {{ $product->warna }}
                            </p>
                        </div>
                        @if($product->stok <= ($product->stok_minimum ?? 10))
                            <span class="bg-red-500/10 border border-red-500/20 text-red-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase">Stok Kritis</span>
                        @else
                            <span class="bg-green-500/10 border border-green-500/20 text-green-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase">Stok Aman</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="p-3 bg-[#111] rounded-lg border border-white/5">
                            <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Harga Jual</p>
                            <p class="text-lg font-bold text-white">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-3 bg-[#111] rounded-lg border border-white/5">
                            <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Estimasi HPP</p>
                            @php
                                $hpp = $product->boms->sum(function($bom) {
                                    return $bom->jumlah_kebutuhan * ($bom->material->harga_satuan ?? 0);
                                });
                                $untung = $product->harga_jual - $hpp;
                                $persenUntung = $hpp > 0 ? ($untung / $hpp) * 100 : 0;
                            @endphp
                            <p class="text-lg font-bold text-teal-500">Rp {{ number_format($hpp, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-3 bg-[#111] rounded-lg border border-white/5">
                            <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Estimasi {{ $untung >= 0 ? 'Untung' : 'Rugi' }}</p>
                            <p class="text-lg font-bold {{ $untung >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                Rp {{ number_format(abs($untung), 0, ',', '.') }}
                                <span class="text-xs {{ $untung >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    ({{ number_format(abs($persenUntung), 1) }}%)
                                </span>
                            </p>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 leading-relaxed">
                        {{ $product->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-white/5 space-y-6">
                {{-- Dimensi & Detail Pola --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-xs text-gray-500 uppercase font-bold">Dimensi Pola</h4>
                            @if($product->panjang || $product->lebar || $product->tinggi)
                                <p class="text-white font-semibold">
                                    {{ $product->panjang + 0 ?? 'N/A' }} x {{ $product->lebar + 0 ?? 'N/A' }} x {{ $product->tinggi + 0 ?? 'N/A' }} cm
                                </p>
                            @else
                                <p class="text-sm text-gray-500 italic">Tidak ada data dimensi.</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xs text-gray-500 uppercase font-bold">Detail Pola / Catatan Potong</h4>
                            @if($product->detail_pola)
                                <p class="text-sm text-gray-300 whitespace-pre-wrap">{{ $product->detail_pola }}</p>
                            @else
                                <p class="text-sm text-gray-500 italic">Tidak ada catatan.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Sketsa --}}
                    <div class="space-y-2">
                        <h4 class="text-xs text-gray-500 uppercase font-bold">Sketsa</h4>
                        @if($product->sketsa)
                            <div class="w-full h-40 bg-[#111] rounded-xl flex items-center justify-center overflow-hidden border border-white/5">
                                <a href="{{ asset('storage/' . $product->sketsa) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $product->sketsa) }}" class="w-full h-full object-contain">
                                </a>
                            </div>
                        @else
                            <div class="w-full h-40 bg-[#111] rounded-xl flex items-center justify-center border border-dashed border-white/10">
                                <div class="text-center">
                                    <svg class="w-10 h-10 text-gray-700 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="text-xs text-gray-600">Tidak ada sketsa</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Manajemen Stok --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 flex flex-col justify-center" x-data="{ showModal: false, actionType: 'add' }">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-white/5">
                <div class="p-2 bg-gray-800 rounded-lg text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <h3 class="font-bold text-sm text-white">Manajemen Stok</h3>
            </div>

            <div class="flex justify-between items-end mb-2">
                <span class="text-xs text-gray-500">Stok Saat Ini</span>
                <span class="text-4xl font-bold {{ $product->stok <= ($product->stok_minimum ?? 10) ? 'text-red-500' : 'text-white' }}">{{ $product->stok }}</span>
            </div>

            <div class="w-full h-2 bg-gray-800 rounded-full mb-6 overflow-hidden">
                <div class="h-full {{ $product->stok <= ($product->stok_minimum ?? 10) ? 'bg-red-500' : 'bg-green-500' }}" style="width: {{ min(100, ($product->stok / 50) * 100) }}%"></div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button @click="showModal = true; actionType = 'reduce'" class="py-3 bg-red-600 hover:bg-red-500 text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    Kurangi
                </button>
            </div>

            {{-- MODAL ADJUST STOCK --}}
            <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4" x-transition>
                <div @click.away="showModal = false" class="bg-[#222] border border-white/10 rounded-xl w-full max-w-sm shadow-2xl">
                    <form action="{{ route('admin.produk-jadi.adjust', $product->id) }}" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="type" :value="actionType">
                        
                        <h3 class="text-lg font-bold text-white mb-1" x-text="actionType == 'add' ? 'Tambah Stok Manual' : 'Kurangi Stok Manual'"></h3>
                        <p class="text-xs text-gray-400 mb-4">Perubahan akan tercatat di sistem.</p>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Jumlah</label>
                                <input type="number" name="amount" min="1" required class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-orange-500 outline-none text-center font-bold text-lg">
                            </div>
                            <div>
                                <label class="block text-[10px] uppercase font-bold text-gray-500 mb-1">Catatan (Opsional)</label>
                                <textarea name="note" rows="2" class="w-full bg-black border border-white/10 rounded-lg px-4 py-2 text-white text-sm focus:border-orange-500 outline-none" placeholder="Contoh: Retur barang, Bonus, dll"></textarea>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-6">
                            <button type="button" @click="showModal = false" class="flex-1 py-2.5 border border-white/10 rounded-lg text-gray-400 hover:text-white text-sm font-bold">Batal</button>
                            <button type="submit" class="flex-1 py-2.5 rounded-lg text-white text-sm font-bold shadow-lg transition" :class="actionType == 'add' ? 'bg-green-600 hover:bg-green-500' : 'bg-red-600 hover:bg-red-500'">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- Tabel BOM --}}
    <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-white">Komponen Bahan Baku (Resep)</h3>
            <span class="text-[10px] bg-gray-800 px-2 py-1 rounded text-gray-400">Per 1 Unit Produk</span>
        </div>

        <div class="overflow-hidden rounded-xl border border-white/5">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#111] text-gray-500 uppercase font-bold text-[9px]">
                    <tr>
                        <th class="p-3 pl-4">Nama Bahan</th>
                        <th class="p-3 text-center">Kebutuhan</th>
                        <th class="p-3 text-center">Stok Gudang</th>
                        <th class="p-3 text-right pr-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($product->boms as $bom)
                    <tr class="hover:bg-white/[0.02]">
                        <td class="p-3 pl-4 font-medium text-white">
                            {{ $bom->material->nama_material ?? 'Material Terhapus' }}
                            <div class="text-[9px] text-gray-500">{{ $bom->material->kode_material ?? '-' }}</div>
                        </td>
                        <td class="p-3 text-center text-gray-400">{{ $bom->jumlah_kebutuhan + 0 }} {{ $bom->satuan }}</td>
                        <td class="p-3 text-center text-white">{{ $bom->material->stok ?? 0 }} {{ $bom->material->satuan ?? '' }}</td>
                        <td class="p-3 text-right pr-4">
                            @if(($bom->material->stok ?? 0) < $bom->jumlah_kebutuhan)
                                <span class="text-red-500 font-bold">Kurang</span>
                            @else
                                <span class="text-green-500 font-bold">Ready</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500 italic">Belum ada resep BOM. Silakan tambahkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection