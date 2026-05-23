@extends('layouts.operator_app')

@section('content')
<div class="bg-[#0B0E14] min-h-screen text-white">

    <!-- HEADER -->
    <header class="h-16 bg-[#111111] border-b border-white/5 flex items-center justify-between px-4 flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('operator.bahan.show', $bahan->id) }}" class="text-gray-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-base font-bold text-white">Edit Warna</h1>
                <p class="text-[10px] text-gray-400 font-mono">{{ $bahan->kode_material }}</p>
            </div>
        </div>
    </header>

    <!-- FORM -->
    <div class="px-4 py-5">
        <div class="bg-[#151515] border border-white/5 rounded-2xl p-5">

            @if ($errors->any())
                <div class="mb-5 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('operator.bahan.update-varian', $bahan->id) }}" method="POST">
                @csrf

                <!-- Nama Material (read only) -->
                <div class="mb-5 pb-4 border-b border-white/10">
                    <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">Nama Material</p>
                    <p class="text-sm text-white font-semibold">{{ $bahan->nama_material }}</p>
                </div>

                <!-- Warna -->
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-400 mb-3">Warna Varian</label>
                    <div class="flex items-center gap-3 bg-[#0a0a0a]/60 border border-white/10 rounded-xl px-4 py-3 w-full">
                        @php
                            $warnaValue = old('warna', $bahan->warna);
                            $selectedWarna = $warnas->firstWhere('nama_warna', $warnaValue);
                        @endphp
                        <span id="warnaSwatch" class="inline-block w-9 h-9 rounded-full border-2 border-white/30 flex-shrink-0" 
                            style="background-color: {{ $selectedWarna && $selectedWarna->kode_warna ? $selectedWarna->kode_warna : ($warnaValue ? '#666' : 'transparent') }};">
                        </span>
                        <select name="warna" id="warna_select" class="w-full bg-transparent border-0 text-white text-sm focus:outline-none focus:ring-0">
                            <option value="" class="bg-[#1C222D] text-white">Pilih Warna</option>
                            @foreach($warnas as $w)
                                <option value="{{ $w->nama_warna }}" 
                                    data-kode-warna="{{ $w->kode_warna }}"
                                    data-gambar="{{ $w->gambar }}"
                                    class="bg-[#1C222D] text-white"
                                    {{ old('warna', $bahan->warna) == $w->nama_warna ? 'selected' : '' }}>
                                    {{ $w->nama_warna }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Gambar Warna Preview -->
                    <div id="warnaImagePreview" class="mt-2 {{ $selectedWarna && $selectedWarna->gambar ? '' : 'hidden' }}">
                        <img id="warnaImage" class="w-16 h-16 rounded-lg border border-white/10 object-cover" 
                            src="{{ $selectedWarna && $selectedWarna->gambar ? '/storage/' . str_replace('storage/', '', $selectedWarna->gambar) : '' }}">
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex gap-4 pt-4 border-t border-white/10">
                    <a href="{{ route('operator.bahan.show', $bahan->id) }}"
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
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const warnaSelect = document.getElementById('warna_select');
    const swatch = document.getElementById('warnaSwatch');
    const warnaImagePreview = document.getElementById('warnaImagePreview');
    const warnaImage = document.getElementById('warnaImage');

    if (warnaSelect && swatch) {
        warnaSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const kodeWarna = selected.dataset.kodeWarna;
            const gambarWarna = selected.dataset.gambar;

            if (selected.value) {
                // Update lingkaran warna
                if (kodeWarna && kodeWarna.startsWith('#')) {
                    swatch.style.backgroundColor = kodeWarna;
                } else {
                    // Fallback berdasarkan nama
                    let hash = 0;
                    for (let i = 0; i < selected.text.length; i++) {
                        hash = selected.text.charCodeAt(i) + ((hash << 5) - hash);
                    }
                    const hue = Math.abs(hash) % 360;
                    swatch.style.backgroundColor = `hsl(${hue}, 70%, 50%)`;
                }

                // Update gambar warna jika ada
                if (gambarWarna && gambarWarna.trim() !== '') {
                    warnaImage.src = '/storage/' + gambarWarna.replace('storage/', '');
                    warnaImagePreview.classList.remove('hidden');
                } else {
                    warnaImagePreview.classList.add('hidden');
                    warnaImage.src = '';
                }
            } else {
                swatch.style.backgroundColor = 'transparent';
                warnaImagePreview.classList.add('hidden');
                warnaImage.src = '';
            }
        });
    }
});
</script>
@endpush
@endsection