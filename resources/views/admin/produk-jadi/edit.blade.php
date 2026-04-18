@extends('layouts.admin')

@section('title', 'Edit Produk: ' . $product->nama_produk)
@section('title_header', 'Edit Produk')

@section('content')

{{-- Select2 Library --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-container--default .select2-selection--multiple {
    background-color: rgba(10, 10, 10, 0.8) !important;
    border: 1px solid rgba(255, 255, 0.2) !important;
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
    border: 1px solid rgba(255, 255, 0.2) !important;
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

::placeholder {
    color: #4b5563 !important;
    opacity: 1;
}

::-ms-input-placeholder {
    color: #4b5563 !important;
}

.space-y-6 {
    margin-bottom: 3rem !important;
}

.border-b {
    border-color: rgba(255, 255, 255, 0.08) !important;
}
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

                    {{-- Field Groups --}}
                    <div x-data="{
                        price: '{{ number_format($product->harga_jual, 0, '.', '.') }}',
                        filePreview: '{{ $product->foto ? asset('storage/' . $product->foto) : null }}',
                        formatRupiah(value) {
                            let number_string = value.replace(/[^,\d]/g, '').toString();
                            let split = number_string.split(',');
                            let sisa = split[0].length % 3;
                            let rupiah = split[0].substr(0, sisa);
                            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                            if (ribuan) {
                                separator = sisa ? '.' : '';
                                rupiah += separator + ribuan.join('.');
                            }
                            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                            return rupiah;
                        },
                        handleFile(e) {
                            const file = e.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    this.filePreview = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            } else {
                                this.filePreview = null;
                            }
                        }
                    }">
            
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
                        <input type="text" name="nama_produk" required value="{{ old('nama_produk', $product->nama_produk) }}"
                            class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-4 py-3 text-white placeholder-gray-500 text-base font-medium focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                            placeholder="Contoh: Tas Kulit Premium Black Edition">
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Kategori</label>
                        <select name="kategori[]" multiple class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50" id="kategori-select">
                            @foreach($kategories as $kategori)
                            <option value="{{ $kategori->nama_kategori }}" {{ in_array($kategori->nama_kategori, $product->kategori ?? []) ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
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
                    {{-- Warna --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Warna <span class="text-red-400">*</span></label>
                        <select name="warna[]" multiple required class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50" id="warna-select">
                            @foreach($warnas as $warna)
                            <option value="{{ $warna->nama_warna }}" {{ in_array($warna->nama_warna, $product->warna ?? []) ? 'selected' : '' }}>{{ $warna->nama_warna }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Motif --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Jenis Motif</label>
                        <select name="jenis_motif[]" multiple class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50" id="motif-select">
                            @foreach($motifs as $motif)
                            <option value="{{ $motif->nama_motif }}" {{ in_array($motif->nama_motif, $product->jenis_motif ?? []) ? 'selected' : '' }}>{{ $motif->nama_motif }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stok Minimum --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Stok Minimum (Alert)</label>
                        <input type="number" min="0" name="stok_minimum" value="{{ old('stok_minimum', (int)$product->stok_minimum ?? 10) }}"
                            class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                            placeholder="10">
                    </div>
                </div>
            </div>

            {{-- Group: Dimensi Pola --}}
            <div class="space-y-6 mb-10">
                <div class="pt-1 pb-4 border-b border-white/10">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <div class="w-2 h-2 bg-cyan-400 rounded-full"></div>
                        Dimensi Pola & Sketsa
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Panjang --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Panjang (cm)</label>
                        <input type="number" step="0.01" name="panjang" value="{{ old('panjang', (int)$product->panjang == $product->panjang ? (int)$product->panjang : $product->panjang) }}"
                            class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                            placeholder="25">
                    </div>

                    {{-- Lebar --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Lebar (cm)</label>
                        <input type="number" step="0.01" name="lebar" value="{{ old('lebar', (int)$product->lebar == $product->lebar ? (int)$product->lebar : $product->lebar) }}"
                            class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                            placeholder="10">
                    </div>

                    {{-- Tinggi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Tinggi (cm)</label>
                        <input type="number" step="0.01" name="tinggi" value="{{ old('tinggi', (int)$product->tinggi == $product->tinggi ? (int)$product->tinggi : $product->tinggi) }}"
                            class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-4 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50"
                            placeholder="15">
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-sm font-semibold text-gray-300">Detail Pola / Catatan Potong</label>
                    <textarea name="detail_pola" rows="3"
                        class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-5 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 resize-vertical text-base"
                        placeholder="Contoh: Pola A dipotong 2x, Pola B dipotong 1x dengan ketebalan 1.2mm...">{{ old('detail_pola', $product->detail_pola) }}</textarea>
                </div>

                <div class="space-y-3" x-data="{ sketsaPreview: '{{ $product->sketsa ? asset('storage/' . $product->sketsa) : null }}' }">
                    <label class="block text-sm font-semibold text-gray-300">Upload Foto Sketsa</label>
                    <div class="space-y-3">
                        {{-- Preview --}}
                        <div x-show="sketsaPreview" class="w-full h-48 rounded-2xl overflow-hidden border-2 border-dashed border-white/20 bg-black/30 flex items-center justify-center shadow-lg">
                            <img x-bind:src="sketsaPreview" class="w-full h-full object-contain">
                        </div>
                        <div x-show="!sketsaPreview" class="group relative w-full h-32 border-2 border-dashed border-white/20 hover:border-cyan-400 hover:bg-white/5 rounded-2xl flex flex-col items-center justify-center p-6 cursor-pointer transition-all duration-300 shadow-lg hover:shadow-cyan-400/10 bg-black/30 backdrop-blur-sm">
                            <svg class="w-12 h-12 text-gray-400 group-hover:text-cyan-400 mb-3 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-4-4V7a4 4 0 014-4h10a4 4 0 014 4v5a4 4 0 01-4 4H7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div class="text-center">
                                <p class="text-white font-medium mb-1">Klik atau drag sketsa ke sini</p>
                                <p class="text-sm text-gray-400">PNG, JPG hingga 2MB</p>
                            </div>
                            <input type="file" name="sketsa" accept="image/*" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                x-on:change="sketsaPreview = URL.createObjectURL($event.target.files[0])">
                        </div>
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
                            <label class="flex items-center p-4 bg-[#0a0a0a]/80 border border-white/20 rounded-2xl cursor-pointer hover:border-gold-400 transition-all">
                                <input type="radio" name="tipe_harga" value="manual" checked 
                                    class="w-5 h-5 text-gold-400 border-gray-600 focus:ring-gold-400 focus:ring-2 bg-transparent"
                                    onchange="toggleHargaField()">
                                <div class="ml-3">
                                    <span class="text-white font-semibold">Harga Manual</span>
                                    <p class="text-xs text-gray-500 mt-1">Input harga secara langsung</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 bg-[#0a0a0a]/80 border border-white/20 rounded-2xl cursor-pointer hover:border-gold-400 transition-all">
                                <input type="radio" name="tipe_harga" value="otomatis" 
                                    class="w-5 h-5 text-gold-400 border-gray-600 focus:ring-gold-400 focus:ring-2 bg-transparent"
                                    onchange="toggleHargaField()">
                                <div class="ml-3">
                                    <span class="text-white font-semibold">Harga Otomatis</span>
                                    <p class="text-xs text-gray-500 mt-1">Markup dari BOM + Margin + PPN</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div class="space-y-2" id="harga-manual-container">
                        <label class="block text-sm font-semibold text-gray-300">Harga Jual <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-lg">Rp</span>
                            <input type="text" name="harga_jual" id="harga_jual_input" required 
                                x-model="price"
                                x-on:input="price = formatRupiah($event.target.value)"
                                class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl py-5 pl-14 pr-5 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 font-mono font-bold text-xl"
                                placeholder="150.000">
                        </div>
                        <p class="text-xs text-gray-500" id="bantuan-harga">Harga akan dihitung otomatis berdasarkan modal bahan baku + Margin + PPN saat produk disimpan.</p>
                    </div>

                    {{-- Biaya Lain-lain --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-300">Biaya Lain-lain (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-lg">Rp</span>
                            <input type="text" name="biaya_lain"
                                   x-data="{ biaya: '' }"
                                   x-model="biaya"
                                   x-on:input="biaya = formatRupiah($event.target.value)"
                                   class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl py-5 pl-14 pr-5 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 font-mono font-bold text-xl"
                                   placeholder="0">
                        </div>
                        <p class="text-xs text-gray-500">Contoh: biaya packaging, stiker, dll. (Opsional)</p>
                    </div>

                    {{-- Foto --}}
                    <div class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-300">Foto Produk</label>
                        <div class="space-y-3">
                            {{-- Preview --}}
                            <div x-show="filePreview" class="w-full h-32 rounded-2xl overflow-hidden border-2 border-dashed border-white/20 bg-black/30 flex items-center justify-center shadow-lg">
                                <img x-bind:src="filePreview" class="w-full h-full object-cover">
                            </div>
                            <div x-show="!filePreview" id="upload-area" class="group relative w-full h-32 border-2 border-dashed border-white/20 hover:border-gold-400 hover:bg-white/5 rounded-2xl flex flex-col items-center justify-center p-6 cursor-pointer transition-all duration-300 shadow-lg hover:shadow-gold-400/10 bg-black/30 backdrop-blur-sm">
                                <svg class="w-12 h-12 text-gray-400 group-hover:text-gold-400 mb-3 transition" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v4a2 2 0 002 2h10l3 3H6a2 2 0 00-2 2v3a2 2 0 002 2h24a2 2 0 002-2v-3a2 2 0 00-2-2H19l3-3h10a2 2 0 002-2v-4a2 2 0 00-2-2H12z"/>
                                </svg>
                                <div class="text-center">
                                    <p class="text-white font-medium mb-1">Klik atau drag gambar ke sini</p>
                                    <p class="text-sm text-gray-400">PNG, JPG hingga 5MB</p>
                                </div>
                                <input type="file" name="foto" accept="image/*" 
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    x-on:change="handleFile($event)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="space-y-3 mb-6">
                <label class="block text-sm font-semibold text-gray-300">Deskripsi Produk</label>
                <textarea name="deskripsi" rows="5"
                    class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-5 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 resize-vertical text-base"
                    placeholder="Jelaskan detail produk, bahan, ukuran, atau spesifikasi khusus lainnya...">{{ old('deskripsi', $product->deskripsi) }}</textarea>
            </div>

            {{-- Keterangan --}}
            <div class="space-y-3">
                <label class="block text-sm font-semibold text-gray-300">Keterangan (Internal)</label>
                <textarea name="keterangan" rows="3"
                    class="w-full bg-[#0a0a0a]/80 backdrop-blur-sm border border-white/20 hover:border-white/30 rounded-2xl px-5 py-5 text-white placeholder-gray-500 focus:border-gold-400 focus:ring-2 focus:ring-gold-400/30 transition-all duration-300 outline-none shadow-lg shadow-black/50 resize-vertical text-base"
                    placeholder="Catatan internal, misal: 'Revisi dari customer X', 'Gunakan benang jenis Y'...">{{ old('keterangan', $product->keterangan) }}</textarea>
            </div>

            </div>

            {{-- Footer --}}
            <div class="flex flex-col sm:flex-row justify-end gap-4 mt-12 pt-8 border-t border-white/10">
                <a href="{{ route('admin.produk-jadi.index') }}"
                   class="px-8 py-4 bg-white/5 hover:bg-white/10 text-white rounded-2xl text-sm font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg shadow-black/30 hover:shadow-white/10">
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

        </div>
    </div>

        </form>
    </div>
</div>
@endsection

<style>
    .gold-400 { color: #d4af37; }
    .shadow-gold-400\/10 { box-shadow: 0 10px 25px rgba(212, 175, 55, 0.1); }
</style>

<script>
function toggleHargaField() {
    const tipeOtomatis = document.querySelector('input[value="otomatis"]').checked;
    const hargaInput = document.getElementById('harga_jual_input');
    const bantuanText = document.getElementById('bantuan-harga');
    const container = document.getElementById('harga-manual-container');
    
    if (tipeOtomatis) {
        hargaInput.disabled = true;
        hargaInput.removeAttribute('required');
        hargaInput.classList.add('bg-gray-800', 'cursor-not-allowed', 'opacity-50');
        container.classList.add('opacity-50');
        bantuanText.classList.remove('hidden');
    } else {
        hargaInput.disabled = false;
        hargaInput.setAttribute('required', 'required');
        hargaInput.classList.remove('bg-gray-800', 'cursor-not-allowed', 'opacity-50');
        container.classList.remove('opacity-50');
        bantuanText.classList.add('hidden');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleHargaField();
    
    // Initialize Select2 for multi-select fields
    $('#kategori-select').select2({
        placeholder: "Pilih Kategori",
        width: '100%',
        theme: 'default'
    });
    
    $('#warna-select').select2({
        placeholder: "Pilih Warna",
        width: '100%',
        theme: 'default'
    });
    
    $('#motif-select').select2({
        placeholder: "Pilih Motif",
        width: '100%',
        theme: 'default'
    });
});
</script>
