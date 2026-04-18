@extends('layouts.operator_app')

@section('title', 'Input Manual Bahan - Berliano Leather')

@section('content')
<!-- Custom Navbar -->
<div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg">
    <div class="max-w-md mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold">Berliano Leather</h1>
                    <p class="text-sm opacity-90">Input Manual Bahan</p>
                </div>
            </div>
            <a href="{{ route('operator.dashboard') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all duration-200">
                <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>
</div>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6 px-4">
    <div class="max-w-md mx-auto">
        <!-- Input Manual Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="text-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Input Manual Bahan</h3>
                <p class="text-gray-600 text-sm">Masukkan data bahan secara manual</p>
            </div>
            
            <form id="manualForm" class="space-y-4">
                <!-- Nama Bahan (Auto-complete) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Bahan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="namaBahan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="Ketik minimal 3 karakter untuk mencari bahan">
                    <div id="bahanSuggestions" class="absolute bg-white border border-gray-300 rounded-lg shadow-lg mt-1 w-full max-h-40 overflow-y-auto z-50 hidden">
                        <!-- Suggestions will be populated here -->
                    </div>
                </div>
                
                <!-- Kode Bahan (Read-only) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kode Bahan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kodeBahan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-gray-50 text-gray-600" placeholder="Kode bahan otomatis" readonly>
                </div>
                
                <!-- Tambah Bahan Baru Button -->
                <div id="tambahBahanBaru" class="hidden">
                    <button type="button" id="btnTambahBahanBaru" class="w-full bg-gradient-to-r from-green-500 to-blue-500 text-white py-2 px-4 rounded-lg hover:shadow-lg transition-all duration-200 font-medium">
                        + Tambah Bahan Baru
                    </button>
                </div>
                
                <!-- Jumlah -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="jumlahBahan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="Masukkan jumlah" step="0.01">
                </div>
                
                <!-- Satuan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Satuan <span class="text-red-500">*</span>
                    </label>
                    <select id="satuanBahan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white">
                        <option value="" disabled selected>Memuat satuan...</option>
                    </select>
                </div>
                
                <!-- No Nota (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        No Nota (Opsional)
                    </label>
                    <input type="text" id="noNota" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="Masukkan nomor nota">
                </div>
                
                <!-- Supplier (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Supplier (Opsional)
                    </label>
                    <input type="text" id="supplier" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" placeholder="Masukkan nama supplier">
                </div>
                
                <!-- Upload Foto (Opsional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Foto (Opsional)
                    </label>
                    <input type="file" id="manualFileInput" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>
                
                <!-- Submit Buttons -->
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-blue-500 text-white py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-200 font-semibold">
                        Simpan Bahan
                    </button>
                    <button type="button" id="btnBatal" class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-xl hover:bg-gray-400 transition-all duration-200 font-semibold">
                        Batal
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Error Message -->
        <div id="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 hidden">
            <p id="errorText"></p>
        </div>
        
        <!-- Success Message -->
        <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 hidden">
            <p id="successText"></p>
        </div>
        
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-8 text-center shadow-xl">
                <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-gray-800 font-bold" id="loadingText">Memproses...</p>
            </div>
        </div>
    </div>
</div>

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

.suggestion-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #e5e7eb;
    transition: background-color 0.2s;
}

.suggestion-item:hover {
    background-color: #f3f4f6;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item.active {
    background-color: #e0f2fe;
    color: #0369a1;
}
</style>

<script>
// ==========================================
// SMART MANUAL INPUT BAHAN
// ==========================================

const namaBahanInput = document.getElementById('namaBahan');
const kodeBahanInput = document.getElementById('kodeBahan');
const bahanSuggestions = document.getElementById('bahanSuggestions');
const tambahBahanBaru = document.getElementById('tambahBahanBaru');
const btnTambahBahanBaru = document.getElementById('btnTambahBahanBaru');
const manualForm = document.getElementById('manualForm');
const manualFileInput = document.getElementById('manualFileInput');
const btnBatal = document.getElementById('btnBatal');
const loadingOverlay = document.getElementById('loadingOverlay');
const loadingText = document.getElementById('loadingText');
const errorMessage = document.getElementById('errorMessage');
const errorText = document.getElementById('errorText');
const successMessage = document.getElementById('successMessage');
const successText = document.getElementById('successText');
const satuanBahanSelect = document.getElementById('satuanBahan');
const flashOverlay = document.createElement('div');
flashOverlay.id = 'flashOverlay';
document.body.appendChild(flashOverlay);

let bahanList = [];
let selectedBahan = null;

// Load Satuan List
async function loadSatuans() {
    try {
        const response = await fetch('/api/satuans');
        const result = await response.json();

        if (result.success && result.data) {
            satuanBahanSelect.innerHTML = '<option value="" disabled selected>Pilih satuan...</option>';
            for (const group in result.data) {
                const optgroup = document.createElement('optgroup');
                optgroup.label = group;
                result.data[group].forEach(satuan => {
                    const option = document.createElement('option');
                    option.value = satuan.nama_satuan.toLowerCase();
                    option.textContent = `${satuan.nama_satuan}`;
                    optgroup.appendChild(option);
                });
                satuanBahanSelect.appendChild(optgroup);
            }
        } else {
            satuanBahanSelect.innerHTML = '<option value="" disabled>Gagal memuat satuan</option>';
        }
    } catch (error) {
        console.error('Error loading satuans:', error);
        satuanBahanSelect.innerHTML = '<option value="" disabled>Error memuat satuan</option>';
    }
}

// Load Bahan List
async function loadBahanList() {
    try {
        const response = await fetch('/api/bahan/search');
        const result = await response.json();
        
        if (result.success && result.data) {
            bahanList = result.data;
        }
    } catch (error) {
        console.error('Error loading bahan list:', error);
    }
}

// Search Bahan
function searchBahan(query) {
    if (query.length < 3) {
        bahanSuggestions.classList.add('hidden');
        tambahBahanBaru.classList.add('hidden');
        return;
    }
    
    const filtered = bahanList.filter(bahan => 
        bahan.nama_bahan.toLowerCase().includes(query.toLowerCase())
    );
    
    if (filtered.length > 0) {
        renderSuggestions(filtered);
        bahanSuggestions.classList.remove('hidden');
        tambahBahanBaru.classList.add('hidden');
    } else {
        bahanSuggestions.classList.add('hidden');
        tambahBahanBaru.classList.remove('hidden');
    }
}

// Render Suggestions
function renderSuggestions(suggestions) {
    bahanSuggestions.innerHTML = '';
    
    suggestions.forEach((bahan, index) => {
        const div = document.createElement('div');
        div.className = 'suggestion-item';
        div.innerHTML = `
            <div class="flex justify-between items-center">
                <div>
                    <div class="font-medium">${bahan.nama_bahan}</div>
                    <div class="text-sm text-gray-500">Kode: ${bahan.kode_bahan}</div>
                </div>
                <div class="text-xs text-gray-400">Pilih</div>
            </div>
        `;
        
        div.addEventListener('click', () => {
            selectBahan(bahan);
        });
        
        bahanSuggestions.appendChild(div);
    });
}

// Select Bahan
function selectBahan(bahan) {
    selectedBahan = bahan;
    namaBahanInput.value = bahan.nama_bahan;
    kodeBahanInput.value = bahan.kode_bahan;
    bahanSuggestions.classList.add('hidden');
    tambahBahanBaru.classList.add('hidden');
}

// Generate Kode Bahan Otomatis
async function generateKodeBahan(namaBahan) {
    try {
        const response = await fetch('/api/generate-kode-bahan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ nama_bahan: namaBahan })
        });
        
        const result = await response.json();
        
        if (result.success && result.kode_bahan) {
            return result.kode_bahan;
        } else {
            // Fallback manual
            const timestamp = Date.now();
            const random = Math.floor(Math.random() * 1000);
            return `BB-${timestamp}-${random}`;
        }
    } catch (error) {
        // Fallback manual
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000);
        return `BB-${timestamp}-${random}`;
    }
}

// Event Listeners
namaBahanInput.addEventListener('input', (e) => {
    searchBahan(e.target.value);
});

namaBahanInput.addEventListener('blur', () => {
    // Hide suggestions after a short delay to allow click
    setTimeout(() => {
        bahanSuggestions.classList.add('hidden');
    }, 200);
});

btnTambahBahanBaru.addEventListener('click', async () => {
    const namaBahan = namaBahanInput.value.trim();
    
    if (!namaBahan) {
        showError('Masukkan nama bahan terlebih dahulu');
        return;
    }
    
    const kodeBahan = await generateKodeBahan(namaBahan);
    selectedBahan = { nama_bahan: namaBahan, kode_bahan: kodeBahan };
    namaBahanInput.value = namaBahan;
    kodeBahanInput.value = kodeBahan;
    tambahBahanBaru.classList.add('hidden');
});

manualForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const namaBahan = document.getElementById('namaBahan').value.trim();
    const jumlahBahan = document.getElementById('jumlahBahan').value.trim();
    const satuanBahan = document.getElementById('satuanBahan').value;
    const noNota = document.getElementById('noNota').value.trim();
    const supplier = document.getElementById('supplier').value.trim();
    const file = manualFileInput.files[0];
    
    if (!namaBahan || !jumlahBahan || !satuanBahan) {
        showError('Mohon lengkapi semua field yang wajib diisi');
        return;
    }
    
    // Process manual data
    await processManualData(namaBahan, jumlahBahan, satuanBahan, noNota, supplier, file);
});

btnBatal.addEventListener('click', () => {
    window.location.href = "{{ route('operator.dashboard') }}";
});

// Process Manual Data
async function processManualData(namaBahan, jumlahBahan, satuanBahan, noNota, supplier, file) {
    loadingOverlay.classList.remove('hidden');
    loadingText.textContent = 'Memproses data manual...';
    
    try {
        let fileBase64 = null;
        
        if (file) {
            fileBase64 = await convertFileToBase64(file);
        }
        
        const payload = {
            nama_bahan: namaBahan,
            jumlah: parseFloat(jumlahBahan),
            satuan: satuanBahan,
            no_nota: noNota || null,
            supplier: supplier || null,
            foto: fileBase64
        };
        
        // Jika bahan sudah ada, update stok
        if (selectedBahan) {
            payload.kode_bahan = selectedBahan.kode_bahan;
            payload.action = 'update_stok';
        } else {
            // Jika bahan baru, buat master data baru
            payload.action = 'create_new';
        }
        
        const response = await fetch('/api/bahan/manual', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify(payload)
        });
        
        const result = await response.json();
        
        if (result.success) {
            loadingText.textContent = 'Selesai!';
            showSuccess(result.message || 'Data bahan berhasil disimpan');
            setTimeout(() => {
                window.location.href = result.redirect_url || '/operator/dashboard';
            }, 2000);
        } else {
            loadingOverlay.classList.add('hidden');
            showError(result.message || 'Gagal menyimpan data manual');
        }
    } catch (error) {
        loadingOverlay.classList.add('hidden');
        showError('Error: ' + error.message);
    }
}

// Convert File to Base64
function convertFileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => resolve(e.target.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

// Show Error
function showError(msg) {
    errorText.textContent = msg;
    errorMessage.classList.remove('hidden');
    successMessage.classList.add('hidden');
    setTimeout(() => errorMessage.classList.add('hidden'), 5000);
}

// Show Success
function showSuccess(msg) {
    successText.textContent = msg;
    successMessage.classList.remove('hidden');
    errorMessage.classList.add('hidden');
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadBahanList();
    loadSatuans();
});
</script>
@endsection