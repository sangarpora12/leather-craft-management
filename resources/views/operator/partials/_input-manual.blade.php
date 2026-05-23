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
        <form id="manualForm" action="{{ route('operator.scan.process') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="pengadaan_id" value="{{ $pengadaan_id }}">
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
                            @forelse ($kategories as $kat)
                                <option value="{{ $kat->nama_kategori }}">{{ $kat->nama_kategori }}</option>
                            @empty
                                <option disabled>Data kategori bahan kosong</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Satuan <span class="text-red-500">*</span></label>
                        <select name="satuan" id="satuanSelect" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none" required>
                            <option value="">-- Pilih Satuan --</option>
                            @forelse ($satuans as $sat)
                                <option value="{{ $sat->nama_satuan }}">{{ $sat->nama_satuan }}</option>
                            @empty
                                <option disabled>Data satuan kosong</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <!-- Warna Selection -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Warna <span class="text-orange-400">*</span></label>
                    <select name="warna" id="warna" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-orange-500 outline-none" required>
                        <option value="">-- Pilih Warna --</option>
                        @forelse ($warnas as $warna)
                            <option value="{{ $warna->nama_warna }}">{{ $warna->nama_warna }}</option>
                        @empty
                            <option disabled>Tidak ada data warna. Tambah di Pengaturan Admin.</option>
                        @endforelse
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Jumlah Masuk <span class="text-red-500">*</span></label>
                        <input type="number" name="jumlah" id="jumlahInput" placeholder="0" step="0.01" min="0.01" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Harga Beli (Rp) <span class="text-red-500">*</span></label>
                        <div id="lastPriceInfo" class="hidden text-xs text-amber-400 mb-2"></div>
                        <input type="number" name="harga_satuan" id="hargaSatuan" placeholder="Rp" step="0.01" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi Bahan</label>
                    <textarea name="detail" id="deskripsi_bahan" rows="3" placeholder="Detail spesifikasi bahan, ketebalan, grade, ukuran, kondisi, dll..." class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none resize-vertical"></textarea>
                </div>

                <!-- Preview & Upload Foto Bahan -->
                <div id="previewFotoVarianContainer" class="mt-4" style="display:none;">
                    <label class="block text-sm font-medium text-emerald-400 mb-2">📸 Foto Bahan Saat Ini</label>
                    <img id="preview-foto-lama" src="" class="w-full h-40 object-cover rounded-xl border border-white/10">
                    <p class="text-xs text-gray-500 mt-1">Foto dari database. Upload foto baru di bawah untuk mengganti.</p>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Foto Bahan <span class="text-gray-500 text-xs">(upload untuk mengganti foto di database)</span></label>
                    <div class="relative">
                        <input type="file" name="foto" id="manualFotoInput" accept="image/*" class="hidden">
                        <div id="manualFotoPreview" class="hidden mb-3">
                            <img id="manualFotoThumbnail" class="w-full h-40 object-cover rounded-xl border border-emerald-500/30">
                        </div>
                        <button type="button" id="manualBtnFoto" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white flex items-center justify-center gap-2 hover:bg-gray-700/50 transition">
                            <i class="fa-solid fa-image text-emerald-400"></i>
                            <span id="manualTextFoto">📷 Pilih Foto Bahan</span>
                        </button>
                        <p class="text-xs text-gray-500 mt-1">Pilih foto untuk varian warna ini. Foto akan langsung tersimpan di database saat submit.</p>
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

<script>
// Upload foto bahan untuk INPUT MANUAL
document.addEventListener('DOMContentLoaded', function() {
    const btnFoto = document.getElementById('manualBtnFoto');
    const inputFoto = document.getElementById('manualFotoInput');
    const previewFoto = document.getElementById('manualFotoPreview');
    const thumbnailFoto = document.getElementById('manualFotoThumbnail');
    const textFoto = document.getElementById('manualTextFoto');

    if (btnFoto && inputFoto) {
        btnFoto.addEventListener('click', function() {
            inputFoto.click();
        });

        inputFoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(event) {
                thumbnailFoto.src = event.target.result;
                previewFoto.classList.remove('hidden');
                textFoto.textContent = '✅ Foto Siap (akan tersimpan saat submit)';
                btnFoto.classList.add('bg-emerald-500/20', 'border-emerald-500/50');
            };
            reader.readAsDataURL(file);
        });
    }
});



// Auto-fill deskripsi & preview foto berdasarkan nama_material + warna
document.addEventListener('DOMContentLoaded', function() {
    const namaInput = document.getElementById('nama_material');
    const warnaSelect = document.getElementById('warna');
    const deskripsiArea = document.getElementById('deskripsi_bahan');
    const previewFotoLama = document.getElementById('preview-foto-lama');
    const previewContainer = document.getElementById('previewFotoVarianContainer');

    if (!namaInput || !warnaSelect) return;

    let debounceTimer;

    function cekBahan() {
        const nama = namaInput.value.trim();
        const warna = warnaSelect.value;

        if (nama.length < 1 || !warna) {
            if (deskripsiArea) deskripsiArea.value = '';
            if (previewFotoLama) { previewFotoLama.style.display = 'none'; previewFotoLama.src = ''; }
            if (previewContainer) previewContainer.style.display = 'none';
            return;
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            fetch('/api/cek-bahan?nama_material=' + encodeURIComponent(nama) + '&warna=' + encodeURIComponent(warna), {
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            })
            .then(r => r.json())
            .then(data => {
                if (deskripsiArea) {
                    if (data && data.detail) {
                        deskripsiArea.value = data.detail;
                    } else if (data && data.deskripsi_bahan) {
                        deskripsiArea.value = data.deskripsi_bahan;
                    } else {
                        deskripsiArea.value = '';
                    }
                }

                if (previewFotoLama && previewContainer) {
                    if (data && data.foto_url) {
                        previewFotoLama.src = data.foto_url;
                        previewFotoLama.style.display = 'block';
                        previewFotoLama.style.width = '100%';
                        previewFotoLama.style.maxHeight = '180px';
                        previewFotoLama.style.objectFit = 'cover';
                        previewFotoLama.style.borderRadius = '8px';
                        previewFotoLama.style.border = '1px solid rgba(255,255,255,0.1)';
                        previewContainer.style.display = 'block';
                    } else {
                        previewFotoLama.style.display = 'none';
                        previewFotoLama.src = '';
                        previewContainer.style.display = 'none';
                    }
                }
            })
            .catch(function() {
                if (deskripsiArea) deskripsiArea.value = '';
                if (previewFotoLama) { previewFotoLama.style.display = 'none'; previewFotoLama.src = ''; }
                if (previewContainer) previewContainer.style.display = 'none';
            });
        }, 300);
    }

    namaInput.addEventListener('input', cekBahan);
    warnaSelect.addEventListener('change', cekBahan);
});

// Intercept Manual Form Submission with JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const manualForm = document.getElementById('manualForm');
    if (manualForm) {
        manualForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(manualForm);

            const loadingOverlay = document.getElementById('loadingOverlay');
            const loadingText = document.getElementById('loadingText');
            if (loadingOverlay) {
                loadingText.textContent = 'Menyimpan Data...';
                loadingOverlay.classList.remove('hidden');
            }

            try {
                const response = await fetch(manualForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    if (loadingOverlay) loadingOverlay.classList.add('hidden');

                    if (typeof showSuccess === 'function') {
                        showSuccess(result.message || 'Data berhasil disimpan!');
                    } else {
                        alert(result.message || 'Data berhasil disimpan!');
                    }

                    const pengadaanIdInput = manualForm.querySelector('input[name="pengadaan_id"]');
                    const pengadaanIdValue = pengadaanIdInput ? pengadaanIdInput.value : null;

                    manualForm.reset();

                    if (pengadaanIdInput && pengadaanIdValue) {
                        pengadaanIdInput.value = pengadaanIdValue;
                    }

                    const previewFoto = document.getElementById('manualFotoPreview');
                    const textFoto = document.getElementById('manualTextFoto');
                    const btnFoto = document.getElementById('manualBtnFoto');
                    if (previewFoto && textFoto && btnFoto) {
                        previewFoto.classList.add('hidden');
                        textFoto.textContent = '📷 Pilih Foto Bahan';
                        btnFoto.classList.remove('bg-emerald-500/20', 'border-emerald-500/50');
                    }

                    // Reset color preview
                    const colorPreview = document.getElementById('colorPreview');
                    const colorImagePreview = document.getElementById('colorImagePreview');
                    if (colorPreview) colorPreview.classList.add('hidden');
                    if (colorImagePreview) colorImagePreview.classList.add('hidden');

                } else {
                    if (loadingOverlay) loadingOverlay.classList.add('hidden');
                    if (typeof showError === 'function') {
                        showError(result.message || 'Terjadi kesalahan saat menyimpan.');
                    } else {
                        alert(result.message || 'Terjadi kesalahan saat menyimpan.');
                    }
                }
            } catch (error) {
                if (loadingOverlay) loadingOverlay.classList.add('hidden');
                if (typeof showError === 'function') {
                    showError('Gagal terhubung ke server. Periksa koneksi Anda.');
                } else {
                    alert('Gagal terhubung ke server. Periksa koneksi Anda.');
                }
            }
        });
    }
});
</script>