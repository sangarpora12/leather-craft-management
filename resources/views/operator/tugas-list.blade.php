<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Gudang - Driver Mode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js untuk handle Modal --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hide-scroll::-webkit-scrollbar { display: none; }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0.5; } to { opacity: 1; } }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex justify-center">

    {{-- CONTAINER UTAMA --}}
    {{-- Kita pasang x-data disini untuk handle Modal Ringkasan dan Detail Produk --}}
    <div class="w-full max-w-md bg-[#121212] min-h-screen relative shadow-2xl border-x border-white/5 flex flex-col pb-24 text-white"
         x-data="{ showSummary: {{ session('task_completed') ? 'true' : 'false' }}, showProductDetail: false, selectedProduct: null }">

        {{-- HEADER --}}
        <header class="px-5 py-4 sticky top-0 bg-[#121212]/95 backdrop-blur-sm z-30 border-b border-white/5 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold tracking-wide text-white">Halo, {{ Auth::user()->name }} 👋</h1>
                    <p class="text-xs text-gray-400">Kepala Gudang / Operator</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold border-2 border-[#121212] shadow-blue-500/20 shadow-lg">
                    {{ substr(Auth::user()->username ?? 'O', 0, 1) }}
                </div>
            </div>
            
            {{-- STATUS BAR (Multitasking Info) --}}
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="bg-[#1E1E1E] border border-white/5 rounded-xl p-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Sedang Aktif</p>
                        <p class="text-sm font-bold text-white">{{ $sedangDikerjakan->count() }} Tugas</p>
                    </div>
                </div>
                <div class="bg-[#1E1E1E] border border-white/5 rounded-xl p-2.5 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Pending</p>
                        <p class="text-sm font-bold text-white">{{ $tugasBaru->count() }} Order</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main id="live-container" class="flex-1 overflow-y-auto px-5 pt-6 space-y-8 hide-scroll">

            {{-- ALERT BIASA (Hanya jika tidak ada modal) --}}
            @if(session('success') && !session('task_completed'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 p-3 rounded-xl text-xs font-medium flex items-center gap-2 animate-pulse">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            
            {{-- 1. ORDERAN MASUK (PENDING) --}}
            <div class="fade-in">
                <div class="flex items-center justify-between mb-3 sticky top-0 bg-[#121212] z-20 py-2">
                    <h3 class="text-sm text-gray-400 font-bold uppercase tracking-wider">Orderan Masuk</h3>
                </div>
                
                <div class="space-y-3">
                    @forelse($tugasBaru as $baru)
                    <div class="bg-[#1E1E1E] rounded-2xl p-4 border border-white/5 relative group hover:border-blue-500/30 transition shadow-lg">
                        <div class="flex gap-4">
                            <div class="w-16 h-16 bg-[#252525] rounded-xl flex items-center justify-center text-gray-500 flex-shrink-0 border border-white/5 overflow-hidden">
                                @if(!empty($baru->product->foto))
                                    <img src="{{ $baru->product->foto }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-[10px] font-bold uppercase">{{ substr(optional($baru->product)->nama_produk ?? 'NA', 0, 3) }}</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <span class="text-[10px] text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded border border-blue-500/20 mb-1 inline-block font-bold">BARU</span>
                                    <span class="text-[10px] text-gray-500 font-mono">{{ $baru->kode_spk }}</span>
                                </div>
                                <h4 class="text-sm font-bold text-white truncate">{{ optional($baru->product)->nama_produk ?? 'Produk Dihapus' }}</h4>
                                <p class="text-[10px] text-gray-500 mt-0.5">Target: <span class="text-white font-bold">{{ $baru->jumlah_produksi }} Pcs</span></p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/5 space-y-2">
                            <button @click="showProductDetail = true; selectedProduct = @json($baru)" class="w-full bg-[#252525] hover:bg-[#333] text-gray-300 text-[10px] font-bold py-2.5 rounded-xl text-center transition">Lihat Detail Produk</button>
                            @if($baru->status == 'PENDING')
                                <form action="{{ route('operator.produksi.ambil', $baru->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-[10px] font-bold py-2.5 rounded-xl text-center shadow-lg shadow-blue-900/20 transition active:scale-95 flex items-center justify-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Konfirmasi & Buat Produk
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @empty
                        <div class="text-center py-8 bg-[#1E1E1E]/50 rounded-xl border border-dashed border-white/5">
                            <p class="text-xs text-gray-500">Tidak ada orderan baru.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <hr class="border-white/5">

            {{-- 2. TUGAS SAYA --}}
            <div class="fade-in pb-12">
                <div class="flex items-center justify-between mb-3 sticky top-0 bg-[#121212] z-20 py-2">
                    <h3 class="text-sm text-yellow-500 font-bold uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
                        Tugas Saya ({{ $sedangDikerjakan->count() }})
                    </h3>
                </div>
                <div class="space-y-3">
                    @forelse($sedangDikerjakan as $proses)
                    <div class="bg-[#1E1E1E] rounded-2xl p-4 border-l-4 border-l-yellow-500 border border-white/5 relative group hover:bg-[#252a33] transition shadow-lg">
                        <div class="flex gap-4">
                            <div class="w-16 h-16 bg-[#252525] rounded-xl flex items-center justify-center text-gray-500 flex-shrink-0 border border-white/5 overflow-hidden relative">
                                @if(!empty($proses->product->foto))
                                    <img src="{{ $proses->product->foto }}" alt="Img" class="w-full h-full object-cover opacity-80">
                                @else
                                    <span class="text-[10px] font-bold uppercase">{{ substr(optional($proses->product)->nama_produk ?? 'NA', 0, 3) }}</span>
                                @endif
                                <div class="absolute bottom-0 left-0 w-full h-1 bg-yellow-500 animate-pulse"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <span class="text-[10px] text-gray-400 bg-white/5 px-2 py-0.5 rounded mb-1 inline-flex items-center gap-1">
                                        ⏱ {{ \Carbon\Carbon::parse($proses->tanggal_mulai_proses)->diffForHumans(null, true) }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 font-mono">{{ $proses->kode_spk }}</span>
                                </div>
                                <h4 class="text-sm font-bold text-white truncate">{{ optional($proses->product)->nama_produk ?? 'Produk Dihapus' }}</h4>
                                <p class="text-[10px] text-gray-500 mt-0.5">Target: <span class="text-white font-bold">{{ $proses->jumlah_produksi }} Pcs</span></p>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/5 flex items-center justify-between gap-3">
                            <div class="text-[10px] text-gray-500">
                                Mulai: {{ \Carbon\Carbon::parse($proses->tanggal_mulai_proses)->format('H:i') }}
                            </div>
                            <a href="{{ route('operator.tugas.detail', $proses->id) }}" class="flex-1 bg-yellow-600 hover:bg-yellow-500 text-white text-[10px] font-bold py-2 rounded-xl flex items-center justify-center gap-2 transition shadow-lg shadow-yellow-900/20">
                                Update Status 
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                    @empty
                        <div class="text-center py-8 bg-[#1E1E1E]/50 rounded-xl border border-dashed border-white/5">
                            <p class="text-xs text-gray-500">Keranjang tugas Anda kosong.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <hr class="border-white/5">

            {{-- 3. RIWAYAT SELESAI --}}
            <div class="fade-in pb-6">
                <div class="flex items-center justify-between mb-3 sticky top-0 bg-[#121212] z-20 py-2">
                    <h3 class="text-sm text-green-500 font-bold uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Riwayat Selesai
                    </h3>
                </div>
                <div class="space-y-3">
                    @forelse($riwayatSelesai as $riwayat)
                    <div class="bg-[#1E1E1E] rounded-xl p-4 border border-white/5 flex gap-4 opacity-75 hover:opacity-100 transition">
                        <div class="w-12 h-12 rounded-full bg-green-500/10 border border-green-500/20 flex items-center justify-center text-green-500 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-white truncate">{{ $riwayat->product->nama_produk ?? '-' }}</h4>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-[10px] text-gray-400 bg-white/5 px-2 py-0.5 rounded">{{ $riwayat->jumlah_produksi }} Pcs</span>
                                <span class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($riwayat->tanggal_selesai)->format('d M, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                        <p class="text-center text-xs text-gray-600 py-4">Belum ada riwayat pekerjaan.</p>
                    @endforelse
                </div>
            </div>
        </main>

        {{-- BOTTOM NAV --}}
        @include('components.bottom-nav-operator')

        {{-- MODAL DETAIL PRODUK --}}
        <div x-show="showProductDetail"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/90 backdrop-blur-sm p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             x-cloak>

            <div class="bg-[#1A1A1A] w-full max-w-sm rounded-2xl border border-white/10 shadow-2xl overflow-hidden relative max-h-[80vh] overflow-y-auto">

                <div class="p-6 relative z-10">
                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-white">Detail Produk</h2>
                        <button @click="showProductDetail = false" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Info Produk --}}
                    <div class="flex gap-4 mb-6">
                        <div class="w-16 h-16 bg-[#252525] rounded-xl flex items-center justify-center text-gray-500 flex-shrink-0 border border-white/5 overflow-hidden">
                            @if(!empty(selectedProduct.product.foto))
                                <img src="{{ selectedProduct.product.foto }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-[10px] font-bold uppercase">{{ substr(selectedProduct.product.nama_produk ?? 'NA', 0, 3) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-base font-bold text-white">{{ selectedProduct.product.nama_produk ?? 'Produk Dihapus' }}</h3>
                            <p class="text-xs text-gray-400 font-mono">{{ selectedProduct.kode_spk }}</p>
                            <p class="text-xs text-gray-500 mt-1">Target: <span class="text-white font-bold">{{ selectedProduct.jumlah_produksi }} Pcs</span></p>
                        </div>
                    </div>

                    {{-- Kebutuhan Material --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-300 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Bahan yang Dibutuhkan
                        </h4>

                        <div class="bg-[#252525] rounded-xl border border-white/5 overflow-hidden">
                            @if(selectedProduct.product && selectedProduct.product.boms && selectedProduct.product.boms.length > 0)
                                <div class="divide-y divide-white/5">
                                    @foreach(selectedProduct.product.boms as $bom)
                                    <div class="p-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400 text-xs font-bold">
                                                {{ $loop->iteration }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-white">{{ $bom.material.nama_material ?? 'Material Hilang' }}</p>
                                                <p class="text-[9px] text-gray-500">Stok: {{ $bom.material.stok ?? 0 }} {{ $bom.material.satuan }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs font-bold text-white">
                                                {{ $bom.jumlah_kebutuhan * selectedProduct.jumlah_produksi }} {{ $bom.material.satuan }}
                                            </p>
                                            <p class="text-[9px] text-gray-500">({{ $bom.jumlah_kebutuhan }} per unit)</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 text-center text-gray-500">
                                    <p class="text-xs">Tidak ada data material.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Close Button --}}
                    <button @click="showProductDetail = false" class="w-full mt-6 bg-gray-600 hover:bg-gray-500 text-white font-bold py-3 rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL POPUP RINGKASAN SELESAI --}}
        <div x-show="showSummary"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/90 backdrop-blur-sm p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             x-cloak>

            <div class="bg-[#1A1A1A] w-full max-w-sm rounded-2xl border border-white/10 shadow-2xl overflow-hidden relative">

                {{-- Confetti Decoration --}}
                <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                    <div class="absolute top-[-50%] left-[-50%] w-[200%] h-[200%] bg-[radial-gradient(circle,rgba(34,197,94,0.15)_0%,transparent_50%)]"></div>
                </div>

                <div class="p-6 text-center relative z-10">
                    <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-green-500/30">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>

                    <h2 class="text-2xl font-bold text-white mb-1">Pekerjaan Selesai!</h2>
                    <p class="text-sm text-gray-400">Stok produk telah ditambahkan ke gudang.</p>

                    <div class="mt-6 bg-[#252525] rounded-xl p-4 border border-white/5 text-left flex gap-4">
                        @if(session('task_completed.foto'))
                            <div class="w-16 h-16 bg-black rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ session('task_completed.foto') }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-500 font-mono mb-1">{{ session('task_completed.kode_spk') }}</p>
                            <h3 class="text-sm font-bold text-white line-clamp-2">{{ session('task_completed.nama_produk') }}</h3>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="bg-green-500/20 text-green-400 text-[10px] font-bold px-2 py-0.5 rounded">
                                    +{{ session('task_completed.jumlah') }} Unit
                                </span>
                            </div>
                        </div>
                    </div>

                    <button @click="showSummary = false" class="w-full mt-6 bg-green-600 hover:bg-green-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-green-900/20 transition active:scale-95">
                        Tutup & Lanjut Kerja
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function refreshContent() {
                if(document.activeElement.tagName === 'BUTTON' || document.activeElement.tagName === 'A') return;
                fetch(window.location.href)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.querySelector('#live-container').innerHTML;
                        const oldContent = document.querySelector('#live-container').innerHTML;
                        if(newContent.replace(/\s/g,'') !== oldContent.replace(/\s/g,'')) {
                             // Update logic here if needed
                        }
                    })
            }
            // setInterval(refreshContent, 5000); 
        });
    </script>
</body>
</html>