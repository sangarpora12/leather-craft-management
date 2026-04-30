@extends('layouts.admin')

@section('title_header', 'Tambah Bahan Baku')

{{-- 1. MUAT ASET SELECT2 --}}
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    {{-- Style tambahan untuk membuat Select2 menyatu dengan tema gelap --}}
    <style>
        .select2-container--default .select2-selection--single {
            background-color: #000;
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: 46px;
            color: white;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white;
            line-height: 44px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
        }
        .select2-dropdown {
            background-color: #0a0a0a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #1a1a1a;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #ef4444; /* Warna merah */
        }
    </style>
@endpush


@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl">
        <h2 class="text-xl font-bold text-white mb-6">Tambah Material Baru</h2>

        <form action="{{ route('admin.bahan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Nama Material <span class="text-red-400">*</span></label>
                    <input type="text" name="nama_material" required
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500" value="{{ old('nama_material') }}">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Kategori <span class="text-red-400">*</span></label>
                    <select name="kategori" required class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="Kulit" {{ old('kategori') == 'Kulit' ? 'selected' : '' }}>Kulit (Leather)</option>
                        <option value="Kain" {{ old('kategori') == 'Kain' ? 'selected' : '' }}>Kain (Fabric)</option>
                        <option value="Aksesoris" {{ old('kategori') == 'Aksesoris' ? 'selected' : '' }}>Aksesoris (Hardware)</option>
                        <option value="Benang" {{ old('kategori') == 'Benang' ? 'selected' : '' }}>Benang (Thread)</option>
                        <option value="Lem" {{ old('kategori') == 'Lem' ? 'selected' : '' }}>Lem (Adhesive)</option>
                        <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya (Others)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Jumlah Masuk</label>
                    <input type="number" name="stok" value="{{ old('stok', 0) }}" required
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Harga Satuan</label>
                    <input type="number" name="harga_satuan" value="{{ old('harga_satuan', 0) }}" required
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Warna</label>
                <select name="warna" class="searchable-select w-full">
                    <option value="">-- Pilih Warna (Opsional) --</option>
                    @foreach($warnas as $warna)
                    <option value="{{ $warna->nama_warna }}" {{ old('warna') == $warna->nama_warna ? 'selected' : '' }}>{{ $warna->nama_warna }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Detail / Keterangan</label>
                <textarea name="detail" rows="3"
                          class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500"
                          placeholder="Masukkan detail tambahan material (opsional)...">{{ old('detail') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Foto Material</label>
                <input type="file" name="foto" accept="image/*"
                       class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
            </div>

            <div class="flex gap-4 pt-6">
                <a href="{{ route('admin.bahan.index') }}"
                   class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg font-bold text-center transition">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold transition shadow-lg shadow-red-900/20">
                    Simpan Material
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- 3. INISIALISASI SCRIPT SELECT2 --}}
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.searchable-select').select2({
                width: '100%', // Pastikan select2 mengisi lebar kontainer
                placeholder: "Pilih atau ketik untuk mencari...",
                allowClear: true
            });
        });
    </script>
@endpush