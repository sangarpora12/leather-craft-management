

<?php $__env->startSection('title', 'Buat Resep BOM'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto" x-data="bomForm()">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-white">Resep Produksi (BOM)</h2>
            <p class="text-gray-400 text-sm">Atur bahan baku untuk: <span class="text-orange-500 font-bold"><?php echo e($product->nama_produk); ?></span></p>
        </div>
        <a href="<?php echo e(route('admin.bom.index')); ?>" class="text-gray-400 hover:text-white text-sm">Kembali</a>
    </div>

    <form action="<?php echo e(route('admin.bom.store', $product->id)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>

        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6">
            
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-white">Komponen Bahan</h3>
                <div class="flex gap-2">
                    
                    <a href="<?php echo e(route('admin.bahan.index')); ?>" target="_blank" class="px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 text-xs font-bold flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Kelola Master Bahan
                    </a>
                    <button type="button" @click="addRow()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Bahan
                    </button>
                </div>
            </div>

            
            <div class="rounded-lg border border-white/5">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black text-gray-500 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-3 pl-4">Nama Material</th>
                            <th class="p-3 w-32 text-center">Jumlah</th>
                            <th class="p-3 w-32 text-center">Satuan</th>
                            <th class="p-3 w-40 text-right">Estimasi Biaya</th>
                            <th class="p-3 w-16 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-[#111]">
                        <template x-for="(row, index) in rows" :key="row.id">
                            <tr class="hover:bg-white/5 transition">
                                <td class="p-3 pl-4 relative align-bottom">
                                    <!-- [FIX] Custom Searchable Dropdown -->
                                    <div x-data="{ open: false, search: '' }" @click.outside="open = false" class="relative">
                                        
                                        <!-- Trigger Button -->
                                        <div @click="open = !open; search = ''; $nextTick(() => $refs.searchInput.focus())"
                                             class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-3 py-2 text-white text-sm cursor-pointer flex justify-between items-center hover:border-orange-500 transition group">
                                            <span x-text="getMaterialName(row.material_id)" :class="!row.material_id ? 'text-gray-500' : 'text-white font-bold'"></span>
                                            <svg class="w-4 h-4 text-gray-500 group-hover:text-orange-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>

                                        <!-- Dropdown Menu -->
                                        
                                        <div x-show="open" style="display: none;" class="absolute z-50 min-w-[300px] mt-1 bg-[#222] border border-white/10 rounded-lg shadow-2xl max-h-60 overflow-y-auto">
                                            <div class="sticky top-0 bg-[#222] p-2 border-b border-white/10 z-10">
                                                <input x-ref="searchInput" x-model="search" type="text" placeholder="Cari nama bahan..." class="w-full bg-[#111] border border-white/10 rounded px-2 py-1.5 text-xs text-white focus:outline-none focus:border-orange-500 placeholder-gray-600">
                                            </div>
                                            <div class="py-1">
                                                <template x-for="mat in filteredMaterials(search, row.material_id)" :key="mat.id">
                                                     <div @click="row.material_id = mat.id; updateUnit(index); open = false; $nextTick(() => { this.$dispatch('input'); calculateGrandTotal(); })" class="px-3 py-2 cursor-pointer hover:bg-orange-600/20 hover:text-orange-400 transition border-b border-white/5 last:border-0">
                                                        <div class="font-bold text-sm text-gray-200" x-text="mat.nama_material"></div>
                                                        <div class="flex justify-between mt-0.5"><span class="text-[10px] text-gray-500" x-text="'Stok: ' + mat.stok + ' ' + mat.satuan"></span><span class="text-[10px] text-gray-500" x-text="formatRupiah(mat.harga_satuan)"></span></div>
                                                    </div>
                                                </template>
                                                <div x-show="filteredMaterials(search, row.material_id).length === 0" class="px-3 py-3 text-xs text-gray-500 text-center italic">
                                                    Tidak ditemukan. 
                                                    <a href="<?php echo e(route('admin.bahan.index')); ?>" target="_blank" class="text-orange-500 hover:underline block mt-1">Tambah di Master Bahan?</a>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" :name="'material_id[' + index + ']'" x-model="row.material_id" required>
                                    </div>
                                </td>
                                <td class="p-3 align-bottom">
                                    
                                    <input type="number" step="any" :name="'quantity[' + index + ']'" x-model.number="row.quantity" 
                                           min="0.00001"
                                           @input="calculateGrandTotal"
                                           class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-3 py-2 text-white text-center text-sm focus:border-orange-500 outline-none" 
                                           required placeholder="0.0">
                                </td>
                                <td class="p-3 align-bottom">
                                    <input type="text" :name="'unit[' + index + ']'" x-model="row.unit" class="w-full bg-[#222] border border-white/5 rounded-lg px-3 py-2 text-gray-400 text-center text-sm outline-none cursor-not-allowed" readonly>
                                </td>
                                <td class="p-3 text-right font-mono text-gray-300 text-sm align-bottom">
                                    <span x-text="formatRupiah(calculateRowTotal(row))"></span>
                                </td>
                                <td class="p-3 text-center align-bottom">
                                    <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-400 p-1 rounded hover:bg-red-500/10 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        
                        <tr x-show="rows.length === 0">
                            <td colspan="5" class="p-8 text-center text-gray-500 border-t border-dashed border-white/10">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h2m-2 4h.01M7 16h.01"/></svg>
                                    <span>Belum ada bahan baku ditambahkan.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-[#151515] border-t border-white/10">
                        <tr>
                            <td colspan="3" class="p-4 text-right font-bold text-gray-400 uppercase text-xs tracking-wider">Total Estimasi HPP Material</td>
                            <td class="p-4 text-right font-bold text-green-400 text-lg font-mono" x-text="formatRupiah(calculateGrandTotal())"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t border-white/10">
            <a href="<?php echo e(route('admin.bom.index')); ?>" class="px-6 py-3 rounded-xl border border-white/10 text-gray-300 font-bold hover:bg-white/5 transition text-sm">Batal</a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-orange-600 text-white font-bold hover:bg-orange-500 shadow-lg shadow-orange-900/20 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Resep BOM
            </button>
        </div>

    </form>
</div>

<script>
function bomForm() {
    return {
        rows: <?php echo json_encode($existingBoms ?? [], 15, 512) ?>,
        materials: <?php echo json_encode($materials ?? [], 15, 512) ?>,

        init() {
            // Jika kosong, tambah 1 baris default
            if (this.rows.length === 0) {
                this.addRow();
            }
        },

        addRow() {
            this.rows.push({
                id: Date.now(), // Unique ID untuk key
                material_id: '',
                quantity: 1,
                unit: 'Satuan',
                price: 0,
                stock: 0,
                is_inventory: false // [MODIFIED] Default is_inventory
            });
        },

        removeRow(index) {
            this.rows.splice(index, 1);
        },

        getMaterialName(id) {
            if (!id) return '-- Pilih Bahan --';
            let mat = this.materials.find(m => m.id == id);
            return mat ? mat.nama_material : 'Bahan Tidak Ditemukan';
        },

        filteredMaterials(search, currentId) {
            return this.materials.filter(m => {
                const matchSearch = m.nama_material.toLowerCase().includes(search.toLowerCase());
                const isUsedInOtherRows = this.rows.some(r => r.material_id == m.id && r.material_id != currentId);
                return matchSearch && !isUsedInOtherRows;
            });
        },

        updateUnit(index) {
            let row = this.rows[index];
            let mat = this.materials.find(m => m.id == row.material_id);
            if (mat) {
                // ✅ Gunakan $set agar Alpine.js mendeteksi perubahan dan trigger re-render otomatis
                this.$set(this.rows[index], 'unit', mat.satuan);
                this.$set(this.rows[index], 'price', parseFloat(mat.harga_satuan));
                this.$set(this.rows[index], 'stock', parseFloat(mat.stok));
                this.$set(this.rows[index], 'is_inventory', mat.is_inventory);
                // ✅ JANGAN reset quantity - biarkan nilai yang sudah diinput tetap ada
                // ✅ JANGAN ada batasan stok sama sekali - admin boleh input berapapun
            } else {
                this.$set(this.rows[index], 'unit', 'Satuan');
                this.$set(this.rows[index], 'price', 0);
                this.$set(this.rows[index], 'stock', 0);
                this.$set(this.rows[index], 'is_inventory', false);
            }
            // ✅ Auto trigger perhitungan ulang total HPP segera setelah bahan dipilih
            this.$nextTick(() => {
                this.calculateGrandTotal();
            });
        },

        calculateRowTotal(row) {
            // ✅ SELALU ambil harga terbaru dari master material, tidak dari cache row
            let currentPrice = 0;
            if (row.material_id) {
                let mat = this.materials.find(m => m.id == row.material_id);
                if (mat) {
                    currentPrice = parseFloat(mat.harga_satuan);
                }
            }
            const qty = parseFloat(row.quantity) || 0;
            // ✅ Support desimal penuh dengan presisi 4 digit akurat
            return Math.round((qty * currentPrice) * 10000) / 10000;
        },

        calculateGrandTotal() {
            return this.rows.reduce((total, row) => total + this.calculateRowTotal(row), 0);
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/bom/create.blade.php ENDPATH**/ ?>