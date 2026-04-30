
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

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Warna <span class="text-orange-400">*</span></label>
                    <select name="warna" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-orange-500 outline-none" required>
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
                        <input type="number" name="stok" id="jumlahInput" placeholder="0" step="0.01" min="0.01" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Harga Beli (Rp) <span class="text-red-500">*</span></label>
                        <div id="lastPriceInfo" class="hidden text-xs text-amber-400 mb-2"></div>
                        <input type="number" name="harga_satuan" id="hargaSatuan" placeholder="Rp" step="0.01" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white font-semibold focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi Bahan <span class="text-purple-400">*</span></label>
                    <textarea name="detail" rows="3" placeholder="Detail spesifikasi bahan, ketebalan, grade, ukuran, kondisi, dll..." class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none resize-vertical" required></textarea>
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

