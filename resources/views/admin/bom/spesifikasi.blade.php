@extends('layouts.admin')

@section('title', 'Edit Spesifikasi Produk')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-white">✏️ Edit Spesifikasi Produk</h2>
            <p class="text-gray-400 text-sm">Edit dimensi, sketsa, dan spesifikasi teknis untuk: <span class="text-orange-500 font-bold">{{ $product->nama_produk }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.bom.index') }}" class="text-gray-400 hover:text-white text-sm">Kembali</a>
            <a href="{{ route('admin.bom.resep', $product->id) }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-500 rounded-lg text-white text-sm font-bold flex items-center gap-2">
                <i class="fa-solid fa-flask"></i> Edit Resep BOM
            </a>
        </div>
    </div>

    <form action="{{ route('admin.bom.update-spesifikasi', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Bagian 1: Informasi Dasar --}}
        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-2 h-2 bg-cyan-400 rounded-full"></div>
                <h3 class="font-bold text-white">📋 Informasi Dasar</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                {{-- Nama Produk --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Nama Produk</label>
                    <input type="text" value="{{ $product->nama_produk }}"
                        class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm outline-none cursor-not-allowed" readonly>
                </div>

                {{-- Kategori --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Kategori</label>
                    <input type="text" value="{{ is_array($product->kategori) ? implode(', ', $product->kategori) : $product->kategori }}"
                        class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm outline-none cursor-not-allowed" readonly>
                </div>

                {{-- Warna --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Warna</label>
                    <input type="text" value="{{ is_array($product->warna) ? implode(', ', $product->warna) : $product->warna }}"
                        class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm outline-none cursor-not-allowed" readonly>
                </div>

                {{-- Motif --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Jenis Motif</label>
                    <input type="text" value="{{ is_array($product->jenis_motif) ? implode(', ', $product->jenis_motif) : $product->jenis_motif }}"
                        class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm outline-none cursor-not-allowed" readonly>
                </div>
            </div>
        </div>

        {{-- Bagian 2: Dimensi Pola --}}
        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6">
            <div class="flex items-center gap-2 mb-5">
                <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                <h3 class="font-bold text-white">📐 Dimensi Pola Produksi</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                {{-- Panjang --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Panjang (cm)</label>
                    <input type="number" step="0.01" name="panjang" value="{{ old('panjang', $product->panjang) }}"
                        class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm focus:border-orange-500 outline-none">
                </div>

                {{-- Lebar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Lebar (cm)</label>
                    <input type="number" step="0.01" name="lebar" value="{{ old('lebar', $product->lebar) }}"
                        class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm focus:border-orange-500 outline-none">
                </div>

                {{-- Tinggi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1.5">Tinggi (cm)</label>
                    <input type="number" step="0.01" name="tinggi" value="{{ old('tinggi', $product->tinggi) }}"
                        class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-lg px-3 py-2.5 text-white text-sm focus:border-orange-500 outline-none">
                </div>
            </div>

            {{-- Detail Pola --}}
            <div class="space-y-3 mb-5">
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">📝 Catatan Potong / Detail Pola</label>
                <textarea name="detail_pola" rows="4"
                    class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-lg px-4 py-3 text-white text-sm focus:border-orange-500 outline-none resize-none"
                    placeholder="Contoh: Pola A dipotong 2x, Pola B dipotong 1x dengan ketebalan 1.2mm...">{{ old('detail_pola', $product->detail_pola) }}</textarea>
            </div>

            {{-- Sketsa Pola --}}
            <div class="space-y-3">
                <label class="block text-xs font-semibold text-gray-400 mb-1.5">📝 Sketsa Pola Produksi</label>
                <div class="space-y-3" x-data="{ sketsaPreview: '{{ $product->sketsa ? asset('storage/' . $product->sketsa) : null }}' }">
                    <div x-show="sketsaPreview" class="w-full h-40 rounded-lg overflow-hidden border border-white/10 bg-black/30 flex items-center justify-center">
                        <img x-bind:src="sketsaPreview" class="w-full h-full object-contain">
                    </div>
                    <div x-show="!sketsaPreview" class="group relative w-full h-32 border-2 border-dashed border-white/20 hover:border-orange-500 hover:bg-white/5 rounded-lg flex flex-col items-center justify-center p-4 cursor-pointer transition bg-black/30">
                        <svg class="w-10 h-10 text-gray-400 group-hover:text-orange-500 mb-2 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-4-4V7a4 4 0 014-4h10a4 4 0 014 4v5a4 4 0 01-4 4H7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <div class="text-center">
                            <p class="text-white font-medium mb-1">Upload Sketsa Pola</p>
                            <p class="text-xs text-gray-400">PNG, JPG hingga 2MB</p>
                        </div>
                        <input type="file" name="sketsa" accept="image/*" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            x-on:change="sketsaPreview = URL.createObjectURL($event.target.files[0])">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t border-white/10">
            <a href="{{ route('admin.bom.index') }}" class="px-6 py-3 rounded-xl border border-white/10 text-gray-300 font-bold hover:bg-white/5 transition text-sm">Batal</a>
            <button type="submit" class="px-6 py-3 rounded-xl bg-[#FF6B00] text-white font-bold hover:bg-[#e65c00] shadow-lg shadow-orange-900/20 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection