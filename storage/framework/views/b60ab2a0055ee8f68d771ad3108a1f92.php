<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produksi - <?php echo e(auth()->user()->name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-container { box-shadow: none !important; border-radius: 0 !important; padding: 10px !important; }
            table { font-size: 10pt !important; }
            th, td { padding: 4px 6px !important; }
        }
        @page {
            size: A4;
            margin: 1.5cm;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="print-container max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-4 md:p-6">
        
        <div class="text-center mb-8 border-b pb-4">
            <h1 class="text-2xl font-bold">Laporan Produksi Operator</h1>
            <p class="text-gray-600 mt-2">Nama: <?php echo e(auth()->user()->name); ?></p>
            <p class="text-gray-600 text-sm">Dicetak: <?php echo e(now()->format('d M Y H:i')); ?></p>
        </div>

        
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Pending</p>
                <p class="text-2xl font-bold text-yellow-600"><?php echo e($pendingCount); ?></p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Proses</p>
                <p class="text-2xl font-bold text-blue-600"><?php echo e($prosesCount); ?></p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600">Selesai</p>
                <p class="text-2xl font-bold text-green-600"><?php echo e($selesaiCount); ?></p>
            </div>
        </div>

        
        <h2 class="text-lg font-bold mb-4">Ringkasan Produksi Selesai</h2>
        <table class="w-full border-collapse mb-6">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2 text-left text-sm">Produk</th>
                    <th class="border px-4 py-2 text-center text-sm">Total Selesai</th>
                    <th class="border px-4 py-2 text-center text-sm">Jumlah SPK</th>
                    <th class="border px-4 py-2 text-center text-sm">Terakhir Update</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="border px-4 py-2 text-sm font-bold"><?php echo e($item->product->nama_produk ?? '-'); ?></td>
                    <td class="border px-4 py-2 text-center text-sm font-bold"><?php echo e(number_format($item->total_qty)); ?> Pcs</td>
                    <td class="border px-4 py-2 text-center text-sm"><?php echo e($item->jumlah_spk); ?> SPK</td>
                    <td class="border px-4 py-2 text-center text-sm text-gray-500"><?php echo e(\Carbon\Carbon::parse($item->latest_date)->format('d M Y')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="border px-4 py-8 text-center text-gray-500">Belum ada riwayat produksi.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        
        <h2 class="text-lg font-bold mb-4 mt-8">Bahan Masuk (Bulan Ini)</h2>
        <table class="w-full border-collapse mb-6">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2 text-left text-sm">Bahan</th>
                    <th class="border px-4 py-2 text-center text-sm">Total Masuk</th>
                    <th class="border px-4 py-2 text-center text-sm">Satuan</th>
                    <th class="border px-4 py-2 text-center text-sm">Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $bahanMasuk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="border px-4 py-2 text-sm font-bold"><?php echo e($item->nama_material); ?></td>
                    <td class="border px-4 py-2 text-center text-sm font-bold"><?php echo e(number_format($item->total_masuk)); ?></td>
                    <td class="border px-4 py-2 text-center text-sm"><?php echo e($item->satuan ?? '-'); ?></td>
                    <td class="border px-4 py-2 text-center text-sm"><?php echo e($item->jumlah_transaksi); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="border px-4 py-8 text-center text-gray-500">Belum ada bahan masuk.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        
        <h2 class="text-lg font-bold mb-4">Bahan Keluar (Dipakai Produksi)</h2>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2 text-left text-sm">Bahan</th>
                    <th class="border px-4 py-2 text-center text-sm">Total Keluar</th>
                    <th class="border px-4 py-2 text-center text-sm">Satuan</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $bahanKeluar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="border px-4 py-2 text-sm font-bold"><?php echo e($item->nama_material); ?></td>
                    <td class="border px-4 py-2 text-center text-sm font-bold text-red-600"><?php echo e(number_format($item->total_keluar)); ?></td>
                    <td class="border px-4 py-2 text-center text-sm"><?php echo e($item->satuan ?? '-'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="3" class="border px-4 py-8 text-center text-gray-500">Belum ada bahan keluar.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        
        <div class="mt-8 pt-4 border-t text-center text-sm text-gray-500">
        </div>
    </div>

    
    <div class="max-w-4xl mx-auto mt-4 text-center no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
            <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h6a2 2 0 002-2z" />
            </svg>
            Cetak Laporan
        </button>
        <a href="<?php echo e(route('operator.laporan.index')); ?>" class="ml-4 text-gray-600 hover:text-gray-800 underline">Kembali</a>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/laporan/cetak.blade.php ENDPATH**/ ?>