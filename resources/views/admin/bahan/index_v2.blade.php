@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="space-y-8 animate-fade-in text-gray-300 font-sans">

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
                        <span class="{{ $b->stok <= $b->stok_minimum ? 'text-rose-400' : 'text-emerald-400' }} font-bold">{{ $b->stok }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $b->satuan }}</td>
                    <td class="px-6 py-4 text-right text-gray-300">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <button onclick='openEditModal(@json($b))' class="text-yellow-500 hover:text-yellow-400">
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

<!-- MODAL TAMBAH - Sama dengan form operator -->
<div id="modalTambah" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center;">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8);" onclick="closeModal('modalTambah')"></div>
    <div style="position: relative; background: #181b21; width: 100%; max-width: 32rem; border-radius: 1rem; border: 1px solid #374151; padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.25rem; font-weight: bold; color: white;">Tambah Material</h2>
            <button onclick="closeModal('modalTambah')" style="color: #9ca3af;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('admin.bahan.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            
            <!-- KODE MATERIAL (Readonly) -->
            <div>
                <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">KODE MATERIAL</label>
                <input type="text" name="kode_material" readonly placeholder="Otomatis terisi..." style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: #9ca3af;">
            </div>

            <!-- NAMA MATERIAL -->
            <div>
                <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">NAMA MATERIAL</label>
                <input type="text" name="nama" required style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
            </div>

            <!-- SUPPLIER & REF/NOTA -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">SUPPLIER</label>
                    <input type="text" name="supplier" placeholder="Contoh: CV. Kulit Jaya" style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">REF / NOTA</label>
                    <input type="text" name="ref_no" placeholder="No Nota / Barcode" style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
                </div>
            </div>

            <!-- KATEGORI & SATUAN -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">KATEGORI</label>
                    <select name="kategori" style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
                        <option value="Kulit">Kulit</option>
                        <option value="Benang">Benang</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">SATUAN</label>
                    <select name="satuan" required style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
                        <option value="Lembar">Lembar</option>
                        <option value="Meter">Meter</option>
                        <option value="Rol">Rol</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Kg">Kg</option>
                    </select>
                </div>
            </div>

            <!-- STOK & HARGA -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">STOK AWAL</label>
                    <input type="number" step="any" name="stok" required style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem; color: white; text-align: center;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #60a5fa; margin-bottom: 0.25rem;">HARGA SATUAN</label>
                    <input type="number" name="harga_satuan" required style="width: 100%; background: #0f1115; border: 1px solid #1e3a8a; border-radius: 0.5rem; padding: 0.5rem; color: #60a5fa; text-align: center;">
                </div>
            </div>

            <div style="padding-top: 1rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #374151;">
                <button type="button" onclick="closeModal('modalTambah')" style="padding: 0.5rem 1rem; font-size: 0.875rem; color: #9ca3af;">Batal</button>
                <button type="submit" style="padding: 0.5rem 1rem; background: #2563eb; color: white; font-size: 0.875rem; font-weight: bold; border-radius: 0.5rem;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="modalEdit" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center;">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8);" onclick="closeModal('modalEdit')"></div>
    <div style="position: relative; background: #181b21; width: 100%; max-width: 32rem; border-radius: 1rem; border: 1px solid #713f12; padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.25rem; font-weight: bold; color: white;"><span style="color: #eab308;">Edit</span> Material</h2>
            <button onclick="closeModal('modalEdit')" style="color: #9ca3af;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEdit" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf @method('PUT')
            
            <!-- NAMA MATERIAL -->
            <div>
                <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">NAMA MATERIAL</label>
                <input type="text" id="edit_nama" name="nama" required style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
            </div>

            <!-- SUPPLIER & REF/NOTA -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">SUPPLIER</label>
                    <input type="text" id="edit_supplier" name="supplier" placeholder="Contoh: CV. Kulit Jaya" style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">REF / NOTA</label>
                    <input type="text" id="edit_ref_no" name="ref_no" placeholder="No Nota / Barcode" style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
                </div>
            </div>

            <!-- KATEGORI & SATUAN -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">KATEGORI</label>
                    <select id="edit_kategori" name="kategori" style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
                        <option value="Kulit">Kulit</option>
                        <option value="Benang">Benang</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">SATUAN</label>
                    <select id="edit_satuan" name="satuan" required style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem 0.75rem; color: white;">
                        <option value="Lembar">Lembar</option>
                        <option value="Meter">Meter</option>
                        <option value="Rol">Rol</option>
                        <option value="Pcs">Pcs</option>
                        <option value="Kg">Kg</option>
                    </select>
                </div>
            </div>

            <!-- STOK & HARGA -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #9ca3af; margin-bottom: 0.25rem;">STOK</label>
                    <input type="number" step="any" id="edit_stok" name="stok" required style="width: 100%; background: #0f1115; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.5rem; color: white; text-align: center;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; color: #eab308; margin-bottom: 0.25rem;">HARGA</label>
                    <input type="number" id="edit_harga_satuan" name="harga_satuan" required style="width: 100%; background: #0f1115; border: 1px solid #713f12; border-radius: 0.5rem; padding: 0.5rem; color: #eab308; text-align: center;">
                </div>
            </div>

            <div style="padding-top: 1rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #374151;">
                <button type="button" onclick="closeModal('modalEdit')" style="padding: 0.5rem 1rem; font-size: 0.875rem; color: #9ca3af;">Batal</button>
                <button type="submit" style="padding: 0.5rem 1rem; background: #ca8a04; color: white; font-size: 0.875rem; font-weight: bold; border-radius: 0.5rem;">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
function openEditModal(data) {
    openModal('modalEdit');
    document.getElementById('formEdit').action = "/admin/bahan/" + data.id;
    document.getElementById('edit_nama').value = data.nama_material;
    document.getElementById('edit_supplier').value = data.supplier || '';
    document.getElementById('edit_ref_no').value = data.ref_no || '';
    document.getElementById('edit_kategori').value = data.kategori;
    document.getElementById('edit_satuan').value = data.satuan;
    document.getElementById('edit_stok').value = data.stok;
    document.getElementById('edit_harga_satuan').value = data.harga_satuan;
}
</script>
@endsection
