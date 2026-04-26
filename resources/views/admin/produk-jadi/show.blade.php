@extends('layouts.admin')

@section('title', 'Detail Produk')

@section('content')
<div class="max-w-6xl mx-auto">
{{-- JUDUL HALAMAN --}}
    {{-- Header --}}
    <div class="mb-6">

    <p class="text-xs text-gray-500">Informasi lengkap produk & pengelolaan stok</p>
</div>
    <div class="flex justify-end items-center mb-8 border-b border-white/10 pb-6">
        <div class="flex gap-3" x-data="{ showModal:false }">

    <a href="{{ route('admin.produk-jadi.edit', $product->id) }}"
       class="bg-[#202020] hover:bg-white hover:text-black text-gray-300 text-xs font-bold px-4 py-2.5 rounded-lg transition border border-white/10">
        Edit Data
    </a>

    <a href="{{ route('admin.bom.edit', $product->id) }}"
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
                                SKU: <span class="font-mono text-orange-400">{{ $product->kode_produk }}</span>
                            </p>
                            
                            <div class="flex flex-col gap-3 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold uppercase text-gray-500 w-16 flex-shrink-0">Kategori</span>
                                    <span class="text-gray-300">{{ is_array($product->kategori) ? implode(', ', $product->kategori) : $product->kategori }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold uppercase text-gray-500 w-16 flex-shrink-0">Warna</span>
                                    <span class="text-gray-300">{{ is_array($product->warna) ? implode(', ', $product->warna) : $product->warna }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold uppercase text-gray-500 w-16 flex-shrink-0">Motif</span>
                                    <span class="text-gray-300">{{ is_array($product->jenis_motif) ? implode(', ', $product->jenis_motif) : $product->jenis_motif }}</span>
                                </div>
                            </div>
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

                    <p class="text-xs text-gray-400 leading-relaxed mt-6 pt-6 border-t border-white/5">
                        {{ $product->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Kartu Spesifikasi Teknis --}}
        <div class="bg-[#1A1A1A] border border-white/5 rounded-2xl p-6 space-y-6">
            <div>
                <h3 class="text-sm font-bold text-white mb-4">Spesifikasi Teknis</h3>
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
            </div>

            <div class="space-y-2 pt-6 border-t border-white/5">
                <h4 class="text-xs text-gray-500 uppercase font-bold">Sketsa Produksi</h4>
                @php $sketsas = is_string($product->sketsa) ? json_decode($product->sketsa, true) : ($product->sketsa ?? []); @endphp
                @if(is_array($sketsas) && count($sketsas) > 0)
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(array_slice($sketsas, 0, 6) as $index => $sketsaPath)
                            <div class="aspect-square bg-[#111] rounded-xl overflow-hidden border border-white/5 cursor-pointer hover:border-orange-500/50 transition"
                                 onclick="openSketsaLightbox('{{ asset('storage/' . $sketsaPath) }}')">
                                <img src="{{ asset('storage/' . $sketsaPath) }}" class="w-full h-full object-cover" alt="Sketsa {{ $index + 1 }}">
                            </div>
                        @endforeach
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

{{-- Lightbox Modal Sketsa --}}
<div id="sketsaLightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90 backdrop-blur-sm p-4" onclick="this.classList.add('hidden'); this.classList.remove('flex');">
    <img id="sketsaLightboxImg" src="" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl border border-white/10">
</div>

<script>
function openSketsaLightbox(src) {
    const box = document.getElementById('sketsaLightbox');
    document.getElementById('sketsaLightboxImg').src = src;
    box.classList.remove('hidden');
    box.classList.add('flex');
}
</script>
@endsection