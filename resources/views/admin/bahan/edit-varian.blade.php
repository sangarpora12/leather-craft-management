@extends('layouts.admin')

@section('title', 'Edit Varian: ' . $bahan->nama_material)
@section('title_header', 'Edit Varian Warna')

@push('styles')
<style>
/* Checkerboard background for color swatches so white/light colors are visible */
.checker-bg {
    background-image:
        linear-gradient(45deg, #808080 25%, transparent 25%),
        linear-gradient(-45deg, #808080 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #808080 75%),
        linear-gradient(-45deg, transparent 75%, #808080 75%);
    background-size: 10px 10px;
    background-position: 0 0, 0 5px, 5px -5px, -5px 0px;
}
</style>
@endpush

@section('content')
<div class="container px-4 py-6 mx-auto max-w-lg">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 shadow-2xl shadow-black/50">
        
        <!-- Header -->
        <div class="mb-6 pb-4 border-b border-white/10">
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.bahan.show', $bahan->id) }}" class="p-2 hover:bg-white/5 rounded-xl transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-white">Edit Varian Warna</h1>
            </div>
            <p class="text-xs text-gray-500 ml-11">
                <span class="text-orange-400 font-mono">{{ $bahan->kode_material }}</span> — {{ $bahan->nama_material }}
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.bahan.update-varian', $bahan->id) }}" method="POST">
            @csrf

            <!-- Warna -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-300 mb-3">Warna Varian</label>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 bg-[#0a0a0a]/60 border border-white/10 rounded-xl px-4 py-3 w-full">
                        @php
                            $warnaValue = old('warna', $bahan->warna);
                        @endphp
                        @if($warnaValue)
                            <span class="inline-block w-9 h-9 rounded-full border-2 border-white/30 flex-shrink-0 checker-bg" style="background-color: {{ $warnaValue }}; background-blend-mode: normal;"></span>
                        @else
                            <span class="inline-block w-9 h-9 rounded-full border-2 border-dashed border-gray-600 flex-shrink-0 checker-bg"></span>
                        @endif
                        <select name="warna" id="warna_select"
                                class="w-full bg-transparent border-0 text-white text-sm focus:outline-none focus:ring-0">
                            <!-- Tambahkan class bg-dark pada option -->
                            <option value="" class="bg-[#1A1A1A] text-white">Pilih Warna</option>
                            @foreach($warnas as $w)
                                <option value="{{ $w->nama_warna }}" data-warna="{{ $w->nama_warna }}"
                                    class="bg-[#1A1A1A] text-white"
                                    {{ old('warna', $bahan->warna) == $w->nama_warna ? 'selected' : '' }}>
                                    {{ $w->nama_warna }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tombol -->
            <div class="flex gap-4 pt-4 border-t border-white/10">
                <a href="{{ route('admin.bahan.show', $bahan->id) }}"
                    class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-xl transition text-center text-sm">
                    Batal
                </a>
                <button type="submit"
                    class="w-full bg-[#FD730D] hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl transition text-sm">
                    Simpan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Live update swatch warna saat dropdown berubah
const warnaSelect = document.getElementById('warna_select');
const swatch = document.querySelector('.checker-bg');

if (warnaSelect && swatch) {
    warnaSelect.addEventListener('change', function() {
        const selected = this.value;
        if (selected) {
            swatch.style.backgroundColor = selected;
            swatch.classList.remove('border-dashed');
        } else {
            swatch.style.backgroundColor = 'transparent';
            swatch.classList.add('border-dashed');
        }
    });
}
</script>
@endpush
