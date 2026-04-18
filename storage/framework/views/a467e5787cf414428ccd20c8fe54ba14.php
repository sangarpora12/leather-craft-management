<!-- ==================== FORM LAPORAN & PDF ==================== -->
<h2 class="text-sm font-medium mb-3 flex items-center">
    <i class="fa-solid fa-file-pdf mr-2 text-red-400"></i>
    Format Laporan & Cetak PDF
</h2>
<p class="text-xs text-zinc-400 mb-4">Atur penandatangan dan catatan kaki untuk dokumen cetak</p>

<?php if(session('success')): ?>
<div class="bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg mb-4 text-xs">
    <i class="fa-solid fa-check-circle mr-1"></i><?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<form action="<?php echo e(route('admin.laporan.pengaturan.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Kolom Kiri: Tanda Tangan Kiri -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-xs font-medium mb-4 flex items-center text-red-400">
                <i class="fa-solid fa-pen-to-square mr-2"></i>
                Tanda Tangan Kiri (Misal: Admin / Operator)
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Nama Lengkap</label>
                    <input type="text" name="ttd_kiri_nama" value="<?php echo e($pengaturanCollection['ttd_kiri_nama'] ?? ''); ?>" placeholder="Nama Lengkap" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-red-500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Jabatan</label>
                    <input type="text" name="ttd_kiri_jabatan" value="<?php echo e($pengaturanCollection['ttd_kiri_jabatan'] ?? ''); ?>" placeholder="Jabatan" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-red-500">
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan: Tanda Tangan Kanan -->
        <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6">
            <h3 class="text-xs font-medium mb-4 flex items-center text-red-400">
                <i class="fa-solid fa-pen-to-square mr-2"></i>
                Tanda Tangan Kanan (Misal: Direktur / Pimpinan)
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Nama Lengkap</label>
                    <input type="text" name="ttd_kanan_nama" value="<?php echo e($pengaturanCollection['ttd_kanan_nama'] ?? ''); ?>" placeholder="Nama Lengkap" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-red-500">
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-zinc-400 mb-1">Jabatan</label>
                    <input type="text" name="ttd_kanan_jabatan" value="<?php echo e($pengaturanCollection['ttd_kanan_jabatan'] ?? ''); ?>" placeholder="Jabatan" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-red-500">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bagian Footer -->
    <div class="bg-neutral-900 border border-zinc-800 rounded-xl p-6 mb-6">
        <h3 class="text-xs font-medium mb-4 flex items-center text-red-400">
            <i class="fa-solid fa-footer mr-2"></i>
            Catatan Kaki / Footer
        </h3>
        
        <div>
            <label class="block text-xs font-medium text-zinc-400 mb-1">Teks Footer Laporan (Dicetak di bagian paling bawah kertas)</label>
            <textarea name="teks_footer_pdf" rows="3" class="w-full bg-zinc-800 border border-zinc-700 rounded px-3 py-2 text-sm text-white focus:outline-none focus:border-red-500 resize-none"><?php echo e($pengaturanCollection['teks_footer_pdf'] ?? 'Dokumen ini dicetak secara otomatis oleh Sistem Berliano Leather.'); ?></textarea>
        </div>
    </div>
    
    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-medium py-2 px-6 rounded-lg transition-colors flex items-center text-sm shadow-lg shadow-blue-900/20">
            <i class="fa-solid fa-save mr-2"></i>
            Simpan Format Laporan
        </button>
    </div>
</form>
<?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/pengaturan/tabs/_laporan.blade.php ENDPATH**/ ?>