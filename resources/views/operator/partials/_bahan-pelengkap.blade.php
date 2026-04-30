
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
                            @forelse ($kategories as $kat)
                                <option value="{{ $kat->nama_kategori }}">{{ $kat->nama_kategori }}</option>
                            @empty
                                <option disabled>Data kategori bahan kosong</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Satuan</label>
                        <select name="satuan" id="satuanPelengkapSelect" class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none">
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
                <input type="hidden" name="stok" value="0">
                <input type="hidden" name="harga_satuan" value="0">
                <input type="hidden" name="is_inventory" value="0">

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Deskripsi Bahan <span class="text-purple-400">*</span></label>
                    <textarea name="detail" rows="3" placeholder="Detail spesifikasi bahan, ketebalan, grade, ukuran, kondisi, dll..." class="w-full bg-[#1C222D]/50 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:ring-2 focus:ring-purple-500 outline-none resize-vertical" required></textarea>
                </div>

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

