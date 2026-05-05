@extends('layouts.admin')

@section('title', 'Buat Produk Baru')
@section('title_header', 'Buat Produk Baru')

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
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">Buat Produk Baru</h1>
                    </div>
                    <p class="text-sm text-gray-400">Isi data dasar produk. Setelah ini Anda akan diarahkan untuk mengisi Resep Bahan (BOM).</p>
                </div>
            </div>

            {{-- Card --}}
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">
                <form action="{{ route('admin.produk-jadi.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @if ($errors->any())
                        <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl backdrop-blur-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div x-data="{ price: '', filePreview: null,
                        formatRupiah(value) {
                            let number_string = value.replace(/[^,\d]/g, '').toString();
                            let split = number_string.split(',');
                            let sisa = split[0].length % 3;
                            let rupiah = split[0].substr(0, sisa);
                            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                            if (ribuan) { separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
                            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
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
                                <div class="lg:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Nama Produk <span class="text-red-400">*</span></label>
                                    <input type="text" name="nama_produk" required value="{{ old('nama_produk') }}"
                                        class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-2xl px-4 py-3 text-white focus:border-gold-400 transition-all outline-none"
                                        placeholder="Contoh: Messenger Bag - Java Heritage">
                                </div>

                                {{-- Kode Produk (Otomatis & Read-Only) --}}
<div class="lg:col-span-1">
    <label class="block text-sm font-semibold text-gray-300 mb-2">Kode Produk (Otomatis)</label>
    <input type="text" value="{{ $newCode }}" readonly
        class="w-full bg-[#0a0a0a]/50 border border-white/10 rounded-2xl px-4 py-3 text-gray-500 font-mono cursor-not-allowed outline-none"
        placeholder="Akan digenerate otomatis">
    <p class="text-[10px] text-gray-500 mt-1">*Kode mengikuti format pengaturan gudang (Prefix-000X)</p>
</div>
                                {{-- Kategori --}}
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Kategori</label>
                                    <select name="kategori[]" multiple class="w-full" id="kategori-select">
                                        @foreach($kategories as $kategori)
                                            <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>
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
                                    Tampilan & Gaya
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Warna --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Warna <span class="text-red-400">*</span></label>
                                    <select name="warna[]" multiple required id="warna-select">
                                        @foreach($warnas as $warna)
                                            <option value="{{ $warna->nama_warna }}">{{ $warna->nama_warna }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Motif --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Jenis Motif <span class="text-red-400">*</span></label>
                                    <select name="jenis_motif[]" multiple required id="motif-select">
                                        @foreach($motifs as $motif)
                                            <option value="{{ $motif->nama_motif }}">{{ $motif->nama_motif }}</option>
                                        @endforeach
                                    </select>
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
                                    <div class="grid grid-cols-1 gap-3">
                                        <label class="flex items-center p-4 bg-[#0a0a0a]/80 border border-white/20 rounded-2xl cursor-pointer hover:border-gold-400 transition-all">
                                            <input type="radio" name="tipe_harga" value="manual" checked onchange="toggleHargaField()" class="w-5 h-5 text-gold-400">
                                            <div class="ml-3">
                                                <span class="text-white font-semibold">Harga Manual</span>
                                                <p class="text-xs text-gray-500">Input harga jual secara langsung</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center p-4 bg-[#0a0a0a]/80 border border-white/20 rounded-2xl cursor-pointer hover:border-gold-400 transition-all">
                                            <input type="radio" name="tipe_harga" value="otomatis" onchange="toggleHargaField()" class="w-5 h-5 text-gold-400">
                                            <div class="ml-3">
                                                <span class="text-white font-semibold">Harga Otomatis</span>
                                                <p class="text-xs text-gray-500">Dihitung otomatis dari total bahan baku</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- Harga --}}
                                <div class="space-y-2" id="harga-manual-container">
                                    <label class="block text-sm font-semibold text-gray-300">Harga Jual <span class="text-red-400">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                        <input type="text" name="harga_jual" id="harga_jual_input" required 
                                            x-model="price" x-on:input="price = formatRupiah($event.target.value)"
                                            class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-2xl py-5 pl-14 pr-5 text-white font-mono font-bold text-xl outline-none"
                                            placeholder="150.000">
                                    </div>
                                    <p class="text-xs text-gray-500 hidden" id="bantuan-harga">Harga akan muncul otomatis setelah resep bahan baku diisi.</p>
                                </div>

                                {{-- Foto Utama --}}
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Foto Produk Utama</label>
                                    <div x-show="filePreview" class="mb-3 w-full h-48 rounded-2xl overflow-hidden border border-white/20 bg-black/30 flex items-center justify-center">
                                        <img x-bind:src="filePreview" class="h-full object-cover">
                                    </div>
                                    <div class="relative group w-full h-32 border-2 border-dashed border-white/20 hover:border-gold-400 rounded-2xl flex flex-col items-center justify-center p-6 cursor-pointer bg-black/30">
                                        <p class="text-white font-medium">Klik untuk upload foto produk</p>
                                        <input type="file" name="foto" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            x-on:change="const file = $event.target.files[0]; if(file){ const reader = new FileReader(); reader.onload = (e) => { filePreview = e.target.result; }; reader.readAsDataURL(file); }">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="space-y-3 mb-6">
                            <label class="block text-sm font-semibold text-gray-300">Deskripsi Singkat</label>
                            <textarea name="deskripsi" rows="3" class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-2xl px-5 py-5 text-white outline-none" placeholder="Messenger Bag vintage berbahan kulit Crazy Horse..."></textarea>
                        </div>

                        {{-- Footer --}}
                        <div class="flex justify-end gap-4 mt-12 pt-8 border-t border-white/10">
                            <a href="{{ route('admin.produk-jadi.index') }}" class="px-8 py-4 bg-white/5 text-white rounded-2xl text-sm font-semibold hover:bg-white/10 transition">Batal</a>
                            <button type="submit" class="px-10 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-900/30 transition hover:scale-105">
                                Simpan & Lanjut ke BOM
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
function toggleHargaField() {
    const tipeOtomatis = document.querySelector('input[value="otomatis"]').checked;
    const hargaInput = document.getElementById('harga_jual_input');
    const bantuanText = document.getElementById('bantuan-harga');
    const container = document.getElementById('harga-manual-container');
    
    if (tipeOtomatis) {
        hargaInput.disabled = true;
        hargaInput.removeAttribute('required');
        container.classList.add('opacity-50');
        bantuanText.classList.remove('hidden');
    } else {
        hargaInput.disabled = false;
        hargaInput.setAttribute('required', 'required');
        container.classList.remove('opacity-50');
        bantuanText.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleHargaField();
    $('#kategori-select').select2({ placeholder: "Pilih Kategori" });
    $('#warna-select').select2({ placeholder: "Pilih Warna" });
    $('#motif-select').select2({ placeholder: "Pilih Motif" });
});
</script>