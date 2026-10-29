
@extends('layouts.admin')


@section('content')

{{-- Select2 Library --}}
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

            {{-- Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('admin.produk-jadi.index') }}" class="p-2 hover:bg-white/5 rounded-xl transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">Edit Produk</h1>
                    </div>
                    <p class="text-sm text-gray-400">
                        Perbarui detail produk <strong class="text-amber-400 font-mono">{{ $product->kode_produk }}</strong>
                    </p>
                </div>
            </div>

            {{-- Card --}}
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">

                <form action="{{ route('admin.produk-jadi.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Error --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl backdrop-blur-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ✅ FIX: x-data ditulis di tag div terpisah, BUKAN di dalam atribut yang campur PHP --}}
                        {{-- Semua nilai PHP di-pass lewat data-attribute agar tidak ada konflik quote --}}
                    <div
                        id="product-edit-wrapper"
                        x-data="productEditData()"
                        x-init="init()"
                        data-harga="{{ $product->harga_jual }}"
                        data-margin="{{ $margin }}"
                        data-foto="{{ $product->foto }}"
                        data-produk-id="{{ $product->id }}"
                    >

                        {{-- Group 1: Informasi Dasar --}}
                        <div class="space-y-6 mb-10">
                            <div class="pt-1 pb-4 border-b border-white/10">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                    Informasi Dasar
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                {{-- Nama --}}
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Nama Produk <span class="text-red-400">*</span></label>
                                    <input type="text" name="nama_produk" required
                                        value="{{ old('nama_produk', $product->nama_produk) }}"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-4 py-3 text-white placeholder-gray-500 text-base font-medium focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                                        placeholder="Contoh: Tas Kulit Premium Black Edition">
                                </div>

                                {{-- Kategori --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Kategori</label>
                                    <select name="kategori[]" multiple id="kategori-select"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-4 text-white outline-none">
                                        @foreach($kategories as $kategori)
                                            <option value="{{ $kategori->nama_kategori }}"
                                                {{ in_array(trim($kategori->nama_kategori), array_map('trim', $product->kategori ?? [])) ? 'selected' : '' }}>
                                                {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(!empty($product->kategori))
                                        <p class="text-xs text-gray-500 mt-1">
                                            Terpilih: <span class="text-amber-400">{{ is_array($product->kategori) ? implode(', ', $product->kategori) : $product->kategori }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Group 2: Spesifikasi --}}
                        <div class="space-y-6 mb-10">
                            <div class="pt-1 pb-4 border-b border-white/10">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                                    Spesifikasi Produk
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- ✅ FIX: Warna – in_array dari $product->warna (sudah array dari controller) --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Warna <span class="text-red-400">*</span></label>
                                    <select name="warna[]" multiple required id="warna-select"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-4 text-white outline-none">
                                        @foreach($warnas as $warna)
                                            <option value="{{ $warna->nama_warna }}"
                                                {{ in_array(trim($warna->nama_warna), array_map('trim', $product->warna ?? [])) ? 'selected' : '' }}>
                                                {{ $warna->nama_warna }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- ✅ FIX: Tampilkan nilai terpilih dengan implode bukan var_dump --}}
                                    @if(!empty($product->warna))
                                        <p class="text-xs text-gray-500 mt-1">
                                            Terpilih: <span class="text-amber-400">{{ implode(', ', $product->warna) }}</span>
                                        </p>
                                    @endif
                                </div>

                                {{-- ✅ FIX: Motif – in_array dari $product->jenis_motif (sudah array) --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Jenis Motif</label>
                                    <select name="jenis_motif[]" multiple id="motif-select"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-4 text-white outline-none">
                                        @foreach($motifs as $motif)
                                            <option value="{{ $motif->nama_motif }}"
                                                {{ in_array(trim($motif->nama_motif), array_map('trim', $product->jenis_motif ?? [])) ? 'selected' : '' }}>
                                                {{ $motif->nama_motif }}
                                            </option>
                                        @endforeach
                                    </select>
                                    {{-- ✅ FIX: Tampilkan nilai terpilih dengan implode --}}
                                    @if(!empty($product->jenis_motif))
                                        <p class="text-xs text-gray-500 mt-1">
                                            Terpilih: <span class="text-amber-400">{{ implode(', ', $product->jenis_motif) }}</span>
                                        </p>
                                    @endif
                                </div>

                                {{-- Stok Minimum --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Stok Minimum (Alert)</label>
                                    <input type="number" min="0" name="stok_minimum"
                                        value="{{ old('stok_minimum', (int)($product->stok_minimum ?? 10)) }}"
                                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                                        placeholder="10">
                                </div>
                            </div>

                        </div>

                        {{-- Group 3: Harga & Media --}}
                        <div class="space-y-6 mb-10">
                            <div class="pt-1 pb-4 border-b border-white/10">
                                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                    <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
                                    Harga & Media
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                                {{-- Tipe Harga --}}
                                <div class="space-y-3">
                                    <label class="block text-sm font-semibold text-gray-300">Tipe Harga</label>
                                    <div class="space-y-3">
                                        <label class="flex items-center p-4 bg-[#0a0a0a]/80 border border-white/20 rounded-2xl cursor-pointer hover:border-yellow-400 transition-all">
                                            <input type="radio" name="tipe_harga" value="manual"
                                                {{ old('tipe_harga', $product->tipe_harga ?? 'manual') === 'manual' ? 'checked' : '' }}
                                                class="w-5 h-5 border-gray-600 bg-transparent"
                                                onchange="toggleHargaField()">
                                            <div class="ml-3">
                                                <span class="text-white font-semibold">Harga Manual</span>
                                                <p class="text-xs text-gray-500 mt-1">Input harga secara langsung</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-4 bg-[#0a0a0a]/80 border border-white/20 rounded-2xl cursor-pointer hover:border-yellow-400 transition-all">
                                            <input type="radio" name="tipe_harga" value="otomatis"
                                                {{ old('tipe_harga', $product->tipe_harga ?? 'manual') === 'otomatis' ? 'checked' : '' }}
                                                class="w-5 h-5 border-gray-600 bg-transparent"
                                                onchange="toggleHargaField()">
                                            <div class="ml-3">
                                                <span class="text-white font-semibold">Harga Otomatis</span>
                                                <p class="text-xs text-gray-500 mt-1">Markup dari BOM + Margin</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Input Harga --}}
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

                                {{-- ✅ FIX: Biaya Lain - HAPUS nested x-data, gunakan x-model dari parent --}}
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
                                {{-- Foto --}}
<div class="space-y-3">
    <label class="block text-sm font-semibold text-gray-300">Foto Produk</label>
    <div class="space-y-3">
        
        {{-- ✅ Pindahkan input file ke luar logic x-show agar dapat dipanggil dari mana saja --}}
        <input type="file" name="foto" id="input_foto_produk" accept="image/*" class="hidden" x-on:change="handleFile($event)">

        {{-- Preview Foto Lama --}}
        <div x-show="!filePreview && hasOldPhoto" class="relative w-full h-40 rounded-2xl overflow-hidden border-2 border-white/20 bg-black/30 flex items-center justify-center shadow-lg">
            <img src="{{ $product->foto ? asset('storage/' . $product->foto) : '' }}" class="w-full h-full object-cover" alt="Foto Produk">
            {{-- Tombol Hapus Foto --}}
            <button type="button" 
                    class="absolute top-2 right-2 bg-red-500/80 hover:bg-red-500 text-white rounded-full p-2 transition-all duration-300 backdrop-blur-sm"
                    x-on:click="clearFile()">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Area Upload Foto Baru (Dropzone tampil JIKA tidak ada foto lama dan baru) --}}
        <div x-show="!filePreview && !hasOldPhoto" 
             onclick="document.getElementById('input_foto_produk').click()"
             class="group relative w-full h-32 border-2 border-dashed border-white/20 hover:border-yellow-400 hover:bg-white/5 rounded-2xl flex flex-col items-center justify-center p-6 cursor-pointer transition-all duration-300 shadow-lg bg-black/30 backdrop-blur-sm">
            <svg class="w-10 h-10 text-gray-400 group-hover:text-yellow-400 mb-2 transition" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"/>
            </svg>
            <p class="text-white font-medium text-sm mb-1">Klik untuk upload gambar</p>
            <p class="text-xs text-gray-400">PNG, JPG hingga 10MB</p>
        </div>
        
        {{-- Preview Foto Baru (Hanya tampil jika ada file yang baru dipilih via input box) --}}
        <div x-show="filePreview" class="relative w-full h-40 rounded-2xl overflow-hidden border-2 border-dashed border-yellow-400 bg-black/30 flex items-center justify-center shadow-lg" x-cloak>
            <img x-bind:src="filePreview" class="w-full h-full object-cover" alt="Preview Foto Baru">
            {{-- Tombol Batal/Hapus Preview --}}
            <button type="button" 
                    class="absolute top-2 right-2 bg-red-500/80 hover:bg-red-500 text-white rounded-full p-2 transition-all duration-300 backdrop-blur-sm"
                    x-on:click="clearFile()">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Tombol Ganti Foto (Tampil di bawah foto lama) --}}
        <div x-show="!filePreview && hasOldPhoto" class="w-full">
            <button type="button" 
                    onclick="document.getElementById('input_foto_produk').click()"
                    class="w-full py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-2xl text-sm font-semibold transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-blue-900/30 hover:shadow-blue-900/50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Ganti Foto Produk
            </button>
        </div>
    </div>
</div>

                        {{-- Deskripsi --}}
                        <div class="space-y-3 mb-6">
                            <label class="block text-sm font-semibold text-gray-300">Deskripsi Produk</label>
                            <textarea name="deskripsi" rows="5"
                                class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-5 text-white placeholder-gray-500 focus:border-yellow-400 focus:ring-2 focus:ring-yellow-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 resize-vertical text-base"
                                placeholder="Jelaskan detail produk, bahan, ukuran, atau spesifikasi khusus...">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                        </div>

                    </div>{{-- end x-data wrapper --}}

                    {{-- ✅ Footer dengan tombol Simpan EMERALD --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 pt-8 border-t border-white/10">
                        <a href="{{ route('admin.produk-jadi.index') }}"
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
        margin: {{ $margin }},
        hpp: 0,
        biayaLain: 0,
        biayaLainFormatted: '0',
        filePreview: '',
        hasOldPhoto: false,
        produkId: '',

        init() {
            const el = document.getElementById('product-edit-wrapper');
            if (!el) return;
            
            const hargaRaw  = el.dataset.harga   || '0';
            this.margin     = parseFloat(el.dataset.margin)  || {{ $margin }};
            
            // Handle foto yang sudah ada
            const existingFoto = el.dataset.foto;
            if (existingFoto && existingFoto !== 'null' && existingFoto !== '') {
                this.hasOldPhoto = true;
                this.filePreview = ''; // filePreview hanya untuk foto BARU
            } else {
                this.hasOldPhoto = false;
                this.filePreview = '';
            }
            
            this.produkId   = el.dataset.produkId || '';

            // Format harga awal
            this.price = this.formatRupiah(parseInt(hargaRaw.replace(/[^0-9]/g, '')) || 0);

            // 🔥 FIX: Jika tipe harga sudah "otomatis" sejak awal, langsung hitung harga otomatis
            const radioOtomatis = document.querySelector('input[name="tipe_harga"][value="otomatis"]');
            if (radioOtomatis && radioOtomatis.checked) {
                const hargaInput = document.getElementById('harga_jual_input');
                const container  = document.getElementById('harga-manual-container');
                if (hargaInput) {
                    hargaInput.readOnly = true;
                    hargaInput.classList.add('opacity-50', 'cursor-not-allowed');
                }
                if (container) {
                    container.classList.add('opacity-75');
                }
                this.hitungHargaOtomatis();
            }
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
            
            // Hapus input hidden delete_foto jika ada (karena user upload foto baru)
            const deleteInput = document.querySelector('input[name="delete_foto"]');
            if (deleteInput) {
                deleteInput.remove();
            }
        },

        clearFile() {
            this.filePreview = '';
            // Clear the file input
            const fileInput = document.querySelector('input[name="foto"]');
            if (fileInput) fileInput.value = '';
            
            // Jika ini foto yang sudah ada, kita perlu menghapusnya dari server
            // Kita akan tambahkan input hidden untuk menandai bahwa foto harus dihapus
            const existingFoto = '{{ $product->foto }}';
            if (existingFoto && existingFoto !== 'null' && existingFoto !== '') {
                // Cek apakah sudah ada input hidden untuk penghapusan
                let deleteInput = document.querySelector('input[name="delete_foto"]');
                if (!deleteInput) {
                    deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_foto';
                    deleteInput.value = '1';
                    const form = document.querySelector('form');
                    if (form) form.appendChild(deleteInput);
                }
            }
        },

        async hitungHargaOtomatis() {
            const bantuanEl = document.getElementById('bantuan-harga');
            if (!bantuanEl) return;
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

// ✅ toggleHargaField: gunakan Alpine.$data (API public & reliable di Alpine v3)
function toggleHargaField() {
    const radioOtomatis = document.querySelector('input[name="tipe_harga"][value="otomatis"]');
    const isOtomatis = radioOtomatis ? radioOtomatis.checked : false;
    const hargaInput = document.getElementById('harga_jual_input');
    const container  = document.getElementById('harga-manual-container');

    if (isOtomatis) {
        hargaInput.readOnly = true;
        hargaInput.classList.add('opacity-50', 'cursor-not-allowed');
        container.classList.add('opacity-75');

        // ✅ FIX: Panggil hitungHargaOtomatis via Alpine.$data (lebih reliable dari _x_dataStack)
        const wrapper = document.getElementById('product-edit-wrapper');
        if (wrapper && window.Alpine) {
            const data = window.Alpine.$data(wrapper);
            if (data && typeof data.hitungHargaOtomatis === 'function') {
                data.hitungHargaOtomatis();
            }
        }
    } else {
        hargaInput.readOnly = false;
        hargaInput.classList.remove('opacity-50', 'cursor-not-allowed');
        container.classList.remove('opacity-75');
        const bantuan = document.getElementById('bantuan-harga');
        if (bantuan) {
            bantuan.innerHTML = 'Harga dihitung otomatis dari HPP BOM + Margin.';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // 🔥 FIX: Tunggu Alpine selesai init sebelum panggil toggleHargaField
    // agar data component (hitungHargaOtomatis) sudah tersedia via Alpine.$data
    if (window.Alpine) {
        document.addEventListener('alpine:initialized', function () {
            toggleHargaField();
        });
    } else {
        // Fallback jika Alpine belum ter-load
        setTimeout(toggleHargaField, 500);
    }

    // Select2 init
    if (typeof $.fn.select2 !== 'undefined') {
        $('#kategori-select').select2({ placeholder: 'Pilih Kategori', width: '100%' });
        $('#warna-select').select2({ placeholder: 'Pilih Warna', width: '100%' });
        $('#motif-select').select2({ placeholder: 'Pilih Motif', width: '100%' });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /**
     * Fungsi untuk inisialisasi Select2 dan mengisi nilainya.
     * @param {string} selector - ID dari elemen select (contoh: '#kategori-select').
     * @param {string} placeholder - Teks placeholder untuk dropdown.
     * @param {Array<string>} values - Nilai awal yang harus dipilih.
     */
    function setupSelect2(selector, placeholder, values) {
        const select = $(selector);

        // 1. Inisialisasi Select2
        select.select2({
            placeholder: placeholder,
            allowClear: true,
            tags: true,
            tokenSeparators: [',']
        });

        // 2. Pastikan 'values' adalah array dan tidak kosong
        if (!Array.isArray(values) || values.length === 0) {
            return;
        }

        // 3. Bersihkan nilai (trim spasi) dan filter nilai kosong
        const cleanedValues = values.map(v => (v || '').trim()).filter(v => v);

        // 4. Buat option baru jika belum ada
        cleanedValues.forEach(function(value) {
            if (select.find("option[value='" + value + "']").length === 0) {
                var newOption = new Option(value, value, true, true);
                select.append(newOption);
            }
        });

        // 5. Set nilai dan trigger 'change' untuk memperbarui tampilan
        select.val(cleanedValues).trigger('change');
    }

    // Panggil fungsi untuk setiap dropdown
    setupSelect2(
        '#kategori-select',
        'Pilih Kategori',
        {!! json_encode($product->kategori ?? []) !!}
    );

    setupSelect2(
        '#warna-select',
        'Pilih Warna',
        {!! json_encode($product->warna ?? []) !!}
    );

    setupSelect2(
        '#motif-select',
        'Pilih Motif',
        {!! json_encode($product->jenis_motif ?? []) !!}
    );
});
</script>
@endsection