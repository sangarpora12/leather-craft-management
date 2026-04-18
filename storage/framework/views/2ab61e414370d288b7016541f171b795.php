

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-md mx-auto bg-gray-900 text-white font-normal" style="font-family: 'Poppins', sans-serif;">
    <!-- Header -->
    <?php echo $__env->make('components.operator-header', ['title' => 'BAHAN BAKU'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Stats -->
    <div class="px-5 py-4">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-500"></i>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-normal uppercase">Total SKU</span>
                        <div class="text-lg font-normal text-white"><?php echo e($totalSKU); ?></div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500 font-normal uppercase">Kritis</span>
                    <div class="text-lg font-normal text-red-400"><?php echo e($totalKritis); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="px-5 pb-4">
        <form method="GET" action="<?php echo e(route('operator.bahan.index')); ?>" class="mb-4">
            <div class="relative">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari bahan baku..." class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-blue-500 focus:outline-none pr-10">
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Bahan Baku List -->
    <div class="px-5 pb-4">
        <?php $__empty_1 = true; $__currentLoopData = $bahanBakus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bahan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 mb-4">
            <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                    <div class="text-base font-normal text-white mb-1"><?php echo e($bahan->nama_material); ?></div>
                    <div class="text-sm text-gray-500 font-normal mb-2"><?php echo e($bahan->kode_material); ?></div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-1 bg-gray-700 text-gray-300 text-xs rounded font-normal"><?php echo e($bahan->kategori); ?></span>
                        <span class="px-2 py-1 bg-blue-500/20 text-blue-400 text-xs rounded font-normal"><?php echo e($bahan->satuan); ?></span>
                    </div>
                    <div class="text-xs text-gray-400 font-normal">
                        Harga: Rp <?php echo e(number_format($bahan->harga_satuan, 0, ',', '.')); ?>

                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-normal <?php echo e($bahan->stok <= $bahan->stok_minimum ? 'text-red-400' : 'text-green-400'); ?>">
                        <?php echo e($bahan->stok); ?>

                    </div>
                    <div class="text-xs text-gray-500 font-normal uppercase">Stok</div>
                    <?php if($bahan->stok <= $bahan->stok_minimum): ?>
                    <div class="text-xs text-red-400 font-normal mt-1">Kritis</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('operator.bahan.edit', $bahan->id)); ?>" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white py-2 rounded-lg text-sm font-normal uppercase text-center">
                    Edit
                </a>
                <a href="<?php echo e(route('operator.bahan.tambah')); ?>" class="flex-1 bg-green-600 hover:bg-green-500 text-white py-2 rounded-lg text-sm font-normal uppercase text-center">
                    Tambah Stok
                </a>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="py-12 border-2 border-dashed border-gray-700 rounded-xl text-center">
            <span class="text-sm text-gray-500 font-normal">Belum ada bahan baku</span>
        </div>
        <?php endif; ?>

        <?php if($bahanBakus->hasPages()): ?>
            <div class="mt-6">
                <?php echo e($bahanBakus->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/bahan-baku/index.blade.php ENDPATH**/ ?>