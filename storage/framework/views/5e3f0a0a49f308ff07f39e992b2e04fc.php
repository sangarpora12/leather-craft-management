<!-- ==================== FORM KONFIGURASI GUDANG & STOK ==================== -->
<div class="flex items-center gap-3 mb-6">
    <div class="p-2 bg-green-500/10 rounded-lg text-green-500">
        <i class="fa-solid fa-warehouse text-xl"></i>
    </div>
    <div>
        <h3 class="text-white font-bold text-lg">Konfigurasi Gudang & Stok</h3>
        <p class="text-zinc-400 text-sm">Atur parameter otomatisasi inventaris pabrik</p>
    </div>
</div>

<?php if(session('success')): ?>
<div class="bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-check-circle mr-1"></i><?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="bg-red-900/30 border border-red-800 text-red-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-circle-exclamation mr-1"></i><?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<form action="<?php echo e(route('admin.pengaturan.store')); ?>" method="POST" class="space-y-6" id="form-konfigurasi">
    <?php echo csrf_field(); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-zinc-400 text-sm mb-2">✅ Stok Minimum Bahan Baku</label>
            <input type="number" name="stok_minimum_bahan" value="<?php echo e($config->stok_minimum_bahan ?? 5); ?>" 
                   class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg focus:ring-green-500 border">
        </div>

        <div>
            <label class="block text-zinc-400 text-sm mb-2">✅ Stok Minimum Produk Jadi</label>
            <input type="number" name="stok_minimum_produk" value="<?php echo e($config->stok_minimum_produk ?? 2); ?>" 
                   class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg focus:ring-green-500 border">
        </div>

        <div>
            <label class="block text-zinc-400 text-sm mb-2">Awalan Kode Bahan (Prefix)</label>
            <input type="text" name="prefix_bahan" value="<?php echo e($config->prefix_bahan ?? 'BHN-'); ?>" 
                   class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg uppercase border">
        </div>

        <div>
            <label class="block text-zinc-400 text-sm mb-2">Awalan Kode Produk (Prefix)</label>
            <input type="text" name="prefix_produk" value="<?php echo e($config->prefix_produk ?? 'PROD-'); ?>" 
                   class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg uppercase border">
        </div>

        <div>
            <label class="block text-zinc-400 text-sm mb-2">💰 Margin Keuntungan Default (%)</label>
            <input type="number" name="margin_keuntungan" value="<?php echo e($pengaturanCollection['margin_keuntungan'] ?? '30'); ?>" step="0.1" 
                   class="w-full bg-zinc-800 border-zinc-700 text-white rounded-lg focus:ring-[#FF6B00] border">
            <p class="text-xs text-zinc-500 mt-1">Margin otomatis untuk perhitungan Harga Jual Produk Jadi.</p>
        </div>

    </div>

    <div class="pt-4 border-t border-zinc-800 flex gap-3">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition flex items-center gap-2" id="btn-simpan">
            <i class="fa-solid fa-save"></i> Simpan Konfigurasi
        </button>
    </div>
</form>

<!-- SweetAlert2 Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Notifikasi untuk tombol Simpan Konfigurasi
    document.getElementById('form-konfigurasi').addEventListener('submit', function(e) {
        // Biarkan form submit normally, SweetAlert akan muncul setelah redirect dengan session
    });

    // Tampilkan SweetAlert berdasarkan session flash message
    <?php if(session('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?php echo e(session('success')); ?>',
            background: '#18181b',
            color: '#fff',
            confirmButtonColor: '#22c55e',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>

    <?php if(session('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?php echo e(session('error')); ?>',
            background: '#18181b',
            color: '#fff',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
</script><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pengaturan/tabs/_gudang.blade.php ENDPATH**/ ?>