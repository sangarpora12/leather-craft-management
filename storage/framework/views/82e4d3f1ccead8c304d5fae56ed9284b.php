<?php $__env->startSection('title', 'Notifikasi'); ?>

<?php $__env->startSection('main_class', 'pb-28'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#0f172a] bg-[radial-gradient(circle_at_top,#1e293b,transparent)]">
    <div class="max-w-md mx-auto shadow-2xl shadow-black/50 min-h-screen relative">
        <?php echo $__env->make('components.operator-header', ['title' => 'NOTIFIKASI'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="px-5 py-5 pb-32">
            <div class="bg-slate-900/60 border border-slate-700 rounded-3xl overflow-hidden">
                <div class="p-4 border-b border-slate-700 flex items-center justify-between">
                    <div class="text-white font-bold">Semua Notifikasi</div>
                    <form method="POST" action="<?php echo e(route('operator.notifications.mark-all-read')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="text-xs font-semibold px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white hover:bg-slate-700">
                            Tandai dibaca
                        </button>
                    </form>
                </div>

                <div>
                    <?php $__empty_1 = true; $__currentLoopData = $paginatedNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isUnread = is_null($notif->read_at);
                            $title = $notif->data['judul'] ?? ($notif->data['title'] ?? 'Notifikasi');
                            $message = $notif->data['pesan'] ?? ($notif->data['message'] ?? ($notif->data['body'] ?? '-'));
                        ?>
                        <a href="<?php echo e(route('operator.notifications.read', $notif->id)); ?>"
                           class="block px-4 py-4 border-b border-slate-800 hover:bg-slate-800/40 <?php echo e($isUnread ? 'bg-blue-500/10' : ''); ?>">
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full mt-2 <?php echo e($isUnread ? 'bg-red-500' : 'bg-slate-600'); ?>"></div>
                                <div class="flex-1">
                                    <div class="text-white font-semibold text-sm"><?php echo e($title); ?></div>
                                    <div class="text-slate-300 text-xs mt-1"><?php echo e($message); ?></div>
                                    <div class="text-slate-500 text-[11px] mt-2"><?php echo e($notif->created_at->diffForHumans()); ?></div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-6 text-center text-slate-400">Belum ada notifikasi.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
                <?php echo e($paginatedNotifications->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/notifications/index.blade.php ENDPATH**/ ?>