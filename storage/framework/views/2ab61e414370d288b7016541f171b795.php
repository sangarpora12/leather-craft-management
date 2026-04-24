<?php $__env->startSection('content'); ?>
<div class="w-full max-w-md mx-auto bg-[#0B0E14] text-white min-h-screen">
    <!-- Header -->
    <?php echo $__env->make('components.operator-header', ['title' => 'BAHAN BAKU'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Stats -->
    <div class="px-4 py-3">
        <div class="bg-[#1C222D] border border-white/5 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-500"></i>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase">Total SKU</span>
                        <div class="text-lg font-medium text-white"><?php echo e($totalSKU); ?></div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-500 uppercase">Kritis</span>
                    <div class="text-lg font-medium text-red-400"><?php echo e($totalKritis); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="px-4 pb-4">
        <form method="GET" action="<?php echo e(route('operator.bahan.index')); ?>" class="space-y-3">
            <div class="relative">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama / kode bahan..." class="w-full bg-[#1C222D] border border-white/5 rounded-2xl px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-emerald-500 focus:outline-none pr-10">
                <button type="submit" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <div class="flex overflow-x-auto gap-2 pb-1 scrollbar-hide">
                <select name="kategori" class="bg-[#1C222D] border border-white/5 rounded-lg px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none min-w-[100px]">
                    <option value="" <?php echo e(!request('kategori') ? 'selected' : ''); ?>>Semua Kategori</option>
                    <?php $__currentLoopData = $kategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k->nama_kategori); ?>" <?php echo e(request('kategori') == $k->nama_kategori ? 'selected' : ''); ?>><?php echo e($k->nama_kategori); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                
                <select name="satuan" class="bg-[#1C222D] border border-white/5 rounded-lg px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none min-w-[90px]">
                    <option value="" <?php echo e(!request('satuan') ? 'selected' : ''); ?>>Semua Satuan</option>
                    <?php $__currentLoopData = $satuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->nama_satuan); ?>" <?php echo e(request('satuan') == $s->nama_satuan ? 'selected' : ''); ?>><?php echo e($s->nama_satuan); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                
                <select name="sort" class="bg-[#1C222D] border border-white/5 rounded-lg px-3 py-2 text-sm text-white focus:border-emerald-500 focus:outline-none min-w-[120px]">
                    <option value="terbaru" <?php echo e(!request('sort') || request('sort') == 'terbaru' ? 'selected' : ''); ?>>Terbaru</option>
                    <option value="stok_terendah" <?php echo e(request('sort') == 'stok_terendah' ? 'selected' : ''); ?>>Stok Rendah</option>
                    <option value="nama_asc" <?php echo e(request('sort') == 'nama_asc' ? 'selected' : ''); ?>>Nama A-Z</option>
                </select>
            </div>
            
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-2 rounded-lg text-sm font-medium">
                Terapkan Filter
            </button>
        </form>
    </div>

    <!-- Bahan Baku List -->
    <div class="px-4 pb-24">
        <?php $__empty_1 = true; $__currentLoopData = $bahanBakus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bahan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="rounded-xl mb-3 overflow-hidden relative <?php if($bahan->stok <= $bahan->stok_minimum): ?> bg-red-500/10 border border-red-500/40 <?php else: ?> bg-[#1C222D] border border-white/5 <?php endif; ?>">
            <!-- ICON EDIT POJOK KANAN ATAS -->
            <a href="<?php echo e(route('operator.bahan.edit', $bahan->id)); ?>" 
               onclick="event.stopPropagation();"
               class="absolute top-3 right-3 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg p-1.5 z-10 transition touch-manipulation">
                <i class="fas fa-pencil-alt"></i>
            </a>
        
            <!-- SELURUH KARTU ADALAH LINK DETAIL -->
            <a href="<?php echo e(route('operator.bahan.show', $bahan->id)); ?>" class="block p-4 hover:bg-white/5 transition">
                <div class="flex items-center gap-3">
                    <!-- ✅ FOTO BAHAN KIRI -->
                    <div class="w-14 h-14 overflow-hidden rounded-xl bg-gray-700 border border-white/5 flex-shrink-0">
                        <?php if($bahan->foto): ?>
                            <img src="<?php echo e(asset('storage/' . $bahan->foto)); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fa-solid fa-box text-gray-500 text-xl"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ✅ INFORMASI TENGAH -->
                    <div class="flex-1 min-w-0 pr-10">
                        <div class="text-sm font-medium text-white mb-0.5 truncate"><?php echo e($bahan->nama_material); ?></div>
                        <div class="text-xs text-gray-400 mb-1"><?php echo e($bahan->kode_material); ?></div>
                        <div class="flex items-center gap-2">
                            <span class="px-1.5 py-0.5 bg-gray-700 text-gray-300 text-[10px] rounded"><?php echo e($bahan->kategori); ?></span>
                            <span class="text-xs font-bold <?php echo e($bahan->stok <= $bahan->stok_minimum ? 'text-red-400' : 'text-green-400'); ?>"><?php echo e(number_format($bahan->stok, 0)); ?> <span class="font-normal text-gray-400"><?php echo e(strtolower($bahan->satuan)); ?></span></span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="py-12 border-2 border-dashed border-white/5 rounded-xl text-center">
            <span class="text-sm text-gray-500">Belum ada bahan baku</span>
        </div>
        <?php endif; ?>

        <?php if($bahanBakus->hasPages()): ?>
            <div class="mt-4">
                <?php echo e($bahanBakus->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Sembunyikan scrollbar tapi tetap bisa scroll */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/bahan-baku/index.blade.php ENDPATH**/ ?>