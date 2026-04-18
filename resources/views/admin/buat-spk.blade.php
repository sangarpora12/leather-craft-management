@php
    $page = 'spk'; // Penanda menu aktif
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat SPK - Berliano Admin</title>
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

    <main class="flex-1 overflow-y-auto p-6 md:p-8 md:ml-64 bg-black">
        
        <div class="flex justify-between items-start mb-8 border-b border-white/10 pb-6">
            <div>
                <h2 class="text-2xl font-bold text-white">Buat Perintah Produksi (SPK)</h2>
                <p class="text-gray-400 text-sm mt-1">Pilih produk dan tentukan target produksi untuk operator.</p>
            </div>
            <a href="{{ route('admin.monitoring.index') }}" class="px-4 py-2 border border-white/10 hover:bg-white/5 text-gray-300 rounded-xl text-sm font-bold transition">
                Batal
            </a>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
                
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-white">Pilih Produk Kritis</h3>
                    <span class="text-[10px] bg-red-500/10 text-red-500 border border-red-500/20 px-2 py-1 rounded">3 Item Perlu Restock</span>
                </div>

                <div class="bg-[#1A1A1A] p-4 rounded-xl border border-white/10 flex gap-4 group hover:border-white/30 transition">
                    <div class="w-16 h-16 bg-[#222] rounded-lg flex-shrink-0 flex items-center justify-center text-xs font-bold text-gray-500">IMG</div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-white text-sm">Rome Bag</h4>
                            <span class="text-[10px] text-gray-500 font-mono">R-001</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">Warna: Coklat Tua</p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-[10px] font-bold text-red-500 bg-red-500/10 px-2 py-0.5 rounded">Stok Kritis: 3 Pcs</span>
                            <button class="text-xs bg-white text-black px-3 py-1.5 rounded font-bold hover:bg-gray-200 transition">
                                + Tambah ke SPK
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1A1A1A] p-4 rounded-xl border border-orange-500/30 flex gap-4 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 bg-orange-600 w-16 h-16 blur-xl opacity-20"></div>
                    <div class="w-16 h-16 bg-[#222] rounded-lg flex-shrink-0 flex items-center justify-center text-xs font-bold text-gray-500">IMG</div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-white text-sm">Milano Wallet</h4>
                            <span class="text-[10px] text-gray-500 font-mono">M-202</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">Warna: Hitam Premium</p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-[10px] font-bold text-yellow-500 bg-yellow-500/10 px-2 py-0.5 rounded">Menipis: 12 Pcs</span>
                            <button class="text-xs bg-orange-600/20 text-orange-500 px-3 py-1.5 rounded font-bold border border-orange-500/20 cursor-default">
                                ✓ Ditambahkan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-[#1A1A1A] p-4 rounded-xl border border-white/10 flex gap-4 group hover:border-white/30 transition">
                    <div class="w-16 h-16 bg-[#222] rounded-lg flex-shrink-0 flex items-center justify-center text-xs font-bold text-gray-500">IMG</div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-white text-sm">Belt Classic</h4>
                            <span class="text-[10px] text-gray-500 font-mono">B-99</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">Warna: Tan</p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-[10px] font-bold text-yellow-500 bg-yellow-500/10 px-2 py-0.5 rounded">Menipis: 20 Pcs</span>
                            <button class="text-xs bg-white text-black px-3 py-1.5 rounded font-bold hover:bg-gray-200 transition">
                                + Tambah ke SPK
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex-1 bg-[#1A1A1A] rounded-xl border border-white/10 flex flex-col overflow-hidden">
                
                <div class="p-6 border-b border-white/10 bg-[#151515]">
                    <h3 class="font-bold text-lg text-white">Formulir SPK Baru</h3>
                </div>

                <div class="p-6 space-y-6">
                    
                    <div class="border border-white/10 rounded-xl p-4 bg-black/20">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-white">Rome Bag - Coklat</h4>
                                <p class="text-[10px] text-gray-500 font-mono">SKU: R-001</p>
                            </div>
                            <div class="flex items-center gap-3 bg-[#111] rounded-lg p-1 border border-white/10">
                                <button class="w-6 h-6 flex items-center justify-center hover:bg-white/10 rounded text-gray-400">-</button>
                                <span class="text-sm font-bold text-white w-8 text-center">5</span>
                                <button class="w-6 h-6 flex items-center justify-center hover:bg-white/10 rounded text-white">+</button>
                            </div>
                        </div>

                        <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[10px] font-bold text-green-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Bahan Baku Cukup
                                </span>
                                <span class="text-[10px] text-gray-400">Estimasi: 5 Unit</span>
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-gray-400">Kulit Sapi (2.5m)</span>
                                    <span class="text-green-400 font-bold">Ready (120m)</span>
                                </div>
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-gray-400">Resleting (5pcs)</span>
                                    <span class="text-green-400 font-bold">Ready (50pcs)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-red-500/30 rounded-xl p-4 bg-red-900/5 relative">
                        <button class="absolute top-2 right-2 text-gray-500 hover:text-white">✕</button>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-bold text-white">Milano Wallet</h4>
                                <p class="text-[10px] text-gray-500 font-mono">SKU: M-202</p>
                            </div>
                            <div class="flex items-center gap-3 bg-[#111] rounded-lg p-1 border border-white/10">
                                <button class="w-6 h-6 flex items-center justify-center hover:bg-white/10 rounded text-gray-400">-</button>
                                <span class="text-sm font-bold text-white w-8 text-center">150</span>
                                <button class="w-6 h-6 flex items-center justify-center hover:bg-white/10 rounded text-white">+</button>
                            </div>
                        </div>

                        <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-3">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[10px] font-bold text-red-400 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Bahan Kurang!
                                </span>
                                <span class="text-[10px] text-gray-400">Estimasi: 150 Unit</span>
                            </div>
                            <div class="space-y-1 mb-2">
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-gray-400">Kulit Sapi (15m)</span>
                                    <span class="text-green-400 font-bold">Ready (120m)</span>
                                </div>
                                <div class="flex justify-between text-[10px]">
                                    <span class="text-gray-400">Benang Nilon (50 Rol)</span>
                                    <span class="text-red-400 font-bold">Kurang (-48 Rol)</span>
                                </div>
                            </div>
                            <a href="{{ route('admin.pengadaan.index') }}" class="block text-center bg-red-500 hover:bg-red-600 text-white text-[10px] font-bold py-1.5 rounded transition">
                                Buat Request Pengadaan Bahan
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-white/10">
                        <div>
                            <label class="block text-[10px] text-gray-500 uppercase font-bold mb-1.5">Tugaskan Operator</label>
                            <div class="relative">
                                <select class="w-full bg-black border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white appearance-none focus:border-orange-500 outline-none cursor-pointer">
                                    <option>Pilih Operator...</option>
                                    <option>Anndi</option>
                                    <option>Zulkifli</option>
                                    <option>Budi</option>
                                </select>
                                <svg class="w-4 h-4 text-gray-500 absolute right-3 top-3 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] text-gray-500 uppercase font-bold mb-1.5">Target Selesai</label>
                            <input type="date" class="w-full bg-black border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:border-orange-500 outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] text-gray-500 uppercase font-bold mb-1.5">Catatan Produksi</label>
                            <textarea rows="2" class="w-full bg-black border border-white/10 rounded-lg px-3 py-2.5 text-sm text-white focus:border-orange-500 outline-none" placeholder="Instruksi khusus..."></textarea>
                        </div>
                    </div>

                </div>

                <div class="p-6 bg-[#151515] border-t border-white/10">
                    <button class="w-full bg-gray-700 text-gray-400 font-bold py-3 rounded-xl cursor-not-allowed flex items-center justify-center gap-2" disabled>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Proses SPK (Error: Bahan Kurang)
                    </button>
                    <p class="text-[10px] text-center text-gray-500 mt-2">*Selesaikan masalah stok bahan terlebih dahulu.</p>
                </div>

            </div>

        </div>

    </main>

</body>
</html>