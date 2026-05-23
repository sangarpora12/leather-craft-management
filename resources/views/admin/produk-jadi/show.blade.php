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
            <div class="flex flex-col md:flex-row gap-6">
                
                <!-- Left Column: Image & Prices -->
                <div class="w-48 flex-shrink-0">
                    <div class="w-48 h-48 bg-[#222] rounded-xl flex items-center justify-center overflow-hidden border border-white/5 mb-4">
                        @if($product->foto)
                            <img src="{{ asset('storage/' . $product->foto) }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-16 h-16 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        @endif
                    </div>
                    
                    <div class="space-y-2">
                        @php
                            $hpp = $product->boms->sum(function($bom) {
                                return ($bom->jumlah_kebutuhan ?? 0) * ($bom->material->harga_satuan ?? 0);
                            });

                            if ($product->tipe_harga == 'otomatis') {
                                $harga_jual_final = $hpp * (1 + ($margin / 100));
                                $untung_final = $harga_jual_final - $hpp;
                            } else { // manual
                                $harga_jual_final = $product->harga_jual;
                                $untung_final = $harga_jual_final - $hpp;
                            }
                        @endphp

                        <div class="p-2.5 bg-[#111] rounded-lg border border-white/5">
                            <p class="text-[9px] text-gray-500 uppercase font-bold">Harga Jual <span class="text-gray-400 normal-case">({{ $product->tipe_harga == 'otomatis' ? 'Otomatis' : 'Manual' }})</span></p>
                            <p class="text-base font-bold text-white flex flex-wrap gap-x-1"><span class="flex-shrink-0">Rp</span> <span class="break-all">{{ number_format($harga_jual_final, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="p-2.5 bg-[#111] rounded-lg border border-white/5">
                            <p class="text-[9px] text-gray-500 uppercase font-bold">Estimasi HPP</p>
                            <p class="text-base font-bold text-teal-500 flex flex-wrap gap-x-1"><span class="flex-shrink-0">Rp</span> <span class="break-all">{{ number_format($hpp, 0, ',', '.') }}</span></p>
                        </div>
                        <div class="p-2.5 bg-[#111] rounded-lg border border-white/5">
                            <p class="text-[9px] text-gray-500 uppercase font-bold">Estimasi Untung</p>
                            <div class="text-base font-bold {{ $untung_final >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                <div class="flex flex-wrap gap-x-1"><span class="flex-shrink-0">Rp</span> <span class="break-all">{{ number_format(abs($untung_final), 0, ',', '.') }}</span></div>
                                @if($product->tipe_harga == 'otomatis')
                                <div class="text-[11px] {{ $untung_final >= 0 ? 'text-green-400' : 'text-red-400' }} mt-0.5">
                                    (Margin {{ $margin }}%)
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details -->
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-1">{{ $product->nama_produk }}</h1>
                            <p class="text-sm text-gray-500 mb-4">
                                SKU: <span class="font-mono text-orange-400">{{ $product->kode_produk }}</span>
                            </p>
                            
                            <div class="flex flex-col gap-3 text-xs">
                                <div class="flex items-start gap-2">
                                    <span class="font-bold uppercase text-gray-500 w-16 flex-shrink-0 mt-1">Kategori</span>
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $items_source = $product->kategori;
                                            if (is_string($items_source)) {
                                                $decoded = json_decode($items_source, true);
                                                if (json_last_error() === JSON_ERROR_NONE) { $items_source = $decoded; } 
                                                else { $items_source = explode(',', str_replace(['[', ']', "'", '"'], '', $items_source)); }
                                            }
                                            $items = is_array($items_source) ? $items_source : [$items_source];
                                        @endphp
                                        @foreach($items as $item)
                                            @if(trim($item))
                                                <span class="bg-blue-900/50 text-blue-300 border border-blue-800/50 px-2 py-0.5 rounded-full text-[10px] font-medium">{{ trim($item) }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-bold uppercase text-gray-500 w-16 flex-shrink-0 mt-1">Warna</span>
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $items_source = $product->warna;
                                            if (is_string($items_source)) {
                                                $decoded = json_decode($items_source, true);
                                                if (json_last_error() === JSON_ERROR_NONE) { $items_source = $decoded; }
                                                else { $items_source = explode(',', str_replace(['[', ']', "'", '"'], '', $items_source)); }
                                            }
                                            $items = is_array($items_source) ? $items_source : [$items_source];
                                        @endphp
                                        @foreach($items as $item)
                                            @if(trim($item))
                                                <span class="bg-purple-900/50 text-purple-300 border border-purple-800/50 px-2 py-0.5 rounded-full text-[10px] font-medium">{{ trim($item) }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="font-bold uppercase text-gray-500 w-16 flex-shrink-0 mt-1">Motif</span>
                                    <div class="flex flex-wrap gap-1">
                                        @php
                                            $items_source = $product->jenis_motif;
                                            if (is_string($items_source)) {
                                                $decoded = json_decode($items_source, true);
                                                if (json_last_error() === JSON_ERROR_NONE) { $items_source = $decoded; }
                                                else { $items_source = explode(',', str_replace(['[', ']', "'", '"'], '', $items_source)); }
                                            }
                                            $items = is_array($items_source) ? $items_source : [$items_source];
                                        @endphp
                                        @foreach($items as $item)
                                            @if(trim($item))
                                                <span class="bg-amber-900/50 text-amber-300 border border-amber-800/50 px-2 py-0.5 rounded-full text-[10px] font-medium">{{ trim($item) }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($product->tipe_harga == 'otomatis')
                    <span class="bg-green-500/10 border border-green-500/20 text-green-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase self-start">Harga Otomatis</span>
                @else
                    <span class="bg-blue-500/10 border border-blue-500/20 text-blue-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase self-start">Harga Manual</span>
                @endif
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
                        <h4 class="text-xs text-gray-500 uppercase font-bold">Stok Tersedia</h4>
                        <div class="flex items-baseline gap-2 mt-1">
                            <p class="text-white font-bold text-2xl">{{ $product->stok }}</p>
                            <p class="text-gray-400 text-sm">Unit</p>
                            @php
                                $stokMinimumProduk = \App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_produk')->value('nilai') ?? 10;
                            @endphp
                            @if($product->stok <= 0)
                                <span class="text-gray-400 font-bold text-xs ml-2">(Habis)</span>
                            @elseif($product->stok <= $stokMinimumProduk)
                                <span class="text-red-500 font-bold text-xs ml-2">(Kritis)</span>
                            @else
                                <span class="text-green-500 font-bold text-xs ml-2">(Aman)</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs text-gray-500 uppercase font-bold mb-2">Dimensi Pola</h4>
                        @if($product->panjang || $product->lebar || $product->tinggi)
                            <div class="flex items-center gap-3 text-center">
                                @if($product->panjang)
                                <div class="bg-[#111] border border-white/5 rounded-lg px-3 py-1.5 flex-1">
                                    <p class="text-sm font-bold text-white">{{ $product->panjang + 0 }} <span class="text-xs text-gray-400">cm</span></p>
                                    <p class="text-[9px] text-gray-500 uppercase">Panjang</p>
                                </div>
                                @endif
                                @if($product->lebar)
                                <div class="bg-[#111] border border-white/5 rounded-lg px-3 py-1.5 flex-1">
                                    <p class="text-sm font-bold text-white">{{ $product->lebar + 0 }} <span class="text-xs text-gray-400">cm</span></p>
                                    <p class="text-[9px] text-gray-500 uppercase">Lebar</p>
                                </div>
                                @endif
                                @if($product->tinggi)
                                <div class="bg-[#111] border border-white/5 rounded-lg px-3 py-1.5 flex-1">
                                    <p class="text-sm font-bold text-white">{{ $product->tinggi + 0 }} <span class="text-xs text-gray-400">cm</span></p>
                                    <p class="text-[9px] text-gray-500 uppercase">Tinggi</p>
                                </div>
                                @endif
                            </div>
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
            @php
                $bomsGroupedByColor = $product->boms->groupBy(function($bom) {
                    $warna = $bom->material->warna ?? 'COKELAT MUDA';
                    if (in_array(strtoupper($warna), ['POLOS', 'N/A', 'NULL', '-', '', 'TIDAK ADA WARNA'])) {
                        return 'COKELAT MUDA';
                    }
                    return strtoupper($warna);
                });
            @endphp

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
                    @forelse($bomsGroupedByColor as $warna => $boms)
                        <tr class="bg-black/20">
                            <td colspan="4" class="p-2 pl-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ \App\Helpers\WarnaHelper::detectColor($warna) }};"></div>
                                    <span class="font-bold text-white">{{ $warna }}</span>
                                    <span class="text-gray-500 text-[10px]">({{ count($boms) }} item)</span>
                                </div>
                            </td>
                        </tr>
                        @foreach($boms as $bom)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="p-3 pl-8 text-white">
                                    {{ $bom->material->nama_material ?? 'Material Terhapus' }}
                                    <div class="text-[9px] text-gray-500 mt-0.5">{{ $bom->material->kode_material ?? '-' }}</div>
                                </td>
                                <td class="p-3 text-center text-gray-400">{{ $bom->jumlah_kebutuhan + 0 }} {{ $bom->satuan }}</td>
                                <td class="p-3 text-center text-white">{{ ($bom->material->stok ?? 0) + 0 }} {{ $bom->material->satuan ?? '' }}</td>
                                <td class="p-3 text-right pr-4">
                                    @if(($bom->material->stok ?? 0) < $bom->jumlah_kebutuhan)
                                        <span class="text-red-500 font-bold">Kurang</span>
                                    @else
                                        <span class="text-green-500 font-bold">Ready</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
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