<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>📊 Kinerja Operator: <?php echo e($operator->name); ?></h2>
            <p class="text-muted">Jabatan: <?php echo e($operator->jabatan ?? 'N/A'); ?></p>
        </div>
    </div>

    <div class="row">
        <!-- Card Statistik -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Produk Bulan Ini</h5>
                    <h3 class="text-success"><?php echo e($totalProdukBulanIni); ?> Pcs</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Rata-rata Per Hari</h5>
                    <h3 class="text-info"><?php echo e(round($rataPerHari, 2)); ?> Pcs</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Status Operator</h5>
                    <h3>
                        <?php if($operator->is_active): ?>
                            <span class="badge badge-success">AKTIF</span>
                        <?php else: ?>
                            <span class="badge badge-danger">NONAKTIF</span>
                        <?php endif; ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>📈 Detail Produk Bulan Ini</h5>
                </div>
                <div class="card-body">
                    <?php if($produksiBulanIni->count() > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Jumlah (Pcs)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $produksiBulanIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($produk->nama_produk); ?></td>
                                        <td><strong><?php echo e($produk->total); ?></strong></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">Belum ada produk yang diselesaikan bulan ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?php echo e(route('admin.pekerja-master.index')); ?>" class="btn btn-secondary">← Kembali</a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/operator/kinerja.blade.php ENDPATH**/ ?>