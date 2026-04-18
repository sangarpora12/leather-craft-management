<?php $__env->startSection('title_header', 'Kotak Masuk'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto space-y-6">

    
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        
        
        <div class="flex bg-[#111] p-1 rounded-lg border border-white/10">
            <a href="<?php echo e(route('admin.notifications.index', ['filter' => 'all'])); ?>"
               class="px-4 py-2 rounded-md text-xs font-bold transition
               <?php echo e($filter == 'all' ? 'bg-white/10 text-white' : 'text-gray-500 hover:text-white'); ?>">
               Semua
            </a>

            <a href="<?php echo e(route('admin.notifications.index', ['filter' => 'unread'])); ?>"
               class="px-4 py-2 rounded-md text-xs font-bold transition
               <?php echo e($filter == 'unread' ? 'bg-emerald-500/20 text-emerald-500' : 'text-gray-500 hover:text-white'); ?>">
               Belum Dibaca
            </a>

            <a href="<?php echo e(route('admin.notifications.index', ['filter' => 'read'])); ?>"
               class="px-4 py-2 rounded-md text-xs font-bold transition
               <?php echo e($filter == 'read' ? 'bg-white/10 text-white' : 'text-gray-500 hover:text-white'); ?>">
               Arsip
            </a>
        </div>

        
        <div class="flex gap-2">
            <?php if(Auth::user()->unreadNotifications()->count() > 0): ?>
            <form action="<?php echo e(route('admin.notifications.mark-all')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button
                    class="bg-blue-600/20 border border-blue-600/50 text-blue-500
                    hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg
                    text-xs font-bold transition flex items-center gap-2">
                    <i class="fas fa-check-double"></i> Baca Semua
                </button>
            </form>
            <?php endif; ?>

            <?php if($paginatedNotifications->count() > 0): ?>
            <form action="<?php echo e(route('admin.notifications.delete-all')); ?>" method="POST"
                  onsubmit="return confirm('Yakin hapus semua riwayat?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button
                    class="bg-red-600/20 border border-red-600/50 text-red-500
                    hover:bg-red-600 hover:text-white px-4 py-2 rounded-lg
                    text-xs font-bold transition flex items-center gap-2">
                    <i class="fas fa-trash"></i> Hapus Semua
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="bg-[#111] border border-white/10 rounded-xl overflow-hidden shadow-2xl min-h-[400px]">

        <?php if($paginatedNotifications->count() > 0): ?>
        <div class="divide-y divide-white/5">

            <?php $__currentLoopData = $paginatedNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isUnread = $notif->read_at === null;
                    $bgClass = $isUnread
                        ? 'bg-[#1a1a1a] border-l-4 border-emerald-500'
                        : 'bg-[#0a0a0a] opacity-75 grayscale hover:grayscale-0';
                ?>

                <div class="group relative flex items-center justify-between p-4 hover:bg-[#222] transition <?php echo e($bgClass); ?>">

                    
                    <a href="<?php echo e(route('admin.notifications.read', $notif->id)); ?>"
                       class="flex-1 flex gap-4 items-start">

                        
                        <div class="mt-1 shrink-0">
                            <?php if(($notif->data['type'] ?? '') === 'warning'): ?>
                                <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center
                                    justify-center text-red-500 border border-red-500/20">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            <?php elseif(($notif->data['type'] ?? '') === 'success'): ?>
                                <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center
                                    justify-center text-emerald-500 border border-emerald-500/20">
                                    <i class="fas fa-check"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center
                                    justify-center text-blue-500 border border-blue-500/20">
                                    <i class="fas fa-info"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div>
                            <h4 class="text-sm mb-1 <?php echo e($isUnread ? 'text-white font-bold' : 'text-gray-400'); ?>">
                                <?php echo e($notif->data['judul'] ?? $notif->data['title'] ?? 'Notifikasi Sistem'); ?>

                                <?php if($isUnread): ?>
                                    <span class="ml-2 w-2 h-2 bg-emerald-500 rounded-full inline-block"></span>
                                <?php endif; ?>
                            </h4>

                            <p class="text-xs text-gray-500 line-clamp-1 group-hover:text-gray-300 transition">
                                <?php echo e($notif->data['pesan'] ?? $notif->data['message'] ?? ''); ?>

                            </p>

                            <span class="text-[10px] text-gray-600 mt-2 block font-mono">
                                <?php echo e(\Carbon\Carbon::parse($notif->created_at)->format('d M Y, H:i')); ?>

                                • <?php echo e(\Carbon\Carbon::parse($notif->created_at)->diffForHumans()); ?>

                            </span>
                        </div>
                    </a>

                    
                    <form action="<?php echo e(route('admin.notifications.destroy', $notif->id)); ?>"
                          method="POST"
                          class="ml-4 opacity-0 group-hover:opacity-100 transition-opacity">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button
                            class="w-8 h-8 flex items-center justify-center rounded-full
                            bg-white/5 hover:bg-red-600 hover:text-white
                            text-gray-500 transition"
                            title="Hapus">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>

        
        <div class="p-4 border-t border-white/5">
            <?php echo e($paginatedNotifications->links()); ?>

        </div>

        <?php else: ?>
        
        <div class="flex flex-col items-center justify-center h-96 text-gray-600">
            <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-inbox text-4xl opacity-30"></i>
            </div>
            <p class="text-sm font-bold">Tidak ada notifikasi ditemukan</p>
            <?php if($filter !== 'all'): ?>
                <a href="<?php echo e(route('admin.notifications.index')); ?>"
                   class="text-xs text-blue-500 hover:underline mt-2">
                   Lihat Semua Kategori
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>