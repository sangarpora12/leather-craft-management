

<?php $__env->startSection('page_title', 'Bill of Materials'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-2">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b border-white/10 pb-6 gap-4">
        <div>
            <p class="text-gray-400 text-sm">Resep bahan baku untuk setiap unit produk jadi.</p>
        </div>
        
        <div class="flex gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64">
                <form action="<?php echo e(route('admin.bom.index')); ?>" method="GET">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari Resep Produk..." class="bg-[#1A1A1A] border border-white/10 rounded-lg py-2.5 px-4 pl-10 text-sm focus:outline-none focus:border-orange-500 w-full transition text-white">
                    <span class="absolute left-3 top-3 text-gray-500">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                </form>
            </div>
            <a href="<?php echo e(route('admin.produk-jadi.create')); ?>" class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold px-4 py-2.5 rounded-lg transition whitespace-nowrap">
                Buat Produk Baru
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-[#1A1A1A] rounded-xl border border-white/5 overflow-hidden group hover:border-white/20 transition-all flex flex-col">
            <div class="h-40 bg-[#222] relative overflow-hidden flex items-center justify-center">
                <?php if($product->foto): ?>
                    <img src="<?php echo e(asset('storage/' . $product->foto)); ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                <?php else: ?>
                    <div class="text-gray-600 flex flex-col items-center">
                        <svg class="w-10 h-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="text-xs">No Image</span>
                    </div>
                <?php endif; ?>
                
                <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md px-2 py-1 rounded text-[10px] text-gray-300 border border-white/10 font-mono">
                    <?php echo e($product->kode_produk); ?>

                </span>

                <?php
                    // Hitung jumlah bahan yang valid saja
                    $validBomsCount = $product->boms->filter(fn($b) => $b->bahanBaku)->count();
                ?>

                <?php if($validBomsCount == 0): ?>
                    <span class="absolute bottom-3 left-3 bg-red-500/80 backdrop-blur-md px-2 py-1 rounded text-[10px] text-white font-bold border border-red-500/20">
                        Belum Ada Resep
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-bold text-white text-lg leading-tight mb-1 truncate"><?php echo e($product->nama_produk); ?></h3>
                <p class="text-xs text-gray-500 mb-4"><?php echo e($product->warna ?? 'Default Color'); ?></p>

                <div class="flex justify-between items-center bg-[#111] p-3 rounded-lg border border-white/5 mb-4 mt-auto">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold">Komponen</p>
                        <p class="text-sm font-bold <?php echo e($validBomsCount > 0 ? 'text-blue-400' : 'text-gray-600'); ?>">
                            <?php echo e($validBomsCount); ?> Item
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-500 uppercase font-bold">HPP Material</p>
                        <p class="text-sm font-bold text-green-400">
                            Rp <?php echo e(number_format($product->boms->sum(function($b) {
                                return $b->jumlah_kebutuhan * ($b->bahanBaku->harga_satuan ?? 0);
                            }), 0, ',', '.')); ?>

                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2">
                    <?php
                        $route = $validBomsCount > 0 ? route('admin.bom.edit', $product->id) : route('admin.bom.create', $product->id);
                    ?>
                    <a href="<?php echo e($route); ?>" class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <?php echo e($validBomsCount == 0 ? 'Buat Resep' : 'Edit Resep'); ?>

                    </a>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="<?php echo e(route('admin.produk-jadi.edit', $product->id)); ?>" class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z"></path></svg>
                            <span>Edit</span>
                        </a>
                        <form action="<?php echo e(route('admin.produk-jadi.copy', $product->id)); ?>" method="POST" class="w-full" onsubmit="return confirm('Anda yakin ingin menyalin produk ini?')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="w-full py-2.5 border border-white/10 hover:bg-white/5 rounded-lg text-xs font-bold text-gray-300 hover:text-white flex items-center justify-center gap-2 transition">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>Salin</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full p-12 text-center text-gray-500 border border-dashed border-white/10 rounded-xl">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p>Belum ada produk yang terdaftar.</p>
            <a href="<?php echo e(route('admin.produk-jadi.create')); ?>" class="text-orange-500 hover:underline text-sm mt-2 inline-block">Tambah Produk Dulu</a>
        </div>
        <?php endif; ?>
    </div>

    <?php if($products->hasPages()): ?>
    <div class="mt-8">
        <?php echo e($products->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/bom/index.blade.php ENDPATH**/ ?>