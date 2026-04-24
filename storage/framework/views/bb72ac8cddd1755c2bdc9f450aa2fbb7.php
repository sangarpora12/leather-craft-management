<?php $__env->startSection('page_title', 'Data Supplier'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="space-y-6 animate-fade-in text-gray-300 font-sans bg-zinc-900 min-h-screen p-6">

    
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-6">
        <div>
            <p class="text-sm text-gray-500">Klik supplier untuk melihat daftar bahan yang disediakan</p>
        </div>
    </div>

    
    <?php if($suppliers->count() > 0): ?>
    <div class="bg-[#111111] rounded-2xl border border-white/5 p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-store text-blue-500"></i>
            Daftar Supplier
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaSupplier => $bahanBakus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-[#0f1115] rounded-xl border border-white/5 p-4 hover:border-blue-500/50 transition cursor-pointer group"
                 onclick="toggleSupplier('supplier-<?php echo e(md5($namaSupplier)); ?>')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center group-hover:bg-blue-500/20 transition">
                            <i class="fa-solid fa-shop text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm"><?php echo e($namaSupplier); ?></h3>
                            <p class="text-xs text-gray-500"><?php echo e($bahanBakus->count()); ?> material tersedia</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-600 group-hover:text-blue-400 transition-transform duration-300"
                       id="arrow-<?php echo e(md5($namaSupplier)); ?>"></i>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $namaSupplier => $bahanBakus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div id="supplier-<?php echo e(md5($namaSupplier)); ?>" class="hidden bg-[#111111] rounded-2xl border border-blue-500/30 overflow-hidden mb-4">
        <div class="px-4 py-3 bg-blue-500/10 border-b border-blue-500/20 flex justify-between items-center">
            <h3 class="text-white font-semibold flex items-center gap-2">
                <i class="fa-solid fa-shop text-blue-400"></i>
                <?php echo e($namaSupplier); ?>

            </h3>
            <button onclick="toggleSupplier('supplier-<?php echo e(md5($namaSupplier)); ?>')" class="text-gray-400 hover:text-white">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-white/5 text-gray-400 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Nama Material</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-center">Satuan</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php $__currentLoopData = $bahanBakus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bahan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="px-4 py-3 text-gray-400"><?php echo e($bahan->kode_material); ?></td>
                        <td class="px-4 py-3 text-white font-medium"><?php echo e($bahan->nama_material); ?></td>
                        <td class="px-4 py-3 text-gray-400"><?php echo e($bahan->kategori); ?></td>
                        <td class="px-4 py-3 text-right text-green-400 font-bold">Rp <?php echo e(number_format($bahan->harga_satuan, 0, ',', '.')); ?></td>
                        <td class="px-4 py-3 text-center text-gray-400"><?php echo e($bahan->satuan); ?></td>
                        <td class="px-4 py-3 text-right">
                            <span class="<?php if($bahan->stok <= $bahan->stok_minimum): ?> text-red-400 <?php else: ?> text-gray-400 <?php endif; ?>">
                                <?php echo e($bahan->stok); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
    <div class="bg-[#111111] rounded-2xl border border-white/5 p-12 text-center">
        <i class="fa-solid fa-store-slash text-4xl text-gray-600 mb-4"></i>
        <p class="text-gray-400">Belum ada data supplier</p>
        <p class="text-sm text-gray-500 mt-2">Data akan muncul otomatis setelah input Bahan Baku dengan nama supplier</p>
    </div>
    <?php endif; ?>

</div>

<style>
.animate-fade-in { animation: fadeIn 0.3s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
function toggleSupplier(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.toggle('hidden');
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/supplier/index.blade.php ENDPATH**/ ?>