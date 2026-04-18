@extends('layouts.operator_app')

@section('title', 'Edit Stok')

@section('content')
<div class="px-5 pt-4">

        <header class="py-4 sticky top-0 z-40 flex items-center gap-4 bg-[#0F1115]/95 backdrop-blur border-b border-white/10 -mx-5 px-5">
            <a href="{{ route('operator.bahan.index') }}" class="w-8 h-8 flex items-center justify-center bg-white/5 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-lg font-bold text-white">Edit Data Stok</h1>
                <p class="text-[10px] text-gray-400 font-mono">{{ $bahan->kode_material }}</p>
            </div>
        </header>

        <main class="pb-24 pt-6">
            
            <form action="{{ route('operator.bahan.update', $bahan->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider pl-1">Nama Material</label>
                    <input type="text" name="nama_material" value="{{ old('nama_material', $bahan->nama_material) }}" required
                           class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider pl-1">Stok Fisik</label>
                        <input type="number" name="stok" value="{{ old('stok', $bahan->stok) }}" required
                               class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 transition font-bold text-center">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-red-400 uppercase tracking-wider pl-1">Min. Stok (Alert)</label>
                        <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $bahan->stok_minimum ?? 10) }}"
                               class="w-full bg-[#1A1A1A] border border-red-500/30 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-red-500 transition font-bold text-center text-red-400">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider pl-1">Kategori</label>
                    <select name="kategori" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 appearance-none">
                        @foreach(['Kulit Asli', 'Kulit Sintetis', 'Aksesoris Logam', 'Benang', 'Bahan Kimia/Lem', 'Kain Lining', 'Lainnya'] as $kat)
                            <option value="{{ $kat }}" {{ old('kategori', $bahan->kategori) == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider pl-1">Satuan</label>
                        <select name="satuan" class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 appearance-none">
                            @foreach(['Pcs', 'Lembar', 'Meter', 'Roll', 'Kg', 'Kaleng', 'Liter', 'Pasang'] as $satuan)
                                <option value="{{ $satuan }}" {{ old('satuan', $bahan->satuan) == $satuan ? 'selected' : '' }}>{{ $satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider pl-1">Harga (Rp)</label>
                        <input type="text" name="harga_satuan" id="harga_satuan" 
                               value="{{ number_format(old('harga_satuan', $bahan->harga_satuan), 0, ',', '.') }}" required
                               class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 transition text-right font-mono">
                    </div>
                </div>

                <div class="space-y-4 pt-2 border-t border-white/5">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider pl-1">Supplier</label>
                        <input type="text" name="supplier" value="{{ old('supplier', $bahan->supplier) }}" placeholder="Nama Toko"
                               class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 transition">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-wider pl-1">No. Referensi / Nota</label>
                        <input type="text" name="ref_no" value="{{ old('ref_no', $bahan->ref_no) }}" placeholder="INV-001"
                               class="w-full bg-[#1A1A1A] border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-blue-500 transition">
                    </div>
                </div>

                <div class="fixed bottom-0 left-0 right-0 max-w-md mx-auto p-5 bg-[#111]/95 backdrop-blur border-t border-white/10 z-50 safe-bottom">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-900/40 transition active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </div>
                
                <div class="h-10"></div> 

            </form>
        </main>

</div>

    <script>
        const hargaInput = document.getElementById('harga_satuan');
        hargaInput.addEventListener('keyup', function(e) {
            hargaInput.value = formatRupiah(this.value);
        });

        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split    = number_string.split(','),
                sisa     = split[0].length % 3,
                rupiah   = split[0].substr(0, sisa),
                ribuan   = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }
    </script>

@endsection