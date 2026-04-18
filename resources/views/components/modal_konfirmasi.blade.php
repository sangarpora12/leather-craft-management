<div x-show="showConfirmModal" style="display: none;"
     class="fixed inset-0 z-[500] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 transition-opacity"
     x-transition.opacity>
    
    <form @submit.prevent="submitForm($event)" 
          class="bg-[#1A1A1A] border border-white/10 rounded-2xl w-full max-w-lg shadow-2xl">
        
        <div class="p-5 border-b border-white/10 flex justify-between bg-[#222] rounded-t-2xl">
            <h3 class="text-xl font-bold text-white">Konfirmasi Perintah Produksi</h3>
            <button type="button" @click="showConfirmModal = false" class="text-gray-400 hover:text-white">✕</button>
        </div>

        <div class="p-6 space-y-4">
            <h4 class="text-sm font-bold text-gray-300">Total Item Produksi: <span class="text-orange-500 text-lg" x-text="cart.length"></span> Jenis Produk</h4>
            
            <div>
                <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Operator Penanggung Jawab</label>
                <select x-model="form.operator_id" name="operator_id" required 
                        class="w-full bg-black border border-white/10 text-white text-xs rounded-lg p-2.5 outline-none focus:border-orange-500">
                    <option value="" disabled selected>-- Pilih Operator --</option>
                    <template x-for="op in operators" :key="op.id">
                        <option :value="op.id" x-text="op.name"></option>
                    </template>
                </select>
                <p x-show="operators.length === 0" class="text-[10px] text-red-500 mt-1">* Data Operator Kosong di Database. (Cek database atau role user)</p>
            </div>

            <div>
                <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Target Selesai</label>
                <input type="date" x-model="form.target_selesai" name="target_selesai" required
                       class="w-full bg-black border border-white/10 text-white text-xs rounded-lg p-2.5 outline-none focus:border-orange-500">
            </div>

            <div>
                <label class="text-[10px] text-gray-500 font-bold uppercase mb-1 block">Catatan (Opsional)</label>
                <textarea x-model="form.catatan" name="catatan" rows="2" placeholder="Catatan tambahan..." 
                          class="w-full bg-black border border-white/10 text-white text-xs rounded-lg p-2.5 outline-none focus:border-orange-500 resize-none"></textarea>
            </div>
        </div>

        <div class="p-5 border-t border-white/10 flex justify-end">
            <button type="submit" 
                    :disabled="cart.length === 0 || !form.operator_id || !form.target_selesai"
                    class="bg-orange-600 hover:bg-orange-500 text-white font-bold py-3.5 px-6 rounded-xl text-sm transition disabled:opacity-50">
                Kirim Perintah Produksi
            </button>
        </div>
        
        <template x-for="(item, index) in cart" :key="index">
            <div>
                <input type="hidden" :name="'items['+index+'][produk_id]'" :value="item.id">
                <input type="hidden" :name="'items['+index+'][jumlah]'" :value="item.qty">
            </div>
        </template>
    </form>
</div>