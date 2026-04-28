<?php $__env->startSection('page_title', 'Manajemen Operator'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#0B0E14] px-6 py-6" x-data="operatorPage()">

    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen Operator</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola akun karyawan yang bertugas menerima perintah produksi.</p>
        </div>
        <button onclick="openModal('modalTambahOperator')" class="bg-orange-600 hover:bg-orange-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-orange-900/20 flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Tambah Operator
        </button>
    </div>

    
    <div class="bg-[#1C222D] rounded-xl p-4 mb-6 border border-white/5">
        <div class="flex flex-wrap items-center gap-3">
            
            <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                <input type="text"
                       x-model="search"
                       @input="filterOperators()"
                       placeholder="Cari operator..."
                       class="w-full bg-[#0B0E14] border border-gray-700 rounded-lg pl-9 pr-4 py-2 text-sm text-white placeholder:text-gray-600 focus:outline-none focus:border-orange-500 transition">
            </div>

            
            <select x-model="filterStatus" @change="filterOperators()"
                    class="bg-[#0B0E14] border border-gray-700 text-white text-sm px-4 py-2 rounded-lg focus:outline-none focus:border-orange-500 cursor-pointer min-w-[140px]">
                <option value="all">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>

        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isActive = $op->is_active == true || $op->is_active === 1 || is_null($op->is_active);
            ?>

            <div class="bg-[#1C222D] rounded-2xl border <?php echo e($isActive ? 'border-white/5' : 'border-red-500/30'); ?> overflow-hidden relative transition-all duration-300 hover:translate-y-[-2px] shadow-lg"
                 data-name="<?php echo e(strtolower($op->name)); ?>"
                 data-status="<?php echo e($isActive ? 'active' : 'inactive'); ?>">

                
                <?php if(!$isActive): ?>
                <div class="absolute inset-0 bg-black/40 z-10 pointer-events-none rounded-2xl"></div>
                <?php endif; ?>

                <div class="p-5 relative">
                    
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            
                            <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 ring-2 <?php echo e($isActive ? 'ring-gray-700' : 'ring-red-500/50'); ?>">
                                <?php if($op->foto_profil || $op->foto): ?>
                                    <img src="<?php echo e(asset('storage/' . ($op->foto_profil ?? $op->foto))); ?>" alt="<?php echo e($op->name); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center text-white font-bold text-base">
                                        <?php echo e(substr($op->name, 0, 1)); ?>

                                    </div>
                                <?php endif; ?>
                            </div>

                            
                            <div>
                                <h3 class="text-base font-bold text-white"><?php echo e($op->name); ?></h3>
                                <p class="text-[11px] text-gray-400"><?php echo e($op->jabatan ?? 'Operator Gudang'); ?></p>
                                <div class="flex gap-1.5 mt-1">
                                    <?php if(!$isActive): ?>
                                    <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded bg-red-500/15 text-red-400 border border-red-500/25">
                                        <i class="fa-solid fa-circle text-[6px] mr-1"></i> Non-Aktif
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                            <button @click="open = !open" class="text-gray-500 hover:text-white transition p-1.5 rounded-lg hover:bg-gray-700/50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                </svg>
                            </button>

                            
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-[#1C222D] border border-gray-700 rounded-xl shadow-xl shadow-black/50 z-20 overflow-hidden"
                                 style="display: none;">

                                <a href="<?php echo e(route('admin.operator.edit', $op->id)); ?>" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition">
                                    <i class="fa-solid fa-pen text-blue-400 text-xs"></i> Edit User
                                </a>

                                
                                <form action="<?php echo e(route('admin.operator.toggle-status', $op->id)); ?>" method="POST" class="block w-full" id="form-toggle-role-<?php echo e($op->id); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <input type="hidden" name="toggle_sub_role" value="1">
                                    <button type="button" onclick="swalConfirm({title: 'Ubah Role?', text: 'Ubah <?php echo e($op->name); ?> menjadi <?php echo e($op->sub_role === 'kepala_gudang' ? 'Pekerja' : 'Kepala Gudang'); ?>?', icon: 'question', confirmText: 'Ya, Ubah'}, () => document.getElementById('form-toggle-role-<?php echo e($op->id); ?>').submit())" class="w-full text-left flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition">
                                        <i class="fa-solid <?php echo e($op->sub_role === 'kepala_gudang' ? 'fa-user' : 'fa-crown'); ?> text-xs"></i>
                                        <?php echo e($op->sub_role === 'kepala_gudang' ? 'Turunkan ke Pekerja' : 'Angkat Kepala Gudang'); ?>

                                    </button>
                                </form>

                                <?php if($isActive): ?>
                                <form action="<?php echo e(route('admin.operator.toggle-status', $op->id)); ?>" method="POST" class="block w-full" id="form-nonaktif-<?php echo e($op->id); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="button" onclick="swalConfirm({title: 'Nonaktifkan Akun?', text: 'Akun <?php echo e($op->name); ?> akan dinonaktifkan dan tidak bisa login.', icon: 'warning', confirmText: 'Ya, Nonaktifkan'}, () => document.getElementById('form-nonaktif-<?php echo e($op->id); ?>').submit())" class="w-full text-left flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition">
                                        <i class="fa-solid fa-ban text-xs"></i> Nonaktifkan Akun
                                    </button>
                                </form>
                                <?php else: ?>
                                <form action="<?php echo e(route('admin.operator.toggle-status', $op->id)); ?>" method="POST" class="block w-full" id="form-aktif-<?php echo e($op->id); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button type="button" onclick="swalConfirm({title: 'Aktifkan Akun?', text: 'Akun <?php echo e($op->name); ?> akan diaktifkan kembali.', icon: 'question', confirmText: 'Ya, Aktifkan'}, () => document.getElementById('form-aktif-<?php echo e($op->id); ?>').submit())" class="w-full text-left flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition">
                                        <i class="fa-solid fa-check-circle text-xs"></i> Aktifkan Akun
                                    </button>
                                </form>
                                <?php endif; ?>

                                <form action="<?php echo e(route('admin.pekerja-master.destroy', $op->id)); ?>" method="POST" class="block w-full" id="form-hapus-<?php echo e($op->id); ?>">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="button" onclick="swalConfirm({title: 'Hapus Operator?', text: 'Data <?php echo e($op->name); ?> tidak bisa dikembalikan!', icon: 'error', confirmText: 'Ya, Hapus'}, () => document.getElementById('form-hapus-<?php echo e($op->id); ?>').submit())" class="w-full text-left flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-red-400 transition">
                                        <i class="fa-solid fa-trash text-xs"></i> Hapus User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    
                    <div class="space-y-3 mb-5">
                        
                        <div class="bg-[#0B0E14] rounded-xl p-3 border border-white/5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-gray-700/30 flex items-center justify-center">
                                        <i class="fa-solid fa-gear text-gray-400 text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-500 uppercase tracking-wider">Input Bahan Terbanyak</p>
                                        <?php if($op->bahan_terbanyak): ?>
                                        <p class="text-xs text-white font-bold truncate max-w-[140px]"><?php echo e($op->bahan_terbanyak->nama); ?></p>
                                        <?php else: ?>
                                        <p class="text-xs text-gray-600">-</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($op->bahan_terbanyak): ?>
                                <span class="text-xs font-bold text-purple-400"><?php echo e($op->bahan_terbanyak->total); ?> <span class="text-[10px] text-gray-500"><?php echo e($op->bahan_terbanyak->satuan); ?></span></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="bg-[#0B0E14] rounded-xl p-3 border border-white/5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-gray-700/30 flex items-center justify-center">
                                        <i class="fa-solid fa-bullseye text-gray-400 text-[10px]"></i>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-gray-500 uppercase tracking-wider">Produk Selesai Terbanyak</p>
                                        <?php if($op->produk_terbanyak): ?>
                                        <p class="text-xs text-white font-bold truncate max-w-[140px]"><?php echo e($op->produk_terbanyak->nama); ?></p>
                                        <?php else: ?>
                                        <p class="text-xs text-gray-600">-</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($op->produk_terbanyak): ?>
                                <span class="text-xs font-bold text-emerald-400"><?php echo e($op->produk_terbanyak->total); ?> <span class="text-[10px] text-gray-500"><?php echo e($op->produk_terbanyak->satuan); ?></span></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="flex gap-3">
                        <?php if($isActive): ?>
                            <a href="<?php echo e(route('admin.operator.edit', $op->id)); ?>"
                               class="flex-1 h-9 bg-[#1A1D29] hover:bg-[#252836] border border-white/5 rounded-lg flex items-center justify-center text-gray-300 text-xs font-semibold transition-colors"
                               title="Edit akun operator">
                                <i class="fa-solid fa-pen mr-1.5 text-[10px]"></i> Edit Akun
                            </a>
                            <a href="<?php echo e(route('admin.pekerja-master.kinerja', $op->id)); ?>"
                               class="flex-1 h-9 bg-[#1A1D29] hover:bg-[#252836] border border-white/5 rounded-lg text-white text-xs font-semibold flex items-center justify-center transition-colors"
                               title="Lihat detail kinerja operator">
                                <i class="fa-solid fa-chart-simple mr-1.5 text-[10px]"></i> Lihat Kinerja
                            </a>
                        <?php else: ?>
                            <form action="<?php echo e(route('admin.operator.toggle-status', $op->id)); ?>" method="POST" class="w-full" id="form-aktifkan-<?php echo e($op->id); ?>">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <button type="button" onclick="swalConfirm({title: 'Aktifkan Kembali?', text: 'Akun <?php echo e($op->name); ?> akan diaktifkan kembali.', icon: 'question', confirmText: 'Ya, Aktifkan'}, () => document.getElementById('form-aktifkan-<?php echo e($op->id); ?>').submit())"
                                        class="w-full h-9 bg-[#1A1D29] hover:bg-[#252836] border border-white/5 rounded-lg text-white text-xs font-semibold flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-power-off mr-1.5 text-[10px]"></i> Aktifkan Kembali
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full text-center py-20">
                <div class="w-16 h-16 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-users-slash text-gray-500 text-2xl"></i>
                </div>
                <p class="text-gray-500">Belum ada operator terdaftar.</p>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if($operators->hasPages()): ?>
    <div class="mt-8 flex justify-center">
        <?php echo e($operators->links()); ?>

    </div>
    <?php endif; ?>

</div>


<?php if($errors->any()): ?>
<div class="fixed top-4 right-4 z-50 bg-red-500/20 border border-red-500 text-red-500 p-4 rounded-xl text-xs max-w-sm">
    <strong>Ups! Ada yang salah:</strong>
    <ul class="mt-2 ml-4 list-disc">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>


<div id="modalTambahOperator" class="fixed inset-0 z-50 hidden items-center justify-center" aria-hidden="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeModal('modalTambahOperator')"></div>
    <div class="relative bg-[#181b21] w-full max-w-md rounded-2xl border border-gray-700 shadow-2xl p-6 m-4 animate-fade-in">

        <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
            <h2 class="text-lg font-bold text-white">Tambah Operator Baru</h2>
            <button onclick="closeModal('modalTambahOperator')" class="text-gray-500 hover:text-white transition"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form action="<?php echo e(route('admin.pekerja-master.store')); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>

            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Contoh: Budi Santoso"
                    class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none placeholder-gray-600">
            </div>

            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Email Login</label>
                <input type="email" name="email" required pattern="[^@\s]+@[^@\s]+\.[^@\s]+"
                    placeholder="email@contoh.com"
                    class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none placeholder-gray-600">
            </div>

            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Posisi / Jabatan</label>
                <select name="jabatan" class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none">
                    <option value="Staff Produksi">Staff Produksi</option>
                    <option value="Operator Jahit">Operator Jahit</option>
                    <option value="Operator Potong">Operator Potong</option>
                    <option value="Kepala Gudang">Kepala Gudang</option>
                </select>
            </div>

            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Tipe Akun</label>
                <select name="sub_role" class="w-full bg-[#0f1115] border border-gray-700 rounded-lg px-4 py-3 text-xs text-white focus:border-orange-500 focus:outline-none">
                    <option value="pekerja">Pekerja (Tidak Bisa Login)</option>
                    <option value="kepala_gudang">Kepala Gudang (Bisa Login)</option>
                </select>
                <p class="text-[9px] text-gray-600 mt-1">*Kepala Gudang bisa login dashboard. Pekerja hanya menerima instruksi via email.</p>
            </div>

            
            <div>
                <label class="block text-[10px] font-bold text-gray-400 mb-1.5 uppercase">Password Default</label>
                <input type="text" name="password" value="123456"
                    class="w-full bg-[#111] border border-gray-800 rounded-lg px-4 py-3 text-xs text-gray-500 cursor-not-allowed">
                <p class="text-[9px] text-gray-600 mt-1">*Password default: 123456 (Bisa diganti operator nanti)</p>
            </div>

            <div class="pt-6 flex justify-end gap-3 border-t border-gray-700 mt-2">
                <button type="button" onclick="closeModal('modalTambahOperator')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white transition">Batal</button>
                <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-500 text-white text-xs font-bold rounded-lg shadow-lg">Simpan Akun</button>
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

    function operatorPage() {
        return {
            search: '',
            filterStatus: 'all',
            filterOperators() {
                const cards = document.querySelectorAll('[data-name]');
                cards.forEach(card => {
                    const name = card.dataset.name;
                    const status = card.dataset.status;

                    const matchSearch = name.includes(this.search.toLowerCase());
                    const matchStatus = this.filterStatus === 'all' || status === this.filterStatus;

                    card.style.display = (matchSearch && matchStatus) ? '' : 'none';
                });
            }
        };
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pekerja-master/index.blade.php ENDPATH**/ ?>