@extends('layouts.operator_app')

@section('page_title', 'Resep Produk')

@section('content')
<div class="p-2">
    {{-- Header Mobile Friendly --}}
    <div class="mb-6 border-b border-white/10 pb-4">
        <div class="mb-4">
            <h1 class="text-xl font-bold text-white mb-1">Resep Produk</h1>
            <p class="text-gray-400 text-xs">Katalog SOP produksi - daftar bahan yang dibutuhkan untuk setiap produk.</p>
        </div>
        
        <!-- Form Pencarian -->
        <form action="{{ route('operator.resep-produk.index') }}" method="GET" class="w-full">
            <div class="relative w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." 
                       class="w-full bg-[#1C222D] border border-white/5 rounded-2xl px-4 py-3 pl-10 text-sm text-white placeholder-gray-500 focus:border-orange-500 focus:outline-none"
                       oninput="this.form.dispatchEvent(new Event('submit'))">
                <span class="absolute left-3 top-3.5 text-gray-500">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                @if(request()->filled('search'))
                    <button type="submit" class="absolute right-3 top-2.5 bg-gray-600 hover:bg-gray-500 text-white text-xs px-2 py-1 rounded-lg transition">Reset</button>
                @endif
            </div>
        </form>
    </div>

    {{-- Daftar Produk (simple list - click to open detail) --}}
    <div class="space-y-3">
        @forelse($products as $index => $product)
        <button onclick="openDetailModal({{ $index }})" class="w-full bg-[#1C222D] rounded-2xl border border-white/5 overflow-hidden group hover:border-orange-500/30 transition-all text-left">
            <div class="flex gap-4 p-4 items-center">
                {{-- Foto Produk --}}
                <div class="w-16 h-16 bg-[#161B26] rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center">
                    @if($product->foto)
                        <img src="{{ asset('storage/' . $product->foto) }}" class="w-full h-full object-cover">
                    @else
                        <svg class="w-7 h-7 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @endif
                </div>

                {{-- Info Singkat --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-semibold text-white text-sm leading-tight truncate">
                            {{ ucwords(str_replace(['tali pingang', 'Tali pingang'], 'Tali Pinggang', $product->nama_produk)) }}
                        </h3>
                        <i class="fa-solid fa-chevron-right text-gray-500 text-xs"></i>
                    </div>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-[10px] text-gray-500 font-mono">{{ $product->kode_produk }}</span>
                        <span class="text-[10px] text-orange-400">{{ $product->boms->count() }} bahan</span>
                        <span class="text-[10px] text-emerald-400">Stok: {{ number_format($product->stok ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </button>

        {{-- Modal Detail (hidden by default) --}}
        <div id="modal-{{ $index }}" class="fixed inset-0 z-50 hidden">
            <div class="absolute inset-0 bg-black/80" onclick="closeDetailModal({{ $index }})"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md mx-auto bg-[#1C222D] rounded-3xl p-5 max-h-[80vh] overflow-y-auto shadow-2xl border border-white/10">
                {{-- Handle bar --}}
                <div class="w-12 h-1.5 bg-white/10 rounded-full mx-auto mb-5"></div>

                {{-- Header Modal --}}
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ ucwords(str_replace(['tali pingang', 'Tali pingang'], 'Tali Pinggang', $product->nama_produk)) }}</h2>
                        <p class="text-xs text-gray-500 font-mono">{{ $product->kode_produk }}</p>
                    </div>
                    <button onclick="closeDetailModal({{ $index }})" class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:bg-white/10">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                {{-- Foto Produk Utama --}}
                <div class="mb-5">
                    <p class="text-[10px] text-gray-500 uppercase font-bold mb-2">Foto Produk</p>
                    <div class="w-full h-40 bg-[#161B26] rounded-xl overflow-hidden flex items-center justify-center">
                        @if($product->foto)
                            <img src="{{ asset('storage/' . $product->foto) }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>
                </div>

                {{-- Sketsa/Pola Produk (maksimal 4 foto - format dari admin) --}}
                <div class="mb-5">
                    <p class="text-[10px] text-gray-500 uppercase font-bold mb-2">Sketsa & Pola Produk</p>
                    <div class="grid grid-cols-4 gap-2">
                        @php
                            // Baca sketsa dari kolom sketsa yang disimpan JSON (format yang dipakai admin)
                            $sketsaList = collect();
                            if($product->sketsa) {
                                $decoded = json_decode($product->sketsa, true);
                                if(is_array($decoded)) {
                                    $sketsaList = collect($decoded);
                                } else {
                                    $sketsaList->push($product->sketsa);
                                }
                            }
                        @endphp

                        @forelse($sketsaList->take(4) as $index => $sketsa)
                        <div class="aspect-square bg-[#161B26] rounded-lg overflow-hidden flex items-center justify-center">
                            <img src="{{ asset('storage/' . $sketsa) }}" class="w-full h-full object-cover">
                        </div>
                        @empty
                        <div class="col-span-4 aspect-video bg-[#161B26] rounded-lg flex items-center justify-center">
                            <span class="text-xs text-gray-600">Belum ada sketsa/pola yang diupload</span>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Detail Pola / Catatan (dari admin) --}}
                @if($product->detail_pola)
                <div class="bg-[#161B26] p-4 rounded-xl border border-white/5 mb-4">
                    <p class="text-[10px] text-gray-500 uppercase font-bold mb-2">Catatan Pola & Jahit</p>
                    <p class="text-xs text-gray-300 leading-relaxed">{{ $product->detail_pola }}</p>
                </div>
                @endif

                {{-- Ukuran Produk (dari kolom panjang, lebar, tinggi yang dipakai admin) --}}
                @if($product->panjang || $product->lebar || $product->tinggi)
                <div class="bg-[#161B26] p-4 rounded-xl border border-white/5 mb-4">
                    <p class="text-[10px] text-gray-500 uppercase font-bold mb-3">Spesifikasi Ukuran</p>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        @if($product->panjang)
                        <div>
                            <p class="text-xs text-gray-400">Panjang</p>
                            <p class="text-sm font-bold text-white">{{ $product->panjang }} cm</p>
                        </div>
                        @endif
                        @if($product->lebar)
                        <div>
                            <p class="text-xs text-gray-400">Lebar</p>
                            <p class="text-sm font-bold text-white">{{ $product->lebar }} cm</p>
                        </div>
                        @endif
                        @if($product->tinggi)
                        <div>
                            <p class="text-xs text-gray-400">Tinggi</p>
                            <p class="text-sm font-bold text-white">{{ $product->tinggi }} cm</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Daftar Bahan Lengkap --}}
                <div class="bg-[#161B26] p-4 rounded-xl border border-white/5">
                    <p class="text-[10px] text-gray-500 uppercase font-bold mb-3">Daftar Bahan yang Dibutuhkan</p>
                    <div class="space-y-3">
                        @foreach($product->boms as $bom)
                            @if($bom->bahanBaku)
                            <div class="flex items-center justify-between py-2 border-b border-white/5 last:border-0">
                                <div class="flex items-center gap-3">
                                    @php
                                        // Handle warna yang disimpan sebagai nama atau hex
                                        $warnaBg = '#6B7280'; // default abu-abu
                                        if($bom->bahanBaku->warna) {
                                            $warna = strtolower(trim($bom->bahanBaku->warna));
                                            $warnaMap = [
                                                'hitam' => '#000000',
                                                'cokelat' => '#8B4513',
                                                'coklat' => '#8B4513',
                                                'putih' => '#FFFFFF',
                                                'merah' => '#DC2626',
                                                'biru' => '#2563EB',
                                                'hijau' => '#16A34A',
                                                'kuning' => '#EAB308',
                                                'abu-abu' => '#6B7280',
                                                'abu' => '#6B7280',
                                                'cream' => '#F5F5DC',
                                            ];
                                            $warnaBg = $warnaMap[$warna] ?? $bom->bahanBaku->warna;
                                        }
                                    @endphp
                                    <span class="w-5 h-5 rounded-full border border-white/30" style="background-color: {{ $warnaBg }}"></span>
                                    <div>
                                        <p class="text-sm text-white font-medium">{{ $bom->bahanBaku->nama_material }}</p>
                                        @if($bom->bahanBaku->warna)
                                            <p class="text-[11px] text-gray-500">Warna: {{ $bom->bahanBaku->warna }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-orange-400">{{ $bom->jumlah_kebutuhan }} {{ $bom->bahanBaku->satuan }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-16">
            <div class="text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"/></svg>
                <p class="text-base text-white/80">Belum ada resep produk</p>
                <p class="text-xs text-gray-600 mt-1">Hubungi admin untuk menambahkan resep</p>
            </div>
        </div>
        @endforelse
    </div>

    <script>
        function openDetailModal(index) {
            document.getElementById('modal-' + index).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDetailModal(index) {
            document.getElementById('modal-' + index).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="mt-8">
        {{ $products->links() }}
    </div>
    @endif
</div>

<script>
let searchTimer;
</script>
@endsection