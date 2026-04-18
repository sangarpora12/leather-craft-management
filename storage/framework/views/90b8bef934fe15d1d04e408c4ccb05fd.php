<?php $__env->startSection('page_title', 'Produk Jadi'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-in font-sans">


<div class="flex justify-between items-center mb-8">
    <div>
        <p class="text-gray-400 text-sm">
            Katalog produk, manajemen stok, dan harga pokok produksi.
        </p>
    </div>

    <form method="GET" action="<?php echo e(route('admin.produk-jadi.index')); ?>">
        <input type="text"
               name="search"
               value="<?php echo e(request('search')); ?>"
               placeholder="Cari nama, SKU..."
               class="bg-[#111] border border-gray-800 rounded-full px-5 py-2 text-sm w-64 focus:outline-none focus:border-orange-500">
    </form>
</div>



<div class="space-y-4 mb-8">

    
    <div class="flex items-center gap-3">
        <span class="text-gray-400 text-sm shrink-0">Kategori:</span>

        <div class="flex gap-2 overflow-x-auto custom-scrollbar py-1">
            <a href="<?php echo e(route('admin.produk-jadi.index', array_filter(['search' => request('search'), 'warna' => request('warna')]))); ?>"
               class="px-4 py-2 rounded-xl text-sm font-semibold border transition whitespace-nowrap
               <?php echo e(!request('kategori') ? 'bg-[#2D3139] border-[#2D3139] text-white' : 'bg-[#111] border-gray-800 text-gray-300 hover:bg-[#20232A]'); ?>">
                Semua
            </a>

            <?php $__currentLoopData = $kategoriList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => $kategori, 'search' => request('search'), 'warna' => request('warna')]))); ?>"
                   class="px-4 py-2 rounded-xl text-sm font-semibold border transition whitespace-nowrap
                   <?php echo e(request('kategori') == $kategori ? 'bg-[#2D3139] border-[#2D3139] text-white' : 'bg-[#111] border-gray-800 text-gray-300 hover:bg-[#20232A]'); ?>">
                    <?php echo e($kategori); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.produk-jadi.index')); ?>" class="flex flex-col md:flex-row gap-3 md:items-center">
        
        <input type="hidden" name="kategori" value="<?php echo e(request('kategori')); ?>">
        <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">

        <div class="flex items-center gap-3">
            <span class="text-gray-400 text-sm shrink-0">Warna:</span>
            <div class="flex items-center bg-[#111] border border-gray-800 rounded-xl px-4 py-2.5">
                <select name="warna" onchange="this.form.submit()" class="bg-transparent border-none outline-none text-sm font-semibold text-gray-200 w-56 cursor-pointer appearance-none">
                    <option value="" class="bg-[#111]">Semua Warna</option>
                    <?php $__currentLoopData = $warnaList ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warna): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($warna); ?>" class="bg-[#111]" <?php echo e(request('warna') == $warna ? 'selected' : ''); ?>><?php echo e($warna); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <i class="fa-solid fa-chevron-down text-gray-500 text-[10px] ml-2 pointer-events-none"></i>
            </div>
        </div>

        <?php if(request('warna')): ?>
            <a href="<?php echo e(route('admin.produk-jadi.index', array_filter(['kategori' => request('kategori'), 'search' => request('search')]))); ?>"
               class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-[#2D3139] text-white hover:bg-gray-700 transition w-fit">
                Reset Warna
            </a>
        <?php endif; ?>
    </form>

</div>



<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

<?php $__empty_1 = true; $__currentLoopData = $produk_jadi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

<div class="bg-[#111] rounded-2xl border border-gray-800 overflow-hidden hover:border-orange-500 transition">


<div class="relative h-48">

<?php if($item->foto): ?>
<img src="<?php echo e(asset('storage/'.$item->foto)); ?>" class="w-full h-full object-cover" alt="<?php echo e($item->nama_produk); ?>">
<?php else: ?>
<div class="w-full h-full bg-gray-900 flex items-center justify-center">
    <i class="fa-solid fa-image text-gray-700 text-4xl"></i>
</div>
<?php endif; ?>

<?php if($item->sketsa): ?>
<div class="absolute top-2 right-2 w-12 h-12 bg-black/50 backdrop-blur-sm rounded-md border border-white/10 p-1 overflow-hidden">
    <img src="<?php echo e(asset('storage/'.$item->sketsa)); ?>" class="w-full h-full object-contain" alt="Sketsa">
</div>
<?php endif; ?>

<div class="absolute top-3 left-3 bg-black/70 px-2 py-1 text-xs rounded">
<?php echo e($item->kode_produk); ?>

</div>

</div>



<div class="p-5">

<h3 class="font-semibold text-lg">
<?php echo e($item->nama_produk); ?>

</h3>

 <div class="flex flex-wrap gap-1 mb-4">
     <span class="px-2 py-0.5 bg-gray-700 rounded text-xs text-gray-200 text-capitalize">
         <?php echo e(is_array(json_decode($item->warna, true)) ? implode(', ', json_decode($item->warna, true)) : ($item->warna ?? '-')); ?>

     </span>
 </div>
 <div class="flex flex-wrap gap-1 mb-2">
     <span class="px-2 py-0.5 bg-blue-900/50 rounded text-xs text-blue-200 text-capitalize">
         <?php echo e(is_array(json_decode($item->kategori, true)) ? implode(', ', json_decode($item->kategori, true)) : ($item->kategori ?? '-')); ?>

     </span>
 </div>

<?php if($item->panjang || $item->lebar || $item->tinggi): ?>
<div class="mb-4">
    <p class="text-xs text-gray-500 font-semibold">Dimensi:</p>
    <p class="text-gray-400 text-sm">
        <?php echo e($item->panjang + 0); ?> x <?php echo e($item->lebar + 0); ?> x <?php echo e($item->tinggi + 0); ?> cm
    </p>
</div>
<?php endif; ?>

<?php if($item->deskripsi): ?>
<div class="mb-4">
    <p class="text-xs text-gray-500 font-semibold">Deskripsi:</p>
    <p class="text-gray-400 text-sm leading-relaxed">
        <?php echo e(Str::limit($item->deskripsi, 100)); ?>

    </p>
</div>
<?php endif; ?>

<?php if($item->keterangan): ?>
<div class="mb-4 p-3 bg-gray-900/50 border border-gray-800 rounded-lg">
    <p class="text-xs text-yellow-400/80 font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-info"></i>
        Catatan Internal:
    </p>
    <p class="text-gray-400 text-sm mt-1">
        <?php echo e(Str::limit($item->keterangan, 100)); ?>

    </p>
</div>
<?php endif; ?>



<div class="flex justify-between text-sm mb-2">
<span class="text-gray-400">Stok</span>
<span><?php echo e($item->stok); ?></span>
</div>

<div class="flex justify-between text-sm mb-5">
<span class="text-gray-400">Harga Jual</span>
<span class="font-semibold <?php echo e($item->tipe_harga === 'otomatis' ? 'text-green-500' : 'text-orange-500'); ?>">
<?php if($item->tipe_harga === 'otomatis' && $item->harga_jual == 0): ?>
<span class="text-xs text-gray-500 block">Belum ada BOM</span>
Rp 0
<?php else: ?>
Rp <?php echo e(number_format($item->harga_jual,0,',','.')); ?>

<?php endif; ?>
</span>
</div>


<div class="flex gap-2">

    <a href="<?php echo e(route('admin.produk-jadi.edit', $item->id)); ?>"
       class="flex-1 border border-gray-700 py-2 rounded-lg text-sm hover:bg-gray-800 text-center">
        Edit
    </a>

    <form action="<?php echo e(route('admin.produk-jadi.copy', $item->id)); ?>" method="POST" class="flex-shrink-0">
        <?php echo csrf_field(); ?>
        <button type="submit"
                class="w-10 h-10 border border-gray-700 rounded-lg text-sm hover:bg-gray-800 text-center"
                title="Duplikat Produk">
            <i class="fa-solid fa-copy"></i>
        </button>
    </form>

    <a href="<?php echo e(route('admin.produk-jadi.show', $item->id)); ?>"
       class="flex-1 bg-orange-600 hover:bg-orange-500 py-2 rounded-lg text-sm text-center">
        Lihat Detail →
    </a>

</div>

</div>
</div>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>

<div class="col-span-full text-center py-20 text-gray-500">
Produk tidak ditemukan
</div>

<?php endif; ?>

</div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/produk-jadi/index.blade.php ENDPATH**/ ?>