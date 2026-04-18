<?php 
$page = 'produk-jadi'; // Penanda menu aktif
@include('admin.header') 
?>

<main class="flex-1 flex flex-col relative overflow-hidden bg-black">
    
    <header class="h-20 bg-[#111111] border-b border-white/5 flex items-center justify-between px-8 flex-shrink-0">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="produk-jadi.php" class="hover:text-white transition">Produk Jadi</a>
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white">Buat Baru</span>
            </div>
            <h2 class="text-xl font-bold text-white">Tambah Produk & Resep</h2>
        </div>
        <div class="flex gap-3">
             <button class="px-6 py-2.5 rounded-xl text-xs font-bold text-gray-400 hover:text-white transition">Batal</button>
             <button class="bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-6 py-2.5 rounded-xl transition flex items-center gap-2 shadow-lg shadow-orange-900/20">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Simpan Produk
            </button>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="space-y-6">
                
                <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
                    <label class="text-xs font-bold text-gray-400 mb-4 block">Foto Produk</label>
                    <div class="border-2 border-dashed border-white/10 rounded-xl h-64 flex flex-col items-center justify-center text-gray-500 hover:border-orange-500/50 hover:bg-white/[0.02] transition cursor-pointer">
                        <svg class="w-10 h-10 mb-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-medium">Klik untuk upload foto</span>
                        <span class="text-[10px] text-gray-600 mt-1">JPG, PNG max 2MB</span>
                    </div>
                </div>

                <div class="bg-[#151515] border border-white/5 rounded-2xl p-6">
                    <div class="flex items-center gap-2 mb-6 pb-2 border-b border-white/5">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <h3 class="text-sm font-bold text-white">Informasi Produk</h3>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">Nama Produk</label>
                            <input type="text" value="Contoh: Rome Bag - Tan" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:border-orange-500 outline-none">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">Kategori</label>
                                <select class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:border-orange-500 outline-none">
                                    <option>Tas Wanita</option>
                                    <option>Dompet</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">Kode SKU</label>
                                <div class="relative">
                                    <input type="text" value="AUTO" disabled class="w-full bg-[#0A0A0A] border border-white/5 rounded-xl py-3 px-4 text-sm text-gray-500 cursor-not-allowed">
                                    <svg class="w-4 h-4 text-gray-600 absolute right-3 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">Harga Jual (IDR)</label>
                            <input type="number" value="0" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl py-3 px-4 text-sm text-white focus:border-orange-500 outline-none">
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-[#151515] border border-white/5 rounded-2xl p-6 flex flex-col h-full">
                
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            <h3 class="text-sm font-bold text-white">Resep Bahan (BOM)</h3>
                        </div>
                        <p class="text-[10px] text-gray-500">Tentukan bahan baku untuk 1 unit produk.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Estimasi HPP</p>
                        <h2 class="text-2xl font-bold text-teal-500">Rp 0</h2>
                    </div>
                </div>

                <div class="bg-[#1A1A1A] p-4 rounded-xl border border-white/5 mb-4">
                    <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">Tambah Bahan Baku</label>
                    <div class="flex gap-2">
                        <input type="text" placeholder="Cari bahan (cth: Kulit, Benang)..." class="flex-1 bg-black border border-white/10 rounded-lg py-2 px-3 text-xs text-white focus:border-teal-500 outline-none">
                        <input type="number" placeholder="Qty" class="w-20 bg-black border border-white/10 rounded-lg py-2 px-3 text-xs text-white focus:border-teal-500 outline-none">
                        <button class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold px-4 rounded-lg transition">
                            + Tambah
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-hidden border border-white/5 rounded-xl mb-4">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#111111] text-gray-500 font-bold uppercase text-[9px]">
                            <tr>
                                <th class="p-3 pl-4">Nama Bahan</th>
                                <th class="p-3 text-center">Kebutuhan</th>
                                <th class="p-3 text-right">Biaya Satuan</th>
                                <th class="p-3 text-right">Subtotal</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5 bg-[#1A1A1A]">
                            <tr class="group hover:bg-[#202020]">
                                <td class="p-3 pl-4">
                                    <span class="font-bold text-white block">Kulit Sapi Full Grain</span>
                                    <span class="text-[9px] text-gray-500">Kategori: Bahan Baku</span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="bg-black border border-white/10 px-2 py-1 rounded text-white font-mono">2.5</span>
                                    <span class="text-[9px] text-gray-500 ml-1">Feet</span>
                                </td>
                                <td class="p-3 text-right text-gray-400">Rp 45.000</td>
                                <td class="p-3 text-right font-bold text-white">Rp 112.500</td>
                                <td class="p-3 text-center">
                                    <button class="text-gray-600 hover:text-red-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                             <tr class="group hover:bg-[#202020]">
                                <td class="p-3 pl-4">
                                    <span class="font-bold text-white block">Resleting YKK Gold (30cm)</span>
                                    <span class="text-[9px] text-gray-500">Kategori: Aksesoris</span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="bg-black border border-white/10 px-2 py-1 rounded text-white font-mono">1</span>
                                    <span class="text-[9px] text-gray-500 ml-1">Pcs</span>
                                </td>
                                <td class="p-3 text-right text-gray-400">Rp 12.500</td>
                                <td class="p-3 text-right font-bold text-white">Rp 12.500</td>
                                <td class="p-3 text-center">
                                    <button class="text-gray-600 hover:text-red-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                             <tr class="group hover:bg-[#202020]">
                                <td class="p-3 pl-4">
                                    <span class="font-bold text-white block">Benang Nilon Waxed</span>
                                    <span class="text-[9px] text-gray-500">Kategori: Bahan Baku</span>
                                </td>
                                <td class="p-3 text-center">
                                    <span class="bg-black border border-white/10 px-2 py-1 rounded text-white font-mono">10</span>
                                    <span class="text-[9px] text-gray-500 ml-1">Mtr</span>
                                </td>
                                <td class="p-3 text-right text-gray-400">Rp 500</td>
                                <td class="p-3 text-right font-bold text-white">Rp 5.000</td>
                                <td class="p-3 text-center">
                                    <button class="text-gray-600 hover:text-red-500"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-auto border-t border-white/10 pt-4 space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Total Biaya Bahan (HPP)</span>
                        <span class="text-white font-bold">Rp 130.000</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400">Biaya Tenaga Kerja (Est)</span>
                        <span class="text-gray-600 italic">Input Manual (Opsional)</span>
                    </div>
                    <div class="mt-4 p-3 bg-teal-900/20 border border-teal-500/20 rounded-lg flex justify-between items-center">
                        <span class="text-xs font-bold text-teal-400">Margin Keuntungan (vs Harga Jual)</span>
                        <span class="text-sm font-black text-white">+ Rp 520.000 (80%)</span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
</body>
</html>