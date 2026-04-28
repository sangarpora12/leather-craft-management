<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#0B0E14] pb-24">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-6">
        <form action="<?php echo e(route('operator.stok.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <?php if($errors->any()): ?>
                <div class="bg-red-500/10 border border-red-500/30 p-4 mb-6 rounded-2xl">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-red-400 text-xl">⚠️</span>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-400">Gagal menyimpan data. Silakan cek masalah berikut:</h3>
                            <ul class="mt-2 text-sm text-red-300 list-disc list-inside">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
                <div class="bg-green-500/10 border border-green-500/30 p-4 mb-6 rounded-2xl">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <span class="text-green-400 text-xl">✅</span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-400"><?php echo e(session('success')); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Header Invoice -->
            <div class="bg-[#1C222D] border border-white/5 rounded-2xl overflow-hidden mb-6">
                <div class="bg-[#161A22] px-6 py-4 border-b border-white/5">
                    <h1 class="text-xl font-bold text-white">📄 Invoice Digital - Verifikasi Hasil OCR</h1>
                    <p class="text-gray-400 text-sm">Nota Pembelian Bahan Baku</p>
                </div>

                <div class="p-6">
                    <!-- Supplier Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-[#0B0E14] border border-white/5 p-4 rounded-xl">
                            <label class="block text-xs font-semibold text-blue-400 uppercase tracking-wide mb-2">Supplier</label>
                            <input type="text" name="supplier" value="<?php echo e(session('ocr_result.supplier') ?? ''); ?>" class="w-full px-3 py-2 bg-[#1C222D] border border-white/10 rounded-lg focus:outline-none focus:border-blue-500 text-white text-sm" placeholder="Nama Toko" required>
                        </div>
                        <div class="bg-[#0B0E14] border border-white/5 p-4 rounded-xl">
                            <label class="block text-xs font-semibold text-green-400 uppercase tracking-wide mb-2">No Nota</label>
                            <input type="text" name="no_nota" value="<?php echo e(session('ocr_result.metadata.no_nota') ?? ''); ?>" class="w-full px-3 py-2 bg-[#1C222D] border border-white/10 rounded-lg focus:outline-none focus:border-green-500 text-white text-sm" placeholder="No Nota">
                        </div>
                        <div class="bg-[#0B0E14] border border-white/5 p-4 rounded-xl">
                            <label class="block text-xs font-semibold text-purple-400 uppercase tracking-wide mb-2">Tanggal</label>
                            <input type="text" name="tanggal" value="<?php echo e(session('ocr_result.metadata.tanggal') ?? ''); ?>" class="w-full px-3 py-2 bg-[#1C222D] border border-white/10 rounded-lg focus:outline-none focus:border-purple-500 text-white text-sm" placeholder="Tanggal">
                        </div>
                        <div class="bg-[#0B0E14] border border-white/5 p-4 rounded-xl">
                            <label class="block text-xs font-semibold text-amber-400 uppercase tracking-wide mb-2">Kontak</label>
                            <input type="text" name="alamat_kontak" value="<?php echo e(session('ocr_result.metadata.alamat_kontak') ?? ''); ?>" class="w-full px-3 py-2 bg-[#1C222D] border border-white/10 rounded-lg focus:outline-none focus:border-amber-500 text-white text-sm" placeholder="Alamat & Kontak">
                        </div>
                    </div>

                    <?php if(session('ocr_result.image_url')): ?>
                    <div class="bg-[#0B0E14] border border-white/5 p-4 rounded-xl">
                        <h3 class="text-sm font-medium text-gray-300 mb-2">📷 Gambar Nota Asli</h3>
                        <img src="<?php echo e(asset(session('ocr_result.image_url'))); ?>" alt="Nota" class="max-w-md h-auto border border-white/10 rounded-xl">
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-[#1C222D] border border-white/5 rounded-2xl overflow-hidden mb-6">
                <div class="bg-[#161A22] px-6 py-4 border-b border-white/5 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-white">📦 Daftar Barang</h2>
                        <p class="text-gray-400 text-sm">Edit jika perlu sebelum konfirmasi.</p>
                    </div>
                    <button type="button" id="add-row" class="inline-flex items-center px-4 py-2 border border-white/10 text-sm font-medium rounded-xl text-white bg-emerald-600 hover:bg-emerald-500 transition-colors">
                        ➕ Tambah Baris
                    </button>
                </div>

                <div class="p-4">
                    <?php if(session('ocr_result.items') && count(session('ocr_result.items')) > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-[900px] divide-y divide-white/5">
                                <thead class="bg-[#161A22]">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-sm font-bold text-gray-300 uppercase tracking-wide" style="width: 40%">Nama Barang</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider" style="width: 12%">Satuan</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider" style="width: 14%">Harga</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider" style="width: 10%">Jml</th>
                                        <th scope="col" class="px-3 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider" style="width: 14%">Subtotal</th>
                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wider" style="width: 10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-[#1C222D] divide-y divide-white/5">
                                     <?php $__currentLoopData = session('ocr_result.items'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                         <tr class="hover:bg-white/5 transition-colors">
                                             <td class="px-4 py-3">
                                                 <div class="flex flex-col gap-2">
                                                     <textarea name="items[<?php echo e($index); ?>][nama]" title="<?php echo e($item['nama'] ?? ''); ?>" rows="1" style="resize: horizontal; height: 40px; overflow: hidden;" class="w-full px-3 py-2 text-sm text-white bg-[#0B0E14] border border-white/10 rounded-lg focus:outline-none focus:border-blue-500" required><?php echo e($item['nama'] ?? ''); ?></textarea>
                                                     <label class="inline-flex items-center text-sm text-gray-400">
                                                         <input type="checkbox" name="items[<?php echo e($index); ?>][is_inventory]" value="1" checked class="rounded border-white/10 bg-[#0B0E14] text-blue-500 focus:ring-blue-500">
                                                         <span class="ml-2">Hitung Stok & Harga</span>
                                                     </label>
                                                 </div>
                                                 <input type="hidden" name="items[<?php echo e($index); ?>][id]" value="<?php echo e($index); ?>">
                                             </td>
                                             <td class="px-4 py-2 text-center">
                                                 <input type="text" name="items[<?php echo e($index); ?>][satuan]" value="<?php echo e($item['satuan'] ?? ''); ?>" class="w-full px-2 py-1 text-sm text-white bg-[#0B0E14] border border-white/10 rounded-lg text-center focus:outline-none focus:border-green-500" placeholder="pcs">
                                             </td>
                                             <td class="px-4 py-2 text-center">
                                                 <input type="number" name="items[<?php echo e($index); ?>][harga]" value="<?php echo e($item['harga'] ?? 0); ?>" class="w-full px-2 py-1 text-sm text-white bg-[#0B0E14] border <?php echo e(($item['harga'] ?? 0) == 0 ? 'border-red-500/50 bg-red-500/5' : 'border-white/10'); ?> rounded-lg text-center focus:outline-none focus:border-purple-500" min="0" required>
                                                 <?php if(($item['harga'] ?? 0) == 0): ?>
                                                     <p class="text-xs text-red-400 mt-1">⚠️ Harga 0 - Perlu dicek</p>
                                                 <?php endif; ?>
                                             </td>
                                             <td class="px-4 py-2 text-center">
                                                 <input type="number" name="items[<?php echo e($index); ?>][qty]" value="<?php echo e($item['qty'] ?? 1); ?>" class="w-full px-2 py-1 text-sm text-white bg-[#0B0E14] border border-white/10 rounded-lg text-center focus:outline-none focus:border-amber-500" min="1" required>
                                             </td>
                                             <td class="px-4 py-2 text-right">
                                                 <span class="font-bold text-white text-sm subtotal-<?php echo e($index); ?>">Rp <?php echo e(number_format(($item['harga'] ?? 0) * ($item['qty'] ?? 1), 0, ',', '.')); ?></span>
                                             </td>
                                             <td class="px-4 py-2 text-center">
                                                 <button type="button" class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded-lg text-white bg-red-600 hover:bg-red-500 transition-colors delete-row" data-index="<?php echo e($index); ?>">
                                                     🗑️ Hapus
                                                 </button>
                                             </td>
                                         </tr>
                                     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <!-- Empty row for manual addition -->
                                    <tr class="bg-[#0B0E14] border-t-2 border-dashed border-white/10">
                                        <td class="px-4 py-2">
                                            <textarea disabled name="items[<?php echo e(count(session('ocr_result.items'))); ?>][nama]" rows="2" style="resize: none;" class="w-full px-2 py-1 text-sm bg-[#0B0E14] border border-white/10 rounded-lg text-gray-500" placeholder="Klik 'Tambah Baris' di atas"></textarea>
                                            <input disabled type="hidden" name="items[<?php echo e(count(session('ocr_result.items'))); ?>][id]" value="<?php echo e(count(session('ocr_result.items'))); ?>">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input disabled type="text" name="items[<?php echo e(count(session('ocr_result.items'))); ?>][satuan]" class="w-full px-2 py-1 text-sm bg-[#0B0E14] border border-white/10 rounded-lg text-center text-gray-500" placeholder="pcs">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input disabled type="number" name="items[<?php echo e(count(session('ocr_result.items'))); ?>][harga]" class="w-full px-2 py-1 text-sm bg-[#0B0E14] border border-white/10 rounded-lg text-center text-gray-500" min="0" placeholder="0">
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <input disabled type="number" name="items[<?php echo e(count(session('ocr_result.items'))); ?>][qty]" value="1" class="w-full px-2 py-1 text-sm bg-[#0B0E14] border border-white/10 rounded-lg text-center text-gray-500" min="1" placeholder="1">
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <span class="font-semibold text-gray-500 text-sm subtotal-<?php echo e(count(session('ocr_result.items'))); ?>">Rp 0</span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button type="button" class="inline-flex items-center px-2 py-1 border border-transparent text-xs leading-4 font-medium rounded-lg text-white bg-red-600 hover:bg-red-500 transition-colors delete-row" data-index="<?php echo e(count(session('ocr_result.items'))); ?>">
                                                🗑️ Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <div class="mx-auto w-24 h-24 bg-[#1C222D] rounded-full flex items-center justify-center mb-4">
                                <span class="text-4xl">📭</span>
                            </div>
                            <h3 class="text-lg font-medium text-white mb-2">Tidak Ada Item Terdeteksi</h3>
                            <p class="text-gray-400">Coba upload gambar nota yang lebih jelas atau dengan pencahayaan yang baik.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Total Section -->
            <div class="bg-[#1C222D] border border-white/5 rounded-2xl overflow-hidden mb-6">
                <div class="bg-[#161A22] px-6 py-4 border-b border-white/5">
                    <h3 class="text-lg font-bold text-white">💰 Ringkasan Pembayaran</h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-end">
                        <div class="w-full max-w-sm">
                            <div class="bg-[#0B0E14] p-4 rounded-xl border border-white/5">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-400">Total Bayar:</span>
                                    <span class="text-lg font-bold text-white" id="total_bayar_display">Rp <?php echo e(number_format(session('ocr_result.metadata.total_bayar') ?? 0, 0, ',', '.')); ?></span>
                                </div>
                                <input type="hidden" id="total_bayar" name="total_bayar" value="<?php echo e(session('ocr_result.metadata.total_bayar') ?? 0); ?>">
                                <p class="text-xs text-gray-500 mt-2">💡 Total otomatis terupdate saat edit harga/qty</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="bg-[#1C222D] border border-white/5 rounded-2xl overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row gap-4 justify-end">
                        <a href="<?php echo e(url('/operator/scan')); ?>" class="inline-flex items-center px-6 py-3 border border-white/10 text-sm font-medium rounded-xl text-white bg-[#161A22] hover:bg-white/5 transition-colors">
                            🔄 Scan Ulang
                        </a>
                        <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-emerald-600 hover:bg-emerald-500 transition-colors">
                            ✅ Konfirmasi & Tambah ke Stok
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to calculate subtotal for a specific row
    function calculateSubtotalForRow(index) {
        const hargaInput = document.querySelector('input[name="items[' + index + '][harga]"]');
        const qtyInput = document.querySelector('input[name="items[' + index + '][qty]"]');
        const subtotalSpan = document.querySelector('.subtotal-' + index);

        if (hargaInput && qtyInput && subtotalSpan) {
            const harga = parseInt(hargaInput.value) || 0;
            const qty = parseInt(qtyInput.value) || 0;
            const subtotal = harga * qty;

            subtotalSpan.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
        }
    }

    // Function to calculate total bayar
    function calculateTotalBayar() {
        let total = 0;
        document.querySelectorAll('[class*="subtotal-"]').forEach(function(span) {
            const text = span.textContent.replace('Rp ', '').replace(/\./g, '').replace(/,/g, '.');
            const value = parseInt(text) || 0;
            total += value;
        });
        const totalBayarInput = document.getElementById('total_bayar');
        const totalBayarDisplay = document.getElementById('total_bayar_display');
        if (totalBayarInput) {
            totalBayarInput.value = total;
        }
        if (totalBayarDisplay) {
            totalBayarDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    }

    // Add event listeners to all inputs that affect subtotal calculation
    document.querySelectorAll('input[name*="[harga]"], input[name*="[qty]"]').forEach(function(input) {
        input.addEventListener('input', function() {
            // Extract index from input name
            const match = input.name.match(/items\[(\d+)\]/);
            if (match) {
                const index = match[1];
                calculateSubtotalForRow(index);
                calculateTotalBayar();
            }
        });
    });

    // Delete row functionality
    document.querySelectorAll('.delete-row').forEach(function(button) {
        button.addEventListener('click', function() {
            const index = this.getAttribute('data-index');
            const row = this.closest('tr');
            row.remove();
            calculateTotalBayar();
        });
    });

    // Add row functionality
    let rowIndex = <?php echo e(count(session('ocr_result.items', [])) + 1); ?>;
    document.getElementById('add-row').addEventListener('click', function() {
        const tbody = document.querySelector('tbody');
        const emptyRow = tbody.querySelector('tr.bg-[#0B0E14]');
        const newRow = emptyRow.cloneNode(true);

        // Update names and ids
        newRow.querySelectorAll('textarea, input').forEach(function(el) {
            el.disabled = false;
            el.classList.remove('text-gray-500');
            el.classList.add('text-white');

            if (el.name) {
                el.name = el.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
            }
            if (el.type === 'hidden' && el.name.includes('[id]')) {
                el.value = rowIndex;
            }
            if (el.placeholder) {
                el.placeholder = el.placeholder.replace('Klik \'Tambah Baris\' di atas', 'Bahan baru');
            }
            // Clear values
            if (el.type !== 'hidden') {
                el.value = '';
            }
        });

        // Update subtotal class
        const subtotalSpan = newRow.querySelector('[class*="subtotal-"]');
        if (subtotalSpan) {
            subtotalSpan.className = subtotalSpan.className.replace(/subtotal-\d+/, 'subtotal-' + rowIndex);
            subtotalSpan.textContent = 'Rp 0';
        }

        // Update delete button data-index
        const deleteBtn = newRow.querySelector('.delete-row');
        if (deleteBtn) {
            deleteBtn.setAttribute('data-index', rowIndex);
        }

        // Insert before empty row
        tbody.insertBefore(newRow, emptyRow);

        // Add event listeners to new inputs
        newRow.querySelectorAll('input[name*="[harga]"], input[name*="[qty]"]').forEach(function(input) {
            input.addEventListener('input', function() {
                const match = input.name.match(/items\[(\d+)\]/);
                if (match) {
                    const index = match[1];
                    calculateSubtotalForRow(index);
                    calculateTotalBayar();
                }
            });
        });

        // Add delete listener
        const newDeleteBtn = newRow.querySelector('.delete-row');
        if (newDeleteBtn) {
            newDeleteBtn.addEventListener('click', function() {
                const index = this.getAttribute('data-index');
                const row = this.closest('tr');
                row.remove();
                calculateTotalBayar();
            });
        }

        rowIndex++;
    });

    // Initial calculation
    calculateTotalBayar();
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/scan-result.blade.php ENDPATH**/ ?>