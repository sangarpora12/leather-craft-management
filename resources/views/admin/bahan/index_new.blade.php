@extends('layouts.admin')

@section('content')
<div class="space-y-8 animate-fade-in text-gray-300 font-sans">

    {{-- HEADER & TITLE --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Master Data Bahan Baku</h1>
            <p class="text-sm text-gray-500">Kelola database material, stok desimal, dan nilai aset.</p>
        </div>
        
        <div class="flex gap-3">
            <button class="bg-[#1f2937] hover:bg-[#374151] text-white px-4 py-2 rounded-lg text-sm transition border border-gray-700">
                <i class="fa-solid fa-file-export mr-2"></i>Export
            </button>
            <button onclick="openModal('modalTambah')" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-medium transition shadow-lg shadow-blue-900/20 flex items-center">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Material
            </button>
        </div>
    </div>

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#1f242c] p-6 rounded-2xl border border-gray-800">
            <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-2">Total SKU</p>
            <h3 class="text-3xl font-bold text-white">{{ $bahans->total() }} <span class="text-sm font-normal text-gray-500">Item</span></h3>
        </div>
        <div class="bg-[#1e293b] p-6 rounded-2xl border border-blue-900/30">
            <p class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-2">Total Nilai Aset</p>
            <h3 class="text-3xl font-bold text-white">Rp {{ number_format($bahans->sum(function($item){ return $item->stok * $item->harga_satuan; }), 0, ',', '.') }}</h3>
        </div>
        <div class="bg-[#1f242c] p-6 rounded-2xl border border-rose-900/50">
            <p class="text-rose-400 text-xs font-bold uppercase tracking-wider mb-2">Stok Kritis</p>
            <h3 class="text-3xl font-bold text-rose-500">{{ $bahans->where('stok', '<=', 10)->count() }} <span class="text-sm font-normal text-rose-300/50">Item</span></h3>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-[#181b21] rounded-2xl border border-gray-800 overflow-hidden">
        <div class="p-5 border-b border-gray-800">
            <form action="{{ route('admin.bahan.index') }}" method="GET" class="flex gap-3">
                <select name="kategori" onchange="this.form.submit()" class="bg-[#0f1115] border border-gray-700 text-gray-300 px-4 py-2.5 rounded-lg text-sm w-48">
                    <option value="">Semua Kategori</option>
                    <option value="Kulit" {{ request('kategori') == 'Kulit' ? 'selected' : '' }}>Kulit</option>
                    <option value="Benang" {{ request('kategori') == 'Benang' ? 'selected' : '' }}>Benang</option>
                    <option value="Aksesoris" {{ request('kategori') == 'Aksesoris' ? 'selected' : '' }}>Aksesoris</option>
                </select>
                @if(request('kategori'))
                    <a href="{{ route('admin.bahan.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2.5 rounded-lg text-sm">Reset</a>
                @endif
            </form>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="text-xs text-gray-500 uppercase border-b border-gray-800 bg-[#1f242c]/50">
                    <th class="px-6 py-5">Nama Material</th>
                    <th class="px-6 py-5 text-center">Stok</th>
                    <th class="px-6 py-5">Satuan</th>
                    <th class="px-6 py-5 text-right">Harga Beli</th>
                    <th class="px-6 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 text-sm">
                @forelse($bahans as $b)
                <tr class="hover:bg-[#1f242c]">
                    <td class="px-6 py-4">
                        <span class="text-white font-medium">{{ $b->nama_material }}</span><br>
                        <span class="text-xs text-gray-500">{{ $b->kode_material }} - {{ $b->kategori }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="{{ $b->stok <= $b->stok_minimum ? 'text-rose-400' : 'text-emerald-400' }} font-bold">
                            {{ $b->stok }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $b->satuan }}</td>
                    <td class="px-6 py-4 text-right text-gray-300">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="openEditModal({{ json_encode($b) }})" class="text-yellow-500 hover:text-yellow-400 mr-2">
                            <i class="fa-solid fa-pen-to-square"></i> Edit
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-500">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-800 text-xs text-gray-500">
            {{ $bahans->links() }}
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div id="modalTambah" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="fixed inset-0 bg-black/80" onclick="closeModal('modalTambah')"></div>
    <div class="relative bg-[#181b21] w-full max-w-lg rounded-2xl border border-gray-700 p-6 m-4">
        <h2 class="text-xl font-bold text-white mb-4">Tambah Material</h2>
        <form action="{{ route('admin.bahan.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 mb-1">NAMA MATERIAL</label>
                <input type="text" name="nama" required class="w-full bg-[#0f1115] border border-gray-700 rounded px-3 py-2 text-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">KATEGORI</label>
                    <select name="kategori" class="w-full bg-[#0f1115] border border-gray-700 rounded px-3 py-2 text-white">
                        <option value="Kulit">Kulit</option>
                        <option value="Benang">Benang</option>
                        <option value="Aksesoris">Aksesoris</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">SATUAN</label>
                    <select name="satuan" class="w-full bg-[#0f1115] border border-gray-700 rounded px-3 py-2 text-white">
                        <option value="Lembar">Lembar</option>
                        <option value="Meter">Meter</option>
                        <option value="Rol">Rol</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Kg">Kg</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">STOK AWAL</label>
                    <input type="number" step="any" name="stok" required class="w-full bg-[#0f1115] border border-gray-700 rounded px-3 py-2 text-white text-center">
                </div>
                <div>
                    <label class="block text-xs text-rose-400 mb-1">MINIMUM</label>
                    <input type="number" name="stok_minimum" value="5" required class="w-full bg-[#0f1115] border border-rose-900 rounded px-3 py-2 text-rose-400 text-center">
                </div>
            </div>
            <div>
                <label class="block text-xs text-blue-400 mb-1">HARGA SATUAN</label>
                <input type="number" name="harga_satuan" required class="w-full bg-[#0f1115] border border-blue-900 rounded px-3 py-2 text-blue-400 text-center">
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalTambah')" class="px-4 py-2 text-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="fixed inset-0 bg-black/80" onclick="closeModal('modalEdit')"></div>
    <div class="relative bg-[#181b21] w-full max-w-lg rounded-2xl border border-yellow-700 p-6 m-4">
        <h2 class="text-xl font-bold text-white mb-4">Edit Material</h2>
        <form id="formEdit" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs text-gray-400 mb-1">NAMA MATERIAL</label>
                <input type="text" id="edit_nama" name="nama" required class="w-full bg-[#0f1115] border border-gray-700 rounded px-3 py-2 text-white">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">KATEGORI</label>
                    <select id="edit_kategori" name="kategori" class="w-full bg-[#0f1115] border border-gray-700 rounded px-3 py-2 text-white">
                        <option value="Kulit">Kulit</option>
                        <option value="Benang">Benang</option>
                        <option value="Aksesoris">Aksesoris</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1">SATUAN</label>
                    <select id="edit_satuan" name="satuan" class="w-full bg-[#0f1115] border border-gray-700 rounded px-3 py-2 text-white">
                        <option value="Lembar">Lembar</option>
                        <option value="Meter">Meter</option>
                        <option value="Rol">Rol</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Kg">Kg</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">STOK</label>
                    <input type="number" step="any" id="edit_stok" name="stok" required class="w-full bg-[#0f1115] border border-gray-700 rounded px-3 py-2 text-white text-center">
                </div>
                <div>
                    <label class="block text-xs text-rose-400 mb-1">MINIMUM</label>
                    <input type="number" id="edit_stok_minimum" name="stok_minimum" required class="w-full bg-[#0f1115] border border-rose-900 rounded px-3 py-2 text-rose-400 text-center">
                </div>
            </div>
            <div>
                <label class="block text-xs text-yellow-500 mb-1">HARGA</label>
                <input type="number" id="edit_harga_satuan" name="harga_satuan" required class="w-full bg-[#0f1115] border border-yellow-700 rounded px-3 py-2 text-yellow-500 text-center">
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 text-gray-400">Batal</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { 
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
}
function closeModal(id) { 
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
}
function openEditModal(data) {
    openModal('modalEdit');
    document.getElementById('formEdit').action = "/admin/bahan/" + data.id;
    document.getElementById('edit_nama').value = data.nama_material;
    document.getElementById('edit_kategori').value = data.kategori;
    document.getElementById('edit_satuan').value = data.satuan;
    document.getElementById('edit_stok').value = data.stok;
    document.getElementById('edit_stok_minimum').value = data.stok_minimum;
    document.getElementById('edit_harga_satuan').value = data.harga_satuan;
}
</script>
@endsection
