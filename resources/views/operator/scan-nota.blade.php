@extends('layouts.operator_app')

@section('title', 'Scan Nota - Berliano Leather')

@section('content')
<div class="w-full max-w-md mx-auto bg-[#0B0E14] min-h-screen">

    @include('components.operator-header', ['title' => 'SCAN NOTA'])

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

@include('operator.partials._scan-kamera')

@include('operator.partials._bahan-pelengkap')

@include('operator.partials._input-manual', ['pengadaan_id' => $pengadaan_id])

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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ image_base64: base64 })
        });
        const result = await response.json();
        if (result.success) {
            loadingText.textContent = 'OCR Berhasil!';
            // Set warna default "Tidak Ada Warna" jika ada dropdown warna
            // dan alihkan ke halaman result
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
        else window.location.href = "{{ route('operator.dashboard') }}";
    }
});

// ==========================================
// AUTOCOMPLETE & RESET FUNCTIONS (GLOBAL)
// ==========================================
function selectMaterial(itemElement) {
    document.getElementById('nama_material').value = itemElement.dataset.nama;
    document.getElementById('kodeMaterial').value  = itemElement.dataset.kode;
    document.getElementById('supplierInput').value = itemElement.dataset.supplier || '';
    document.getElementById('refNoInput').value    = itemElement.dataset.refNo || '';

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

function resetManualForm(clearName = true) {
    const manualForm = document.getElementById('manualForm');
    if (!manualForm) return;
    if (clearName) {
        manualForm.reset();
    } else {
        Array.from(manualForm.elements).forEach(el => {
            if (el.name !== 'nama_material' && el.type !== 'hidden' && el.type !== 'submit' && el.type !== 'button') {
                if (el.type === 'checkbox' || el.type === 'radio') el.checked = false;
                else el.value = '';
            }
        });
        manualForm.querySelector('[name="is_inventory"]').checked = true;
    }
    document.getElementById('suggestions').innerHTML = '';
    document.getElementById('suggestions').classList.add('hidden');
    document.getElementById('lastPriceInfo').classList.add('hidden');
}

function resetPelengkapForm() {
    const pelengkapForm = document.getElementById('pelengkapForm');
    if (pelengkapForm) pelengkapForm.reset();
}

// FIX: Satu DOMContentLoaded saja
document.addEventListener('DOMContentLoaded', function() {
    const input       = document.getElementById('nama_material');
    const suggestions = document.getElementById('suggestions');
    let timeout;
    if (!input || !suggestions) return;

    input.addEventListener('input', function() {
        document.getElementById('lastPriceInfo').classList.add('hidden');
        if (document.getElementById('kodeMaterial').value) {
            resetManualForm(false);
        }
        const query = this.value.trim();
        if (query.length < 1) { suggestions.classList.add('hidden'); suggestions.innerHTML = ''; return; }
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            fetch('{{ route("api.bahan.search") }}?q=' + encodeURIComponent(query) + '&is_inventory=1', {
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
                                <div class="flex flex-wrap gap-1 mt-0.5">
                                    ${item.warna ? `<span class="text-[9px] px-1.5 py-0.5 bg-blue-600/20 text-blue-400 rounded border border-blue-600/30">${item.warna}</span>` : ''}
                                    <span class="text-xs text-gray-400">Stok: ${item.stok} ${item.satuan}</span>
                                </div>
                            </div>`;

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

    const namaPelengkap = document.getElementById('nama_pelengkap');
    if (namaPelengkap) {
        namaPelengkap.addEventListener('input', function() {
            document.getElementById('kodePelengkap').value = '';
        });
    }
});

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
@endsection