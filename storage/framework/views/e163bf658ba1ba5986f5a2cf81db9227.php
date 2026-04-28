<?php $__env->startSection('title', 'Scan Nota - Berliano Leather'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-md mx-auto bg-[#0B0E14] min-h-screen">

    <?php echo $__env->make('components.operator-header', ['title' => 'SCAN NOTA'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="px-6 py-6 space-y-6">
        <!-- Tab Navigation -->
        <div class="bg-[#1C222D] backdrop-blur-sm rounded-2xl p-3 border border-white/5 shadow-xl">
            <div class="flex gap-2">
                <button id="tabScan" class="flex-1 text-center py-2 px-3 text-sm font-semibold rounded-xl transition-all duration-300 bg-emerald-500/20 border-2 border-emerald-500 text-emerald-400">
                    <div class="flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Scan Kamera</span>
                    </div>
                </button>
                <button id="tabManual" class="flex-1 text-center py-2 px-3 text-sm font-semibold rounded-xl transition-all duration-300 bg-emerald-500/20 border-2 border-emerald-500/50 text-emerald-400 hover:bg-emerald-500/30 hover:border-emerald-500">
                    <div class="flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Input Manual</span>
                    </div>
                </button>
                <button id="tabPelengkap" class="flex-1 text-center py-2 px-3 text-sm font-semibold rounded-xl transition-all duration-300 bg-emerald-500/20 border-2 border-emerald-500/50 text-emerald-400 hover:bg-emerald-500/30 hover:border-emerald-500">
                    <div class="flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Bahan Pelengkap</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Scan Tab Content -->
        <div id="contentScan">
            <div class="bg-[#1C222D] backdrop-blur-sm rounded-2xl p-6 border border-white/5 shadow-xl">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Scan via Kamera</h3>
                    <p class="text-gray-400 text-sm mt-1">Klik tombol untuk ambil foto nota</p>
                </div>
                <button id="scanBtn" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 px-6 rounded-2xl shadow-lg hover:shadow-blue-500/30 transition-all duration-200 font-semibold text-base flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Ambil Foto</span>
                </button>
                <input type="file" id="cameraInput" accept="image/*" capture="environment" class="hidden">
            </div>
        </div>

        <!-- Bahan Pelengkap Tab Content -->
        <div id="contentPelengkap" class="hidden">
            <div class="bg-[#1A1A1A] rounded-3xl shadow-xl border border-white/10 p-6">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Input Bahan Pelengkap</h3>
                    <p class="text-gray-400 text-sm mt-1">Bahan non-stok (stok = 0, harga = 0)</p>
                </div>
                <form id="pelengkapForm" action="<?php echo e(route('operator.bahan.store')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="bg-[#1C222D] backdrop-blur-sm rounded-2xl p-5 border border-white/5 shadow-xl">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Kode Material</label>
                            <input type="text" name="kode_material" id="kodePelengkap" placeholder="Otomatis terisi saat pilih bahan..." class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-400 focus:ring-2 focus:ring-purple-500 outline-none" readonly>
                        </div>
                         <div class="mt-4">
                             <label class="block text-sm font-medium text-gray-300 mb-2">Nama Bahan Pelengkap <span class="text-red-500">*</span></label>
                             <input type="text" id="nama_pelengkap" name="nama_material" placeholder="Ketik nama bahan pelengkap..." class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none" required autocomplete="off">
                         </div>
                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                                <select name="kategori" id="kategoriPelengkapSelect" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php $__empty_1 = true; $__currentLoopData = $kategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($kat->nama_kategori); ?>"><?php echo e($kat->nama_kategori); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option disabled>Data kategori bahan kosong</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Satuan</label>
                                <select name="satuan" id="satuanPelengkapSelect" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none">
                                    <option value="">-- Pilih Satuan --</option>
                                    <?php $__empty_1 = true; $__currentLoopData = $satuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($sat->nama_satuan); ?>"><?php echo e($sat->nama_satuan); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option disabled>Data satuan kosong</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="stok" value="0">
                        <input type="hidden" name="harga_satuan" value="0">
                        <input type="hidden" name="is_inventory" value="0">
                        <div class="mt-4 bg-purple-500/10 border border-purple-500/30 rounded-lg p-3">
                            <p class="text-sm text-purple-300"><strong>Info:</strong> Bahan pelengkap tidak dihitung stok dan harganya (stok = 0, harga = 0).</p>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:shadow-purple-500/30 transition-all duration-200">Simpan</button>
                        <button type="button" id="cancelPelengkapBtn" class="flex-1 bg-gray-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:bg-gray-600 transition-all duration-200">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Manual Input Tab Content -->
        <div id="contentManual" class="hidden">
            <div class="bg-[#1A1A1A] rounded-3xl shadow-xl border border-white/10 p-6">
                <div class="text-center mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Input Manual Bahan</h3>
                    <p class="text-gray-400 text-sm mt-1">Masukkan data bahan secara manual</p>
                </div>
                <form id="manualForm" action="<?php echo e(route('operator.bahan.store')); ?>" method="POST" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <div class="bg-[#1C222D] backdrop-blur-sm rounded-2xl p-5 border border-white/5 shadow-xl">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Kode Material</label>
                            <input type="text" name="kode_material" id="kodeMaterial" placeholder="Otomatis terisi saat pilih bahan..." class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-gray-400 focus:ring-2 focus:ring-blue-500 outline-none" readonly>
                        </div>
                        <div class="relative mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Nama Bahan <span class="text-red-500">*</span></label>
                            <input type="text" id="nama_material" name="nama_material" placeholder="Ketik nama bahan..." class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none" required autocomplete="off">
                            <div id="suggestions" class="absolute top-full left-0 right-0 bg-[#1C222D] border border-white/10 rounded-xl shadow-2xl mt-1 max-h-40 overflow-y-auto z-20 hidden"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Supplier</label>
                                <input type="text" name="supplier" id="supplierInput" placeholder="Contoh: CV. Kulit Jaya" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Ref / Nota</label>
                                <input type="text" name="ref_no" id="refNoInput" placeholder="No Nota / Barcode" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                                <select name="kategori" id="kategoriSelect" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php $__empty_1 = true; $__currentLoopData = $kategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($kat->nama_kategori); ?>"><?php echo e($kat->nama_kategori); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option disabled>Data kategori bahan kosong</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Satuan <span class="text-red-500">*</span></label>
                                <select name="satuan" id="satuanSelect" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <?php $__empty_1 = true; $__currentLoopData = $satuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <option value="<?php echo e($sat->nama_satuan); ?>"><?php echo e($sat->nama_satuan); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <option disabled>Data satuan kosong</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Jumlah Masuk <span class="text-red-500">*</span></label>
                                <input type="number" name="stok" id="jumlahInput" placeholder="0" step="0.01" min="0.01" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Harga Beli (Rp) <span class="text-red-500">*</span></label>
                                <div id="lastPriceInfo" class="hidden text-xs text-amber-400 mb-2"></div>
                                <input type="number" name="harga_satuan" id="hargaSatuan" placeholder="Rp" step="0.01" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Foto Bukti (Opsional)</label>
                            <div class="relative">
                                <input type="file" name="foto_bukti" id="fotoBuktiInput" accept="image/*" class="hidden">
                                <div id="fotoBuktiPreview" class="hidden mb-3">
                                    <img id="fotoBuktiThumbnail" class="w-full h-32 object-cover rounded-xl border border-white/10">
                                </div>
                                <button type="button" id="btnFotoBukti" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white flex items-center justify-center gap-2 hover:bg-gray-700/50 transition">
                                    <i class="fa-solid fa-camera text-gray-400"></i>
                                    <span id="textFotoBukti">Tambah Foto Bukti Fisik</span>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_inventory" value="1" class="w-5 h-5 rounded border-white/10 bg-[#1C222D] text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-900" checked>
                                <span class="text-sm text-gray-300"><strong>Hitung Stok & Harga</strong> - Bahan ini akan dihitung stok dan harganya</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-8">Uncheck jika bahan ini adalah aksesoris/non-stok (seperti jaring, DLL)</p>
                        </div>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:shadow-blue-500/30 transition-all duration-200">Simpan</button>
                        <button type="button" id="cancelManualBtn" class="flex-1 bg-gray-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:bg-gray-600 transition-all duration-200">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Area -->
        <div id="previewArea" class="bg-[#1C222D] backdrop-blur-sm rounded-2xl p-6 border border-white/5 shadow-xl hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Preview Foto</h3>
                <div class="flex gap-2">
                    <button id="retakeBtn" class="text-blue-400 hover:text-blue-300 font-medium text-sm">Ambil Ulang</button>
                    <button id="cancelBtn" class="text-gray-400 hover:text-gray-300 font-medium text-sm">Batal</button>
                </div>
            </div>
            <div class="border-2 border-dashed border-white/10 rounded-lg p-4 mb-4">
                <img id="previewImage" class="w-full h-auto rounded-lg shadow-md" alt="Preview Nota">
            </div>
            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 mb-4">
                <p class="text-sm text-blue-300">Pastikan foto nota terlihat jelas sebelum diproses.</p>
            </div>
            <button id="confirmBtn" class="w-full bg-gradient-to-r from-green-500 to-blue-500 text-white py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-200 font-semibold">
                Proses OCR
            </button>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 hidden">
            <p id="errorText" class="font-medium"></p>
        </div>

        <!-- Success Message -->
        <div id="successMessage" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl mb-6 hidden">
            <p id="successText" class="font-medium"></p>
        </div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 hidden">
            <div class="bg-[#1C222D] rounded-xl p-8 text-center shadow-2xl max-w-sm mx-4 border border-gray-700">
                <div class="w-16 h-16 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-white font-bold text-lg" id="loadingText">Memproses OCR...</p>
                <p class="text-gray-400 text-sm mt-2">Mohon tunggu, sedang menganalisis nota</p>
            </div>
        </div>
    </div>
</div>

<script>
// ==========================================
// TAB NAVIGATION — FIX: logic benar + previewArea ikut hidden saat ganti tab
// ==========================================
const tabScan      = document.getElementById('tabScan');
const tabManual    = document.getElementById('tabManual');
const tabPelengkap = document.getElementById('tabPelengkap');
const contentScan      = document.getElementById('contentScan');
const contentManual    = document.getElementById('contentManual');
const contentPelengkap = document.getElementById('contentPelengkap');
const previewArea      = document.getElementById('previewArea');

const allTabs     = [tabScan, tabManual, tabPelengkap];
const allContents = [contentScan, contentManual, contentPelengkap, previewArea];

let currentActiveTab = tabScan;

function switchTab(activeTab, activeContent) {
    if (currentActiveTab !== activeTab) {
        if (document.getElementById('manualForm')) resetManualForm();
        if (document.getElementById('pelengkapForm')) resetPelengkapForm();
    }
    currentActiveTab = activeTab;

    allContents.forEach(c => c.classList.add('hidden'));
    allTabs.forEach(t => {
        t.classList.remove('border-emerald-500');
        t.classList.add('border-emerald-500/50');
    });
    activeContent.classList.remove('hidden');
    activeTab.classList.remove('border-emerald-500/50');
    activeTab.classList.add('border-emerald-500');
}

tabScan.addEventListener('click',      () => switchTab(tabScan,      contentScan));
tabManual.addEventListener('click',    () => switchTab(tabManual,    contentManual));
tabPelengkap.addEventListener('click', () => switchTab(tabPelengkap, contentPelengkap));

// ==========================================
// CAMERA SCAN
// ==========================================
const scanBtn        = document.getElementById('scanBtn');
const cameraInput    = document.getElementById('cameraInput');
const previewImage   = document.getElementById('previewImage');
const retakeBtn      = document.getElementById('retakeBtn');
const cancelBtn      = document.getElementById('cancelBtn');
const confirmBtn     = document.getElementById('confirmBtn');
const loadingOverlay = document.getElementById('loadingOverlay');
const loadingText    = document.getElementById('loadingText');
const errorMessage   = document.getElementById('errorMessage');
const errorText      = document.getElementById('errorText');
const successMessage = document.getElementById('successMessage');
const successText    = document.getElementById('successText');

const flashOverlay = document.createElement('div');
flashOverlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:white;opacity:0;pointer-events:none;z-index:9999;transition:opacity 0.1s;';
document.body.appendChild(flashOverlay);

let currentImageBase64 = null;

scanBtn.addEventListener('click', () => cameraInput.click());

cameraInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 10 * 1024 * 1024) { showError('Ukuran file terlalu besar. Maksimal 10MB.'); return; }
    processFile(file);
});

function processFile(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const maxWidth = 1200, maxHeight = 800;
            let width = img.width, height = img.height;
            if (width > height) {
                if (width > maxWidth) { height = Math.round(height * maxWidth / width); width = maxWidth; }
            } else {
                if (height > maxHeight) { width = Math.round(width * maxHeight / height); height = maxHeight; }
            }
            canvas.width = width; canvas.height = height;
            ctx.drawImage(img, 0, 0, width, height);
            const imageData = ctx.getImageData(0, 0, width, height);
            const data = imageData.data;
            for (let i = 0; i < data.length; i += 4) {
                const gray = 0.299 * data[i] + 0.587 * data[i+1] + 0.114 * data[i+2];
                let val = Math.min(255, Math.max(0, (gray - 128) * 1.5 + 128 + 10));
                data[i] = data[i+1] = data[i+2] = val;
            }
            ctx.putImageData(imageData, 0, 0);
            currentImageBase64 = canvas.toDataURL('image/jpeg', 0.9);
            previewImage.src = currentImageBase64;
            allContents.forEach(c => c.classList.add('hidden'));
            previewArea.classList.remove('hidden');
            flashOverlay.style.opacity = '1';
            setTimeout(() => flashOverlay.style.opacity = '0', 100);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

retakeBtn.addEventListener('click', () => {
    previewArea.classList.add('hidden');
    contentScan.classList.remove('hidden');
    currentImageBase64 = null;
    cameraInput.value = '';
});

cancelBtn.addEventListener('click', () => {
    currentImageBase64 = null;
    cameraInput.value = '';
    switchTab(tabScan, contentScan);
});

confirmBtn.addEventListener('click', () => {
    if (currentImageBase64) processOCR(currentImageBase64);
});

async function processOCR(base64) {
    loadingOverlay.classList.remove('hidden');
    try {
        const response = await fetch('/operator/scan/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
            body: JSON.stringify({ image_base64: base64 })
        });
        const result = await response.json();
        if (result.success) {
            loadingText.textContent = 'OCR Berhasil!';
            setTimeout(() => { window.location.href = result.redirect_url || '/operator/scan/result'; }, 500);
        } else {
            loadingOverlay.classList.add('hidden');
            showError(result.message || 'Gagal memproses OCR. Pastikan foto jelas dan coba lagi.');
        }
    } catch (error) {
        loadingOverlay.classList.add('hidden');
        showError('Koneksi gagal. Silakan coba lagi.');
    }
}

// ==========================================
// CANCEL BUTTONS
// ==========================================
document.getElementById('cancelManualBtn').addEventListener('click', () => switchTab(tabScan, contentScan));
document.getElementById('cancelPelengkapBtn').addEventListener('click', () => switchTab(tabScan, contentScan));

// ==========================================
// FOTO BUKTI
// ==========================================
const fotoBuktiInput     = document.getElementById('fotoBuktiInput');
const btnFotoBukti       = document.getElementById('btnFotoBukti');
const fotoBuktiPreview   = document.getElementById('fotoBuktiPreview');
const fotoBuktiThumbnail = document.getElementById('fotoBuktiThumbnail');
const textFotoBukti      = document.getElementById('textFotoBukti');

btnFotoBukti.addEventListener('click', () => fotoBuktiInput.click());
fotoBuktiInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (event) => {
        fotoBuktiThumbnail.src = event.target.result;
        fotoBuktiPreview.classList.remove('hidden');
        textFotoBukti.textContent = 'Ganti Foto Bukti';
        btnFotoBukti.classList.add('bg-blue-500/20', 'border-blue-500/50');
    };
    reader.readAsDataURL(file);
});

// ==========================================
// HELPERS
// ==========================================
function showError(msg) {
    errorText.textContent = msg;
    errorMessage.classList.remove('hidden');
    setTimeout(() => errorMessage.classList.add('hidden'), 5000);
}
function showSuccess(msg) {
    successText.textContent = msg;
    successMessage.classList.remove('hidden');
    setTimeout(() => successMessage.classList.add('hidden'), 3000);
}
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (!previewArea.classList.contains('hidden')) cancelBtn.click();
        else window.location.href = "<?php echo e(route('operator.dashboard')); ?>";
    }
});

// ==========================================
// AUTOCOMPLETE — FIX utama:
// 1. Pakai getElementById bukan querySelector (agar tidak ambil select pelengkap)
// 2. Handle kategori/satuan yang mungkin object atau string
// ==========================================
function selectMaterial(itemElement) {
    document.getElementById('nama_material').value = itemElement.dataset.nama;
    document.getElementById('kodeMaterial').value  = itemElement.dataset.kode;
    document.getElementById('supplierInput').value = itemElement.dataset.supplier || '';
    document.getElementById('refNoInput').value    = itemElement.dataset.refNo || '';

    // Disable tombol foto
    btnFotoBukti.disabled = true;
    btnFotoBukti.classList.add('opacity-50', 'cursor-not-allowed');
    btnFotoBukti.classList.remove('hover:bg-gray-700/50');
    textFotoBukti.textContent = 'Master Foto Sudah Ada';
    fotoBuktiInput.disabled = true;

    // FIX: getElementById bukan querySelector — hindari ambil select pelengkap
    const kategoriSelect = document.getElementById('kategoriSelect');
    const satuanSelect   = document.getElementById('satuanSelect');

    const kategoriVal = itemElement.dataset.kategori || '';
    const satuanVal   = itemElement.dataset.satuan || '';

    if (kategoriVal !== '') {
        kategoriSelect.value = kategoriVal;
    }

    if (satuanVal !== '') {
        satuanSelect.value = satuanVal;
    }

    const lastPrice = parseFloat(itemElement.dataset.harga);
    const lastPriceInfo = document.getElementById('lastPriceInfo');
    if (lastPrice > 0) {
        lastPriceInfo.innerHTML = `ℹ️ Harga terakhir: <strong>Rp ${new Intl.NumberFormat('id-ID').format(lastPrice)}</strong>`;
        lastPriceInfo.classList.remove('hidden');
        lastPriceInfo.classList.add('text-amber-400');
    } else {
        lastPriceInfo.classList.add('hidden');
    }

    document.getElementById('suggestions').classList.add('hidden');
    document.getElementById('suggestions').innerHTML = '';
}

// Reset saat user ketik ulang (bahan baru)
document.getElementById('nama_material').addEventListener('input', function() {
    // If user types, it's a new material or a search, so reset dependent fields
    if (document.getElementById('kodeMaterial').value) {
        resetManualForm(false); // false to not clear the typed name
    }
});

// FIX: Satu DOMContentLoaded saja
document.addEventListener('DOMContentLoaded', function() {
    const input       = document.getElementById('nama_material');
    const suggestions = document.getElementById('suggestions');
    let timeout;
    if (!input || !suggestions) return;

    input.addEventListener('input', function() {
        document.getElementById('lastPriceInfo').classList.add('hidden');
        const query = this.value.trim();
        if (query.length < 1) { suggestions.classList.add('hidden'); suggestions.innerHTML = ''; return; }
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            fetch('<?php echo e(route("api.bahan.search")); ?>?q=' + encodeURIComponent(query) + '&is_inventory=1', {
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            })
            .then(r => r.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(function(item) {
                        const pathFoto = item.foto || item.foto_url;
                        let htmlFoto = pathFoto && !pathFoto.includes('placeholder')
                            ? `<img src="${pathFoto.startsWith('http') ? pathFoto : '/storage/' + pathFoto.replace('storage/', '')}" class="w-full h-full object-cover">`
                            : `<div class="w-full h-full bg-gray-700 flex items-center justify-center"><i class="fa-solid fa-box text-gray-500"></i></div>`;

                        const itemDiv = document.createElement('div');
                        itemDiv.className = 'px-3 py-2 hover:bg-gray-700 cursor-pointer text-sm text-white flex items-center gap-3';
                        itemDiv.innerHTML = `
                            <div class="w-14 h-14 flex-shrink-0 rounded-xl overflow-hidden border border-white/10 shadow-inner flex items-center justify-center bg-gray-700">${htmlFoto}</div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-white text-base truncate">${item.nama_material || '-'}</div>
                                <div class="text-xs text-yellow-400 font-mono">${item.kode_material || '-'}</div>
                                <div class="text-xs text-gray-400">Stok: ${item.stok} ${item.satuan}</div>
                            </div>`;

                        // FIX: pastikan kategori & satuan selalu string, bukan object
                        itemDiv.dataset.nama     = item.nama_material || '';
                        itemDiv.dataset.kode     = item.kode_material || '';
                        itemDiv.dataset.kategori = (item.kategori && typeof item.kategori === 'object')
                                                    ? (item.kategori.nama_kategori || '') : (item.kategori || '');
                        itemDiv.dataset.satuan   = (item.satuan && typeof item.satuan === 'object')
                                                    ? (item.satuan.nama_satuan || '') : (item.satuan || '');
                        itemDiv.dataset.supplier = item.supplier || '';
                        itemDiv.dataset.refNo    = item.ref_no || '';
                        itemDiv.dataset.harga    = item.harga_satuan || '0';

                        itemDiv.addEventListener('click', function() { selectMaterial(this); });
                        suggestions.appendChild(itemDiv);
                    });
                    suggestions.classList.remove('hidden');
                } else {
                    suggestions.classList.add('hidden');
                }
            })
            .catch(() => suggestions.classList.add('hidden'));
        }, 250);
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.classList.add('hidden');
        }
    });

     // Bahan Pelengkap - SELALU BARU, TIDAK ADA CARI / AUTOCOMPLETE
     document.getElementById('nama_pelengkap').addEventListener('input', function() {
         // Setiap input adalah bahan baru, reset kode material
         document.getElementById('kodePelengkap').value = '';
     });
});



function resetFotoBukti() {
    fotoBuktiInput.value = '';
    fotoBuktiThumbnail.src = '';
    fotoBuktiPreview.classList.add('hidden');
    textFotoBukti.textContent = 'Tambah Foto Bukti Fisik';
    btnFotoBukti.classList.remove('bg-blue-500/20', 'border-blue-500/50');
    btnFotoBukti.disabled = false;
    btnFotoBukti.classList.remove('opacity-50', 'cursor-not-allowed');
    btnFotoBukti.classList.add('hover:bg-gray-700/50');
    fotoBuktiInput.disabled = false;
}

function resetManualForm(clearName = true) {
    const manualForm = document.getElementById('manualForm');
    if (clearName) {
        manualForm.reset();
    } else {
        // Reset all fields except the name
        Array.from(manualForm.elements).forEach(el => {
            if (el.name !== 'nama_material' && el.type !== 'hidden' && el.type !== 'submit' && el.type !== 'button') {
                if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
                else el.value = '';
            }
        });
        // Restore defaults for specific fields
        manualForm.querySelector('[name="is_inventory"]').checked = true;
    }
    document.getElementById('suggestions').innerHTML = '';
    document.getElementById('suggestions').classList.add('hidden');
    document.getElementById('lastPriceInfo').classList.add('hidden');
    resetFotoBukti();
}

function resetPelengkapForm() {
    document.getElementById('pelengkapForm').reset();
}

function resetFotoBukti() {
    fotoBuktiInput.value = '';
    fotoBuktiThumbnail.src = '';
    fotoBuktiPreview.classList.add('hidden');
    textFotoBukti.textContent = 'Tambah Foto Bukti Fisik';
    btnFotoBukti.classList.remove('bg-blue-500/20', 'border-blue-500/50');
    btnFotoBukti.disabled = false;
    btnFotoBukti.classList.remove('opacity-50', 'cursor-not-allowed');
    btnFotoBukti.classList.add('hover:bg-gray-700/50');
    fotoBuktiInput.disabled = false;
}

function resetManualForm(clearName = true) {
    const manualForm = document.getElementById('manualForm');
    if (clearName) {
        manualForm.reset();
    } else {
        // Reset all fields except the name
        Array.from(manualForm.elements).forEach(el => {
            if (el.name !== 'nama_material' && el.type !== 'hidden' && el.type !== 'submit' && el.type !== 'button') {
                if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
                else el.value = '';
            }
        });
        // Restore defaults for specific fields
        manualForm.querySelector('[name="is_inventory"]').checked = true;
    }
    document.getElementById('suggestions').innerHTML = '';
    document.getElementById('suggestions').classList.add('hidden');
    document.getElementById('lastPriceInfo').classList.add('hidden');
    resetFotoBukti();
}


</script>

<style>
#flashOverlay {
    position: fixed; top: 0; left: 0;
    width: 100%; height: 100%;
    background: white; opacity: 0;
    pointer-events: none; z-index: 9999;
    transition: opacity 0.1s;
}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.operator_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\berliano_leather\berliano-leather\resources\views/operator/scan-nota.blade.php ENDPATH**/ ?>