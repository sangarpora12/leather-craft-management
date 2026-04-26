<?php $__env->startSection('content'); ?>
<main class="flex-1 h-full overflow-y-auto bg-[#1c2029] text-white">
    <div class="flex h-full">
        
        <div class="w-full lg:w-2/3 xl:w-3/4 p-5 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <a href="<?php echo e(route('admin.produksi.index')); ?>" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>

            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <input type="text" id="warnaFilter" placeholder="Filter Warna..." class="bg-[#111316] border border-gray-700 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none placeholder-gray-600">
                <input type="text" id="motifFilter" placeholder="Filter Motif..." class="bg-[#111316] border border-gray-700 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none placeholder-gray-600">
                <input type="text" id="searchInput" placeholder="Cari nama produk atau SKU..." class="bg-[#111316] border border-gray-700 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none placeholder-gray-600">
            </div>

            <div class="flex-1 overflow-y-auto pr-2 pb-20 custom-scrollbar">
                
                <div id="emptySearch" class="hidden flex-col items-center justify-center h-64 text-gray-500">
                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-30"></i>
                    <p class="text-sm font-bold">Produk tidak ditemukan</p>
                    <p class="text-xs">Coba kata kunci lain.</p>
                </div>

<div id="productGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <?php $__currentLoopData = $boms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $bom = $produk->boms->first();

                            $data = $productsForJs[$produk->id] ?? [];
                            $isReady = $data['is_ready'] ?? false;
                            $reason = $data['reason'] ?? '';
                            $foto = $produk->foto ?? null;

                            // Tentukan warna status berdasarkan ketersediaan bahan
                            $statusColor = 'gray';
                            if ($isReady) {
                                $statusColor = 'green';
                            } elseif (!$bom) {
                                // Jika produk TIDAK PUNYA BOM
                                $statusColor = 'red';
                            } else {
                                // Periksa stok bahan
                                $stokBahan = $bom->material->stok_saat_ini ?? 0;
                                $totalKebutuhan = $bom->jumlah_kebutuhan;
                                if ($stokBahan >= $totalKebutuhan) {
                                    $statusColor = 'green';
                                } elseif ($stokBahan > 0 && $stokBahan < $totalKebutuhan) {
                                    $statusColor = 'yellow';
                                } else {
                                    $statusColor = 'red';
                                }
                            }
                        ?>

                        <button
                            type="button"
                            class="product-card text-left bg-[#1f242c] rounded-xl border border-gray-800 p-4 flex flex-col shadow-lg transition-all duration-300 relative group focus:outline-none hover:border-orange-500 hover:shadow-orange-500/20 <?php echo e(!$isReady ? 'opacity-70 hover:opacity-100' : ''); ?>"
                            data-id="<?php echo e($produk->id); ?>"
                            data-bom-id="<?php echo e(optional($bom)->id ?? ''); ?>"
                            data-nama="<?php echo e(e($produk->nama_produk ?? '')); ?>"
                            data-kode="<?php echo e(e($produk->kode_produk ?? '')); ?>"
                            data-stok="<?php echo e((int) ($produk->stok ?? 0)); ?>"
                            data-max="<?php echo e($produk->max_production ?? 0); ?>"
                            data-max="<?php echo e($produk->max_production ?? 0); ?>"
                            data-foto="<?php echo e(e($foto ?? '')); ?>"
                            data-warna="<?php echo e(e(is_array($produk->warna) ? strtolower(implode(', ', $produk->warna)) : strtolower($produk->warna ?? ''))); ?>"
                            data-motif="<?php echo e(e(is_array($produk->jenis_motif) ? strtolower(implode(', ', $produk->jenis_motif)) : strtolower($produk->jenis_motif ?? ''))); ?>"
                            data-is-ready="<?php echo e($isReady ? 'true' : 'false'); ?>"
                            data-reason="<?php echo e(e($reason)); ?>"
                        >
                            <!-- Foto Produk -->
                            <div class="h-48 w-full bg-[#111] rounded-lg mb-3 overflow-hidden relative">
                                <?php if(!empty($foto)): ?>
                                    <img src="<?php echo e(asset('storage/' . $foto)); ?>" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110" alt="<?php echo e($produk->nama_produk); ?>">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center text-gray-700 bg-[#16181d]">
                                        <i class="fa-solid fa-image text-3xl"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Overlay Status (selalu terlihat) -->
                                <div class="absolute top-2 right-2 z-10">
                                    <span class="font-bold text-[10px] uppercase px-2 py-1 rounded
                                        <?php if($statusColor === 'green'): ?> bg-green-500/20 text-green-400 border border-green-500/50
                                        <?php elseif($statusColor === 'yellow'): ?> bg-yellow-500/20 text-yellow-400 border border-yellow-500/50
                                        <?php elseif($statusColor === 'red'): ?> bg-red-500/20 text-red-400 border border-red-500/50
                                        <?php else: ?> bg-gray-500/20 text-gray-400 border border-gray-500/50 <?php endif; ?>">
                                        <?php if($statusColor === 'green'): ?> Siap Produksi
                                        <?php elseif($statusColor === 'yellow'): ?> Stok Terbatas
                                        <?php elseif($statusColor === 'red'): ?> Stok Habis
                                        <?php else: ?> Error
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Info Produk -->
                            <div class="flex-1 flex flex-col w-full overflow-hidden">
                                <!-- Info Utama -->
                                <div>
                                    <p class="text-xs font-mono text-gray-500"><?php echo e($produk->kode_produk ?? 'NO-SKU'); ?></p>
                                    <h3 class="text-sm font-bold text-white truncate" title="<?php echo e($produk->nama_produk); ?>"><?php echo e($produk->nama_produk); ?></h3>
                                </div>



                                <!-- Detail Tersembunyi (muncul saat hover) -->
                                <div class="flex-1 flex flex-col justify-end opacity-0 max-h-0 group-hover:opacity-100 group-hover:max-h-screen transition-all duration-300 overflow-hidden w-full">
                                    <div class="pt-2 mt-2 border-t border-gray-700/50 w-full">
                                        <p class="text-xs text-gray-500 italic truncate mb-2" title="<?php echo e($produk->keterangan); ?>"><?php echo e($produk->keterangan ?? 'Tidak ada keterangan.'); ?></p>
                                        <div class="text-xs text-gray-300 space-y-1 w-full">
                                            <div class="space-y-2">
                                                <div>
                                                    <span class="font-semibold text-gray-500 flex-shrink-0 text-xs block mb-1">Warna:</span>
                                                     <div class="flex flex-wrap gap-1">
                                                         <span class="px-1.5 py-0.5 bg-gray-700/50 rounded text-[10px] text-gray-200 text-capitalize">
                                                            <?php echo e(is_array($produk->warna) ? implode(', ', $produk->warna) : ($produk->warna ?? '-')); ?>

                                                         </span>
                                                     </div>
                                                 </div>
                                                 <div>
                                                     <span class="font-semibold text-gray-500 flex-shrink-0 text-xs block mb-1">Motif:</span>
                                                     <div class="flex flex-wrap gap-1">
                                                         <span class="px-1.5 py-0.5 bg-gray-700/50 rounded text-[10px] text-gray-200 text-capitalize">
                                                            <?php echo e(is_array($produk->jenis_motif) ? implode(', ', $produk->jenis_motif) : ($produk->jenis_motif ?? '-')); ?>

                                                         </span>
                                                     </div>
                                                 </div>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="font-semibold text-gray-500">Stok Jadi:</span>
                                                <span class="font-bold"><?php echo e((int) ($produk->stok ?? 0)); ?></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="font-semibold text-gray-500">Max Produksi:</span>
                                                <span class="font-bold <?php echo e((($produk->max_production ?? 0) > 0 ? 'text-emerald-400' : 'text-red-500')); ?>">
                                                    <?php echo e(($produk->max_production ?? 0)); ?> Unit
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        
        <div class="w-full lg:w-1/3 xl:w-1/4 bg-[#16181d] border-l border-gray-800 h-full flex flex-col">
            <div class="p-5 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Formulir SPK</h2>
                <p class="text-xs text-gray-500">Buat surat perintah kerja.</p>
            </div>
            <div class="flex-1 p-5 overflow-y-auto custom-scrollbar">
                <form action="<?php echo e(route('admin.produksi.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div id="selectedProductsList" class="space-y-3">
                        
                    </div>

                    <div id="emptyState" class="flex flex-col items-center justify-center text-center text-gray-600 py-10">
                        <i class="fa-solid fa-hand-pointer text-3xl mb-3"></i>
                        <p class="font-bold text-sm text-gray-400">Pilih Produk</p>
                        <p class="text-xs">Silakan pilih produk di samping kiri terlebih dahulu.</p>
                    </div>

                    <div id="formFields" class="hidden mt-5 space-y-4">
                        <div>
                            <label for="operator_id" class="block text-xs font-bold text-gray-400 uppercase mb-2">Operator</label>
                            <select name="operator_id" id="operator_id" required class="w-full bg-[#111316] border border-gray-700 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none">
                                <option value="">Pilih...</option>
                                <option value="all" class="font-bold text-orange-400">-- Semua Operator (Tugas Umum) --</option>
                                <?php $__currentLoopData = $operators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operator): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($operator->id); ?>"><?php echo e($operator->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label for="tanggal_target" class="block text-xs font-bold text-gray-400 uppercase mb-2">Target Selesai</label>
                            <input type="date" name="tanggal_target" id="tanggal_target" required class="w-full bg-[#111316] border border-gray-700 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none" min="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <button type="submit" id="submitBtn" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span id="submitText">Terbitkan SPK</span>
                            <span id="submitLoading" class="hidden">
                                <i class="fa-solid fa-spinner fa-spin"></i> Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productGrid = document.getElementById('productGrid');
    const selectedProductsList = document.getElementById('selectedProductsList');
    const emptyState = document.getElementById('emptyState');
    const formFields = document.getElementById('formFields');
    const searchInput = document.getElementById('searchInput');
    const warnaFilter = document.getElementById('warnaFilter');
    const motifFilter = document.getElementById('motifFilter');
    const emptySearch = document.getElementById('emptySearch');

    let selectedProducts = new Set();

    function updateFormVisibility() {
        if (selectedProducts.size > 0) {
            emptyState.classList.add('hidden');
            formFields.classList.remove('hidden');
        } else {
            emptyState.classList.remove('hidden');
            formFields.classList.add('hidden');
        }
    }

    function addProductToForm(data) {
        const index = selectedProductsList.children.length;
        const productCardHTML = `
            <div class="selected-product-card bg-[#1d2029] p-3 rounded-lg border border-gray-700" data-id="${data.id}">
                <input type="hidden" name="products[${index}][id]" value="${data.id}">
                <input type="hidden" name="products[${index}][bom_id]" value="${data.bomId}">
                
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-900 rounded-md overflow-hidden flex-shrink-0">
                            ${data.foto ? `<img src="/storage/${data.foto}" class="w-full h-full object-cover">` : '<div class="w-full h-full flex items-center justify-center text-gray-600"><i class="fa-solid fa-image"></i></div>'}
                        </div>
                        <div>
                            <p class="text-xs font-mono text-gray-500">${data.kode}</p>
                            <p class="text-sm font-bold text-white">${data.nama}</p>
                        </div>
                    </div>
                    <button type="button" class="remove-product-btn text-gray-500 hover:text-red-500 transition-colors">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-3 mt-3">
                    <div class="col-span-1">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Qty</label>
                        <input type="number" name="products[${index}][jumlah]" min="0.01" step="0.01" value="1" required 
                               class="w-full bg-[#111316] border border-gray-700 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Catatan (Opsional)</label>
                        <input type="text" name="products[${index}][catatan]" placeholder="Catatan khusus..."
                               class="w-full bg-[#111316] border border-gray-700 rounded-lg px-3 py-2 text-xs text-white focus:border-orange-500 outline-none placeholder-gray-600">
                    </div>
                </div>
            </div>
        `;
        selectedProductsList.insertAdjacentHTML('beforeend', productCardHTML);
    }

    function updateProductIndices() {
        const cards = selectedProductsList.querySelectorAll('.selected-product-card');
        cards.forEach((card, index) => {
            card.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/products\[\d+\]/, `products[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
    }

        productGrid.addEventListener('click', function (e) {
            const card = e.target.closest('.product-card');
            if (!card) return;

            const productId = card.dataset.id;
            const isReady = card.dataset.isReady === 'true';
            const reason = card.dataset.reason || '';
            const nama = card.dataset.nama || '';
            const foto = card.dataset.foto || '';
            const bomId = card.dataset.bomId || '';

            // Jika produk tidak ready, tampilkan SweetAlert2
            if (!isReady) {
                Swal.fire({
                    icon: isReady ? 'success' : 'warning',
                    title: nama,
                    html: reason.replace(/\n/g, '<br>'),
                    confirmButtonColor: '#f97316',
                    confirmButtonText: 'Tutup',
                    background: '#1f242c',
                    color: '#ffffff',
                    iconColor: isReady ? '#10b981' : '#f59e0b'
                });
                return;
            }

            // ✅ MASUKKAN DATA KE FORM OTOMATIS
            if (selectedProducts.has(productId)) {
                const selectedCard = selectedProductsList.querySelector(`.selected-product-card[data-id="${productId}"]`);
                if(selectedCard) {
                    selectedCard.classList.add('border-red-500');
                    selectedCard.style.animation = 'shake 0.5s ease-in-out';
                    setTimeout(() => {
                        selectedCard.classList.remove('border-red-500');
                        selectedCard.style.animation = '';
                    }, 600);
                }
                return;
            }

            selectedProducts.add(productId);
            addProductToForm(card.dataset);
            updateFormVisibility();

            // ✅ TAMPILKAN SWEETALERT SUKSES
            Swal.fire({
                icon: 'success',
                title: 'Produk Ditambahkan',
                text: nama + ' telah ditambahkan ke Formulir SPK.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                background: '#1f242c',
                color: '#ffffff',
            });

        if (selectedProducts.has(productId)) {
            const selectedCard = selectedProductsList.querySelector(`.selected-product-card[data-id="${productId}"]`);
            if(selectedCard) {
                selectedCard.classList.add('border-red-500');
                selectedCard.style.animation = 'shake 0.5s ease-in-out';
                setTimeout(() => {
                    selectedCard.classList.remove('border-red-500');
                    selectedCard.style.animation = '';
                }, 600);
            }
            return;
        }

        selectedProducts.add(productId);
        addProductToForm(card.dataset);
        updateFormVisibility();
    });

    selectedProductsList.addEventListener('click', function(e) {
        if (e.target.closest('.remove-product-btn')) {
            const card = e.target.closest('.selected-product-card');
            const productId = card.dataset.id;
            
            selectedProducts.delete(productId);
            card.remove();
            updateFormVisibility();
            updateProductIndices();
        }
    });

    // ✅ Loading state saat submit
    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitLoading = document.getElementById('submitLoading');

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
        submitText.classList.add('hidden');
        submitLoading.classList.remove('hidden');

        Swal.fire({
            icon: 'info',
            title: 'Memproses SPK',
            text: 'Sedang memvalidasi stok, menyimpan data, dan mengirim email...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            background: '#1f242c',
            color: '#ffffff',
        });
    });

    function filterProducts() {
        const searchTerm = searchInput.value.toLowerCase();
        const warnaTerm = warnaFilter.value.toLowerCase();
        const motifTerm = motifFilter.value.toLowerCase();
        let visibleCount = 0;

        productGrid.querySelectorAll('.product-card').forEach(card => {
            const nama = (card.dataset.nama || '').toLowerCase();
            const kode = (card.dataset.kode || '').toLowerCase();
            const warna = (card.dataset.warna || '').toLowerCase();
            const motif = (card.dataset.motif || '').toLowerCase();

            const isMatch = (nama.includes(searchTerm) || kode.includes(searchTerm)) &&
                            (warna.includes(warnaTerm)) &&
                            (motif.includes(motifTerm));

            if (isMatch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            emptySearch.classList.remove('hidden');
            productGrid.classList.add('hidden');
        } else {
            emptySearch.classList.add('hidden');
            productGrid.classList.remove('hidden');
        }
    }

    searchInput.addEventListener('input', filterProducts);
    warnaFilter.addEventListener('input', filterProducts);
    motifFilter.addEventListener('input', filterProducts);
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/produksi/perintah_produksi.blade.php ENDPATH**/ ?>