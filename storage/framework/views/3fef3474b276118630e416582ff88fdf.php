<?php $__env->startSection('title', 'Edit Produk: ' . $product->nama_produk); ?>
<?php $__env->startSection('title_header', 'Edit Produk'); ?>

<?php $__env->startSection('content'); ?>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-container--default .select2-selection--multiple {
    background-color: rgba(10, 10, 10, 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 1rem !important;
    padding: 0.5rem !important;
    min-height: 56px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: rgba(212, 175, 55, 0.2) !important;
    border: 1px solid #d4af37 !important;
    color: white !important;
    border-radius: 0.5rem !important;
    padding: 0.25rem 0.5rem !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: white !important;
}
.select2-dropdown {
    background-color: #0f0f0f !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
}
.select2-results__option {
    color: white !important;
    padding: 0.75rem 1rem !important;
}
.select2-results__option--highlighted {
    background-color: #d4af37 !important;
    color: black !important;
}
.select2-container .select2-selection--multiple {
    border-radius: 1rem !important;
}
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #d4af37 !important;
    box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2) !important;
}
::placeholder { color: #4b5563 !important; opacity: 1; }
::-ms-input-placeholder { color: #4b5563 !important; }
.space-y-6 { margin-bottom: 3rem !important; }
.border-b { border-color: rgba(255, 255, 255, 0.08) !important; }
[x-cloak] { display: none !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="<?php echo e(route('admin.produk-jadi.index')); ?>" class="p-2 hover:bg-white/5 rounded-xl transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">Edit Produk</h1>
                    </div>
                    <p class="text-sm text-gray-400">
                        Perbarui detail produk <strong class="text-amber-400 font-mono"><?php echo e($product->kode_produk); ?></strong>
                    </p>
                </div>
            </div>

            
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">

                <form action="<?php echo e(route('admin.produk-jadi.update', $product->id)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    
                    <?php if($errors->any()): ?>
                        <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl backdrop-blur-sm">
                            <ul class="list-disc list-inside space-y-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    
                    
                    <div
                        id="product-edit-wrapper"
                        x-data="productEditData()"
                        x-init="init()"
                        data-harga="<?php echo e($product->harga_jual); ?>"
                        data-margin="<?php echo e($margin); ?>"
                        data-foto="<?php echo e($product->foto ? asset('storage/' . $product->foto) : ''); ?>"
                        data-produk-id="<?php echo e($product->id); ?>"
                    >

                        
                        <div class="space-y-6 mb-10">
                            <div class="pt-1 pb-4 border-b border-white/10">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                    Informasi Dasar
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Nama Produk <span class="text-red-400">*</span></label>
                                    <input type="text" name="nama_produk" required
                                        value="<?php echo e(old('nama_produk', $product->nama_produk)); ?>"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-4 py-3 text-white placeholder-gray-500 text-base font-medium focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                                        placeholder="Contoh: Tas Kulit Premium Black Edition">
                                </div>

                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Kategori</label>
                                    <select name="kategori[]" multiple id="kategori-select"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-4 text-white outline-none">
                                        <?php $__currentLoopData = $kategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($kategori->nama_kategori); ?>"
                                                <?php echo e(in_array($kategori->nama_kategori, $product->kategori ?? []) ? 'selected' : ''); ?>>
                                                <?php echo e($kategori->nama_kategori); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php if(!empty($product->kategori)): ?>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Terpilih: <span class="text-amber-400"><?php echo e(is_array($product->kategori) ? implode(', ', $product->kategori) : $product->kategori); ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <div class="space-y-6 mb-10">
                            <div class="pt-1 pb-4 border-b border-white/10">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                                    Spesifikasi Produk
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Warna <span class="text-red-400">*</span></label>
                                    <select name="warna[]" multiple required id="warna-select"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-4 text-white outline-none">
                                        <?php $__currentLoopData = $warnas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warna): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($warna->nama_warna); ?>"
                                                <?php echo e(in_array($warna->nama_warna, $product->warna) ? 'selected' : ''); ?>>
                                                <?php echo e($warna->nama_warna); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    
                                    <?php if(!empty($product->warna)): ?>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Terpilih: <span class="text-amber-400"><?php echo e(implode(', ', $product->warna)); ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Jenis Motif</label>
                                    <select name="jenis_motif[]" multiple id="motif-select"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-4 text-white outline-none">
                                        <?php $__currentLoopData = $motifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $motif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($motif->nama_motif); ?>"
                                                <?php echo e(in_array($motif->nama_motif, $product->jenis_motif) ? 'selected' : ''); ?>>
                                                <?php echo e($motif->nama_motif); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    
                                    <?php if(!empty($product->jenis_motif)): ?>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Terpilih: <span class="text-amber-400"><?php echo e(implode(', ', $product->jenis_motif)); ?></span>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Stok Minimum (Alert)</label>
                                    <input type="number" min="0" name="stok_minimum"
                                        value="<?php echo e(old('stok_minimum', (int)($product->stok_minimum ?? 10))); ?>"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                                        placeholder="10">
                                </div>
                            </div>

                        </div>

                        
                        <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-5 mb-8">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-amber-400 font-bold">⚙️ Detail Produksi</h4>
                                    <p class="text-zinc-400 text-sm mt-1">Dimensi, Pola, Sketsa, dan Resep BOM ada di halaman khusus Produksi</p>
                                </div>
                                <a href="<?php echo e(route('admin.bom.edit', $product->id)); ?>"
                                   class="px-5 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-bold transition flex items-center gap-2">
                                    <i class="fa-solid fa-gears"></i> Ke Halaman Produksi
                                </a>
                            </div>
                        </div>

                        
                        <div class="space-y-6 mb-10">
                            <div class="pt-1 pb-4 border-b border-white/10">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
                                    Harga & Media
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                                
                                <div class="space-y-3">
                                    <label class="block text-sm font-semibold text-gray-300">Tipe Harga</label>
                                    <div class="space-y-3">
                                        <label class="flex items-center p-4 bg-[#0a0a0a]/80 border border-white/20 rounded-2xl cursor-pointer hover:border-yellow-400 transition-all">
                                            <input type="radio" name="tipe_harga" value="manual"
                                                <?php echo e(old('tipe_harga', $product->tipe_harga ?? 'manual') === 'manual' ? 'checked' : ''); ?>

                                                class="w-5 h-5 border-gray-600 bg-transparent"
                                                onchange="toggleHargaField()">
                                            <div class="ml-3">
                                                <span class="text-white font-semibold">Harga Manual</span>
                                                <p class="text-xs text-gray-500 mt-1">Input harga secara langsung</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-4 bg-[#0a0a0a]/80 border border-white/20 rounded-2xl cursor-pointer hover:border-yellow-400 transition-all">
                                            <input type="radio" name="tipe_harga" value="otomatis"
                                                <?php echo e(old('tipe_harga', $product->tipe_harga ?? 'manual') === 'otomatis' ? 'checked' : ''); ?>

                                                class="w-5 h-5 border-gray-600 bg-transparent"
                                                onchange="toggleHargaField()">
                                            <div class="ml-3">
                                                <span class="text-white font-semibold">Harga Otomatis</span>
                                                <p class="text-xs text-gray-500 mt-1">Markup dari BOM + Margin</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                
                                <div class="space-y-2" id="harga-manual-container">
                                    <label class="block text-sm font-semibold text-gray-300">Harga Jual <span class="text-red-400">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-lg">Rp</span>
                                        <input
                                            type="text"
                                            name="harga_jual"
                                            id="harga_jual_input"
                                            required
                                            x-model="price"
                                            x-on:input="onHargaInput($event)"
                                            class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl py-5 pl-14 pr-5 text-white placeholder-gray-500 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 font-mono font-bold text-xl"
                                            placeholder="150.000">
                                    </div>
                                    <p class="text-xs text-gray-500" id="bantuan-harga">
                                        Harga dihitung otomatis dari HPP BOM + Margin <span x-text="margin + '%'"></span>.
                                    </p>
                                </div>

                                
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-300">Biaya Lain-lain (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-lg">Rp</span>
                                        <input
                                            type="text"
                                            name="biaya_lain"
                                            x-model="biayaLainFormatted"
                                            x-on:input="onBiayaInput($event)"
                                            class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl py-5 pl-14 pr-5 text-white placeholder-gray-500 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 font-mono font-bold text-xl"
                                            placeholder="0">
                                    </div>
                                    <p class="text-xs text-gray-500">Contoh: biaya packaging, stiker, dll. (Opsional)</p>
                                </div>

                                
                                <div class="space-y-3">
                                    <label class="block text-sm font-semibold text-gray-300">Foto Produk</label>
                                    <div class="space-y-3">
                                        <div x-show="filePreview" class="w-full h-32 rounded-2xl overflow-hidden border-2 border-dashed border-white/20 bg-black/30 flex items-center justify-center shadow-lg" x-cloak>
                                            <img x-bind:src="filePreview" class="w-full h-full object-cover" alt="Preview">
                                        </div>
                                        <div x-show="!filePreview" class="group relative w-full h-32 border-2 border-dashed border-white/20 hover:border-yellow-400 hover:bg-white/5 rounded-2xl flex flex-col items-center justify-center p-6 cursor-pointer transition-all duration-300 shadow-lg bg-black/30 backdrop-blur-sm">
                                            <svg class="w-10 h-10 text-gray-400 group-hover:text-yellow-400 mb-2 transition" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
                                            </svg>
                                            <p class="text-white font-medium text-sm mb-1">Klik atau drag gambar ke sini</p>
                                            <p class="text-xs text-gray-400">PNG, JPG hingga 10MB</p>
                                            <input type="file" name="foto" accept="image/*"
                                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                                x-on:change="handleFile($event)">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        
                        <div class="space-y-3 mb-6">
                            <label class="block text-sm font-semibold text-gray-300">Deskripsi Produk</label>
                            <textarea name="deskripsi" rows="5"
                                class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-5 text-white placeholder-gray-500 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 resize-vertical text-base"
                                placeholder="Jelaskan detail produk, bahan, ukuran, atau spesifikasi khusus..."><?php echo e(old('deskripsi', $product->deskripsi)); ?></textarea>
                        </div>

                        
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-gray-300">Keterangan (Internal)</label>
                            <textarea name="keterangan" rows="3"
                                class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-5 text-white placeholder-gray-500 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 resize-vertical text-base"
                                placeholder="Catatan internal..."><?php echo e(old('keterangan', $product->keterangan)); ?></textarea>
                        </div>

                    </div>

                    
                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 pt-8 border-t border-white/10">
                        <a href="<?php echo e(route('admin.produk-jadi.index')); ?>"
                           class="px-8 py-4 bg-white/5 hover:bg-white/10 text-white rounded-2xl text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg shadow-black/30">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>
                        <button type="submit"
                            class="px-10 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-900/30 hover:shadow-emerald-900/50 transition-all duration-300 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<style>
.shadow-gold-400\/10 { box-shadow: 0 10px 25px rgba(212, 175, 55, 0.1); }
</style>

<script>
/**
 * ✅ FIX TOTAL: Alpine component didefinisikan sebagai fungsi global.
 * Semua nilai PHP diambil dari data-attribute, BUKAN diembed langsung
 * di dalam string x-data="" agar tidak ada konflik quote / kode bocor.
 */
function productEditData() {
    return {
        price: '',
        margin: <?php echo e($margin); ?>,
        hpp: 0,
        biayaLain: 0,
        biayaLainFormatted: '0',
        filePreview: '',
        produkId: '',

        init() {
            const el = document.getElementById('product-edit-wrapper');
            const hargaRaw  = el.dataset.harga   || '0';
            this.margin     = parseFloat(el.dataset.margin)  || <?php echo e($margin); ?>;
            this.filePreview = el.dataset.foto    || '';
            this.produkId   = el.dataset.produkId || '';

            // Format harga awal
            this.price = this.formatRupiah(parseInt(hargaRaw.replace(/[^0-9]/g, '')) || 0);
        },

        formatRupiah(n) {
            if (!n || isNaN(n)) return '0';
            return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0 }).format(n);
        },

        onHargaInput(e) {
            // Bersihkan input, format ulang
            const raw = e.target.value.replace(/[^0-9]/g, '');
            this.price = raw ? this.formatRupiah(parseInt(raw)) : '';
        },

        onBiayaInput(e) {
            const raw = e.target.value.replace(/[^0-9]/g, '');
            this.biayaLain = parseInt(raw) || 0;
            this.biayaLainFormatted = raw ? this.formatRupiah(this.biayaLain) : '';
        },

        handleFile(e) {
            const file = e.target.files[0];
            if (!file) { this.filePreview = ''; return; }
            const reader = new FileReader();
            reader.onload = (ev) => { this.filePreview = ev.target.result; };
            reader.readAsDataURL(file);
        },

        async hitungHargaOtomatis() {
            const bantuanEl = document.getElementById('bantuan-harga');
            bantuanEl.innerHTML = '⏳ Menghitung HPP dari BOM...';
            try {
                const res  = await fetch('/admin/get-hpp-produk/' + this.produkId);
                if (!res.ok) throw new Error('Response tidak OK: ' + res.status);
                const data = await res.json();

                this.hpp    = data.hpp;
                this.margin = data.margin;

                const hargaJual = Math.ceil(this.hpp + (this.hpp * this.margin / 100) + this.biayaLain);
                this.price = this.formatRupiah(hargaJual);

                // Update hidden input agar value yang tersubmit benar
                const inputEl = document.getElementById('harga_jual_input');
                if (inputEl) inputEl.value = this.price;

                bantuanEl.innerHTML =
                    '✅ Harga otomatis: HPP <strong>Rp ' + this.formatRupiah(this.hpp) +
                    '</strong> + Margin ' + this.margin + '% = <strong class="text-amber-400">Rp ' +
                    this.formatRupiah(hargaJual) + '</strong>';
            } catch (e) {
                console.error('getHpp error:', e);
                bantuanEl.innerHTML = '⚠️ Gagal mengambil data HPP. Pastikan BOM produk sudah diisi.';
            }
        }
    };
}

// ✅ toggleHargaField: gunakan Alpine.$data untuk akses component
function toggleHargaField() {
    const isOtomatis = document.querySelector('input[name="tipe_harga"][value="otomatis"]').checked;
    const hargaInput = document.getElementById('harga_jual_input');
    const container  = document.getElementById('harga-manual-container');

    if (isOtomatis) {
        hargaInput.readOnly = true;
        hargaInput.classList.add('opacity-50', 'cursor-not-allowed');
        container.classList.add('opacity-75');

        // Panggil hitungHargaOtomatis via Alpine
        const wrapper = document.getElementById('product-edit-wrapper');
        if (wrapper && wrapper._x_dataStack) {
            wrapper._x_dataStack[0].hitungHargaOtomatis();
        }
    } else {
        hargaInput.readOnly = false;
        hargaInput.classList.remove('opacity-50', 'cursor-not-allowed');
        container.classList.remove('opacity-75');
        document.getElementById('bantuan-harga').innerHTML =
            'Harga dihitung otomatis dari HPP BOM + Margin.';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Jalankan toggle awal (default = manual, jadi tidak fetch HPP)
    toggleHargaField();

    // Select2 init
    if (typeof $.fn.select2 !== 'undefined') {
        $('#kategori-select').select2({ placeholder: 'Pilih Kategori', width: '100%' });
        $('#warna-select').select2({ placeholder: 'Pilih Warna', width: '100%' });
        $('#motif-select').select2({ placeholder: 'Pilih Motif', width: '100%' });
    }
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/admin/produk-jadi/edit.blade.php ENDPATH**/ ?>