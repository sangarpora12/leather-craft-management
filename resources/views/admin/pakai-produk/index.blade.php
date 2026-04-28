@extends('layouts.admin')

@section('page_title', 'Pakai / Kurangi Stok Produk')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Filter Panel -->
    <div class="bg-[#1A1C24] border border-[#343842] rounded-2xl p-4 mb-6">
        <form method="GET" action="{{ route('admin.pakai-produk.index') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <!-- Pencarian -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-400 mb-1">Cari Produk</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nama produk..."
                           class="w-full bg-[#2D3139] border border-[#343842] text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FD730D]">
                </div>

                <!-- Filter Kategori -->
                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-400 mb-1">Kategori</label>
                    <select name="kategori" id="kategori" class="w-full bg-[#2D3139] border border-[#343842] text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FD730D]">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                                {{ ucwords($kategori) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status Stok -->
                <div>
                    <label for="status_stok" class="block text-sm font-medium text-gray-400 mb-1">Status Stok</label>
                    <select name="status_stok" id="status_stok" class="w-full bg-[#2D3139] border border-[#343842] text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FD730D]">
                        <option value="">Semua Status</option>
                        <option value="aman" {{ request('status_stok') == 'aman' ? 'selected' : '' }}>Aman</option>
                        <option value="kritis" {{ request('status_stok') == 'kritis' ? 'selected' : '' }}>Kritis</option>
                        <option value="habis" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>Habis</option>
                    </select>
                </div>

                <!-- Urutkan -->
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-400 mb-1">Urutkan</label>
                    <select name="sort_by" id="sort_by" class="w-full bg-[#2D3139] border border-[#343842] text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#FD730D]">
                        <option value="nama_asc" {{ request('sort_by') == 'nama_asc' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="nama_desc" {{ request('sort_by') == 'nama_desc' ? 'selected' : '' }}>Nama Z-A</option>
                        <option value="stok_desc" {{ request('sort_by') == 'stok_desc' ? 'selected' : '' }}>Stok Terbanyak</option>
                        <option value="stok_asc" {{ request('sort_by') == 'stok_asc' ? 'selected' : '' }}>Stok Paling Sedikit</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-4 mt-4">
                <a href="{{ route('admin.pakai-produk.history') }}" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition text-sm whitespace-nowrap">
                    <i class="fa-solid fa-history mr-2"></i>
                    Lihat Riwayat
                </a>
                <a href="{{ route('admin.pakai-produk.index') }}" class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                    Reset
                </a>
                <button type="submit" class="bg-[#FD730D] hover:bg-[#E6670B] text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Success & Error Messages -->
    @if (session('success'))
        <div class="bg-green-500 bg-opacity-20 text-green-300 border border-green-500 rounded-lg p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-500 bg-opacity-20 text-red-300 border border-red-500 rounded-lg p-4 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Grid Produk -->
    @if($produkJadis->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @foreach ($produkJadis as $produk)
                @php
                    $stok = $produk->stok;
                    $stokHabis = $stok <= 0;
                    $stokKritis = $stok > 0 && $stok < 5;

                    $badgeClass = '';
                    if ($stokHabis) {
                        $badgeClass = 'bg-red-600 text-white';
                    } elseif ($stokKritis) {
                        $badgeClass = 'bg-yellow-500 text-black';
                    } else {
                        $badgeClass = 'bg-green-600 text-white';
                    }
                @endphp
                <div class="bg-[#1f242c] border border-gray-800 rounded-xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:border-orange-500/50 hover:shadow-orange-500/10">
                    <div class="relative">
                        @if($produk->foto)
                            <img src="{{ asset('storage/'.$produk->foto) }}" alt="{{ $produk->nama_produk }}" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-[#16181d] flex items-center justify-center">
                                <i class="fa-solid fa-image text-gray-700 text-3xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col flex-grow">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-mono text-white font-bold text-[10px]">{{ $produk->kode_produk }}</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $badgeClass }}">
                                Stok: {{ $stok }}
                            </span>
                        </div>

                        <h3 class="text-xs font-bold text-white mb-1 leading-tight truncate" title="{{ ucwords(strtolower($produk->nama_produk)) }}">{{ ucwords(strtolower($produk->nama_produk)) }}</h3>
                        
                        <div class="mb-2">
                            <span class="text-base font-bold text-orange-400">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</span>
                        </div>

                        <div class="text-xs text-gray-400 mb-3 space-y-1.5 flex-grow">
                            @php
                                if (!function_exists('parseAttribute')) {
                                    function parseAttribute($attribute) {
                                        if (is_array($attribute)) return $attribute;
                                        if (is_string($attribute)) {
                                            $decoded = json_decode($attribute, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) return $decoded;
                                            return array_filter(array_map('trim', explode(',', $attribute)));
                                        }
                                        return [];
                                    }
                                }
                                $kategoriList = parseAttribute($produk->kategori);
                                $warnaList = parseAttribute($produk->warna);
                                $motifList = parseAttribute($produk->jenis_motif);
                                $warnaMapping = ['merah' => 'bg-red-500/20 text-red-300', 'hitam' => 'bg-gray-400/20 text-gray-200', 'coklat' => 'bg-amber-800/30 text-amber-300', 'brown' => 'bg-amber-800/30 text-amber-300', 'putih' => 'bg-white/10 text-white', 'hijau' => 'bg-green-500/20 text-green-300', 'biru' => 'bg-blue-500/20 text-blue-300', 'kuning' => 'bg-yellow-500/20 text-yellow-300', 'pink' => 'bg-pink-500/20 text-pink-300', 'orange' => 'bg-orange-500/20 text-orange-300', 'ungu' => 'bg-purple-500/20 text-purple-300', 'abu' => 'bg-gray-500/20 text-gray-300', 'black' => 'bg-gray-400/20 text-gray-200'];
                                $motifMapping = ['polos' => 'bg-slate-500/20 text-slate-200', 'croco' => 'bg-emerald-500/20 text-emerald-300', 'kulit buaya' => 'bg-emerald-500/20 text-emerald-300', 'lizard' => 'bg-cyan-500/20 text-cyan-300', 'jeruk' => 'bg-orange-500/20 text-orange-300', 'kulit jeruk' => 'bg-orange-500/20 text-orange-300', 'suede' => 'bg-amber-500/20 text-amber-300', 'nappa' => 'bg-violet-500/20 text-violet-300', 'crazy horse' => 'bg-rose-500/20 text-rose-300'];
                            @endphp
                            
                            @if(!empty($kategoriList))
                            <div class="flex flex-wrap gap-1 items-center">
                                <span class="text-[10px] font-semibold text-gray-500 uppercase flex-shrink-0">Kategori:</span>
                                @foreach($kategoriList as $kategori)
                                    <span class="px-1.5 py-0.5 bg-sky-500/20 text-sky-300 rounded text-[9px] font-medium capitalize">{{ $kategori }}</span>
                                @endforeach
                            </div>
                            @endif

                            @if(!empty($warnaList))
                            <div class="flex flex-wrap gap-1 items-center">
                                <span class="text-[10px] font-semibold text-gray-500 uppercase flex-shrink-0">Warna:</span>
                                @foreach($warnaList as $warna)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-medium capitalize {{ $warnaMapping[strtolower($warna)] ?? 'bg-gray-700/50 text-gray-200' }}">{{ $warna }}</span>
                                @endforeach
                            </div>
                            @endif

                            @if(!empty($motifList))
                            <div class="flex flex-wrap gap-1 items-center">
                                <span class="text-[10px] font-semibold text-gray-500 uppercase flex-shrink-0">Motif:</span>
                                @foreach($motifList as $motif)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-medium capitalize {{ $motifMapping[strtolower($motif)] ?? 'bg-indigo-700/40 text-indigo-200' }}">{{ $motif }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        @if(!$stokHabis)
                            <button
                                onclick="openModal('{{ $produk->id }}', '{{ e(ucwords(strtolower($produk->nama_produk))) }}', '{{ $stok }}')"
                                class="w-full text-white font-bold py-2 px-3 rounded-lg transition mt-2 bg-[#FD730D] hover:bg-[#E6670B] text-xs">
                                <i class="fa-solid fa-box-minus mr-1"></i>
                                Pakai Stok
                            </button>
                        @else
                            <div class="w-full text-gray-400 font-bold py-2 px-3 rounded-lg mt-2 bg-gray-700 text-center cursor-not-allowed text-xs">
                                <i class="fa-solid fa-ban mr-1"></i>
                                Stok Habis
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            {{ $produkJadis->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-20 bg-[#1A1C24] border border-[#343842] rounded-2xl">
            <p class="text-gray-400 text-lg">Produk tidak ditemukan.</p>
        </div>
    @endif
</div>

<!-- Modal Pengurangan Stok -->
<div id="stockModal" class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden flex items-center justify-center">
    <div class="bg-[#1A1C24] border border-[#343842] rounded-2xl shadow-xl p-8 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">Kurangi Stok: <span id="modalProductName" class="text-[#FD730D]"></span></h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-white text-2xl">&times;</button>
        </div>
        <form id="stockForm" action="{{ route('admin.pakai-produk.store') }}" method="POST">
            @csrf
            <input type="hidden" name="produk_jadi_id" id="modalProdukId">

            <div class="mb-4">
                <p class="text-sm text-gray-400">Stok saat ini: <span id="modalCurrentStock" class="font-bold text-white"></span></p>
            </div>

            <div class="mb-4">
                <label for="jumlah" class="block text-sm font-medium text-gray-300 mb-2">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" min="1" required
                       class="w-full bg-[#2D3139] border border-[#343842] text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#FD730D]">
            </div>

            <div class="mb-6">
                <label for="alasan" class="block text-sm font-medium text-gray-300 mb-2">Alasan Pengurangan</label>
                <select name="alasan" id="alasan" required
                        class="w-full bg-[#2D3139] border border-[#343842] text-white rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-[#FD730D]">
                    <option value="" disabled selected>Pilih alasan...</option>
                    <option value="Penjualan">Penjualan</option>
                    <option value="Produk Rusak">Produk Rusak</option>
                    <option value="Sampel untuk Klien">Sampel untuk Klien</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeModal()" class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition mr-3">
                    Batal
                </button>
                <button type="submit" class="bg-[#FD730D] hover:bg-[#E6670B] text-white font-bold py-2 px-4 rounded-lg transition">
                    Simpan Pengurangan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const modal = document.getElementById('stockModal');
    const modalProductName = document.getElementById('modalProductName');
    const modalCurrentStock = document.getElementById('modalCurrentStock');
    const modalProdukId = document.getElementById('modalProdukId');
    const jumlahInput = document.getElementById('jumlah');

    function openModal(produkId, produkName, currentStock) {
        modalProductName.textContent = produkName;
        modalCurrentStock.textContent = currentStock;
        modalProdukId.value = produkId;
        jumlahInput.max = currentStock; // Set max value for input
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        document.getElementById('stockForm').reset(); // Reset form on close
    }

    // Close modal on escape key press
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // Close modal on outside click
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
</script>
@endpush