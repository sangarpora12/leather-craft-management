@extends('layouts.operator_app')

@section('content')
<div class="bg-[#0B0E14] min-h-screen text-white">

    <!-- HEADER -->
    <header class="h-16 bg-[#111111] border-b border-white/5 flex items-center justify-between px-4 flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('operator.bahan.index') }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-white">Edit Bahan</h1>
                <p class="text-[10px] text-gray-400 font-mono">{{ $bahan->kode_material }}</p>
            </div>
        </div>
    </header>

    <!-- FORM -->
    <div class="px-4 py-5">
        <div class="bg-[#151515] border border-white/5 rounded-2xl p-5">
            <form action="{{ route('operator.bahan.update', $bahan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="mb-5 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Informasi Dasar -->
                <div class="pb-4 mb-4 border-b border-white/10">
                    <h3 class="text-sm font-bold text-white">Informasi Dasar</h3>
                </div>

                <!-- Kode + Nama -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5">Kode Material</label>
                        <input type="text" value="{{ $bahan->kode_material }}" readonly
                            class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-gray-500 text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5">Nama Material <span class="text-red-400">*</span></label>
                        <input type="text" name="nama_material" required
                            value="{{ old('nama_material', $bahan->nama_material) }}"
                            class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition outline-none">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5">Kategori</label>
                        <select name="kategori"
                                class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition outline-none">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategories as $k)
                                <option value="{{ $k->nama_kategori }}"
                                    {{ old('kategori', $bahan->kategori) == $k->nama_kategori ? 'selected' : '' }}>
                                    {{ $k->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Satuan -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5">Satuan</label>
                        <select name="satuan"
                                class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition outline-none">
                            @foreach($satuans as $s)
                                <option value="{{ $s->nama_satuan }}"
                                    {{ old('satuan', $bahan->satuan) == $s->nama_satuan ? 'selected' : '' }}>
                                    {{ $s->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stok & Harga -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 mb-1.5">Stok</label>
                            <input type="number" name="stok"
                                value="{{ old('stok', $bahan->stok) }}"
                                class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-white text-sm text-center focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 mb-1.5">Harga Satuan</label>
                            <input type="text" id="harga_satuan" name="harga_satuan"
                                value="{{ old('harga_satuan', number_format($bahan->harga_satuan, 0, ',', '.')) }}"
                                class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-white text-sm text-right focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition outline-none">
                        </div>
                    </div>

                    <!-- Deskripsi / Detail -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5">Deskripsi / Detail</label>
                        <textarea name="detail" rows="3"
                            class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-white text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition outline-none resize-vertical">{{ old('detail', $bahan->detail) }}</textarea>
                    </div>

                    <!-- Foto -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1.5">Foto Material</label>
                        @if($bahan->foto)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $bahan->foto) }}" alt="Foto"
                                    class="w-full h-40 object-cover rounded-xl border border-white/10">
                            </div>
                        @endif
                        <input type="file" name="foto" id="foto_input" accept="image/*"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-white/10 file:text-gray-300 hover:file:bg-white/20 transition cursor-pointer">
                    </div>

                    <!-- Preview Foto Baru -->
                    <div id="preview-container" class="hidden">
                        <img id="preview-img" class="w-full h-40 object-cover rounded-xl border border-white/10">
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex gap-4 pt-6 mt-6 border-t border-white/10">
                    <a href="{{ route('operator.bahan.index') }}"
                        class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl transition text-center text-sm">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full bg-[#FD730D] hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl transition text-sm">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// FORMAT HARGA RIBUAN
const harga = document.getElementById('harga_satuan');
if (harga) {
    harga.addEventListener('input', function() {
        let angka = this.value.replace(/\D/g, '');
        this.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    });
}

// PREVIEW FOTO SEBELUM UPLOAD
const fotoInput = document.getElementById('foto_input');
const previewContainer = document.getElementById('preview-container');
const previewImg = document.getElementById('preview-img');

if (fotoInput) {
    fotoInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.classList.add('hidden');
            previewImg.src = '';
        }
    });
}

// BERSIHKAN TITIK SEBELUM SUBMIT
document.querySelector('form').addEventListener('submit', function() {
    if (harga) {
        harga.value = harga.value.replace(/\./g, '');
    }
});
</script>
@endpush