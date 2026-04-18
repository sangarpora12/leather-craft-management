<div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto bg-[#0f172a]/95 backdrop-blur-lg border-t border-slate-800 px-6 py-3 flex justify-between items-end z-50 safe-bottom">
    
    
    <?php
        $isDashboard = Request::is('operator/dashboard') || Route::is('operator.dashboard');
    ?>
    <a href="<?php echo e(route('operator.dashboard')); ?>" class="flex flex-col items-center gap-1 group relative py-2">
        <i class="fas <?php echo e($isDashboard ? 'fa-solid fa-house' : 'fa-regular fa-house'); ?> text-lg transition-all duration-300 <?php echo e($isDashboard ? 'text-emerald-400 scale-110' : 'text-slate-500 group-hover:text-slate-300'); ?>"></i>
        <span class="text-[10px] font-normal transition-all duration-300 <?php echo e($isDashboard ? 'text-emerald-400 font-bold' : 'text-slate-500 group-hover:text-slate-300'); ?>">Dashboard</span>
        <?php if($isDashboard): ?>
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-1 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        <?php endif; ?>
    </a>

    
    <?php
        $isTugas = Request::is('operator/tugas*') || Request::is('operator/produksi*') || Route::is('operator.tugas.index') || Route::is('operator.tugas.detail') || Route::is('operator.produksi.show');
    ?>
    <a href="<?php echo e(route('operator.tugas.index')); ?>" class="flex flex-col items-center gap-1 group relative py-2">
        <i class="fas <?php echo e($isTugas ? 'fa-solid fa-clipboard-list' : 'fa-regular fa-clipboard-list'); ?> text-lg transition-all duration-300 <?php echo e($isTugas ? 'text-emerald-400 scale-110' : 'text-slate-500 group-hover:text-slate-300'); ?>"></i>
        <span class="text-[10px] font-normal transition-all duration-300 <?php echo e($isTugas ? 'text-emerald-400 font-bold' : 'text-slate-500 group-hover:text-slate-300'); ?>">Tugas</span>
        <?php if(isset($taskCount) && $taskCount > 0): ?>
        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse">
            <?php echo e($taskCount > 9 ? '9+' : $taskCount); ?>

        </span>
        <?php endif; ?>
        <?php if($isTugas): ?>
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-1 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        <?php endif; ?>
    </a>

    
    <?php
        $isScan = Request::is('operator/scan*') || Route::is('operator.scan.index') || Route::is('operator.scan.*');
    ?>
    <div class="relative -top-6">
        <a href="<?php echo e(route('operator.scan.index')); ?>" class="w-14 h-14 bg-gradient-to-b from-emerald-500 to-cyan-600 border-4 border-[#0f172a] rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/30 hover:scale-105 hover:shadow-emerald-500/50 transition-all duration-300">
            <i class="fas fa-qrcode text-white text-xl"></i>
        </a>
        <?php if($isScan): ?>
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-2 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        <?php endif; ?>
    </div>

    
    <?php
        $isStok = Request::is('operator/bahan*') || Route::is('operator.bahan.index') || Route::is('operator.bahan.create') || Route::is('operator.bahan.edit') || Route::is('operator.bahan.store') || Route::is('operator.bahan.update');
        $isInputManual = Request::is('operator/input-manual-bahan') || Route::is('operator.input-manual-bahan');
    ?>
    <a href="<?php echo e(route('operator.bahan.index')); ?>" class="flex flex-col items-center gap-1 group relative py-2">
        <i class="fas <?php echo e($isStok ? 'fa-solid fa-box' : 'fa-regular fa-box'); ?> text-lg transition-all duration-300 <?php echo e($isStok ? 'text-emerald-400 scale-110' : 'text-slate-500 group-hover:text-slate-300'); ?>"></i>
        <span class="text-[10px] font-normal transition-all duration-300 <?php echo e($isStok ? 'text-emerald-400 font-bold' : 'text-slate-500 group-hover:text-slate-300'); ?>">Stok</span>
        <?php if($isStok): ?>
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-1 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        <?php endif; ?>
    </a>
    

    
    <?php
        $isPengadaan = Request::is('operator/pengadaan*') || Request::is('operator/belanja*') || Route::is('operator.pengadaan') || Route::is('operator.belanja.index') || Route::is('operator.pengadaan.request') || Route::is('operator.belanja.selesai') || Route::is('operator.pengadaan.receive_form') || Route::is('operator.pengadaan.receive');
    ?>
    <a href="<?php echo e(route('operator.pengadaan')); ?>" class="flex flex-col items-center gap-1 group relative py-2">
        <i class="fas <?php echo e($isPengadaan ? 'fa-solid fa-cart-flatbed' : 'fa-regular fa-cart-flatbed'); ?> text-lg transition-all duration-300 <?php echo e($isPengadaan ? 'text-emerald-400 scale-110' : 'text-slate-500 group-hover:text-slate-300'); ?>"></i>
        <span class="text-[10px] font-normal transition-all duration-300 <?php echo e($isPengadaan ? 'text-emerald-400 font-bold' : 'text-slate-500 group-hover:text-slate-300'); ?>">Pengadaan</span>
        <?php if($isPengadaan): ?>
        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full absolute -bottom-1 left-1/2 -translate-x-1/2 shadow-[0_0_10px_rgba(52,211,153,0.8)] glow-dot"></div>
        <?php endif; ?>
    </a>

</div>
<?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/components/bottom-nav-operator.blade.php ENDPATH**/ ?>