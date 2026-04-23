<nav class="fixed bottom-0 left-0 right-0 bg-[#161A22] border-t border-white/5 safe-bottom z-40">
    <div class="max-w-md mx-auto grid grid-cols-5 py-3">
        <?php
            $isDashboard = Request::is('operator/dashboard') || Route::is('operator.dashboard');
            $isTugas     = Request::is('operator/tugas*') || Request::is('operator/produksi*') || Route::is('operator.tugas.*') || Route::is('operator.produksi.*');
            $isScan      = Request::is('operator/scan*') || Route::is('operator.scan.*');
            $isStok      = Request::is('operator/bahan*') || Request::is('operator/input-manual-bahan') || Route::is('operator.bahan.*') || Route::is('operator.input-manual-bahan');
            $isPengadaan = Request::is('operator/pengadaan*') || Request::is('operator/belanja*') || Request::is('operator/daftar-belanja*') || Route::is('operator.pengadaan*') || Route::is('operator.belanja.*');
        ?>

        
        <a href="<?php echo e(route('operator.dashboard')); ?>" class="flex flex-col items-center gap-1 <?php echo e($isDashboard ? 'text-blue-400' : 'text-gray-500 hover:text-white transition'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[10px] font-medium">Dashboard</span>
        </a>

        
        <a href="<?php echo e(route('operator.tugas.index')); ?>" class="flex flex-col items-center gap-1 <?php echo e($isTugas ? 'text-blue-400' : 'text-gray-500 hover:text-white transition'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="text-[10px] font-medium">Tugas</span>
        </a>

        
        <a href="<?php echo e(route('operator.scan.index')); ?>" class="flex flex-col items-center gap-1 -mt-5">
            <div class="w-14 h-14 bg-[#2A3140] rounded-2xl border border-white/10 flex items-center justify-center shadow-xl <?php echo e($isScan ? 'ring-2 ring-blue-400' : ''); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 <?php echo e($isScan ? 'text-blue-400' : 'text-white'); ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <span class="text-[10px] font-medium <?php echo e($isScan ? 'text-blue-400' : 'text-gray-400'); ?>">Scan</span>
        </a>

        
        <a href="<?php echo e(route('operator.bahan.index')); ?>" class="flex flex-col items-center gap-1 <?php echo e($isStok ? 'text-blue-400' : 'text-gray-500 hover:text-white transition'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span class="text-[10px] font-medium">Stok</span>
        </a>

        
        <a href="<?php echo e(route('operator.pengadaan.index')); ?>" class="flex flex-col items-center gap-1 <?php echo e($isPengadaan ? 'text-blue-400' : 'text-gray-500 hover:text-white transition'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M12 16h.01" />
            </svg>
            <span class="text-[10px] font-medium">Pengadaan</span>
        </a>
    </div>
</nav>
<?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/components/bottom-nav-operator.blade.php ENDPATH**/ ?>