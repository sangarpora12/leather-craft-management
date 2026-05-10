@php
    $page = 'produk-jadi';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Produk Jadi - Berliano Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
    </style>
</head>
<body class="bg-black text-white flex h-screen overflow-hidden">

    @include('components.sidebar')

    <main class="flex-1 overflow-y-auto p-8 md:ml-64 bg-black">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b border-white/10 pb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">Master Data Produk Jadi</h2>
                <p class="text-gray-400 text-sm mt-1">Katalog produk, manajemen stok, dan harga jual.</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <div class="relative hidden md:block group">
                    <input type="text" placeholder="Cari produk..." class="bg-[#1A1A1A] border border-white/10 rounded-lg py-2.5 px-4 pl-10 text-sm focus:outline-none focus:border-orange-500 w-64 transition-all group-hover:bg-[#222]">
                    <span class="absolute left-3 top-3 text-gray-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                </div>
                <button class="bg-orange-600 hover:bg-orange-500 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-orange-900/20 flex items-center gap-2 transition w-full md:w-auto justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Produk
                </button>
            </div>
        </div>

        {{-- FILTER KATEGORI DINAMIS --}}
        <div class="flex gap-2 mb-8 overflow-x-auto pb-2 no-scrollbar">
            <a href="{{ route('admin.produk-jadi.index') }}" 
               class="px-5 py-2 {{ !request('kategori') ? 'bg-white text-black' : 'bg-[#1A1A1A] hover:bg-[#252525] border border-white/10 text-gray-300' }} rounded-full text-xs font-bold whitespace-nowrap shadow-md transition">
                Semua
            </a>
            @foreach($kategoriList as $kategori)
                <a href="{{ route('admin.produk-jadi.index', ['kategori' => $kategori]) }}" 
                   class="px-5 py-2 {{ request('kategori') == $kategori ? 'bg-white text-black' : 'bg-[#1A1A1A] hover:bg-[#252525] border border-white/10 text-gray-300' }} rounded-full text-xs font-medium whitespace-nowrap transition">
                    {{ $kategori }}
                </a>
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all shadow-sm hover:shadow-md">
                <div class="h-48 bg-[#222] relative overflow-hidden flex items-center justify-center">
                    <img src="https://images.unsplash.com/photo-1590874103328-eac38a683ce7?q=80&w=400&auto=format&fit=crop" 
                         alt="Rome Bag" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition duration-500">
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] font-mono font-bold text-white border border-white/10">PRD-TT-001</div>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-white text-base group-hover:text-orange-500 transition">Rome Bag</h3>
                        <p class="text-xs text-gray-500 mt-1">Varian: Coklat Tua</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 border-t border-white/5 pt-4 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Stok</p>
                            <p class="font-bold text-white text-lg">30</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Harga</p>
                            <p class="font-bold text-orange-500">650rb</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button class="flex-1 py-2 border border-white/10 rounded-lg text-xs font-bold text-gray-300 hover:bg-white/5 transition">Edit</button>
                        <button class="flex-1 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-xs font-bold text-white transition">Detail</button>
                    </div>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all shadow-sm hover:shadow-md">
                <div class="h-48 bg-[#222] relative overflow-hidden flex items-center justify-center">
                    <img src="https://images.unsplash.com/photo-1584917865442-de89df76afd3?q=80&w=400&auto=format&fit=crop" 
                         alt="Ades Bag" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition duration-500">
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] font-mono font-bold text-white border border-white/10">PRD-TT-002</div>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-white text-base group-hover:text-orange-500 transition">Ades Bag</h3>
                        <p class="text-xs text-gray-500 mt-1">Varian: Hitam Premium</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 border-t border-white/5 pt-4 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Stok</p>
                            <p class="font-bold text-white text-lg">20</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Harga</p>
                            <p class="font-bold text-orange-500">450rb</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button class="flex-1 py-2 border border-white/10 rounded-lg text-xs font-bold text-gray-300 hover:bg-white/5 transition">Edit</button>
                        <button class="flex-1 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-xs font-bold text-white transition">Detail</button>
                    </div>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all shadow-sm hover:shadow-md">
                <div class="h-48 bg-[#222] relative overflow-hidden flex items-center justify-center">
                    <img src="https://images.unsplash.com/photo-1627123424574-181ce5171c98?q=80&w=400&auto=format&fit=crop" 
                         alt="Slim Cardholder" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition duration-500">
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] font-mono font-bold text-white border border-white/10">PRD-CRD-09</div>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-white text-base group-hover:text-orange-500 transition">Slim Cardholder</h3>
                        <p class="text-xs text-gray-500 mt-1">Varian: Leather Brown</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 border-t border-white/5 pt-4 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Stok</p>
                            <p class="font-bold text-white text-lg">100</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Harga</p>
                            <p class="font-bold text-orange-500">250rb</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button class="flex-1 py-2 border border-white/10 rounded-lg text-xs font-bold text-gray-300 hover:bg-white/5 transition">Edit</button>
                        <button class="flex-1 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-xs font-bold text-white transition">Detail</button>
                    </div>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-red-900/30 overflow-hidden group hover:border-red-500/50 transition-all shadow-sm hover:shadow-md">
                <div class="h-48 bg-[#222] relative overflow-hidden flex items-center justify-center">
                    <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?q=80&w=400&auto=format&fit=crop" 
                         alt="Weekender" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition duration-500">
                    <div class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] font-mono font-bold text-white border border-white/10">PRD-TRV-12</div>
                    <div class="absolute bottom-3 left-3 bg-red-500 text-white px-2 py-0.5 rounded text-[10px] font-bold shadow-lg animate-pulse">Stok Menipis</div>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <h3 class="font-bold text-white text-base group-hover:text-red-500 transition">Weekender Bag</h3>
                        <p class="text-xs text-gray-500 mt-1">Varian: Vintage Brown</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 border-t border-white/5 pt-4 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Stok</p>
                            <p class="font-bold text-red-500 text-lg">2</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">Harga</p>
                            <p class="font-bold text-orange-500">2.1jt</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button class="flex-1 py-2 border border-white/10 rounded-lg text-xs font-bold text-gray-300 hover:bg-white/5 transition">Edit</button>
                        <button class="flex-1 py-2 bg-white/5 hover:bg-white/10 rounded-lg text-xs font-bold text-white transition">Detail</button>
                    </div>
                </div>
            </div>

        </div>

    </main>

</body>
</html>