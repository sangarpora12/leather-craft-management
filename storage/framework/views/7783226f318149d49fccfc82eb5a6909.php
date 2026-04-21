<!-- ==================== FORM NOTIFIKASI ==================== -->
<h2 class="text-sm font-medium mb-3 flex items-center">
    <i class="fa-solid fa-bell mr-2 text-pink-400"></i>
    Pengaturan Notifikasi Sistem
</h2>
<p class="text-xs text-zinc-400 mb-4">Pilih aktivitas pabrik apa saja yang akan memunculkan peringatan pada lonceng notifikasi Anda</p>

<?php if(session('success')): ?>
<div class="bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-check-circle mr-1"></i><?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<form action="<?php echo e(route('admin.laporan.pengaturan.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    
    <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6 mb-6">
        <!-- Item 1: Peringatan Stok Bahan Menipis -->
        <div class="flex items-center justify-between py-4 border-b border-zinc-800">
            <div class="flex-1 pr-4">
                <h3 class="text-sm font-medium text-white">Peringatan Stok Bahan Menipis</h3>
                <p class="text-xs text-zinc-500 mt-1">Tampilkan notifikasi saat stok bahan baku hampir habis</p>
            </div>
            <select name="notif_stok_menipis" class="bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-pink-500 w-48">
                <option value="1" <?php echo e(($pengaturanCollection['notif_stok_menipis'] ?? '1') == '1' ? 'selected' : ''); ?>>Aktif (Tampilkan Notifikasi)</option>
                <option value="0" <?php echo e(($pengaturanCollection['notif_stok_menipis'] ?? '1') == '0' ? 'selected' : ''); ?>>Tidak Aktif</option>
            </select>
        </div>
        
        <!-- Item 2: Notifikasi Produksi Selesai -->
        <div class="flex items-center justify-between py-4 border-b border-zinc-800">
            <div class="flex-1 pr-4">
                <h3 class="text-sm font-medium text-white">Notifikasi Produksi Selesai</h3>
                <p class="text-xs text-zinc-500 mt-1">Beritahu saat proses produksi telah selesai</p>
            </div>
            <select name="notif_produksi_selesai" class="bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-pink-500 w-48">
                <option value="1" <?php echo e(($pengaturanCollection['notif_produksi_selesai'] ?? '1') == '1' ? 'selected' : ''); ?>>Aktif</option>
                <option value="0" <?php echo e(($pengaturanCollection['notif_produksi_selesai'] ?? '1') == '0' ? 'selected' : ''); ?>>Tidak Aktif</option>
            </select>
        </div>
        
        <!-- Item 3: Notifikasi Barang / Bahan Masuk -->
        <div class="flex items-center justify-between py-4 border-b border-zinc-800">
            <div class="flex-1 pr-4">
                <h3 class="text-sm font-medium text-white">Notifikasi Barang / Bahan Masuk</h3>
                <p class="text-xs text-zinc-500 mt-1">Beritahu saat ada bahan baru yang masuk gudang</p>
            </div>
            <select name="notif_bahan_masuk" class="bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-pink-500 w-48">
                <option value="1" <?php echo e(($pengaturanCollection['notif_bahan_masuk'] ?? '1') == '1' ? 'selected' : ''); ?>>Aktif</option>
                <option value="0" <?php echo e(($pengaturanCollection['notif_bahan_masuk'] ?? '1') == '0' ? 'selected' : ''); ?>>Tidak Aktif</option>
            </select>
        </div>
        
        <!-- Item 4: Peringatan Penyesuaian Stok Manual -->
        <div class="flex items-center justify-between py-4">
            <div class="flex-1 pr-4">
                <h3 class="text-sm font-medium text-white">Peringatan Penyesuaian Stok Manual</h3>
                <p class="text-xs text-zinc-500 mt-1">Beritahu saat ada penyesuaian stok yang dilakukan manual</p>
            </div>
            <select name="notif_penyesuaian_stok" class="bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-pink-500 w-48">
                <option value="1" <?php echo e(($pengaturanCollection['notif_penyesuaian_stok'] ?? '1') == '1' ? 'selected' : ''); ?>>Aktif</option>
                <option value="0" <?php echo e(($pengaturanCollection['notif_penyesuaian_stok'] ?? '1') == '0' ? 'selected' : ''); ?>>Tidak Aktif</option>
            </select>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-6 rounded-lg transition-colors flex items-center text-sm shadow-lg shadow-blue-900/20">
            <i class="fa-solid fa-save mr-2"></i>
            Simpan Pengaturan Notifikasi
        </button>
    </div>
</form>
<?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pengaturan/tabs/_notifikasi.blade.php ENDPATH**/ ?>