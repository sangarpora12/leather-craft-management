@extends('layouts.admin')

@section('title', 'Edit Bahan Baku: ' . $bahan->nama_material)
@section('title_header', 'Edit Bahan Baku')

@push('styles')
<style>
/* ============================================================
   CUSTOM SELECT — Dark Theme, Bootstrap-proof
   ============================================================ */

.dd-wrap {
    position: relative;
    width: 100%;
}

/* Trigger box */
.dd-trigger {
    width: 100%;
    min-height: 46px;
    background: rgba(10,10,10,0.85);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 0.75rem;
    padding: 6px 38px 6px 12px;
    cursor: pointer;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    position: relative;
    transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box;
    user-select: none;
}
.dd-trigger:hover  { border-color: rgba(255,255,255,0.35); }
.dd-trigger.active { border-color: #FD730D; box-shadow: 0 0 0 3px rgba(253,115,13,.2); }

/* Arrow chevron */
.dd-arrow {
    position: absolute;
    right: 13px; top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    flex-shrink: 0;
    transition: transform .2s;
}
.dd-arrow svg { display: block; }
.dd-trigger.active .dd-arrow { transform: translateY(-50%) rotate(180deg); }

/* Placeholder & value text */
.dd-placeholder { color: #6B7280; font-size: .875rem; }
.dd-value       { color: #ffffff; font-size: .875rem; }

/* Chips (multi) */
.dd-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(253,115,13,.13);
    border: 1px solid rgba(253,115,13,.45);
    color: #FD730D;
    border-radius: .45rem;
    padding: 2px 7px 2px 9px;
    font-size: .78rem;
    font-weight: 500;
    line-height: 1.5;
    flex-shrink: 0;
    max-width: 180px;
    overflow: hidden;
    white-space: nowrap;
}
.dd-chip-x {
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    color: rgba(253,115,13,.55);
    font-size: 1rem;
    line-height: 1;
    display: flex;
    align-items: center;
    flex-shrink: 0;
    transition: color .15s;
}
.dd-chip-x:hover { color: #FD730D; }

/* Dropdown panel */
.dd-panel {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    left: 0; right: 0;
    background: #131313;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: .85rem;
    overflow: hidden;
    z-index: 99999;
    box-shadow: 0 12px 32px rgba(0,0,0,.7);
}
.dd-panel.open { display: block; }

/* Search inside panel */
.dd-search-wrap {
    padding: 8px;
    border-bottom: 1px solid rgba(255,255,255,.07);
    background: #131313;
}
.dd-search {
    display: block;
    width: 100%;
    box-sizing: border-box;
    background: #0c0e13 !important;
    background-color: #0c0e13 !important;
    border: 1px solid rgba(255,255,255,.13) !important;
    border-radius: .55rem !important;
    padding: 7px 12px !important;
    color: #fff !important;
    font-size: .875rem !important;
    outline: none !important;
    box-shadow: none !important;
    -webkit-appearance: none !important;
    appearance: none !important;
    transition: border-color .2s !important;
}
.dd-search:focus { border-color: rgba(253,115,13,.5) !important; }
.dd-search::placeholder { color: #4B5563 !important; opacity: 1 !important; }

/* Options list */
.dd-list {
    max-height: 210px;
    overflow-y: auto;
    background: #131313;
    padding: 4px 0;
}
.dd-list::-webkit-scrollbar       { width: 3px; }
.dd-list::-webkit-scrollbar-track { background: #131313; }
.dd-list::-webkit-scrollbar-thumb { background: #FD730D; border-radius: 3px; }

.dd-item {
    padding: 9px 14px;
    color: #C9D1D9;
    font-size: .875rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    background: #131313;
    transition: background .1s, color .1s;
    user-select: none;
}
.dd-item:hover     { background: rgba(253,115,13,.1); color: #FD730D; }
.dd-item.sel       { background: rgba(253,115,13,.07); color: #FD730D; }
.dd-item.sel:hover { background: rgba(253,115,13,.14); }

/* Checkbox indicator (multi) */
.dd-chk {
    width: 16px; height: 16px;
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 11px;
    background: transparent;
    transition: background .15s, border-color .15s;
}
.dd-item.sel .dd-chk { background: #FD730D; border-color: #FD730D; color: #fff; }

.dd-empty {
    padding: 14px;
    color: #4B5563;
    font-size: .875rem;
    text-align: center;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Header --}}
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('admin.bahan.index') }}" class="p-2 hover:bg-white/5 rounded-xl transition">
                            <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">Edit Bahan Baku</h1>
                    </div>
                    <p class="text-sm text-gray-400">
                        Perbarui detail untuk <strong class="text-orange-400 font-mono">{{ $bahan->kode_material }}</strong>
                    </p>
                </div>
            </div>

            {{-- Card --}}
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">
                <form action="{{ route('admin.bahan.update', $bahan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-6">

                        <div class="pt-1 pb-4 border-b border-white/10">
                            <h3 class="text-lg font-bold text-white">Informasi Dasar</h3>
                        </div>

                        {{-- Kode, Jenis & Nama --}}
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Kode Material</label>
                                <input type="text" value="{{ $bahan->kode_material }}" readonly
                                    class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-gray-500 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-300 mb-2">Nama Material <span class="text-red-400">*</span></label>
                                <input type="text" name="nama_material" required
                                    value="{{ old('nama_material', $bahan->nama_material) }}"
                                    class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition outline-none">
                            </div>
                        </div>

                        {{-- Kategori & Satuan --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- KATEGORI (multi) --}}
                          <div class="mb-4">
    <label class="text-xs text-gray-400">Kategori</label>
    <select name="kategori"
            class="w-full bg-gray-800 rounded-xl px-4 py-3 text-white">
        <option value="">Pilih Kategori</option>
        @foreach($kategories as $k)
            <option value="{{ $k->nama_kategori }}"
                {{ old('kategori', $bahan->kategori) == $k->nama_kategori ? 'selected' : '' }}>
                {{ $k->nama_kategori }}
            </option>
        @endforeach
    </select>
</div>
                            {{-- SATUAN (single) --}}
                            <div class="mb-4">
<label class="text-xs text-gray-400">Satuan</label>
<select name="satuan" class="w-full bg-gray-800 rounded-xl px-4 py-3 text-white">
@foreach($satuans as $s)
<option value="{{ $s->nama_satuan }}"
{{ old('satuan',$bahan->satuan)==$s->nama_satuan?'selected':'' }}>
{{ $s->nama_satuan }}
</option>
@endforeach
</select>
</div>

                        {{-- WARNA (multi) --}}
                        <div class="mb-4">
    <label class="text-xs text-gray-400">Warna</label>
    <select name="warna"
            class="w-full bg-gray-800 rounded-xl px-4 py-3 text-white">
        <option value="">Pilih Warna</option>
        @foreach($warnas as $w)
            <option value="{{ $w->nama_warna }}"
                {{ old('warna', $bahan->warna) == $w->nama_warna ? 'selected' : '' }}>
                {{ $w->nama_warna }}
            </option>
        @endforeach
    </select>
</div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Deskripsi / Detail</label>
                            <textarea name="detail" rows="3"
                                class="w-full bg-[#0a0a0a]/80 border border-white/20 rounded-xl px-4 py-3 text-white focus:border-orange-500 focus:ring-2 focus:ring-orange-500/30 transition outline-none resize-vertical">{{ old('detail', $bahan->detail) }}</textarea>
                        </div>

                        {{-- Foto --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-300 mb-2">Foto Material</label>
                            @if($bahan->foto)
                                <div class="my-2">
                                    <img src="{{ asset('storage/' . $bahan->foto) }}" alt="Foto"
                                        class="max-h-32 rounded-lg border border-white/10">
                                </div>
                            @endif
                            <input type="file" name="foto" accept="image/*"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-white/10 file:text-gray-300 hover:file:bg-white/20 transition cursor-pointer">
                        </div>

                    </div>{{-- end space-y-6 --}}

                    {{-- Footer --}}
                    <div class="flex gap-4 pt-8 mt-8 border-t border-white/10">
                        <a href="{{ route('admin.bahan.index') }}"
                            class="w-full bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-xl transition text-center">
                            Batal
                        </a>
                        <button type="submit"
                            class="w-full bg-[#FD730D] hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-xl transition">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script kustom jika diperlukan di masa mendatang
</script>
@endpush