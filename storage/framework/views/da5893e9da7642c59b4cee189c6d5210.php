<?php $__env->startSection('content'); ?>
<div class="w-full max-w-md mx-auto bg-gradient-to-br from-neutral-900 via-neutral-900 to-neutral-800 min-h-screen">

    <?php echo $__env->make('components.operator-header', ['title' => 'DASHBOARD'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-6 py-6 space-y-6">

        
        <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 shadow-xl">
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                    <span class="text-white text-xl font-bold"><?php echo e(substr(Auth::user()->name, 0, 1)); ?></span>
                </div>
                <div class="flex-1">
                    <div class="text-lg font-semibold text-white"><?php echo e(Auth::user()->name); ?></div>
                    <div class="text-sm text-emerald-400 font-medium">Operator Produksi</div>
                </div>
            </div>

            
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-neutral-800/50 rounded-xl p-3 text-center border border-neutral-700/50">
                    <div class="text-2xl font-bold text-white mb-1"><?php echo e($tugasAktifCount); ?></div>
                    <div class="text-xs text-gray-400 font-medium">Tugas Aktif</div>
                    <div class="text-xs text-amber-400 font-medium"><?php echo e($tugasPendingCount); ?> Pending</div>
                </div>
                <div class="bg-neutral-800/50 rounded-xl p-3 text-center border border-neutral-700/50">
                    <div class="text-2xl font-bold text-emerald-400 mb-1"><?php echo e(number_format($jumlahSelesai)); ?></div>
                    <div class="text-xs text-gray-400 font-medium">Selesai</div>
                    <div class="text-xs text-gray-500 font-medium">Pcs OK</div>
                </div>
                <div class="bg-neutral-800/50 rounded-xl p-3 text-center border border-neutral-700/50">
                    <div class="text-2xl font-bold text-blue-400 mb-1"><?php echo e(number_format($totalBulanIni)); ?></div>
                    <div class="text-xs text-gray-400 font-medium">Total</div>
                    <div class="text-xs text-gray-500 font-medium">Bulan Ini</div>
                </div>
            </div>
        </div>

        
        <div>
            <h2 class="text-lg font-semibold text-white mb-4">Status Produksi</h2>
            <div class="grid grid-cols-3 gap-3">
                
                <div class="bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 border border-emerald-500/20 rounded-2xl p-4 text-center backdrop-blur-sm">
                    <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-calendar-check text-white text-sm"></i>
                    </div>
                    <div class="text-xl font-bold text-white mb-1"><?php echo e(number_format($selesaiHariIni)); ?></div>
                    <div class="text-xs text-emerald-300 font-medium">Selesai Hari Ini</div>
                </div>

                
                <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 border border-blue-500/20 rounded-2xl p-4 text-center backdrop-blur-sm">
                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-arrow-down text-white text-sm"></i>
                    </div>
                    <div class="text-xl font-bold text-white mb-1"><?php echo e(number_format($bahanMasukBulanIni)); ?></div>
                    <div class="text-xs text-blue-300 font-medium">Bahan Masuk</div>
                </div>

                
                <div class="bg-gradient-to-br from-amber-500/10 to-amber-600/10 border border-amber-500/20 rounded-2xl p-4 text-center backdrop-blur-sm">
                    <div class="w-10 h-10 bg-gradient-to-r from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-arrow-up text-white text-sm"></i>
                    </div>
                    <div class="text-xl font-bold text-white mb-1"><?php echo e(number_format($bahanKeluarBulanIni)); ?></div>
                    <div class="text-xs text-amber-300 font-medium">Bahan Keluar</div>
                </div>

                
                <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/10 border border-purple-500/20 rounded-2xl p-4 text-center backdrop-blur-sm">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-money-bill text-white text-sm"></i>
                    </div>
                    <div class="text-xl font-bold text-white mb-1">Rp <?php echo e(number_format($totalHargaBeli, 0, ',', '.')); ?></div>
                    <div class="text-xs text-purple-300 font-medium">Total Beli</div>
                </div>

                
                <div class="bg-gradient-to-br from-cyan-500/10 to-cyan-600/10 border border-cyan-500/20 rounded-2xl p-4 text-center backdrop-blur-sm">
                    <div class="w-10 h-10 bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-plus text-white text-sm"></i>
                    </div>
                    <div class="text-xl font-bold text-white mb-1"><?php echo e($perintahBaruCount); ?></div>
                    <div class="text-xs text-cyan-300 font-medium">Perintah Baru</div>
                </div>

                
                <div class="bg-gradient-to-br from-orange-500/10 to-orange-600/10 border border-orange-500/20 rounded-2xl p-4 text-center backdrop-blur-sm">
                    <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-cog text-white text-sm"></i>
                    </div>
                    <div class="text-xl font-bold text-white mb-1"><?php echo e($sedangAktifCount); ?></div>
                    <div class="text-xs text-orange-300 font-medium">Sedang Aktif</div>
                </div>
            </div>
        </div>

    </div>
</div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/dashboard.blade.php ENDPATH**/ ?>