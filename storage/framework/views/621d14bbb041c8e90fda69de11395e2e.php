<?php $__env->startComponent('mail::message'); ?>

# <?php echo new \Illuminate\Support\EncodedHtmlString($isAssigned ? 'Tugas Produksi Baru Untuk Anda' : 'Informasi Tugas Produksi Baru'); ?>


<?php $__currentLoopData = $spks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php if($spk->product): ?>


<div style="text-align: center; margin-bottom: 20px;">
    <h1 style="color: #2c3e50; font-size: 24px;"><?php echo new \Illuminate\Support\EncodedHtmlString($spk->product->nama_produk); ?></h1>
    <p style="color: #7f8c8d;">Kode: <?php echo new \Illuminate\Support\EncodedHtmlString($spk->product->kode_produk); ?></p>
</div>


<table style="width: 100%; margin-bottom: 20px;">
    <tr>
        <td style="width: 50%; text-align: center; padding: 10px; border: 1px solid #eee;">
            <div style="font-size: 11px; color: #95a5a6;">📸 FOTO PRODUK</div>
            <?php if($spk->product->foto): ?>
                <img src="<?php echo new \Illuminate\Support\EncodedHtmlString(Storage::disk('public')->url($spk->product->foto)); ?>" width="100%" style="border-radius: 8px; margin-top: 5px;">
            <?php else: ?>
                <div style="padding: 20px; color: #e74c3c; font-size: 12px;">🚫 Tidak Ada Foto Produk</div>
            <?php endif; ?>
        </td>
        <td style="width: 50%; text-align: center; padding: 10px; border: 1px solid #eee;">
            <div style="font-size: 11px; color: #95a5a6;">📏 SKETSA POLA</div>
            <?php if($spk->product->sketsa): ?>
                <img src="<?php echo new \Illuminate\Support\EncodedHtmlString(Storage::disk('public')->url($spk->product->sketsa)); ?>" width="100%" style="border-radius: 8px; margin-top: 5px;">
            <?php else: ?>
                <div style="padding: 20px; color: #e74c3c; font-size: 12px;">🚫 Tidak Ada Sketsa</div>
            <?php endif; ?>
        </td>
    </tr>
</table>


<div style="background: #f8f9fa; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;">
    Tinggi: <?php echo new \Illuminate\Support\EncodedHtmlString((int)$spk->product->tinggi); ?>cm | Panjang: <?php echo new \Illuminate\Support\EncodedHtmlString((int)$spk->product->panjang); ?>cm | Lebar: <?php echo new \Illuminate\Support\EncodedHtmlString((int)$spk->product->lebar); ?>cm
</div>


<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <tr style="background: #fafafa; font-size: 12px;">
        <th style="border: 1px solid #eee; padding: 8px;">Kategori</th>
        <th style="border: 1px solid #eee; padding: 8px;">Warna</th>
        <th style="border: 1px solid #eee; padding: 8px;">Motif</th>
    </tr>
    <tr style="text-align: center; font-size: 13px;">
        <td style="border: 1px solid #eee; padding: 8px;"><?php echo new \Illuminate\Support\EncodedHtmlString(str_replace(['[',']','"'], '', $spk->product->kategori) ?: 'Tidak Ada'); ?></td>
        <td style="border: 1px solid #eee; padding: 8px;"><?php echo new \Illuminate\Support\EncodedHtmlString(str_replace(['[',']','"'], '', $spk->product->warna) ?: 'Tidak Ada'); ?></td>
        <td style="border: 1px solid #eee; padding: 8px;"><?php echo new \Illuminate\Support\EncodedHtmlString(str_replace(['[',']','"'], '', $spk->product->jenis_motif) ?: 'Tidak Ada'); ?></td>
    </tr>
</table>


<div style="font-size: 13px; line-height: 1.6; color: #2c3e50;">
    <strong>📝 Detail Pola:</strong> <?php echo new \Illuminate\Support\EncodedHtmlString($spk->product->detail_pola ?: 'Tidak Ada'); ?> <br>
    <strong>📖 Deskripsi Produk:</strong> <?php echo new \Illuminate\Support\EncodedHtmlString($spk->product->deskripsi ?: 'Tidak Ada'); ?> <br>
    <strong>⚠️ Keterangan Internal:</strong> <?php echo new \Illuminate\Support\EncodedHtmlString($spk->product->keterangan_internal ?: 'Tidak Ada'); ?> <br>
</div>

<hr style="margin: 20px 0; border: 1px dashed #eee;">


<strong>📦 Daftar Bahan Baku:</strong>
<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px;">
    <tr style="background: #34495e; color: white;">
        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Bahan</th>
        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Total</th>
    </tr>
    <?php $__currentLoopData = $spk->product->boms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;"><?php echo new \Illuminate\Support\EncodedHtmlString($bom->bahanBaku->nama_material); ?></td>
        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;"><?php echo new \Illuminate\Support\EncodedHtmlString((int)($bom->jumlah_kebutuhan * $spk->jumlah_produksi)); ?> <?php echo new \Illuminate\Support\EncodedHtmlString(str_replace('buah-buahan', 'Pcs', $bom->bahanBaku->satuan)); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>

<div style="text-align: center; margin: 20px 0; background: #ebf5fb; padding: 10px;">
    Target: <strong><?php echo new \Illuminate\Support\EncodedHtmlString((int)$spk->jumlah_produksi); ?> Pcs</strong>
</div>

<?php if(!$loop->last): ?> <hr style="margin: 40px 0; border: 2px solid #3498db;"> <?php endif; ?>

<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<div style="text-align: center; margin-top: 20px;">
    <a href="<?php echo new \Illuminate\Support\EncodedHtmlString(route('login')); ?>" style="background: #27ae60; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">LOGIN KE SISTEM</a>
</div>

<?php echo $__env->renderComponent(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/emails/produksi/perintah_baru.blade.php ENDPATH**/ ?>