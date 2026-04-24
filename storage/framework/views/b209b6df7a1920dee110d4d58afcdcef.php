<?php $__env->startSection('title', 'Edit Bahan Baku'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-md mx-auto bg-gray-900 text-white min-h-screen">
    <!-- Header -->
    <header class="py-4 sticky top-0 z-40 flex items-center gap-4 bg-gray-900/95 backdrop-blur border-b border-gray-700 px-5">
        <a href="<?php echo e(route('operator.bahan.index')); ?>" class="w-8 h-8 flex items-center justify-center bg-gray-800 rounded-lg text-gray-400 hover:text-white hover:bg-gray-700 transition">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-white">Edit Bahan</h1>
            <p class="text-xs text-gray-400 font-mono"><?php echo e($bahan->kode_material); ?></p>
        </div>
    </header>

    <main class="pb-40 pt-6 px-5">
        <!-- ✅ FOTO MASTER + PREVIEW -->
        <div class="mb-8 text-center">
            <div class="mx-auto w-36 h-36 overflow-hidden rounded-2xl border-4 border-gray-700 shadow-xl bg-gray-700">
                <img id="previewFoto" 
                     src="<?php echo e($bahan->foto ? asset('storage/' . $bahan->foto) : ''); ?>" 
                     class="w-full h-full object-cover"
                     onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&#34;w-full h-full flex items-center justify-center bg-gray-700&#34;><i class=&#34;fa-solid fa-box text-gray-500 text-4xl&#34;></i></div>'">
            </div>
            
            <input type="file" id="fotoBahan" name="foto" form="editForm" accept="image/*" class="hidden">
            <button type="button" onclick="document.getElementById('fotoBahan').click()" class="mt-4 bg-gray-800 hover:bg-gray-700 text-white px-5 py-2.5 rounded-xl text-sm transition">
                <i class="fas fa-camera mr-2"></i>Ubah Foto
            </button>
        </div>

        <!-- FORM -->
        <form id="editForm" action="<?php echo e(route('operator.bahan.update', $bahan->id)); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- ✅ KODE BAHAN (READONLY) -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Kode Bahan</label>
                <input type="text" value="<?php echo e($bahan->kode_material); ?>" readonly
                       class="w-full bg-gray-800/40 border border-gray-700/30 rounded-xl px-4 py-3.5 text-sm text-gray-400 font-mono cursor-not-allowed">
            </div>

            <!-- ✅ NAMA MATERIAL -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Nama Material</label>
                <input type="text" name="nama_material" value="<?php echo e(old('nama_material', $bahan->nama_material)); ?>" required
                       class="w-full bg-gray-800 border border-gray-700/50 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 transition">
            </div>

            <!-- ✅ STOK & HARGA BERDAMPINGAN -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Stok Fisik</label>
                    <input type="number" name="stok" value="<?php echo e(old('stok', number_format($bahan->stok, 0, '', ''))); ?>" required
                           class="w-full bg-gray-800 border border-gray-700/50 rounded-xl px-4 py-3 text-sm text-white font-bold text-center focus:outline-none focus:border-emerald-500 transition">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Harga Beli</label>
                    <input type="text" name="harga_satuan" id="harga_satuan" 
                           value="<?php echo e(old('harga_satuan', number_format($bahan->harga_satuan, 0, ',', '.'))); ?>" required
                           class="w-full bg-gray-800 border border-gray-700/50 rounded-xl px-4 py-3 text-sm text-white text-right font-mono focus:outline-none focus:border-emerald-500 transition">
                </div>
            </div>

            <!-- ✅ KATEGORI & SATUAN -->
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Kategori</label>
                    <select name="kategori" class="w-full bg-gray-800 border border-gray-700/50 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500 appearance-none">
                        <option value="">Pilih</option>
                        <?php $__currentLoopData = $kategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k->nama_kategori); ?>" <?php echo e(old('kategori', $bahan->kategori) == $k->nama_kategori ? 'selected' : ''); ?>><?php echo e($k->nama_kategori); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Satuan</label>
                    <select name="satuan" class="w-full bg-gray-800 border border-gray-700/50 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-emerald-500 appearance-none">
                        <option value="">Pilih</option>
                        <?php $__currentLoopData = $satuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->nama_satuan); ?>" <?php echo e(old('satuan', $bahan->satuan) == $s->nama_satuan ? 'selected' : ''); ?>><?php echo e($s->nama_satuan); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <!-- ✅ SUPPLIER -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">Supplier</label>
                <input type="text" name="supplier" value="<?php echo e(old('supplier', $bahan->supplier)); ?>" placeholder="Nama Toko / Supplier"
                       class="w-full bg-gray-800 border border-gray-700/50 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-emerald-500 transition">
            </div>

            <!-- ✅ NO REFERENSI -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider pl-1">No. Referensi</label>
                <input type="text" name="ref_no" value="<?php echo e(old('ref_no', $bahan->ref_no)); ?>" placeholder="Nomor Nota / Invoice"
                       class="w-full bg-gray-800 border border-gray-700/50 rounded-xl px-4 py-3.5 text-sm text-white focus:outline-none focus:border-emerald-500 transition">
            </div>
        </form>

        <!-- ✅ TOMBOL AKSI -->
        <div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto p-5 bg-gray-900/95 backdrop-blur border-t border-gray-700 z-50 safe-bottom">
            <div class="space-y-3">
                <button type="submit" form="editForm" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-900/30 transition active:scale-[0.98]">
                    <i class="fas fa-check mr-2"></i>Simpan Perubahan
                </button>
                <a href="<?php echo e(route('operator.bahan.index')); ?>" class="block w-full bg-gray-800 hover:bg-gray-700 text-white font-medium py-3 rounded-xl transition active:scale-[0.98] text-center">
                    Batal
                </a>
            </div>
        </div>
    </main>
</div>

<script>
    // ✅ INSTANT PREVIEW FOTO BARU
    const fotoInput = document.getElementById('fotoBahan');
    const previewFoto = document.getElementById('previewFoto');

    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            previewFoto.style.display = 'block';
            previewFoto.src = event.target.result;
            previewFoto.onerror = null;
        };
        reader.readAsDataURL(file);
    });

    // ✅ FORMAT RUPIAH OTOMATIS
    const hargaInput = document.getElementById('harga_satuan');
    hargaInput.addEventListener('input', function(e) {
        hargaInput.value = formatRupiah(this.value);
    });

    function formatRupiah(angka) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split    = number_string.split(','),
            sisa     = split[0].length % 3,
            rupiah   = split[0].substr(0, sisa),
            ribuan   = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    }
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/bahan-baku/edit.blade.php ENDPATH**/ ?>