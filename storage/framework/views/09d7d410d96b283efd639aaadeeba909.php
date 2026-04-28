<?php $__env->startSection('title', 'Laporan Operator'); ?>

<?php $__env->startSection('main_class', 'pb-28'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#0f172a] bg-[radial-gradient(circle_at_top,#1e293b,transparent)]">
    <div class="max-w-md mx-auto shadow-2xl shadow-black/50 min-h-screen relative">
        <?php echo $__env->make('components.operator-header', ['title' => 'LAPORAN'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="px-5 pt-5 pb-32 space-y-4">

            
            <div class="op-glass rounded-2xl p-4">
                <form method="GET" action="<?php echo e(route('operator.laporan.index')); ?>" id="filterForm">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] text-gray-400 font-bold">Tanggal Mulai</label>
                            <input type="date" name="from" value="<?php echo e($fromDate); ?>" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-xs mt-1">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 font-bold">Tanggal Selesai</label>
                            <input type="date" name="to" value="<?php echo e($toDate); ?>" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-xs mt-1">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="text-[10px] text-gray-400 font-bold">Kategori</label>
                        <select name="kategori" class="w-full bg-[#1e293b] border border-white/10 rounded-lg px-3 py-2 text-white text-xs mt-1 appearance-none cursor-pointer">
                            <option value="semua" <?php echo e($kategori == 'semua' ? 'selected' : ''); ?>>Semua (Produk + Bahan)</option>
                            <option value="produk" <?php echo e($kategori == 'produk' ? 'selected' : ''); ?>>Hanya Produk Jadi</option>
                            <option value="bahan" <?php echo e($kategori == 'bahan' ? 'selected' : ''); ?>>Hanya Bahan Baku</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full mt-3 bg-emerald-500 hover:bg-emerald-600 text-black font-bold text-xs py-2 rounded-lg">
                        FILTER DATA
                    </button>

                    
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <a href="<?php echo e(route('operator.laporan.export-pdf', ['from' => $fromDate, 'to' => $toDate, 'kategori' => $kategori])); ?>" target="_blank" class="flex items-center justify-center gap-1 bg-blue-500 hover:bg-blue-600 text-white font-bold text-xs py-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            CETAK PDF
                        </a>
                        <button id="btnShare" class="flex items-center justify-center gap-1 bg-purple-500 hover:bg-purple-600 text-white font-bold text-xs py-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                            SHARE LAPORAN
                        </button>
                    </div>
                </form>
            </div>

            <style>
                @media print {
                    #filterForm, nav, footer, .no-print { display: none !important; }
                    body { background: white !important; color: black !important; }
                    * { box-shadow: none !important; }
                }
            </style>

            <script>
                document.getElementById('btnShare').addEventListener('click', async () => {
                    const from = '<?php echo e($fromDate); ?>';
                    const to = '<?php echo e($toDate); ?>';
                    const totalBahan = '<?php echo e($bahanMingguIni); ?>';
                    const totalProduk = '<?php echo e($mingguIniSelesaiQty); ?>';
                    
                    const shareData = {
                        title: 'Laporan Berliano Leather',
                        text: `📊 LAPORAN BERLIANO LEATHER\nPeriode: ${from} s/d ${to}\n\n✅ Bahan Diterima: ${totalBahan} unit\n✅ Produk Selesai: ${totalProduk} pcs\n\nLihat detail lengkap di sistem.`,
                        url: window.location.href
                    };
                    
                    try {
                        if (navigator.share) {
                            await navigator.share(shareData);
                        } else {
                            await navigator.clipboard.writeText(window.location.href);
                            alert('✅ Link laporan berhasil disalin!');
                        }
                    } catch (err) {
                        console.log('Error sharing:', err);
                    }
                });
            </script>

            <div class="grid grid-cols-3 gap-3">
                <div class="op-glass rounded-2xl p-3 text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Pending</div>
                    <div class="text-xl font-extrabold text-amber-300 mt-1"><?php echo e($pendingCount); ?></div>
                </div>
                <div class="op-glass rounded-2xl p-3 text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Proses</div>
                    <div class="text-xl font-extrabold text-blue-300 mt-1"><?php echo e($prosesCount); ?></div>
                </div>
                <div class="op-glass rounded-2xl p-3 text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Selesai</div>
                    <div class="text-xl font-extrabold text-emerald-300 mt-1"><?php echo e($selesaiCount); ?></div>
                </div>
            </div>

            <div class="op-glass rounded-2xl p-4">
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Ringkasan Hari Ini</div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Produk Selesai</div>
                        <div class="text-lg font-extrabold text-blue-300 mt-1"><?php echo e($hariIniSelesaiQty); ?></div>
                        <div class="text-[10px] text-gray-500">pcs</div>
                    </div>
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Bahan Diterima</div>
                        <div class="text-lg font-extrabold text-emerald-300 mt-1"><?php echo e($bahanHariIni); ?></div>
                        <div class="text-[10px] text-gray-500">unit</div>
                    </div>
                </div>
            </div>

            <div class="op-glass rounded-2xl p-4">
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Ringkasan Minggu Ini</div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Produk Selesai</div>
                        <div class="text-lg font-extrabold text-blue-300 mt-1"><?php echo e($mingguIniSelesaiQty); ?></div>
                        <div class="text-[10px] text-gray-500">pcs</div>
                    </div>
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Bahan Diterima</div>
                        <div class="text-lg font-extrabold text-emerald-300 mt-1"><?php echo e($bahanMingguIni); ?></div>
                        <div class="text-[10px] text-gray-500">unit</div>
                    </div>
                </div>
            </div>

            <div class="op-glass rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Riwayat Transaksi</div>
                        <div class="text-[10px] text-gray-500">Produk selesai & Bahan diterima</div>
                    </div>
                </div>

                <?php $__empty_1 = true; $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-4 py-3 border-b border-white/5 hover:bg-white/5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <?php if($item->jenis == 'produk'): ?>
                                        <span class="inline-flex px-2 py-0.5 bg-blue-500/20 text-blue-300 text-[10px] font-bold rounded">PRODUK</span>
                                    <?php else: ?>
                                        <span class="inline-flex px-2 py-0.5 bg-emerald-500/20 text-emerald-300 text-[10px] font-bold rounded">BAHAN</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs font-bold text-white mt-1">
                                    <?php echo e($item->jenis == 'produk' ? ($item->product->nama_produk ?? 'Produk') : $item->nama); ?>

                                </div>
                                <div class="text-[10px] text-gray-500 mt-1">
                                    <?php echo e(\Carbon\Carbon::parse($item->tanggal_terakhir)->format('d M Y, H:i')); ?>

                                </div>
                                <?php if($item->jumlah_transaksi > 1): ?>
                                    <div class="text-[10px] text-gray-400 mt-1">
                                        ✅ <?php echo e($item->jumlah_transaksi); ?> x transaksi digabung
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-extrabold <?php echo e($item->jenis == 'produk' ? 'text-blue-300' : 'text-emerald-300'); ?>"><?php echo e((int) $item->total_qty); ?></div>
                                <div class="text-[10px] text-gray-500"><?php echo e($item->jenis == 'produk' ? 'pcs' : ($item->satuan ?? 'unit')); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-6 text-center text-xs text-gray-500">
                        Belum ada data pada rentang tanggal yang dipilih.
                    </div>
                <?php endif; ?>

                <div class="p-3">
                    <?php echo e($riwayat->appends(request()->query())->links()); ?>

                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/laporan/index.blade.php ENDPATH**/ ?>