@extends('layouts.admin')


@section('title_header', 'Edit SPK Produksi')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-[#0a0a0a] border border-white/5 rounded-2xl p-6 shadow-xl">
        <h2 class="text-xl font-bold text-white mb-6">Edit SPK Produksi</h2>

        <form action="{{ route('admin.produksi.update', $produksi->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Produk Jadi</label>
                <select name="produk_jadi_id" required class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                    @foreach($produks as $produk)
                        <option value="{{ $produk->id }}" {{ $produksi->produk_jadi_id == $produk->id ? 'selected' : '' }}>
                            {{ $produk->nama_produk }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Jumlah Target</label>
                    <input type="number" step="0.01" name="jumlah_target" value="{{ $produksi->jumlah_target }}" required min="0.01"
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">

                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ $produksi->tanggal_mulai }}" required
                           class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-400 uppercase mb-2">Status</label>
                <select name="status" required class="w-full bg-black border border-white/10 rounded-lg px-4 py-3 text-white focus:border-red-500">
                    <option value="PENDING" {{ $produksi->status == 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="PROSES" {{ $produksi->status == 'PROSES' ? 'selected' : '' }}>Proses</option>
                    <option value="VALIDASI" {{ $produksi->status == 'VALIDASI' ? 'selected' : '' }}>Validasi</option>
                    <option value="SELESAI" {{ $produksi->status == 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>

            <div class="flex gap-4 pt-6">
                <a href="{{ route('admin.produksi.index') }}"
                   class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg font-bold text-center transition">
                    Batal
                </a>
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold transition shadow-lg shadow-red-900/20">
                    Update SPK
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
