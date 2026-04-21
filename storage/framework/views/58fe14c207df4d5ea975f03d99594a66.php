<?php $__env->startSection('page_title', 'Manajemen User'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 animate-fade-in font-sans text-gray-300">

    
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-6 border-b border-gray-800 pb-6">
        <div>
            <p class="text-xs text-gray-500">Kelola akun staf produksi, gudang, dan pengerjaan.</p>
        </div>
        
        <button onclick="openModal('modalTambahOperator')" 
            class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-blue-900/20 flex items-center gap-2 transition transform hover:scale-105">
            <i class="fa-solid fa-user-plus"></i> Tambah Operator
        </button>
    </div>

    

<?php if($errors->any()): ?>
    <div class="bg-red-500/20 border border-red-500 text-red-500 p-4 rounded-xl mb-4 text-xs">
        <strong>Ups! Ada yang salah:</strong>
        <ul class="mt-2 ml-4 list-disc">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-[#1f242c] rounded-2xl border border-gray-800 p-5 flex flex-col gap-4 relative group hover:border-gray-600 transition shadow-lg hover:shadow-xl">
            
            
            <div class="flex items-start justify-between">
                <div class="flex gap-4">
                    
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-700 to-gray-800 border border-gray-600 flex items-center justify-center text-white font-bold text-lg shadow-inner">
                        <?php echo e(substr($op->name, 0, 1)); ?>

                    </div>
                    <div>
                        <h3 class="text-white font-bold text-md leading-tight"><?php echo e($op->name); ?></h3>
                        <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded bg-blue-900/20 text-blue-400 border border-blue-900/30">
                            <?php echo e($op->jabatan ?? 'Staff Produksi'); ?>

                        </span>
                    </div>
                </div>

                
                <?php if($op->role !== 'admin'): ?>
                <form action="<?php echo e(route('admin.pekerja-master.destroy', $op->id)); ?>" method="POST" onsubmit="return confirm('Hapus operator ini? Data tidak bisa dikembalikan.');">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-600 hover:text-red-500 hover:bg-red-500/10 transition">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <hr class="border-gray-800">

            
            <div class="space-y-2">
                <div class="flex items-center gap-3 text-xs text-gray-400 bg-[#15181d] p-2 rounded-lg border border-gray-800/50">
                    <i class="fa-solid fa-envelope text-gray-600 w-4 text-center"></i> 
                    <span class="truncate"><?php echo e($op->email); ?></span>
                </div>
            </div>

            
            <div class="mt-2 flex justify-between items-center">
                <span class="text-[10px] text-gray-500">Status Akun:</span>
                <?php if($op->is_active == true || $op->is_active === 1 || is_null($op->is_active)): ?>
                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-400">
                        <i class="fa-solid fa-check-circle"></i> Aktif
                    </span>
                <?php else: ?>
                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-red-400">
                        <i class="fa-solid fa-times-circle"></i> Nonaktif
                    </span>
                <?php endif; ?>
            </div>
            
            
            <div class="mt-2 pt-2 border-t border-gray-800">
                <?php if($op->is_active == true || $op->is_active === 1 || is_null($op->is_active)): ?>
                <form action="<?php echo e(route('admin.operator.toggle-status', $op->id)); ?>" method="POST" class="block w-full">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <button type="submit" onclick="return confirm('Nonaktifkan akun operator ini? Operator tidak akan bisa login.')" class="flex items-center justify-center gap-2 w-full px-3 py-2 text-[10px] font-bold text-orange-400 hover:bg-orange-900/20 rounded-lg transition border border-orange-900/30">
                        <i class="fa-solid fa-power-off"></i> Nonaktifkan Akun
                    </button>
                </form>
                <?php else: ?>
                <form action="<?php echo e(route('admin.operator.toggle-status', $op->id)); ?>" method="POST" class="block w-full">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <button type="submit" onclick="return confirm('Aktifkan kembali akun operator ini?')" class="flex items-center justify-center gap-2 w-full px-3 py-2 text-[10px] font-bold text-green-400 hover:bg-green-900/20 rounded-lg transition border border-green-900/30">
                        <i class="fa-solid fa-power-off"></i> Aktifkan Akun
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full py-12 text-center text-gray-500 border-2 border-dashed border-gray-800 rounded-2xl">
            <div class="w-16 h-16 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-users-slash text-2xl"></i>
            </div>
            <p class="font-bold">Belum ada operator terdaftar.</p>
            <p class="text-xs mt-1">Klik tombol tambah untuk mendaftarkan staf baru.</p>
        </div>
        <?php endif; ?>
    </div>

</div>




<div id="modalTambahOperator" class="fixed inset-0 z-50 hidden items-center justify-center transition-opacity duration-300" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeModal('modalTambahOperator')"></div>

    
    <div class="relative bg-[#1f242c] w-full max-w-md rounded-2xl border border-gray-700 shadow-2xl transform transition-all scale-100 overflow-hidden m-4">
        
        
        <div class="bg-[#181b21] px-6 py-4 border-b border-gray-700 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-white">Tambah Operator</h3>
                <p class="text-[10px] text-gray-500">Undangan akan dikirim via email.</p>
            </div>
            <button type="button" onclick="closeModal('modalTambahOperator')" class="text-gray-500 hover:text-white transition bg-gray-800 hover:bg-gray-700 rounded-lg p-2">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?php echo e(route('admin.pekerja-master.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="p-6 space-y-5">
                
                
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-wide">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-gray-500 text-xs"></i>
                        </div>
                        <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                            class="w-full bg-[#111316] border border-gray-700 rounded-xl pl-9 pr-4 py-3 text-xs text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-600">
                    </div>
                </div>

                
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-wide">Email Login</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-500 text-xs"></i>
                        </div>
                        
                        <input type="email" name="email" required 
                            pattern="[^@\s]+@[^@\s]+\.[^@\s]+" 
                            title="Harap masukkan format email yang valid (harus ada @ dan nama domain)"
                            placeholder="email@contoh.com"
                            class="w-full bg-[#111316] border border-gray-700 rounded-xl pl-9 pr-4 py-3 text-xs text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-600 valid:border-emerald-500/50 invalid:border-red-500/50">
                    </div>
                    <p class="text-[9px] text-gray-500 mt-1">*Pastikan email aktif untuk verifikasi akun.</p>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-wide">Jabatan</label>
                        <div class="relative">
                            <select name="jabatan" class="w-full bg-[#111316] border border-gray-700 rounded-xl px-3 py-3 text-xs text-white focus:border-blue-500 focus:outline-none appearance-none cursor-pointer">
                                <option value="Staff Produksi">Staff Produksi</option>
                                <option value="Operator Jahit">Operator Jahit</option>
                                <option value="Operator Potong">Operator Potong</option>
                                <option value="Kepala Gudang">Kepala Gudang</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-chevron-down text-gray-600 text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 mb-2 uppercase tracking-wide">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa-solid fa-lock text-gray-500 text-xs"></i>
                            </div>
                            <input type="text" name="password" value="123456" 
                                class="w-full bg-[#111316] border border-gray-700 rounded-xl pl-9 pr-4 py-3 text-xs text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition placeholder-gray-600">
                        </div>
                    </div>
                </div>

            </div>

            
            <div class="bg-[#181b21] px-6 py-4 border-t border-gray-700 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalTambahOperator')" class="px-5 py-2.5 rounded-xl text-xs font-bold text-gray-400 hover:text-white hover:bg-gray-700 transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-blue-900/20 transition flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { 
        document.getElementById(id).classList.remove('hidden'); 
        document.getElementById(id).classList.add('flex'); 
    }
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
        document.getElementById(id).classList.remove('flex'); 
    }
</script>

<style>
    #modalTambahOperator.flex > div:last-child {
        animation: modalIn 0.3s ease-out forwards;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pekerja-master/index.blade.php ENDPATH**/ ?>