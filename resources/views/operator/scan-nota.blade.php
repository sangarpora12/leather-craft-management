@extends('layouts.operator_app')

@section('title', 'Scan Nota - Berliano Leather')

@section('content')
<div class="w-full max-w-md mx-auto bg-gradient-to-br from-neutral-900 via-neutral-900 to-neutral-800 min-h-screen">

    @include('components.operator-header', ['title' => 'SCAN NOTA'])

    <div class="px-6 py-6 space-y-6">
        <!-- Tab Navigation -->
        <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl p-3 border border-gray-700/50 shadow-xl">
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
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 shadow-xl">
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
                
                <!-- Hidden file input for camera -->
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

                <form id="pelengkapForm" action="{{ route('operator.bahan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl p-5 border border-gray-700/50 shadow-xl">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Kode Material</label>
                            <input type="text" name="kode_material" id="kodePelengkap" placeholder="Otomatis terisi saat pilih bahan..." class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-gray-400 focus:ring-2 focus:ring-purple-500 outline-none" readonly>
                        </div>

                        <div class="relative mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Nama Bahan Pelengkap <span class="text-red-500">*</span></label>
                            <input type="text" id="nama_pelengkap" name="nama_material" placeholder="Ketik nama bahan pelengkap..." class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none" required autocomplete="off">
                            <div id="suggestionsPelengkap" class="absolute top-full left-0 right-0 bg-gray-800 border border-gray-600 rounded-xl shadow-2xl mt-1 max-h-40 overflow-y-auto z-20 hidden"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                                <select name="kategori" id="kategoriPelengkapSelect" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none">
                                    <option value="">-- Pilih Kategori --</option>
                                    @forelse ($kategories as $kategori)
                                        <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>
                                    @empty
                                        <option disabled>Data kategori bahan kosong</option>
                                    @endforelse
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Satuan</label>
                                <select name="satuan" id="satuanPelengkapSelect" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none">
                                    <option value="">-- Pilih Satuan --</option>
                                    @forelse ($satuans as $satuan)
                                        <option value="{{ $satuan->nama_satuan }}">{{ $satuan->nama_satuan }}</option>
                                    @empty
                                        <option disabled>Data satuan kosong</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <!-- Hidden fields for non-inventory -->
                        <input type="hidden" name="stok" value="0">
                        <input type="hidden" name="harga_satuan" value="0">
                        <input type="hidden" name="is_inventory" value="0">

                        <div class="mt-4 bg-purple-500/10 border border-purple-500/30 rounded-lg p-3">
                            <p class="text-sm text-purple-300">
                                <strong>Info:</strong> Bahan pelengkap tidak dihitung stok dan harganya (stok = 0, harga = 0).
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:shadow-purple-500/30 transition-all duration-200">
                            Simpan
                        </button>
                        <button type="button" id="cancelPelengkapBtn" class="flex-1 bg-gray-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:bg-gray-600 transition-all duration-200">
                            Batal
                        </button>
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

                <form id="manualForm" action="{{ route('operator.bahan.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl p-5 border border-gray-700/50 shadow-xl">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Kode Material</label>
                            <input type="text" name="kode_material" id="kodeMaterial" placeholder="Otomatis terisi saat pilih bahan..." class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-gray-400 focus:ring-2 focus:ring-blue-500 outline-none" readonly>
                        </div>

                        <div class="relative mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Nama Bahan <span class="text-red-500">*</span></label>
                            <input type="text" id="nama_material" name="nama_material" placeholder="Ketik nama bahan..." class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none" required autocomplete="off">
                            <div id="suggestions" class="absolute top-full left-0 right-0 bg-gray-800 border border-gray-600 rounded-xl shadow-2xl mt-1 max-h-40 overflow-y-auto z-20 hidden"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Supplier</label>
                                <input type="text" name="supplier" id="supplierInput" placeholder="Contoh: CV. Kulit Jaya" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Ref / Nota</label>
                                <input type="text" name="ref_no" id="refNoInput" placeholder="No Nota / Barcode" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Kategori</label>
                                <select name="kategori" id="kategoriSelect" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none">
                                     <option value="">-- Pilih Kategori --</option>
                                     @forelse ($kategories as $kategori)
                                         <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>
                                     @empty
                                         <option disabled>Data kategori bahan kosong</option>
                                     @endforelse
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Satuan <span class="text-red-500">*</span></label>
                                <select name="satuan" id="satuanSelect" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    @forelse ($satuans as $satuan)
                                        <option value="{{ $satuan->nama_satuan }}">{{ $satuan->nama_satuan }}</option>
                                    @empty
                                        <option disabled>Data satuan kosong</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Jumlah Masuk <span class="text-red-500">*</span></label>
                                <input type="number" name="stok" id="jumlahInput" placeholder="0" step="0.01" min="0.01" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Harga Beli (Rp) <span class="text-red-500">*</span></label>
                                <div id="lastPriceInfo" class="hidden text-xs text-amber-400 mb-2"></div>
                                <input type="number" name="harga_satuan" id="hargaSatuan" placeholder="Rp" step="0.01" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                            </div>
                        </div>

                        <!-- UPLOAD FOTO BUKTI -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Foto Bukti (Opsional)</label>
                            <div class="relative">
                                <input type="file" name="foto_bukti" id="fotoBuktiInput" accept="image/*" class="hidden">
                                <div id="fotoBuktiPreview" class="hidden mb-3">
                                    <img id="fotoBuktiThumbnail" class="w-full h-32 object-cover rounded-xl border border-gray-600">
                                </div>
                                <button type="button" id="btnFotoBukti" class="w-full bg-gray-800/50 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white flex items-center justify-center gap-2 hover:bg-gray-700/50 transition">
                                    <i class="fa-solid fa-camera text-gray-400"></i>
                                    <span id="textFotoBukti">Tambah Foto Bukti Fisik</span>
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_inventory" value="1" class="w-5 h-5 rounded border-gray-600 bg-gray-800 text-blue-500 focus:ring-blue-500 focus:ring-offset-gray-900" checked>
                                <span class="text-sm text-gray-300">
                                    <strong>Hitung Stok & Harga</strong> - Bahan ini akan dihitung stok dan harganya
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 ml-8">
                                Uncheck jika bahan ini adalah aksesoris/non-stok (seperti jaring, DLL)
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:shadow-blue-500/30 transition-all duration-200">
                            Simpan
                        </button>
                        <button type="button" id="cancelManualBtn" class="flex-1 bg-gray-700 text-white font-semibold py-4 rounded-2xl shadow-lg hover:bg-gray-600 transition-all duration-200">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Preview Area (for camera scan) -->
        <div id="previewArea" class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 shadow-xl hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Preview Foto</h3>
                <div class="flex gap-2">
                    <button id="retakeBtn" class="text-blue-400 hover:text-blue-300 font-medium text-sm">
                        Ambil Ulang
                    </button>
                    <button id="cancelBtn" class="text-gray-400 hover:text-gray-300 font-medium text-sm">
                        Batal
                    </button>
                </div>
            </div>
            <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 mb-4">
                <img id="previewImage" class="w-full h-auto rounded-lg shadow-md" alt="Preview Nota">
            </div>
            <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3 mb-4">
                <p class="text-sm text-blue-300">
                    
                </p>
            </div>
            <button id="confirmBtn" class="w-full bg-gradient-to-r from-green-500 to-blue-500 text-white py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-200 font-semibold">
               
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
            <div class="bg-gray-800 rounded-xl p-8 text-center shadow-2xl max-w-sm mx-4 border border-gray-700">
                <div class="w-16 h-16 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-white font-bold text-lg" id="loadingText">Memproses OCR...</p>
                <p class="text-gray-400 text-sm mt-2">Mohon tunggu, sedang menganalisis nota</p>
            </div>
        </div>
    </div>
</div>

<script>
// ==========================================
// TAB NAVIGATION
// ==========================================
const tabScan = document.getElementById('tabScan');
const tabManual = document.getElementById('tabManual');
const tabPelengkap = document.getElementById('tabPelengkap');
const contentScan = document.getElementById('contentScan');
const contentManual = document.getElementById('contentManual');
const contentPelengkap = document.getElementById('contentPelengkap');

function switchTab(activeTab, activeContent, otherTabs, otherContents) {
    activeContent.classList.remove('hidden');
    activeTab.classList.add('bg-emerald-500/20', 'border-emerald-500', 'text-emerald-400');
    activeTab.classList.remove('bg-emerald-500/20', 'border-emerald-500/50', 'text-emerald-400');
    
    otherTabs.forEach(tab => {
        tab.classList.remove('bg-emerald-500/20', 'border-emerald-500', 'text-emerald-400');
        tab.classList.add('bg-emerald-500/20', 'border-emerald-500/50', 'text-emerald-400');
    });
    
    otherContents.forEach(content => {
        content.classList.add('hidden');
    });
}

tabScan.addEventListener('click', () => {
    switchTab(tabScan, contentScan, [tabManual, tabPelengkap], [contentManual, contentPelengkap]);
});

tabManual.addEventListener('click', () => {
    switchTab(tabManual, contentManual, [tabScan, tabPelengkap], [contentScan, contentPelengkap]);
});

tabPelengkap.addEventListener('click', () => {
    switchTab(tabPelengkap, contentPelengkap, [tabScan, tabManual], [contentScan, contentManual]);
});

// ==========================================
// CAMERA SCAN
// ==========================================
const scanBtn = document.getElementById('scanBtn');
const cameraInput = document.getElementById('cameraInput');
const previewArea = document.getElementById('previewArea');
const previewImage = document.getElementById('previewImage');
const retakeBtn = document.getElementById('retakeBtn');
const cancelBtn = document.getElementById('cancelBtn');
const confirmBtn = document.getElementById('confirmBtn');
const loadingOverlay = document.getElementById('loadingOverlay');
const loadingText = document.getElementById('loadingText');
const errorMessage = document.getElementById('errorMessage');
const errorText = document.getElementById('errorText');
const successMessage = document.getElementById('successMessage');
const successText = document.getElementById('successText');
const flashOverlay = document.createElement('div');
flashOverlay.id = 'flashOverlay';
flashOverlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:white;opacity:0;pointer-events:none;z-index:9999;transition:opacity 0.1s;';
document.body.appendChild(flashOverlay);

let currentImageBase64 = null;

// Scan button click - open camera
scanBtn.addEventListener('click', () => {
    cameraInput.click();
});

// Camera input change - after taking photo
cameraInput.addEventListener('change', async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file size (max 10MB)
    if (file.size > 10 * 1024 * 1024) {
        showError('Ukuran file terlalu besar. Maksimal 10MB.');
        return;
    }

    // Convert to base64
    const reader = new FileReader();
    reader.onload = async (event) => {
        const base64Image = event.target.result;
        
        // Process image with canvas
        processFile(file);
    };
    reader.readAsDataURL(file);
});

// Process file with canvas enhancement
function processFile(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        const img = new Image();
        img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            // Resize for optimal processing
            const maxWidth = 1200;
            const maxHeight = 800;
            let width = img.width;
            let height = img.height;
            
            if (width > height) {
                if (width > maxWidth) {
                    height = Math.round((height * maxWidth) / width);
                    width = maxWidth;
                }
            } else {
                if (height > maxHeight) {
                    width = Math.round((width * maxHeight) / height);
                    height = maxHeight;
                }
            }
            
            canvas.width = width;
            canvas.height = height;
            
            ctx.drawImage(img, 0, 0, width, height);
            
            // Apply image enhancement with moderate contrast for OCR
            const imageData = ctx.getImageData(0, 0, width, height);
            const data = imageData.data;
            
            for (let i = 0; i < data.length; i += 4) {
                const r = data[i];
                const g = data[i + 1];
                const b = data[i + 2];
                
                // Convert to grayscale (luminance formula)
                const gray = 0.299 * r + 0.587 * g + 0.114 * b;
                
                // Moderate contrast enhancement (factor 1.5)
                let contrast = (gray - 128) * 1.5 + 128;
                contrast = contrast < 0 ? 0 : contrast > 255 ? 255 : contrast;
                
                // Slight brightness adjustment
                let final = contrast + 10;
                final = final < 0 ? 0 : final > 255 ? 255 : final;
                
                // Set grayscale value to all channels
                data[i] = final;
                data[i + 1] = final;
                data[i + 2] = final;
            }
            
            ctx.putImageData(imageData, 0, 0);
            
            currentImageBase64 = canvas.toDataURL('image/jpeg', 0.9);
            
            // Show preview
            previewImage.src = currentImageBase64;
            contentScan.classList.add('hidden');
            previewArea.classList.remove('hidden');
            
            // Flash effect
            flashOverlay.style.opacity = '1';
            setTimeout(() => flashOverlay.style.opacity = '0', 100);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Retake button
retakeBtn.addEventListener('click', () => {
    previewArea.classList.add('hidden');
    contentScan.classList.remove('hidden');
    currentImageBase64 = null;
    cameraInput.value = '';
});

// Cancel button
cancelBtn.addEventListener('click', () => {
    previewArea.classList.add('hidden');
    contentScan.classList.remove('hidden');
    currentImageBase64 = null;
    cameraInput.value = '';
});

// Confirm OCR button
confirmBtn.addEventListener('click', () => {
    if (currentImageBase64) {
        processOCR(currentImageBase64);
    }
});

// Process OCR
async function processOCR(base64) {
    loadingOverlay.classList.remove('hidden');
   
    try {
        const response = await fetch('/operator/scan/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                image_base64: base64
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadingText.textContent = 'OCR Berhasil!';
            setTimeout(() => {
                window.location.href = result.redirect_url || '/operator/scan/result';
            }, 500);
        } else {
            loadingOverlay.classList.add('hidden');
            showError(result.message || 'Gagal memproses OCR. Pastikan foto jelas dan coba lagi.');
        }
    } catch (error) {
        loadingOverlay.classList.add('hidden');
        
    }
}

// ==========================================
// MANUAL INPUT - Submit form normally (not AJAX)
// ==========================================
const manualForm = document.getElementById('manualForm');
const cancelManualBtn = document.getElementById('cancelManualBtn');

cancelManualBtn.addEventListener('click', () => {
    manualForm.reset();
    tabScan.click(); // Switch back to scan tab
});

// ==========================================
// BAHAN PELENGKAP - Submit form normally
// ==========================================
const pelengkapForm = document.getElementById('pelengkapForm');
const cancelPelengkapBtn = document.getElementById('cancelPelengkapBtn');

cancelPelengkapBtn.addEventListener('click', () => {
    pelengkapForm.reset();
    tabScan.click(); // Switch back to scan tab
});

// ==========================================
// FOTO BUKTI HANDLER
// ==========================================
const fotoBuktiInput = document.getElementById('fotoBuktiInput');
const btnFotoBukti = document.getElementById('btnFotoBukti');
const fotoBuktiPreview = document.getElementById('fotoBuktiPreview');
const fotoBuktiThumbnail = document.getElementById('fotoBuktiThumbnail');
const textFotoBukti = document.getElementById('textFotoBukti');

btnFotoBukti.addEventListener('click', () => {
    fotoBuktiInput.click();
});

fotoBuktiInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Preview foto
    const reader = new FileReader();
    reader.onload = (event) => {
        fotoBuktiThumbnail.src = event.target.result;
        fotoBuktiPreview.classList.remove('hidden');
        textFotoBukti.textContent = 'Ganti Foto Bukti';
        btnFotoBukti.classList.add('bg-blue-500/20', 'border-blue-500/50');
    };
    reader.readAsDataURL(file);
});

// Form will submit normally to operator.bahan.store
// No need for AJAX - just let the form submit naturally

// Show error message
function showError(msg) {
    errorText.textContent = msg;
    errorMessage.classList.remove('hidden');
    setTimeout(() => errorMessage.classList.add('hidden'), 5000);
}

// Show success message
function showSuccess(msg) {
    successText.textContent = msg;
    successMessage.classList.remove('hidden');
    setTimeout(() => successMessage.classList.add('hidden'), 3000);
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (!previewArea.classList.contains('hidden')) {
            cancelBtn.click();
        } else {
            window.location.href = "{{ route('operator.dashboard') }}";
        }
    }
});

// ==========================================
// AUTOCOMPLETE SEARCH (Same as tambah-bahan)
// ==========================================
console.log('Autocomplete script loaded');
function selectMaterial(itemElement) {
    document.getElementById('nama_material').value = itemElement.dataset.nama;
    document.querySelector('input[name="kode_material"]').value = itemElement.dataset.kode;
    
    // ✅ LOGIC TOMBOL FOTO: BAHAN SUDAH ADA = DISABLE
    const btnFotoBukti = document.getElementById('btnFotoBukti');
    const textFotoBukti = document.getElementById('textFotoBukti');
    
    btnFotoBukti.disabled = true;
    btnFotoBukti.classList.add('opacity-50', 'cursor-not-allowed');
    btnFotoBukti.classList.remove('hover:bg-gray-700/50');
    textFotoBukti.textContent = 'Master Foto Sudah Ada';
    fotoBuktiInput.disabled = true;
    
    // AUTO FILL KATEGORI & SATUAN
    const kategoriSelect = document.querySelector('select[name="kategori"]');
    const satuanSelect = document.querySelector('select[name="satuan"]');
    
    if(itemElement.dataset.kategori && itemElement.dataset.kategori !== '') {
        kategoriSelect.value = itemElement.dataset.kategori;
        kategoriSelect.disabled = true;
        kategoriSelect.classList.add('bg-gray-900', 'text-gray-400');
        
        // Highlight effect
        kategoriSelect.style.boxShadow = '0 0 0 2px rgba(16, 185, 129, 0.3)';
        setTimeout(() => kategoriSelect.style.boxShadow = '', 800);
    }
    
    if(itemElement.dataset.satuan && itemElement.dataset.satuan !== '') {
        satuanSelect.value = itemElement.dataset.satuan;
        satuanSelect.disabled = true;
        satuanSelect.classList.add('bg-gray-900', 'text-gray-400');
        
        // Highlight effect
        satuanSelect.style.boxShadow = '0 0 0 2px rgba(16, 185, 129, 0.3)';
        setTimeout(() => satuanSelect.style.boxShadow = '', 800);
    }
    
    document.querySelector('input[name="supplier"]').value = itemElement.dataset.supplier;
    document.querySelector('input[name="ref_no"]').value = itemElement.dataset.refNo;
    
    // TAMPILKAN HARGA TERAKHIR SEBAGAI INFORMASI SAJA (TIDAK DIISI OTOMATIS)
    const hargaInput = document.getElementById('hargaSatuan');
    const lastPrice = parseFloat(itemElement.dataset.harga);
    if (lastPrice > 0) {
        const lastPriceInfo = document.getElementById('lastPriceInfo');
        lastPriceInfo.innerHTML = `ℹ️ Harga terakhir: <strong>Rp ${new Intl.NumberFormat('id-ID').format(lastPrice)}</strong>`;
        lastPriceInfo.classList.remove('hidden', 'text-red-400', 'text-green-400');
        lastPriceInfo.classList.add('text-amber-400');
    } else {
        const lastPriceInfo = document.getElementById('lastPriceInfo');
        lastPriceInfo.classList.add('hidden');
    }

    document.getElementById('suggestions').classList.add('hidden');
    document.getElementById('suggestions').innerHTML = '';
}

// SET DEFAULT VALUES JIKA BAHAN BARU
document.getElementById('nama_material').addEventListener('input', function() {
    // Reset disabled fields
    const kategoriSelect = document.querySelector('select[name="kategori"]');
    const satuanSelect = document.querySelector('select[name="satuan"]');
    const btnFotoBukti = document.getElementById('btnFotoBukti');
    const textFotoBukti = document.getElementById('textFotoBukti');
    
    kategoriSelect.disabled = false;
    satuanSelect.disabled = false;
    kategoriSelect.classList.remove('bg-gray-900', 'text-gray-400');
    satuanSelect.classList.remove('bg-gray-900', 'text-gray-400');
    
    // ✅ LOGIC TOMBOL FOTO: BAHAN BARU = ENABLE
    btnFotoBukti.disabled = false;
    btnFotoBukti.classList.remove('opacity-50', 'cursor-not-allowed');
    btnFotoBukti.classList.add('hover:bg-gray-700/50');
    textFotoBukti.textContent = 'Tambah Master Foto Baru';
    fotoBuktiInput.disabled = false;
    
    // Set default values untuk bahan baru
    if(kategoriSelect.value === '') {
        kategoriSelect.value = 'Bahan Utama';
    }
    if(satuanSelect.value === '') {
        satuanSelect.value = 'Pcs';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    
    // Load satuan options
    // loadSatuans();

    var input = document.getElementById('nama_material');
    var suggestions = document.getElementById('suggestions');
    var timeout;

    if (!input || !suggestions) {
        console.log('Input or suggestions element not found');
        return;
    }

    input.addEventListener('input', function() {
        const lastPriceInfo = document.getElementById('lastPriceInfo');
        if (lastPriceInfo) lastPriceInfo.classList.add('hidden');

        var query = this.value.trim();
        if (query.length < 1) {
            suggestions.classList.add('hidden');
            suggestions.innerHTML = '';
            return;
        }

        clearTimeout(timeout);
        timeout = setTimeout(function() {
            console.log('Searching for:', query);
            fetch('{{ route("api.bahan.search") }}?q=' + encodeURIComponent(query), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => {
                console.log('Data received:', data);
                suggestions.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(function(item) {
                        console.log('DATA DARI SERVER:', item);
                        
                        var itemDiv = document.createElement('div');
                        itemDiv.className = 'px-3 py-2 hover:bg-gray-700 cursor-pointer text-sm text-white flex items-center gap-3';
                        
                        // ✅ STABIL TIDAK BERKEDIP - 100% KONSISTEN
                        const pathFoto = item.foto || item.foto_url;
                        let htmlFoto = '';

                        if (pathFoto && !pathFoto.includes('placeholder')) {
                            // Jika ada data foto asli
                            const finalSrc = pathFoto.startsWith('http') ? pathFoto : ('/storage/' + pathFoto.replace('storage/', ''));
                            htmlFoto = `<img src="${finalSrc}" class="w-full h-full object-cover">`;
                        } else {
                            // Jika TIDAK ADA foto - Default Logo Abu-abu LANGSUNG
                            htmlFoto = `<div class="w-full h-full bg-gray-700 flex items-center justify-center"><i class="fa-solid fa-box text-gray-500"></i></div>`;
                        }
                        
                        const namaTampil = item.nama_bahan || item.nama_material || 'Tanpa Nama';
                        const kodeTampil = item.kode_bahan || item.kode_material || '-';
                        
                        itemDiv.innerHTML = `
                            <div class="w-14 h-14 flex-shrink-0 rounded-xl overflow-hidden border border-gray-600 shadow-inner flex items-center justify-center bg-gray-700">
                                ${htmlFoto}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-white text-base truncate">${namaTampil}</div>
                                <div class="text-xs text-yellow-400 font-mono">${kodeTampil}</div>
                                <div class="text-xs text-gray-400">Stok: ${item.stok} ${item.satuan}</div>
                            </div>
                        `;
                        itemDiv.dataset.nama = item.nama_material;
                        itemDiv.dataset.kode = item.kode_material;
                        itemDiv.dataset.kategori = item.kategori || '';
                        itemDiv.dataset.satuan = item.satuan || '';
                        itemDiv.dataset.supplier = item.supplier || '';
                        itemDiv.dataset.refNo = item.ref_no || '';
                        itemDiv.dataset.harga = item.harga_satuan || '0';
                        
                        itemDiv.addEventListener('click', function() {
                            selectMaterial(this);
                        });
                        suggestions.appendChild(itemDiv);
                    });
                    suggestions.classList.remove('hidden');
                    console.log('Suggestions shown');
                } else {
                    suggestions.classList.add('hidden');
                }
            })
            .catch(error => {
                console.log('Fetch error:', error);
            });
        }, 250);
    });

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.classList.add('hidden');
        }
    });
});

async function loadSatuans() {
        const satuanSelect = document.getElementById('satuanSelect');
        const satuanPelengkapSelect = document.getElementById('satuanPelengkapSelect');
        try {
            const response = await fetch('/api/satuans');
            const result = await response.json();

            if (result.success && result.data) {
                satuanSelect.innerHTML = '<option value="" disabled selected class="bg-gray-800">Pilih satuan...</option>';
                satuanPelengkapSelect.innerHTML = '<option value="" disabled selected class="bg-gray-800">Pilih satuan...</option>';
                
                for (const group in result.data) {
                    const optgroup = document.createElement('optgroup');
                    optgroup.label = group;
                    
                    const optgroupPelengkap = document.createElement('optgroup');
                    optgroupPelengkap.label = group;

                    result.data[group].forEach(satuan => {
                        const option = document.createElement('option');
                        option.value = satuan.nama_satuan.toLowerCase();
                        option.textContent = `${satuan.nama_satuan}`;
                        option.className = 'bg-gray-800';
                        
                        const optionPelengkap = option.cloneNode(true);

                        optgroup.appendChild(option);
                        optgroupPelengkap.appendChild(optionPelengkap);
                    });
                    satuanSelect.appendChild(optgroup);
                    satuanPelengkapSelect.appendChild(optgroupPelengkap);
                }
            } else {
                satuanSelect.innerHTML = '<option value="" disabled class="bg-gray-800">Gagal memuat</option>';
                satuanPelengkapSelect.innerHTML = '<option value="" disabled class="bg-gray-800">Gagal memuat</option>';
            }
        } catch (error) {
            console.error('Error loading satuans:', error);
            satuanSelect.innerHTML = '<option value="" disabled class="bg-gray-800">Error</option>';
            satuanPelengkapSelect.innerHTML = '<option value="" disabled class="bg-gray-800">Error</option>';
        }
    }

    // Function to load Kategori from API
    function loadKategoris() {
        const kategoriSelect = document.getElementById('kategoriSelect');
        const kategoriPelengkapSelect = document.getElementById('kategoriPelengkapSelect');

        fetch('/api/kategoris')
            .then(response => response.json())
            .then(data => {
                kategoriSelect.innerHTML = '<option value="" disabled selected>Pilih kategori...</option>';
                kategoriPelengkapSelect.innerHTML = '<option value="" disabled selected>Pilih kategori...</option>';
                data.forEach(kategori => {
                    const option = `<option class="bg-gray-800" value="${kategori.nama_kategori}">${kategori.nama_kategori}</option>`;
                    kategoriSelect.innerHTML += option;
                    kategoriPelengkapSelect.innerHTML += option;
                });
            })
            .catch(error => {
                console.error('Error loading kategoris:', error);
                kategoriSelect.innerHTML = '<option value="" disabled selected>Gagal memuat</option>';
                kategoriPelengkapSelect.innerHTML = '<option value="" disabled selected>Gagal memuat</option>';
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // ... (existing code)

        // Load Satuan and Kategori data
        // Data is now loaded via Blade from the controller
        // loadSatuans();
        // loadKategoris();
    });
</script>

<style>
#flashOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    opacity: 0;
    pointer-events: none;
    z-index: 9999;
    transition: opacity 0.1s;
}
</style>
@endsection