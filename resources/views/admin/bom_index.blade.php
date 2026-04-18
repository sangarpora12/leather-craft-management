@php
    $page = 'bom'; // Penanda menu aktif
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill of Materials - Berliano Admin</title>
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
                <h2 class="text-2xl font-bold text-white">Manajemen Bill of Materials</h2>
                <p class="text-gray-400 text-sm mt-1">Resep bahan baku untuk setiap unit produk jadi.</p>
            </div>
            
            <div class="flex gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-64">
                    <input type="text" placeholder="Cari Resep Produk..." class="bg-[#1A1A1A] border border-white/10 rounded-lg py-2.5 px-4 pl-10 text-sm focus:outline-none focus:border-orange-500 w-full transition">
                    <span class="absolute left-3 top-3 text-gray-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                </div>
                <button class="bg-orange-600 hover:bg-orange-500 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-orange-900/20 flex items-center gap-2 whitespace-nowrap transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah BOM Baru
                </button>
            </div>
        </div>

        <div class="flex gap-2 mb-8">
            <button class="px-4 py-2 bg-white text-black rounded-lg text-xs font-bold shadow-md">Semua</button>
            <button class="px-4 py-2 bg-[#1A1A1A] hover:bg-[#252525] border border-white/10 text-gray-400 hover:text-white rounded-lg text-xs font-medium transition">Sudah Ada BOM</button>
            <button class="px-4 py-2 bg-[#1A1A1A] hover:bg-[#252525] border border-white/10 text-gray-400 hover:text-white rounded-lg text-xs font-medium transition">Belum Ada</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all">
                <div class="h-40 bg-[#222] relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1590874103328-eac38a683ce7?q=80&w=400&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10">PRO-T-001</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-white text-lg leading-tight mb-1">Rome Bag</h3>
                    <p class="text-xs text-gray-500 mb-4">Coklat Tua (Dark Brown)</p>

                    <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-white/5 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                            <p class="text-sm font-bold text-blue-400">12 Item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                            <p class="text-sm font-bold text-green-400">Rp 250k</p>
                        </div>
                    </div>

                    <button class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Resep
                    </button>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all">
                <div class="h-40 bg-[#222] relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1627123424574-181ce5171c98?q=80&w=400&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10">PRO-D-005</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-white text-lg leading-tight mb-1">Milano Wallet</h3>
                    <p class="text-xs text-gray-500 mb-4">Hitam Premium</p>

                    <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-white/5 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                            <p class="text-sm font-bold text-blue-400">8 Item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                            <p class="text-sm font-bold text-green-400">Rp 75k</p>
                        </div>
                    </div>

                    <button class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Resep
                    </button>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all">
                <div class="h-40 bg-[#222] relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?q=80&w=400&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10">PRO-K-009</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-white text-lg leading-tight mb-1">Key Holder</h3>
                    <p class="text-xs text-gray-500 mb-4">Tan (Custom)</p>

                    <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-white/5 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                            <p class="text-sm font-bold text-blue-400">4 Item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                            <p class="text-sm font-bold text-green-400">Rp 25k</p>
                        </div>
                    </div>

                    <button class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Resep
                    </button>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-dashed border-white/20 overflow-hidden group hover:border-white/40 transition-all opacity-80 hover:opacity-100">
                <div class="h-40 bg-[#222] relative overflow-hidden flex items-center justify-center">
                    <img src="https://images.unsplash.com/photo-1591561954557-26941169b49e?q=80&w=400&auto=format&fit=crop" class="w-full h-full object-cover grayscale opacity-50">
                    <span class="absolute px-3 py-1 bg-black/70 rounded-full text-xs font-bold text-gray-400 border border-white/10">Belum Ada BOM</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-white text-lg leading-tight mb-1">Canvas Tote</h3>
                    <p class="text-xs text-gray-500 mb-4">Natural White</p>

                    <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-dashed border-white/10 mb-4">
                        <p class="text-xs text-gray-600 text-center w-full italic">Komponen belum diatur</p>
                    </div>

                    <button class="w-full py-2.5 bg-white/5 hover:bg-orange-600 hover:text-white border border-white/10 hover:border-orange-600 rounded-lg text-xs font-bold text-gray-400 flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Resep
                    </button>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all">
                <div class="h-40 bg-[#222] relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1624222247344-550fb60583dc?q=80&w=400&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10">PRO-I-012</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-white text-lg leading-tight mb-1">Classic Belt</h3>
                    <p class="text-xs text-gray-500 mb-4">Tan Leather</p>

                    <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-white/5 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                            <p class="text-sm font-bold text-blue-400">3 Item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                            <p class="text-sm font-bold text-green-400">Rp 95k</p>
                        </div>
                    </div>

                    <button class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Resep
                    </button>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all">
                <div class="h-40 bg-[#222] relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?q=80&w=400&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10">PRO-B-033</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-white text-lg leading-tight mb-1">Exec Briefcase</h3>
                    <p class="text-xs text-gray-500 mb-4">Black Onyx</p>

                    <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-white/5 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                            <p class="text-sm font-bold text-blue-400">18 Item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                            <p class="text-sm font-bold text-green-400">Rp 450k</p>
                        </div>
                    </div>

                    <button class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Resep
                    </button>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all">
                <div class="h-40 bg-[#222] relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1627123424574-181ce5171c98?q=80&w=400&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10">PRO-D-009</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-white text-lg leading-tight mb-1">Slim Card</h3>
                    <p class="text-xs text-gray-500 mb-4">Navy Blue</p>

                    <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-white/5 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                            <p class="text-sm font-bold text-blue-400">4 Item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                            <p class="text-sm font-bold text-green-400">Rp 35k</p>
                        </div>
                    </div>

                    <button class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Resep
                    </button>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all">
                <div class="h-40 bg-[#222] relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?q=80&w=400&auto=format&fit=crop" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                    <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10">PRO-T-015</span>
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-white text-lg leading-tight mb-1">Weekender</h3>
                    <p class="text-xs text-gray-500 mb-4">Vintage Brown</p>

                    <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-white/5 mb-4">
                        <div>
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                            <p class="text-sm font-bold text-blue-400">15 Item</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                            <p class="text-sm font-bold text-green-400">Rp 380k</p>
                        </div>
                    </div>

                    <button class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Resep
                    </button>
                </div>
            </div>

        </div>

    </main>

</body>
</html>