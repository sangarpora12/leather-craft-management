<!-- ==================== FORM LOG AKTIVITAS ==================== -->
<h2 class="text-sm font-medium mb-3 flex items-center">
    <i class="fa-solid fa-history mr-2 text-teal-400"></i>
    Log Aktivitas
</h2>
<p class="text-xs text-zinc-400 mb-4">Riwayat aktivitas pengguna sistem</p>

<div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-zinc-800 text-zinc-400">
                <tr>
                    <th class="px-4 py-3 rounded-tl-lg w-16">No</th>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">Pengguna</th>
                    <th class="px-4 py-3">Modul</th>
                    <th class="px-4 py-3 rounded-tr-lg">Aktivitas</th>
                </tr>
            </thead>
            <tbody class="text-zinc-300 divide-y divide-zinc-800">
                <?php $__empty_1 = true; $__currentLoopData = $logs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-zinc-800/50">
                    <td class="px-4 py-3"><?php echo e($index + 1); ?></td>
                    <td class="px-4 py-3 text-zinc-400"><?php echo e(isset($log->created_at) ? $log->created_at->format('d/m/Y H:i') : '-'); ?></td>
                    <td class="px-4 py-3"><?php echo e($log->user->name ?? 'Unknown'); ?></td>
                    <td class="px-4 py-3"><span class="px-2 py-1 bg-teal-500/20 text-teal-400 rounded text-xs"><?php echo e($log->modul ?? '-'); ?></span></td>
                    <td class="px-4 py-3"><?php echo e($log->aktivitas ?? '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-zinc-500">
                        <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 block"></i>
                        Belum ada riwayat aktivitas yang terekam.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pengaturan/tabs/_log.blade.php ENDPATH**/ ?>