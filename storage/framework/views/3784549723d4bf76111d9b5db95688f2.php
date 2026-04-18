
<?php $__env->startSection('page_title', 'Pengadaan Material'); ?>

<?php $__env->startSection('content'); ?>

<div x-data="procurementApp()" class="min-h-screen bg-[#0D1117] p-6 space-y-6 animate-fade-in font-sans text-gray-300">

    
    <div class="flex flex-col gap-4">
        <div>
            <p class="text-[10px] text-gray-500 uppercase tracking-[0.2em]">Inventory Control System</p>
        </div>

        <?php if(isset($lowStockMaterials) && $lowStockMaterials->count() > 0): ?>
        <div class="bg-[#1A1C24] border border-red-500/20 rounded-xl p-4 shadow-lg overflow-hidden">
            <div class="flex items-center gap-2 mb-3">
                <span class="flex h-2 w-2 rounded-full bg-red-500 animate-ping"></span>
                <h3 class="text-white font-bold text-xs uppercase italic">Stok Kritis Terdeteksi</h3>
            </div>
            
            <div class="flex gap-3 overflow-x-auto custom-scrollbar pb-2">
                <?php $__currentLoopData = $lowStockMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="shrink-0 w-[180px] bg-[#0D1117] border border-white/5 rounded-lg p-3 flex flex-col justify-between hover:border-red-500/30 transition">
                    <div class="mb-2">
                        <p class="text-white text-[11px] font-bold truncate"><?php echo e($mat->nama_material); ?></p>
                        <p class="text-[9px] text-red-500 font-black">Sisa: <?php echo e($mat->stok); ?> <?php echo e($mat->satuan); ?></p>
                    </div>
                    <button @click="addToCart(<?php echo e($mat->id); ?>)" class="w-full bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white text-[9px] font-bold py-1 rounded transition uppercase">
                        + Tambah
                    </button>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        
        <div class="w-full lg:w-[350px] shrink-0 sticky top-6">
            <div class="bg-[#1A1C24] border border-white/5 rounded-xl shadow-2xl flex flex-col">
                <div class="p-4 border-b border-white/5 flex justify-between items-center bg-white/5">
                    <h3 class="text-orange-500 font-black text-[10px] uppercase tracking-widest text-center">Keranjang Order</h3>
                    <span class="text-[10px] text-gray-500" x-text="cart.length + ' Item'"></span>
                </div>

                
                <div class="max-h-[400px] overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="bg-[#0D1117] border border-white/5 p-3 rounded-lg flex flex-col gap-2">
                            <div class="flex justify-between items-start">
                                <p class="text-white text-[11px] font-bold truncate pr-2" x-text="item.nama_material"></p>
                                <button @click="removeFromCart(index)" class="text-gray-700 hover:text-red-500"><i class="fas fa-times text-[10px]"></i></button>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex-1 flex items-center bg-black/40 rounded border border-white/5 p-1">
                                    <button @click="item.qty > 1 ? item.qty-- : null" class="w-6 h-6 text-gray-500">-</button>
                                    <input type="number" x-model.number="item.qty" class="w-full bg-transparent text-center text-[10px] text-white outline-none">
                                    <button @click="item.qty++" class="w-6 h-6 text-gray-500">+</button>
                                </div>
                                <span class="text-[9px] text-gray-600 uppercase w-12 text-center" x-text="item.satuan"></span>
                                <span class="text-xs font-mono text-gray-400 w-24 text-right" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.qty * item.harga_satuan)"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="cart.length === 0" class="py-10 text-center opacity-20 italic text-[10px]">Belum ada pilihan</div>
                </div>

                
                <div class="p-4 border-t border-white/5 bg-black/20 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] font-bold text-gray-500 uppercase">Estimasi</span>
                        <span class="text-sm font-black text-[#00BFA5]" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalEstimasi)"></span>
                    </div>
                    <button @click="showSubmitModal = true" :disabled="cart.length === 0" 
                            class="w-full py-3 bg-orange-600 hover:bg-orange-500 disabled:bg-gray-800 disabled:text-gray-600 rounded-lg text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-orange-900/20 transition-all">
                        BUAT PENGADAAN
                    </button>
                </div>
            </div>
        </div>

        
        <div class="flex-1 w-full">
            <div class="bg-[#1A1C24] border border-white/5 rounded-xl shadow-2xl overflow-hidden">
                <div class="p-4 border-b border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <h3 class="text-white font-black text-[11px] uppercase tracking-widest">Katalog Material</h3>
                    <div class="relative w-full md:w-64">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-700 text-[10px]"></i>
                        <input type="text" x-model="search" placeholder="Cari material..." 
                               class="w-full bg-[#0D1117] border border-white/10 rounded-lg pl-9 pr-3 py-2 text-[11px] text-white focus:border-orange-500 outline-none transition">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-black/20 text-[9px] text-gray-500 font-black uppercase tracking-widest border-b border-white/5">
                            <tr>
                                <th class="px-6 py-4">Informasi Material</th>
                                <th class="px-6 py-4 text-center">Stok</th>
                                <th class="px-6 py-4 text-right">Harga Satuan</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <template x-for="mat in filteredMaterials" :key="mat.id">
                                <tr class="hover:bg-white/[0.02] transition">
                                    <td class="px-6 py-4">
                                        <p class="text-white text-xs font-bold" x-text="mat.nama_material"></p>
                                        <p class="text-[9px] text-gray-600 font-mono mt-0.5" x-text="mat.kode_material"></p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span :class="mat.stok <= 5 ? 'text-red-500 bg-red-500/10 border-red-500/20' : 'text-green-500 bg-green-500/10 border-green-500/20'" 
                                              class="text-[9px] font-black px-2 py-0.5 rounded border" 
                                              x-text="mat.stok + ' ' + mat.satuan"></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs font-mono text-gray-400" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(mat.harga_satuan || 0)"></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="addToCart(mat.id)" class="bg-orange-600/10 text-orange-500 hover:bg-orange-600 hover:text-white px-4 py-1.5 rounded-md border border-orange-600/20 text-[9px] font-black transition-all">
                                            TAMBAH
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <div x-show="showSubmitModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm p-4">
        <div @click.outside="showSubmitModal = false" class="w-full max-w-md bg-[#1A1C24] border border-white/10 rounded-2xl shadow-2xl overflow-hidden animate-zoom-in">
            <div class="p-4 border-b border-white/5 bg-black/20 flex justify-between items-center">
                <h3 class="text-white font-black text-[10px] uppercase tracking-widest">Konfirmasi Pengadaan</h3>
                <button @click="showSubmitModal = false" class="text-gray-500 hover:text-white"><i class="fas fa-times text-xs"></i></button>
            </div>
            
            <form action="<?php echo e(route('admin.pengadaan.store')); ?>" method="POST" class="p-6 space-y-4" x-ref="submitForm">
                <?php echo csrf_field(); ?>
                <template x-for="(item, index) in cart" :key="index">
                    <div>
                        <input type="hidden" :name="`items[${index}][bahan_baku_id]`" :value="item.id">
                        <input type="hidden" :name="`items[${index}][jumlah]`" :value="item.qty">
                    </div>
                </template>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Prioritas</label>
                        <select name="prioritas" class="w-full bg-[#0D1117] border border-white/10 rounded-lg text-white text-[11px] px-3 py-2 outline-none focus:border-orange-500">
                            <option value="BIASA">BIASA</option>
                            <option value="URGENT">URGENT</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Target Tanggal</label>
                        <input type="date" name="tanggal_dibutuhkan" x-model="form.tanggal" class="w-full bg-[#0D1117] border border-white/10 rounded-lg text-white text-[11px] px-3 py-2 outline-none focus:border-orange-500">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-gray-500 uppercase mb-1">Operator</label>
                        <select name="operator_id" class="w-full bg-[#0D1117] border border-white/10 rounded-lg text-white text-[11px] px-3 py-2 outline-none focus:border-orange-500">
                            <option value="">-- Pilih --</option>
                            <?php $__currentLoopData = $operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($op->id); ?>"><?php echo e($op->name); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-white/5">
                    <button type="button" @click="showSubmitModal = false" class="text-[10px] text-gray-500 font-bold hover:text-white transition">BATAL</button>
                    <button type="button" @click="submitPengadaan()" :disabled="!isFormValid" class="px-6 py-2 bg-orange-600 text-white text-[10px] font-black rounded-lg shadow-lg shadow-orange-900/40 uppercase tracking-widest hover:scale-105 active:scale-95 disabled:opacity-50">KIRIM ORDER</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script>
function procurementApp() {
    return {
        search: '',
        cart: [],
        materials: <?php echo json_encode($bahanBakus, 15, 512) ?>,
        showSubmitModal: false,
        form: { prioritas: 'BIASA', tanggal: '' },

        get filteredMaterials() {
            if (!this.search) return this.materials;
            return this.materials.filter(m =>
                m.nama_material.toLowerCase().includes(this.search.toLowerCase()) ||
                (m.kode_material && m.kode_material.toLowerCase().includes(this.search.toLowerCase()))
            );
        },

        get totalEstimasi() {
            return this.cart.reduce((sum, item) => sum + (item.qty * (item.harga_satuan || 0)), 0);
        },

        get isFormValid() {
            return this.form.tanggal !== '' && this.cart.length > 0;
        },

        addToCart(id) {
            const mat = this.materials.find(m => m.id === id);
            if (!mat) return;
            const existing = this.cart.find(i => i.id === id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({
                    id: mat.id,
                    nama_material: mat.nama_material,
                    kode_material: mat.kode_material,
                    satuan: mat.satuan,
                    harga_satuan: mat.harga_satuan || 0,
                    qty: 1
                });
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        submitPengadaan() {
            if (this.isFormValid) this.$refs.submitForm.submit();
        }
    }
}
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #2D3139; border-radius: 10px; }
    input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pengadaan/index.blade.php ENDPATH**/ ?>