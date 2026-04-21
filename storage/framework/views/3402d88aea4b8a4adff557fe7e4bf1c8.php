<!-- ==================== FORM HAK AKSES ==================== -->
<h2 class="text-sm font-medium mb-3 flex items-center">
    <i class="fa-solid fa-shield-alt mr-2 text-cyan-400"></i>
    Hak Akses - Kelola Role Pengguna
</h2>

<?php if(session('success')): ?>
<div class="bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-check-circle mr-1"></i><?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="bg-red-900/30 border border-red-800 text-red-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-x-circle mr-1"></i><?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<div class="bg-neutral-900 border border-zinc-800 rounded-lg overflow-hidden mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-zinc-800 text-zinc-400">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Jabatan</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-zinc-300 divide-y divide-zinc-800">
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-zinc-800/50">
                    <td class="px-4 py-3"><?php echo e($index + 1); ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-cyan-600 flex items-center justify-center text-white font-bold text-xs">
                                <?php echo e(strtoupper(substr($user->name, 0, 2))); ?>

                            </div>
                            <span class="font-medium"><?php echo e($user->name); ?></span>
                            <?php if($user->id === Auth::id()): ?>
                            <span class="px-2 py-0.5 text-xs bg-blue-500/20 text-blue-400 rounded">Anda</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-4 py-3"><?php echo e($user->email); ?></td>
                    <td class="px-4 py-3">
                        <?php if($user->role === 'admin'): ?>
                        <span class="px-2 py-1 text-xs font-medium bg-purple-500/20 text-purple-400 rounded">Administrator</span>
                        <?php else: ?>
                        <span class="px-2 py-1 text-xs font-medium bg-blue-500/20 text-blue-400 rounded">Operator</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3"><?php echo e($user->jabatan ?? '-'); ?></td>
                    <td class="px-4 py-3">
                        <?php if($user->id !== Auth::id()): ?>
                        <form action="<?php echo e(route('admin.hak-akses.update-role', $user->id)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <select name="role" onchange="this.form.submit()" class="bg-zinc-800 text-zinc-300 text-xs rounded px-2 py-1 border border-zinc-700 focus:border-cyan-500 focus:outline-none">
                                <option value="admin" <?php echo e($user->role === 'admin' ? 'selected' : ''); ?>>Administrator</option>
                                <option value="operator" <?php echo e($user->role === 'operator' ? 'selected' : ''); ?>>Operator</option>
                            </select>
                        </form>
                        <?php else: ?>
                        <span class="text-zinc-500 text-xs italic">Tidak dapat diubah</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-zinc-500">
                        <i class="fa-solid fa-users text-xl mb-2 block"></i>
                        Tidak ada pengguna ditemukan.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="bg-neutral-900 border border-zinc-800 rounded-lg p-4">
    <div class="flex items-start gap-4">
        <div class="w-10 h-10 rounded-full bg-cyan-500/20 flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-circle-info text-cyan-400"></i>
        </div>
        <div>
            <h3 class="text-white font-medium text-sm">Informasi Hak Akses</h3>
            <p class="text-zinc-400 text-xs mt-1">
                <strong>Administrator</strong> memiliki akses penuh ke semua fitur termasuk mengelola pengguna, melihat semua laporan, dan pengaturan sistem.
                <br>
                <strong>Operator</strong> memiliki akses terbatas untuk mengoperasikan fitur produksi dan input data.
            </p>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pengaturan/tabs/_hakakses.blade.php ENDPATH**/ ?>