<div id="modalFilter" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-[#111827] w-[400px] rounded-xl border border-gray-800 shadow-2xl overflow-hidden">
        
        <div class="p-5 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-bold text-lg flex items-center gap-2">
                <span class="text-gray-400">⚙️</span> Filter Produk
            </h3>
            <button onclick="document.getElementById('modalFilter').classList.add('hidden')" class="text-gray-400 hover:text-white">✕</button>
        </div>

        <div class="p-6 space-y-6">
            
            <div>
                <label class="block text-sm font-medium text-gray-400 mb-3">Kategori</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 bg-[#1F2937] p-3 rounded-lg border border-gray-700 cursor-pointer hover:border-orange-500 transition">
                        <input type="checkbox" checked class="accent-orange-500 w-4 h-4">
                        <span class="text-sm">Tas Wanita</span>
                    </label>
                    <label class="flex items-center gap-3 bg-[#1F2937] p-3 rounded-lg border border-gray-700 cursor-pointer hover:border-orange-500 transition">
                        <input type="checkbox" checked class="accent-orange-500 w-4 h-4">
                        <span class="text-sm">Dompet Pria</span>
                    </label>
                    <label class="flex items-center gap-3 bg-[#1F2937] p-3 rounded-lg border border-gray-700 cursor-pointer hover:border-orange-500 transition">
                        <input type="checkbox" checked class="accent-orange-500 w-4 h-4">
                        <span class="text-sm">Ikat Pinggang</span>
                    </label>
                    <label class="flex items-center gap-3 bg-[#1F2937] p-3 rounded-lg border border-gray-700 cursor-pointer hover:border-orange-500 transition">
                        <input type="checkbox" checked class="accent-orange-500 w-4 h-4">
                        <span class="text-sm">Aksesoris</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-3">Rentang Harga</label>
                <div class="flex items-center gap-3">
                    <input type="number" placeholder="Min" class="w-full bg-[#1F2937] border border-gray-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-orange-500 text-center">
                    <span class="text-gray-500">-</span>
                    <input type="number" placeholder="Max" class="w-full bg-[#1F2937] border border-gray-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-orange-500 text-center">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-400 mb-3">Status Stok</label>
                <div class="flex gap-2">
                    <button class="bg-orange-600 text-white text-xs px-4 py-2 rounded-lg font-medium">Semua</button>
                    <button class="bg-[#1F2937] border border-gray-700 text-gray-400 text-xs px-4 py-2 rounded-lg hover:text-white">Aman (>20)</button>
                    <button class="bg-[#1F2937] border border-gray-700 text-gray-400 text-xs px-4 py-2 rounded-lg hover:text-white">Menipis (<10)</button>
                </div>
            </div>

             <div>
                <label class="block text-sm font-medium text-gray-400 mb-3">Urutkan Berdasarkan</label>
                <select class="w-full bg-[#1F2937] border border-gray-700 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-orange-500 appearance-none">
                    <option>Terbaru Ditambahkan</option>
                    <option>Harga Terendah</option>
                    <option>Harga Tertinggi</option>
                </select>
            </div>

        </div>

        <div class="p-5 border-t border-gray-800 flex gap-4">
            <button class="w-1/3 border border-gray-600 text-gray-300 py-3 rounded-lg font-bold hover:bg-gray-800 transition">Reset</button>
            <button class="w-2/3 bg-orange-600 hover:bg-orange-500 text-white py-3 rounded-lg font-bold shadow-lg shadow-orange-500/20 transition">Terapkan Filter</button>
        </div>
    </div>
</div>