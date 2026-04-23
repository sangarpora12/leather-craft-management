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
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => 'menipis', 'sort_by' => request('sort_by')]))); ?>" <?php echo e(request('status_stok') == 'menipis' ? 'selected' : ''); ?>>Stok Menipis</option>
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
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok'), 'sort_by' => 'stok_terbanyak']))); ?>" <?php echo e(request('sort_by') == 'stok_terbanyak' ? 'selected' : ''); ?>>Stok Terbanyak</option>
                    <option value="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search'), 'status_stok' => request('status_stok'), 'sort_by' => 'stok_terkecil']))); ?>" <?php echo e(request('sort_by') == 'stok_terkecil' ? 'selected' : ''); ?>>Stok Terkecil</option>
                </select>
            </div>

        </div>
    </div>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    <?php $__empty_1 = true; $__currentLoopData = $produk_jadi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $stokMinProduk = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_produk')->value('nilai') ?? 2);
            $isStokKritis = $item->stok <= $stokMinProduk;
        ?>

        
        <div class="bg-[#1C222D] rounded-2xl border border-white/5 overflow-hidden flex flex-col hover:translate-y-[-2px] transition-all duration-300 shadow-xl">

            
            <div class="p-3 pb-0">
                <div class="aspect-square w-full bg-[#F5F5F5] rounded-xl overflow-hidden relative">
                    <?php if($item->foto): ?>
                    <img src="<?php echo e(asset('storage/'.$item->foto)); ?>"
                         class="w-full h-full object-cover"
                         alt="<?php echo e($item->nama_produk); ?>">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fa-solid fa-image text-neutral-400 text-4xl"></i>
                    </div>
                    <?php endif; ?>

                    
                    <div class="absolute top-3 right-3 bg-black/70 backdrop-blur-sm px-2.5 py-1 rounded-md text-[10px] text-white font-medium">
                        <?php echo e($item->kode_produk ?? $item->sku ?? '-'); ?>

                    </div>

                    
                    <?php if($isStokKritis): ?>
                    <div class="absolute top-3 left-3 bg-red-500/90 backdrop-blur-sm px-2 py-1 rounded-md text-[10px] text-white font-bold">
                        ⚠ MENIPIS
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="p-4 flex flex-col flex-1">

                
                <div class="mb-1">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-base font-bold text-white truncate">
                            <?php echo e(ucwords($item->nama_produk)); ?>

                        </h3>
                        <?php if($item->tipe_harga === 'otomatis' && $item->harga_jual > 0): ?>
                        <span class="bg-green-500/15 text-green-400 text-[9px] font-bold px-2 py-0.5 rounded border border-green-500/25 whitespace-nowrap">BOM: OK</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-500 text-[11px] mt-0.5 truncate">
                        <?php echo e($item->deskripsi ? Str::limit($item->deskripsi, 40) : 'Deskripsi produk belum diisi'); ?>

                    </p>
                </div>

                
                <div class="grid grid-cols-2 gap-3 my-3">
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase tracking-wider">Stok</p>
                        <p class="text-white text-sm font-bold mt-0.5 <?php echo e($isStokKritis ? 'text-red-400' : ''); ?>"><?php echo e($item->stok); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-[10px] uppercase tracking-wider">Harga Jual</p>
                        <p class="text-[#22C55E] text-sm font-bold mt-0.5">Rp <?php echo e(number_format($item->harga_jual, 0, ',', '.')); ?></p>
                    </div>
                </div>

                
                <div class="flex items-center gap-2 mt-auto">
                    <a href="<?php echo e(route('admin.produk-jadi.edit', $item->id)); ?>"
                       class="w-10 h-9 border border-zinc-600 rounded-lg flex items-center justify-center text-gray-300 hover:bg-zinc-700 hover:text-white transition-colors shrink-0"
                       title="Edit">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>

                    <a href="<?php echo e(route('admin.produk-jadi.show', $item->id)); ?>"
                       class="flex-1 h-9 bg-[#FF6B00] hover:bg-[#E55D00] rounded-lg text-white text-xs font-semibold flex items-center justify-center transition-colors">
                        Lihat Detail <i class="fa-solid fa-arrow-right ml-2 text-[10px]"></i>
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