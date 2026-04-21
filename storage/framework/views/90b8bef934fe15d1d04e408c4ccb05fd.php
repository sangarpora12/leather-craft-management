<?php $__env->startSection('page_title', 'Produk Jadi'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#0B0E14] px-6 py-4">

    
    <form method="GET" action="<?php echo e(route('admin.produk-jadi.index')); ?>" class="mb-6">
        <div class="relative">
            <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-gray-500"></i>
            <input type="text"
                   name="search"
                   value="<?php echo e(request('search')); ?>"
                   placeholder="Cari nama produk, kode SKU, deskripsi..."
                   class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-4 pl-12 text-sm text-white placeholder:text-gray-500 focus:outline-none focus:border-orange-500 transition-all">
        </div>
    </form>

    
    <div class="bg-[#1C222D] rounded-xl p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            

            
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">Stok:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-3 py-1.5 rounded-lg border-0 outline-none cursor-pointer">
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'sort_by' => request('sort_by')]))); ?>" <?php echo e(!request('status_stok') ? 'selected' : ''); ?>>Semua</option>
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => 'tersedia', 'sort_by' => request('sort_by')]))); ?>" <?php echo e(request('status_stok') == 'tersedia' ? 'selected' : ''); ?>>Stok Tersedia</option>
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => 'menipis', 'sort_by' => request('sort_by')]))); ?>" <?php echo e(request('status_stok') == 'menipis' ? 'selected' : ''); ?>>Stok Menipis (< 10)</option>
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => 'habis', 'sort_by' => request('sort_by')]))); ?>" <?php echo e(request('status_stok') == 'habis' ? 'selected' : ''); ?>>Stok Habis</option>
                </select>
            </div>

            <div class="h-6 w-px bg-[#2A3140]"></div>

            
            <div class="flex items-center gap-2">
                <span class="text-gray-400 text-sm">Urutkan:</span>
                <select onchange="window.location.href = this.value" class="bg-[#2A3140] text-white px-3 py-1.5 rounded-lg border-0 outline-none cursor-pointer">
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok')]))); ?>" <?php echo e(!request('sort_by') ? 'selected' : ''); ?>>Default</option>
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok'), 'sort_by' => 'harga_termahal']))); ?>" <?php echo e(request('sort_by') == 'harga_termahal' ? 'selected' : ''); ?>>Termahal</option>
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok'), 'sort_by' => 'harga_termurah']))); ?>" <?php echo e(request('sort_by') == 'harga_termurah' ? 'selected' : ''); ?>>Termurah</option>
                </select>
            </div>

        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    <?php $__empty_1 = true; $__currentLoopData = $produk_jadi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

        
        <div class="bg-[#1C222D] rounded-2xl border-0 overflow-hidden flex flex-col hover:translate-y-[-2px] transition-all duration-300 shadow-xl">

            
            <div class="aspect-square w-full overflow-hidden relative">
                <?php if($item->foto): ?>
                <img src="<?php echo e(asset('storage/'.$item->foto)); ?>" 
                     class="w-full h-full object-cover" 
                     alt="<?php echo e($item->nama_produk); ?>">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fa-solid fa-image text-neutral-600 text-4xl"></i>
                </div>
                <?php endif; ?>
                
                
                <div class="absolute top-4 left-3 bg-black/60 px-2 py-1 rounded text-[10px] text-white">
                    <?php echo e($item->kode_produk ?? $item->sku ?? '-'); ?>

                </div>
            </div>

            
            <div class="p-5 flex flex-col flex-1">
                
                
                <div class="mb-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">
                            <?php echo e(ucwords($item->nama_produk)); ?>

                        </h3>
                        <?php if($item->tipe_harga === 'otomatis' && $item->harga_jual > 0): ?>
                        <span class="bg-green-500/10 text-green-500 text-[9px] font-bold px-2 py-1 rounded border border-green-500/20">BOM: OK</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">
                        <?php echo e($item->deskripsi ? Str::limit($item->deskripsi, 50) : 'Deskripsi produk belum diisi'); ?>

                    </p>
                </div>

                
                <div class="flex justify-between items-center pb-2 mb-2 border-b border-neutral-800">
                    <span class="text-gray-500 text-xs">Stok</span>
                    <span class="text-white text-xs font-bold"><?php echo e($item->stok); ?></span>
                </div>

                
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-400 text-sm">Harga Jual</span>
                    <span class="font-bold text-[#22C55E] text-lg">
                        Rp <?php echo e(number_format($item->harga_jual, 0, ',', '.')); ?>

                    </span>
                </div>

                
                <div class="flex gap-3 mt-auto">
                    <a href="<?php echo e(route('admin.produk-jadi.edit', $item->id)); ?>"
                       class="w-16 h-9 border border-zinc-800 rounded-lg flex items-center justify-center text-gray-300 text-xs font-bold hover:bg-zinc-800 transition-colors">
                        <i class="fa-solid fa-pen mr-1"></i> Edit
                    </a>

                    <a href="<?php echo e(route('admin.produk-jadi.show', $item->id)); ?>"
                       class="flex-1 h-9 bg-[#FF6B00] hover:bg-[#FF8A33] rounded-lg text-white text-sm font-semibold text-center flex items-center justify-center transition-colors">
                        Lihat Detail
                    </a>
                </div>

            </div>
        </div>

    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-20">
            <i class="fa-solid fa-box-open text-neutral-600 text-5xl mb-4"></i>
            <p class="text-gray-500">Produk tidak ditemukan</p>
        </div>
    <?php endif; ?>

    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/produk-jadi/index.blade.php ENDPATH**/ ?>