@php
    $page = 'pakai-produk'; // Penanda menu aktif
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pengeluaran - Berliano Admin</title>
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
                <h2 class="text-2xl font-bold text-white">Halaman Produk Keluar (Manual)</h2>
                <p class="text-gray-400 text-sm mt-1">Formulir khusus untuk barang rusak, sampel, atau donasi.</p>
            </div>
            <div class="w-10 h-10 rounded-full bg-orange-600 flex items-center justify-center font-bold text-white shadow-lg border-2 border-[#111]">
                AD
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6">
                    <h3 class="font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        Input Pengurangan Stok
                    </h3>

                    <form action="#" class="space-y-6">
                        
                        <div>
                            <label class="block text-[10px] text-gray-500 uppercase font-bold mb-2">Produk Yang Akan Dikeluarkan</label>
                            <div class="relative">
                                <input type="text" placeholder="Ketik nama produk atau SKU..." class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-orange-500 outline-none transition">
                                <span class="absolute right-4 top-3.5 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold mb-2">Jumlah Keluar</label>
                                <div class="flex items-center">
                                    <input type="number" placeholder="0" class="w-full bg-black border border-white/10 rounded-l-xl px-4 py-3 text-sm text-white focus:border-orange-500 outline-none transition text-center font-bold text-lg">
                                    <span class="bg-[#222] border border-white/10 border-l-0 rounded-r-xl px-4 py-3.5 text-sm text-gray-400 font-bold">Pcs</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] text-gray-500 uppercase font-bold mb-2">Alasan Pengeluaran</label>
                                <div class="relative">
                                    <select class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-orange-500 outline-none appearance-none cursor-pointer">
                                        <option value="" disabled selected>-- Pilih Alasan --</option>
                                        <option value="reject">Barang Rusak (Reject)</option>
                                        <option value="sample">Sampel Marketing</option>
                                        <option value="donation">Donasi / Hibah</option>
                                        <option value="opname">Selisih Stok (Opname)</option>
                                    </select>
                                    <svg class="w-4 h-4 text-gray-500 absolute right-4 top-3.5 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] text-gray-500 uppercase font-bold mb-2">Catatan Tambahan</label>
                            <textarea rows="3" class="w-full bg-black border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-orange-500 outline-none transition" placeholder="Jelaskan detail kerusakan atau tujuan pengeluaran..."></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                            <button type="button" class="px-6 py-2.5 rounded-xl border border-white/10 text-gray-400 font-bold text-sm hover:bg-white/5 transition">Batal</button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-600 text-white font-bold text-sm hover:bg-orange-500 shadow-lg shadow-orange-900/20 transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Data
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <div class="flex flex-col gap-6">
                
                <div class="bg-gradient-to-br from-orange-900/40 to-black border border-orange-500/30 rounded-xl p-6 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-24 h-24 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z" clip-rule="evenodd"/></svg>
                    </div>
                    <p class="text-[10px] text-orange-200 uppercase font-bold tracking-widest mb-1">Total Keluar Hari Ini</p>
                    <h3 class="text-4xl font-extrabold text-white">8 <span class="text-sm font-medium text-gray-400">Item</span></h3>
                </div>

                <div class="bg-[#1A1A1A] rounded-xl border border-white/10 flex-1 flex flex-col overflow-hidden">
                    <div class="p-5 border-b border-white/10 bg-[#151515]">
                        <h3 class="font-bold text-white text-sm">Riwayat Hari Ini</h3>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto p-5">
                        <div class="relative border-l border-white/10 ml-2 space-y-8">
                            
                            <div class="relative pl-6">
                                <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-red-500 ring-4 ring-[#1A1A1A]"></span>
                                <p class="text-[10px] font-bold text-gray-500 mb-1">10:45</p>
                                <h4 class="font-bold text-white text-sm">5 Pcs Sling Bag</h4>
                                <p class="text-xs text-red-400 mt-0.5">Barang Rusak (Reject QC)</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-gray-700 flex items-center justify-center text-[9px]">Z</div>
                                    <span class="text-[10px] text-gray-500">Oleh: Zulkifli</span>
                                </div>
                            </div>

                            <div class="relative pl-6">
                                <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-blue-500 ring-4 ring-[#1A1A1A]"></span>
                                <p class="text-[10px] font-bold text-gray-500 mb-1">09:12</p>
                                <h4 class="font-bold text-white text-sm">2 Pcs Wallet</h4>
                                <p class="text-xs text-blue-400 mt-0.5">Sample Marketing</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-gray-700 flex items-center justify-center text-[9px]">B</div>
                                    <span class="text-[10px] text-gray-500">Oleh: Admin Budi</span>
                                </div>
                            </div>

                            <div class="relative pl-6">
                                <span class="absolute -left-[5px] top-1 w-2.5 h-2.5 rounded-full bg-yellow-500 ring-4 ring-[#1A1A1A]"></span>
                                <p class="text-[10px] font-bold text-gray-500 mb-1">08:30</p>
                                <h4 class="font-bold text-white text-sm">1 Pcs Belt</h4>
                                <p class="text-xs text-yellow-500 mt-0.5">Selisih Stok (Opname)</p>
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-gray-700 flex items-center justify-center text-[9px]">G</div>
                                    <span class="text-[10px] text-gray-500">Oleh: Gudang</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </main>

</body>
</html>