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
                <a href="/operator/bahan/tambah" class="flex-1 text-center py-2 px-3 text-sm font-semibold rounded-xl transition-all duration-300 bg-emerald-500/20 border-2 border-emerald-500/50 text-emerald-400 hover:bg-emerald-500/30 hover:border-emerald-500">
                    <div class="flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Input Manual</span>
                    </div>
                </a>
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
        
        <!-- Manual Form (hidden by default) -->
        <div id="manualForm" class="hidden">
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-700/50 shadow-xl">
                <h3 class="text-lg font-semibold text-white mb-4">Input Manual Bahan</h3>
                <form id="bahanForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Nama Material <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_material" id="namaMaterial" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white" placeholder="Contoh: Kulit Sapi Krom" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Jumlah <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah" id="jumlahInput" step="0.01" min="0.01" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white" placeholder="0" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Satuan <span class="text-red-500">*</span></label>
                            <select name="satuan" id="satuanSelect" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white" required>
                                <option value="pcs">PCS</option>
                                <option value="kg">KG</option>
                                <option value="meter">METER</option>
                                <option value="liter">LITER</option>
                                <option value="lembar">LEMBAR</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Harga Satuan (Rp)</label>
                            <input type="number" name="harga_satuan" step="0.01" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Kategori</label>
                            <select name="kategori" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white">
                                <option value="Bahan Utama">Bahan Utama</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Kimia">Kimia</option>
                                <option value="Packaging">Packaging</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Supplier (Opsional)</label>
                        <input type="text" name="supplier" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white" placeholder="Nama supplier">
                    </div>
                    
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">No. Nota/Ref (Opsional)</label>
                        <input type="text" name="ref_no" class="w-full bg-gray-800 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white" placeholder="Nomor nota">
                    </div>
                    
                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 bg-emerald-600 text-white py-3 rounded-xl font-semibold hover:bg-emerald-700 transition">
                            Simpan
                        </button>
                        <button type="button" id="cancelBtn" class="flex-1 bg-gray-700 text-white py-3 rounded-xl font-semibold hover:bg-gray-600 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Error Message -->
        <div id="errorMessage" class="bg-red-500/10 border border-red-500/30 text-red-400 px-4 py-3 rounded-xl mb-6 hidden">
            <p id="errorText"></p>
        </div>
        
        <!-- Success Message -->
        <div id="successMessage" class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-xl mb-6 hidden">
            <p id="successText"></p>
        </div>
    </div>
</div>

<script>
// Camera setup
const scanBtn = document.getElementById('scanBtn');
const cameraInput = document.getElementById('cameraInput');
const manualForm = document.getElementById('manualForm');
const contentScan = document.getElementById('contentScan');
const cancelBtn = document.getElementById('cancelBtn');
const bahanForm = document.getElementById('bahanForm');

// Scan button click - open camera
scanBtn.addEventListener('click', () => {
    cameraInput.click();
});

// Camera input change - after taking photo
cameraInput.addEventListener('change', async (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    // Convert to base64
    const reader = new FileReader();
    reader.onload = async (event) => {
        const base64Image = event.target.result;
        
        // Show loading
        scanBtn.disabled = true;
        scanBtn.innerHTML = '<span class="animate-pulse">Memproses...</span>';
        
        try {
            // Send to OCR endpoint
            const response = await fetch('/operator/scan/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    image_base64: base64Image
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Redirect to success page
                window.location.href = result.redirect_url;
            } else {
                alert(result.message || 'Gagal memproses gambar');
                scanBtn.disabled = false;
                scanBtn.innerHTML = '<span>Ambil Foto</span>';
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses gambar');
            scanBtn.disabled = false;
            scanBtn.innerHTML = '<span>Ambil Foto</span>';
        }
        
        // Reset input
        cameraInput.value = '';
    };
    reader.readAsDataURL(file);
});

// Cancel button
cancelBtn.addEventListener('click', () => {
    manualForm.classList.add('hidden');
    contentScan.classList.remove('hidden');
});

// Form submit
bahanForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const data = {
        image_base64: null,
        nama_material: document.getElementById('namaMaterial').value,
        jumlah: parseFloat(document.getElementById('jumlahInput').value),
        satuan: document.getElementById('satuanSelect').value,
        harga_satuan: document.querySelector('input[name="harga_satuan"]').value ? parseFloat(document.querySelector('input[name="harga_satuan"]').value) : 0,
        supplier: document.querySelector('input[name="supplier"]').value || null,
        ref_no: document.querySelector('input[name="ref_no"]').value || null,
        kategori: document.querySelector('select[name="kategori"]').value
    };
    
    try {
        const response = await fetch('/operator/scan/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.href = result.redirect_url;
        } else {
            alert(result.message || 'Gagal menyimpan data');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
    }
});
</script>
@endsection