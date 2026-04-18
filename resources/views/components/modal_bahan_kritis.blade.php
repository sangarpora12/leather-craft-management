<div x-show="showBOMDetail" style="display: none;"
     class="fixed inset-0 z-[500] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 transition-opacity"
     x-transition.opacity>
    
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl w-full max-w-3xl shadow-2xl" 
         @click.away="closeBOMDetail()">
        
        <template x-if="detailProduct">
            <div>
                <div class="p-5 border-b border-white/10 flex justify-between bg-red-800/50 rounded-t-2xl">
                    <div>
                        <h3 class="text-xl font-bold text-white">⛔ GAGAL: Batas Stok Bahan Tercapai</h3>
                        <p class="text-xs text-gray-300 mt-1">Tidak bisa menambah kuantitas untuk produk: <span class="text-orange-300" x-text="detailProduct.nama_produk"></span></p>
                    </div>
                    <button @click="closeBOMDetail()" class="text-gray-400 hover:text-white">✕</button>
                </div>

                <div class="p-6">
                    <h4 class="text-sm font-bold text-red-400 uppercase tracking-wider mb-3">Faktor Pembatas Utama (Bottleneck):</h4>
                    
                    <div class="border border-red-900/50 rounded-lg overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-black text-gray-400">
                                <tr>
                                    <th class="p-3">Kode & Nama Bahan</th>
                                    <th class="p-3 text-center">Butuh / pcs</th>
                                    <th class="p-3 text-center">Stok Gudang</th>
                                    <th class="p-3 text-center">Bisa Dibuat</th>
                                    <th class="p-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-900/30 bg-[#151515]">
                                <template x-for="bom in detailProduct.boms" :key="bom.id">
                                    <tr class="hover:bg-white/5 transition"
                                        x-data="{ 
                                            maxPerBom: Math.floor((bom.material ? parseFloat(bom.material.stok) : 0) / parseFloat(bom.quantity_used)),
                                            isBottleneck: Math.floor((bom.material ? parseFloat(bom.material.stok) : 0) / parseFloat(bom.quantity_used)) === getMaxQty(detailProduct)
                                        }">
                                        
                                        <template x-if="isBottleneck || (maxPerBom < getMaxQty(detailProduct) && maxPerBom >= 1) || (getMaxQty(detailProduct) === 0 && maxPerBom < 1)">
                                            <td class="p-3">
                                                <span class="text-white font-bold" x-text="bom.material ? bom.material.nama_material : 'Material Hilang'"></span>
                                                <p class="text-[10px] text-gray-500" x-text="bom.material ? bom.material.kode_material : ''"></p>
                                            </td>
                                            <td class="p-3 text-center text-gray-300">
                                                 <span x-text="parseFloat(bom.quantity_used)"></span> 
                                                <span x-text="bom.unit" class="text-[10px]"></span>
                                            </td>
                                            <td class="p-3 text-center font-mono font-bold">
                                                 <span :class="maxPerBom < 1 ? 'text-red-400' : 'text-white'" x-text="bom.material ? parseFloat(bom.material.stok) : 0"></span>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span x-text="maxPerBom + ' pcs'"></span>
                                            </td>
                                            <td class="p-3 text-center">
                                                <span class="px-2 py-1 rounded text-[10px] font-bold"
                                                      :class="isBottleneck || maxPerBom < 1 ? 'bg-red-500/20 text-red-500' : 'bg-yellow-500/20 text-yellow-500'">
                                                    <span x-text="isBottleneck ? 'PEMBATAS' : (maxPerBom < 1 ? 'HABIS TOTAL' : 'HAMPIR HABIS')"></span>
                                                </span>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-4 bg-red-900/30 text-white font-bold flex justify-between items-center rounded-lg">
                        <span>Batas Maksimal Produksi:</span>
                        <span class="text-xl text-yellow-300" x-text="getMaxQty(detailProduct)"></span>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button @click="closeBOMDetail()" 
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white rounded-xl text-sm font-bold transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </template>
        </div
    </div>
</div>