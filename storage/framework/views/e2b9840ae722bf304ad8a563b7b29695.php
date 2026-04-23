

<?php $__env->startSection('content'); ?>
<div class="min-h-screen">
    <div class="p-6 pt-2">
        <div id="menu-grid" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php
                $menus = [
                    ['id' => 'satuan', 'icon' => 'fa-ruler-combined', 'color' => 'purple', 'title' => 'Satuan & Kategori', 'desc' => 'Kelola satuan dan kategori'],
                    ['id' => 'gudang', 'icon' => 'fa-warehouse', 'color' => 'green', 'title' => 'Konfigurasi Gudang', 'desc' => 'Batas stok, margin & harga jual'],
                    // ['id' => 'laporan', 'icon' => 'fa-file-pdf', 'color' => 'red', 'title' => 'Laporan & PDF', 'desc' => 'Format & template laporan'],
                    ['id' => 'notifikasi', 'icon' => 'fa-bell', 'color' => 'pink', 'title' => 'Notifikasi', 'desc' => 'Bot Telegram & stok kritis'],
                    // ['id' => 'shift', 'icon' => 'fa-clock', 'color' => 'indigo', 'title' => 'Shift & Operasional', 'desc' => 'Jadwal buka/tutup pabrik'],
                    // ['id' => 'hakakses', 'icon' => 'fa-shield-alt', 'color' => 'cyan', 'title' => 'Hak Akses', 'desc' => 'Manajemen role & izin'],
                    // ['id' => 'log', 'icon' => 'fa-history', 'color' => 'teal', 'title' => 'Log Aktivitas', 'desc' => 'Riwayat aktivitas sistem'],
                    ['id' => 'danger', 'icon' => 'fa-exclamation-triangle', 'color' => 'red', 'title' => 'Danger Zone', 'desc' => 'Reset & hapus data'],
                ];
            ?>

            <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div onclick="bukaMenu('<?php echo e($menu['id']); ?>')" 
                 class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 cursor-pointer hover:scale-105 hover:bg-zinc-800 hover:border-<?php echo e($menu['color']); ?>-500 transition-all duration-300 group shadow-sm">
                <div class="w-14 h-14 bg-<?php echo e($menu['color']); ?>-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:bg-<?php echo e($menu['color']); ?>-500/30 transition-colors">
                    <i class="fa-solid <?php echo e($menu['icon']); ?> text-2xl text-<?php echo e($menu['color']); ?>-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2"><?php echo e($menu['title']); ?></h3>
                <p class="text-sm text-zinc-400"><?php echo e($menu['desc']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div id="area-form" class="hidden">
            <button onclick="kembaliKeMenu()" class="mb-4 flex items-center gap-2 text-xs text-zinc-500 hover:text-zinc-300 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Menu Pengaturan</span>
            </button>

            <div class="form-section hidden" id="form-satuan"><?php echo $__env->make('admin.pengaturan.tabs._satuan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="form-section hidden" id="form-gudang"><?php echo $__env->make('admin.pengaturan.tabs._gudang', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="form-section hidden" id="form-laporan"><?php echo $__env->make('admin.pengaturan.tabs._laporan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="form-section hidden" id="form-keuangan"><?php echo $__env->make('admin.pengaturan.tabs._keuangan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="form-section hidden" id="form-notifikasi"><?php echo $__env->make('admin.pengaturan.tabs._notifikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="form-section hidden" id="form-shift"><?php echo $__env->make('admin.pengaturan.tabs._shift', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="form-section hidden" id="form-hakakses"><?php echo $__env->make('admin.pengaturan.tabs._hakakses', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="form-section hidden" id="form-log"><?php echo $__env->make('admin.pengaturan.tabs._log', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <div class="form-section hidden" id="form-danger"><?php echo $__env->make('admin.pengaturan.tabs._danger', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
        </div>
    </div>
</div>

<script>
    function bukaMenu(id_menu) {
        document.getElementById('menu-grid').classList.add('hidden');
        document.getElementById('area-form').classList.remove('hidden');
        document.querySelectorAll('.form-section').forEach(section => section.classList.add('hidden'));
        document.getElementById('form-' + id_menu).classList.remove('hidden');
        window.scrollTo(0, 0);
    }
    
    function kembaliKeMenu() {
        document.getElementById('area-form').classList.add('hidden');
        document.getElementById('menu-grid').classList.remove('hidden');
        window.scrollTo(0, 0);
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pengaturan/index.blade.php ENDPATH**/ ?>