@php
    // Pastikan relasi produk_jadi ada untuk menghindari error
    $produk = $bom->produk_jadi; 
    if (!$produk) {
        // Jika karena alasan aneh sebuah BOM tidak punya relasi ke produk,
        // jangan render kartu ini sama sekali untuk mencegah error.
        return;
    }

    $isReady = $bom->is_ready;
    $foto = $produk->foto ?? null;

    $statusText = $isReady ? 'Siap Produksi' : 'Bahan Kurang';
    $statusColor = $isReady ? 'green' : 'yellow';
    if (!$isReady && str_contains($bom->reason, 'ERROR')) {
        $statusText = 'Resep Error';
        $statusColor = 'red';
    }
@endphp

<button
    type="button"
    class="product-card text-left bg-[#1f242c] rounded-xl border border-gray-800 p-4 flex flex-col shadow-lg transition-all duration-300 relative group focus:outline-none hover:border-orange-500 hover:shadow-orange-500/20
    {{ !$isReady ? 'opacity-70 hover:opacity-100' : '' }}"
    {{-- ID Produk Jadi untuk identifikasi unik di form --}}
    data-id="{{ $produk->id }}" 
    {{-- ID BOM untuk referensi saat menyimpan SPK --}}
    data-bom-id="{{ $bom->id }}"
    data-nama="{{ e($produk->nama_produk) }}"
    data-kode="{{ e($produk->kode_produk) }}"
    data-stok="{{ (int) ($produk->stok ?? 0) }}"
    data-max="{{ $bom->max_production }}"
    data-foto="{{ e($foto ?? '') }}"
    data-warna="{{ e(strtolower($produk->warna ?? '')) }}"
    data-motif="{{ e(strtolower($produk->jenis_motif ?? '')) }}"
    data-is-ready="{{ $bom->is_ready ? 'true' : 'false' }}"
    data-reason="{{ e($bom->reason) }}"
>
    <div class="h-48 w-full bg-[#111] rounded-lg mb-3 overflow-hidden relative">
        @if(!empty($foto))
            <img src="{{ asset('storage/' . $foto) }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" alt="{{ $produk->nama_produk }}">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-700 bg-[#16181d]">
                <i class="fa-solid fa-image text-3xl"></i>
            </div>
        @endif
        
        <div class="absolute top-2 right-2 z-10">
            <span class="font-bold text-[10px] uppercase px-2 py-1 rounded
                @if($statusColor === 'green') bg-green-500/20 text-green-400 border border-green-500/50
                @elseif($statusColor === 'yellow') bg-yellow-500/20 text-yellow-400 border border-yellow-500/50
                @else bg-red-500/20 text-red-400 border border-red-500/50 @endif">
                {{ $statusText }}
            </span>
        </div>
    </div>

    <div class="flex-1 flex flex-col w-full overflow-hidden">
        <div>
            <p class="text-xs font-mono text-gray-500">{{ $produk->kode_produk ?? 'NO-SKU' }}</p>
            <h3 class="text-sm font-bold text-white truncate" title="{{ $produk->nama_produk }}">{{ $produk->nama_produk }}</h3>
        </div>

        @if(!$isReady && !empty($bom->reason))
        <div class="mt-2 text-[11px] {{ str_contains($bom->reason, 'ERROR') ? 'bg-red-900/50 border-red-700/50' : 'bg-yellow-900/50 border-yellow-700/50' }} rounded-md p-2">
            <p class="font-bold {{ str_contains($bom->reason, 'ERROR') ? 'text-red-400' : 'text-yellow-400' }} mb-1">
                {{ str_contains($bom->reason, 'ERROR') ? 'Kesalahan Resep:' : 'Rincian Kebutuhan:' }}
            </p>
            <pre class="font-mono {{ str_contains($bom->reason, 'ERROR') ? 'text-red-300' : 'text-yellow-300' }} whitespace-pre-wrap leading-tight">{{ $bom->reason }}</pre>
        </div>
        @endif

        <div class="flex-1 flex flex-col justify-end opacity-0 max-h-0 group-hover:opacity-100 group-hover:max-h-screen transition-all duration-300 overflow-hidden w-full">
            <div class="pt-2 mt-2 border-t border-gray-700/50 w-full">
                <p class="text-xs text-gray-500 italic truncate mb-2" title="{{ $produk->keterangan }}">{{ $produk->keterangan ?? 'Tidak ada keterangan.' }}</p>
                <div class="text-xs text-gray-300 space-y-1 w-full">
                    <div class="flex justify-between gap-2">
                        <span class="font-semibold text-gray-500 flex-shrink-0">Warna:</span>
                        <span class="text-right truncate">{{ $produk->warna ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="font-semibold text-gray-500 flex-shrink-0">Motif:</span>
                        <span class="text-right truncate">{{ $produk->jenis_motif ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-500">Stok Jadi:</span>
                        <span class="font-bold">{{ (int) ($produk->stok ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-500">Max Produksi:</span>
                        <span class="font-bold {{ $bom->max_production > 0 ? 'text-emerald-400' : 'text-red-500' }}">
                            {{ $bom->max_production }} Unit
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</button>