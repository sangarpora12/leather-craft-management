

<?php $__env->startSection('page_title', 'Riwayat Produk Keluar'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-white">Riwayat Pengurangan Stok</h2>
        <a href="<?php echo e(route('admin.pakai-produk.index')); ?>" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
            <i class="fa-solid fa-plus mr-2"></i>
            Kurangi Stok Baru
        </a>
    </div>

    <!-- Tabel Riwayat -->
    <div class="bg-[#1A1C24] border border-[#343842] rounded-2xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-400">
                <thead class="text-xs text-gray-300 uppercase bg-[#2D3139]">
                    <tr>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Nama Produk</th>
                        <th scope="col" class="px-6 py-3">Jumlah</th>
                        <th scope="col" class="px-6 py-3">Alasan</th>
                        <th scope="col" class="px-6 py-3">Dicatat oleh</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $transaksis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaksi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="bg-[#1A1C24] border-b border-[#343842] hover:bg-[#252830]">
                            <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
                                <?php echo e($transaksi->tanggal_keluar->format('d M Y, H:i')); ?>

                            </td>
                            <td class="px-6 py-4">
                                <?php echo e($transaksi->produk_jadi->nama_produk ?? 'Produk tidak ditemukan'); ?>

                            </td>
                            <td class="px-6 py-4 font-bold text-red-400">
                                -<?php echo e($transaksi->jumlah_keluar); ?>

                            </td>
                            <td class="px-6 py-4">
                                <?php echo e($transaksi->alasan); ?>

                            </td>
                            <td class="px-6 py-4">
                                <?php echo e($transaksi->user->name ?? 'Sistem'); ?>

                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="<?php echo e(route('admin.pakai-produk.destroy', $transaksi->id)); ?>" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus riwayat ini? Stok produk akan dikembalikan.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-400 hover:text-red-300 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-10">
                                <p class="text-gray-500">Belum ada riwayat pengurangan stok.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if($transaksis->hasPages()): ?>
        <div class="mt-8">
            <?php echo e($transaksis->links()); ?>

        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/produk_keluar/index.blade.php ENDPATH**/ ?>