

<?php $__env->startSection('title', 'Edit Resep BOM'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto" x-data="bomForm()">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-white">Edit Resep Produksi (BOM)</h2>
            <p class="text-gray-400 text-sm">Atur bahan baku untuk: <span class="text-orange-500 font-bold"><?php echo e($product->nama_produk); ?></span></p>
        </div>
        <div class="flex gap-3 items-center">
            <a href="<?php echo e(route('admin.produk-jadi.edit', $product->id)); ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-bold flex items-center gap-2 transition">
                <i class="fa-solid fa-tag"></i> Edit Informasi Produk
            </a>
            <a href="<?php echo e(route('admin.bom.index')); ?>" class="text-gray-400 hover:text-white text-sm">Kembali</a>
        </div>
    </div>

    <form action="<?php echo e(route('admin.bom.update', $product->id)); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                <h3 class="font-bold text-white">Spesifikasi Teknis Produk</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Panjang (cm)</label>
                    <input type="number" step="0.01" name="panjang"
                        value="<?php echo e(old('panjang', $product->panjang)); ?>"
                        class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-3 text-white text-sm focus:border-orange-500 outline-none"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Lebar (cm)</label>
                    <input type="number" step="0.01" name="lebar"
                        value="<?php echo e(old('lebar', $product->lebar)); ?>"
                        class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-3 text-white text-sm focus:border-orange-500 outline-none"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-300 mb-2">Tinggi (cm)</label>
                    <input type="number" step="0.01" name="tinggi"
                        value="<?php echo e(old('tinggi', $product->tinggi)); ?>"
                        class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-3 text-white text-sm focus:border-orange-500 outline-none"
                        placeholder="0">
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-300 mb-2">Detail Pola / Catatan Potong</label>
                <textarea name="detail_pola" rows="4"
                    class="w-full bg-[#111] border border-white/10 rounded-lg px-4 py-3 text-white text-sm focus:border-orange-500 outline-none resize-vertical"
                    placeholder="Catatan pola, jahitan, atau instruksi potong khusus..."><?php echo e(old('detail_pola', $product->detail_pola)); ?></textarea>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-300 mb-2">Sketsa Produk <span class="text-xs text-gray-500">(Maksimal 4 foto)</span></label>

                
                <?php $existingSketsas = $product->sketsa ?? []; ?>
                <?php if(count($existingSketsas) > 0): ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <?php $__currentLoopData = $existingSketsas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sketsaPath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="relative group">
                                <div class="aspect-square rounded-xl overflow-hidden border border-white/10 bg-black/30">
                                    <img src="<?php echo e(asset('storage/' . $sketsaPath)); ?>" class="w-full h-full object-cover" alt="Sketsa <?php echo e($index + 1); ?>">
                                </div>
                                <label class="absolute top-2 right-2 flex items-center gap-1 bg-red-600/90 hover:bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded cursor-pointer transition">
                                    <input type="checkbox" name="delete_sketsa[]" value="<?php echo e($sketsaPath); ?>" class="w-3 h-3">
                                    Hapus
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                
                <?php if(count($existingSketsas) < 4): ?>
                    <div class="group relative w-full h-32 border-2 border-dashed border-white/20 hover:border-orange-500 hover:bg-white/5 rounded-xl flex flex-col items-center justify-center p-6 cursor-pointer transition-all duration-300 bg-black/30" id="sketsaDropzone">
                        <svg class="w-10 h-10 text-gray-400 group-hover:text-orange-500 mb-2 transition" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
                        </svg>
                        <p class="text-white font-medium text-sm mb-1">Klik atau drag gambar sketsa ke sini</p>
                        <p class="text-xs text-gray-400" id="sketsaHint">Maks <?php echo e(4 - count($existingSketsas)); ?> foto lagi. PNG, JPG, WEBP hingga 10MB</p>
                        <input type="file" name="sketsa[]" accept="image/*" multiple
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            id="sketsaInput"
                            onchange="handleSketsaSelect(this)">
                    </div>
                    
                    <div id="sketsaPreviewContainer" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden"></div>
                    <?php $__errorArgs = ['sketsa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['sketsa.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php else: ?>
                    <p class="text-xs text-gray-500">✅ Sudah mencapai batas maksimal 4 foto sketsa. Hapus salah satu untuk mengganti.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6">
            
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-white">Komponen Bahan</h3>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('admin.bahan.index')); ?>" target="_blank" class="px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white hover:bg-white/5 text-xs font-bold flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Kelola Master Bahan
                    </a>
                    <button type="button" @click="addRow()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Bahan
                    </button>
                </div>
            </div>

            <div class="rounded-lg border border-white/5">
                <table class="w-full text-left text-sm">
                    <thead class="bg-black text-gray-500 text-xs uppercase font-bold">
                        <tr>
                            <th class="p-3 pl-4">Nama Material</th>
                            <th class="p-3 w-32 text-center">Jumlah</th>
                            <th class="p-3 w-32 text-center">Satuan</th>
                            <th class="p-3 w-40 text-right">Estimasi Biaya</th>
                            <th class="p-3 w-16 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 bg-[#111]">
                        <template x-for="(row, index) in rows" :key="row.id">
                            <tr class="hover:bg-white/5 transition">
                                <td class="p-3 pl-4 relative align-bottom">
                                    <div x-data="{ open: false, search: '' }" @click.outside="open = false" class="relative">
                                        
                                        <div @click="open = !open; search = ''; $nextTick(() => $refs.searchInput.focus())"
                                             class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-3 py-2 text-white text-sm cursor-pointer flex justify-between items-center hover:border-orange-500 transition group">
                                            <span x-text="getMaterialName(row.material_id)" :class="!row.material_id ? 'text-gray-500' : 'text-white font-bold'"></span>
                                            <svg class="w-4 h-4 text-gray-500 group-hover:text-orange-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>

                                        <div x-show="open" style="display: none;" class="absolute z-50 min-w-[300px] mt-1 bg-[#222] border border-white/10 rounded-lg shadow-2xl max-h-60 overflow-y-auto">
                                            <div class="sticky top-0 bg-[#222] p-2 border-b border-white/10 z-10">
                                                <input x-ref="searchInput" x-model="search" type="text" placeholder="Cari nama bahan..." class="w-full bg-[#111] border border-white/10 rounded px-2 py-1.5 text-xs text-white focus:outline-none focus:border-orange-500 placeholder-gray-600">
                                            </div>
                                            <div class="py-1">
                                                <template x-for="mat in filteredMaterials(search, row.material_id)" :key="mat.id">
                                                    <div @click="row.material_id = mat.id; updateUnit(index); open = false" class="px-3 py-2 cursor-pointer hover:bg-orange-600/20 hover:text-orange-400 transition border-b border-white/5 last:border-0">
                                                        <div class="font-bold text-sm text-gray-200" x-text="mat.nama_material"></div>
                                                        <div class="flex justify-between mt-0.5"><span class="text-[10px] text-gray-500" x-text="'Stok: ' + mat.stok + ' ' + mat.satuan"></span><span class="text-[10px] text-gray-500" x-text="formatRupiah(mat.harga_satuan)"></span></div>
                                                    </div>
                                                </template>
                                                <div x-show="filteredMaterials(search, row.material_id).length === 0" class="px-3 py-3 text-xs text-gray-500 text-center italic">
                                                    Tidak ditemukan. 
                                                    <a href="<?php echo e(route('admin.bahan.index')); ?>" target="_blank" class="text-orange-500 hover:underline block mt-1">Tambah di Master Bahan?</a>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" :name="'material_id[' + index + ']'" x-model="row.material_id" required>
                                    </div>
                                </td>
                                <td class="p-3 align-bottom">
                                    <p class="text-[9px] text-gray-500 text-center mb-1" x-show="row.is_inventory" x-text="'Max: ' + (row.stock || 0)"></p>
                                    <input type="number" step="0.01" :name="'quantity[' + index + ']'" x-model="row.quantity" 
                                           :max="row.is_inventory ? row.stock : null" min="0.01"
                                           class="w-full bg-[#1A1A1A] border border-white/10 rounded-lg px-3 py-2 text-white text-center text-sm focus:border-orange-500 outline-none" 
                                           required placeholder="0">
                                </td>
                                <td class="p-3 align-bottom">
                                    <input type="text" :name="'unit[' + index + ']'" x-model="row.unit" class="w-full bg-[#222] border border-white/5 rounded-lg px-3 py-2 text-gray-400 text-center text-sm outline-none cursor-not-allowed" readonly>
                                </td>
                                <td class="p-3 text-right font-mono text-gray-300 text-sm align-bottom">
                                    <span x-text="formatRupiah(calculateRowTotal(row))"></span>
                                </td>
                                <td class="p-3 text-center align-bottom">
                                    <button type="button" @click="removeRow(index)" class="text-red-500 hover:text-red-400 p-1 rounded hover:bg-red-500/10 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        
                        <tr x-show="rows.length === 0">
                            <td colspan="5" class="p-8 text-center text-gray-500 border-t border-dashed border-white/10">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h2m-2 4h.01M7 16h.01"/></svg>
                                    <span>Belum ada bahan baku ditambahkan.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-[#151515] border-t border-white/10">
                        <tr>
                            <td colspan="3" class="p-4 text-right font-bold text-gray-400 uppercase text-xs tracking-wider">Total Estimasi HPP Material</td>
                            <td class="p-4 text-right font-bold text-green-400 text-lg font-mono" x-text="formatRupiah(calculateGrandTotal())"></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t border-white/10">
            <a href="<?php echo e(route('admin.bom.index')); ?>" class="px-6 py-3 rounded-xl border border-white/10 text-gray-300 font-bold hover:bg-white/5 transition text-sm">Batal</a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-orange-600 text-white font-bold hover:bg-orange-500 shadow-lg shadow-orange-900/20 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

<script>
let sketsaFileTransfer = new DataTransfer();

function handleSketsaSelect(input) {
    const files = Array.from(input.files);
    const container = document.getElementById('sketsaPreviewContainer');
    const dropzone = document.getElementById('sketsaDropzone');
    const hint = document.getElementById('sketsaHint');
    const existingCount = document.querySelectorAll('[name^="delete_sketsa"]').length;
    const maxTotal = 4;
    const currentPreviews = container.querySelectorAll('.preview-item').length;
    const remainingSlots = maxTotal - existingCount - currentPreviews;

    if (files.length === 0) return;

    // Validasi: jumlah total tidak boleh lebih dari 4
    if (files.length > remainingSlots) {
        alert('Maksimal ' + maxTotal + ' foto sketsa. Kamu bisa upload maksimal ' + remainingSlots + ' foto lagi.');
        return;
    }

    container.classList.remove('hidden');

    files.forEach((file) => {
        sketsaFileTransfer.items.add(file);

        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-item relative group aspect-square rounded-xl overflow-hidden border border-white/10 bg-black/30';
            wrapper.dataset.filename = file.name;
            wrapper.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover" alt="Preview">
                <button type="button" onclick="removeSketsaPreview(this, '${file.name.replace(/'/g, "\\'")}')" class="absolute top-2 right-2 bg-red-600/90 hover:bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded transition z-10">
                    Hapus
                </button>
                <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[10px] px-2 py-1 truncate">${file.name}</div>
            `;
            container.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });

    // Assign kembali ke input supaya ikut submit
    input.files = sketsaFileTransfer.files;

    // Update hint text
    const newRemaining = remainingSlots - files.length;
    if (hint) {
        hint.textContent = newRemaining > 0
            ? 'Maks ' + newRemaining + ' foto lagi. PNG, JPG, WEBP hingga 10MB'
            : 'Slot sketsa penuh. Hapus preview atau sketsa lama untuk mengganti.';
    }

    // Sembunyikan dropzone kalau sudah penuh
    if (newRemaining <= 0 && dropzone) {
        dropzone.classList.add('hidden');
    }
}

function removeSketsaPreview(btn, fileName) {
    const wrapper = btn.closest('.preview-item');
    const container = document.getElementById('sketsaPreviewContainer');
    const dropzone = document.getElementById('sketsaDropzone');
    const hint = document.getElementById('sketsaHint');
    const input = document.getElementById('sketsaInput');
    const existingCount = document.querySelectorAll('[name^="delete_sketsa"]').length;
    const maxTotal = 4;

    wrapper.remove();

    // Hapus file dari DataTransfer
    const newDt = new DataTransfer();
    Array.from(sketsaFileTransfer.files).forEach(file => {
        if (file.name !== fileName) {
            newDt.items.add(file);
        }
    });
    sketsaFileTransfer = newDt;
    if (input) input.files = sketsaFileTransfer.files;

    const currentPreviews = container.querySelectorAll('.preview-item').length;
    const remaining = maxTotal - existingCount - currentPreviews;

    if (currentPreviews === 0) {
        container.classList.add('hidden');
    }

    if (hint) {
        hint.textContent = 'Maks ' + remaining + ' foto lagi. PNG, JPG, WEBP hingga 10MB';
    }

    if (dropzone) {
        dropzone.classList.remove('hidden');
    }
}

function bomForm() {
    return {
        rows: <?php echo json_encode($existingBoms ?? [], 15, 512) ?>,
        materials: <?php echo json_encode($materials ?? [], 15, 512) ?>,

        init() {
            // Jika kosong, tambah 1 baris default
            if (this.rows.length === 0) {
                this.addRow();
            }
        },

        addRow() {
            this.rows.push({
                id: Date.now(), // Unique ID untuk key
                material_id: '',
                quantity: 1,
                unit: 'Satuan',
                price: 0,
                stock: 0,
                is_inventory: false
            });
        },

        removeRow(index) {
            this.rows.splice(index, 1);
        },

        getMaterialName(id) {
            if (!id) return '-- Pilih Bahan --';
            let mat = this.materials.find(m => m.id == id);
            return mat ? mat.nama_material : 'Bahan Tidak Ditemukan';
        },

        filteredMaterials(search, currentId) {
            return this.materials.filter(m => {
                const matchSearch = m.nama_material.toLowerCase().includes(search.toLowerCase());
                const isUsedInOtherRows = this.rows.some(r => r.material_id == m.id && r.material_id != currentId);
                return matchSearch && !isUsedInOtherRows;
            });
        },

        updateUnit(index) {
            let row = this.rows[index];
            let mat = this.materials.find(m => m.id == row.material_id);
            if (mat) {
                row.unit = mat.satuan;
                row.price = mat.harga_satuan;
                row.stock = mat.stok;
                row.is_inventory = mat.is_inventory;
            } else {
                row.unit = 'Satuan';
                row.price = 0;
                row.stock = 0;
                row.is_inventory = false;
            }
        },

        calculateRowTotal(row) {
            if (!row.price && row.material_id) {
                let mat = this.materials.find(m => m.id == row.material_id);
                if (mat) row.price = mat.harga_satuan;
            }
            return (parseFloat(row.quantity) || 0) * (parseFloat(row.price) || 0);
        },

        calculateGrandTotal() {
            return this.rows.reduce((total, row) => total + this.calculateRowTotal(row), 0);
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
        }
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/bom/edit.blade.php ENDPATH**/ ?>