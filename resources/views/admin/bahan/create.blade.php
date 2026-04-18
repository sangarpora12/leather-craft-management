@extends('layouts.admin')

@section('title_header', 'Tambah Bahan Baku')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl">
        <h2 class="text-xl font-bold text-white mb-6">Tambah Material Baru</h2>

        <form action="{{ route('admin.bahan.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Nama Material</label>
                <input type="text" name="nama" required
                       class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Stok Awal</label>
                    <input type="number" name="stok" value="0" required
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Satuan</label>
                    <input type="text" name="satuan" placeholder="Pcs, Meter, dll" required
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                </div>
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
