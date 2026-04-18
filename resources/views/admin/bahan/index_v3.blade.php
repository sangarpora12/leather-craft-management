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
                    <option value="Bahan Utama" {{ request('kategori') == 'Bahan Utama' ? 'selected' : '' }}>Bahan Utama</option>
                    <option value="Aksesoris" {{ request('kategori') == 'Aksesoris' ? 'selected' : '' }}>Aksesoris</option>
                    <option value="Kimia" {{ request('kategori') == 'Kimia' ? 'selected' : '' }}>Kimia</option>
                    <option value="Packaging" {{ request('kategori') == 'Packaging' ? 'selected' : '' }}>Packaging</option>
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

<!-- MODAL TAMBAH - SAMA DENGAN FORM OPERATOR -->
<div id="modalTambah" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center;">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8);" onclick="closeModal('modalTambah')"></div>
    <div style="position: relative; background: #1A1A1A; width: 100%; max-width: 32rem; border-radius: 1.5rem; border: 1px solid rgba(255,255,255,0.1); padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; color: white; font-style: italic; text-transform: uppercase;">Input Stok Masuk</h2>
                <p style="font-size: 0.625rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em;">Catat bahan baku baru dari supplier</p>
            </div>
            <button onclick="closeModal('modalTambah')" style="color: #9ca3af; background: rgba(255,255,255,0.05); width: 2.5rem; height: 2.5rem; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1);">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.bahan.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            
            <!-- KODE MATERIAL (Readonly) -->
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Kode Material</label>
                <input type="text" name="kode_material" readonly placeholder="Otomatis terisi saat pilih bahan..." style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: #9ca3af; font-size: 0.875rem;">
            </div>

            <!-- NAMA MATERIAL -->
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Nama Bahan</label>
                <input type="text" id="nama_material_add" name="nama_material" placeholder="Ketik nama bahan..." required style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
            </div>

            <!-- SUPPLIER & REF/NOTA -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Supplier</label>
                    <input type="text" name="supplier" placeholder="Contoh: CV. Kulit Jaya" style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Ref / Nota</label>
                    <input type="text" name="ref_no" placeholder="No Nota / Barcode" style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
            </div>

            <!-- KATEGORI & SATUAN - SAMA DENGAN OPERATOR -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Kategori</label>
                    <select name="kategori" style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="Bahan Utama">Bahan Utama</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Kimia">Kimia</option>
                        <option value="Packaging">Packaging</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Satuan</label>
                    <select name="satuan" style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="Pcs">Pcs</option>
                        <option value="Lembar">Lembar</option>
                        <option value="Rol">Rol</option>
                        <option value="Kg">Kg</option>
                        <option value="Kaleng">Kaleng</option>
                    </select>
                </div>
            </div>

            <!-- STOK & HARGA - SAMA DENGAN OPERATOR -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Jumlah Masuk</label>
                    <input type="number" name="stok" placeholder="0" required style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem; color: white; font-size: 0.875rem; font-weight: bold; text-align: center;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Harga Beli (Rp)</label>
                    <input type="number" name="harga_satuan" placeholder="Rp" required style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem; color: white; font-size: 0.875rem; font-weight: bold; text-align: center;">
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #ea580c; color: white; font-weight: bold; padding: 1rem; border-radius: 1rem; font-size: 0.875rem; margin-top: 1rem; box-shadow: 0 10px 15px -3px rgba(234, 88, 12, 0.2);">
                Simpan Data
            </button>
        </form>
    </div>
</div>

<!-- MODAL EDIT - SAMA DENGAN FORM OPERATOR -->
<div id="modalEdit" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center;">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8);" onclick="closeModal('modalEdit')"></div>
    <div style="position: relative; background: #1A1A1A; width: 100%; max-width: 32rem; border-radius: 1.5rem; border: 1px solid rgba(255,255,255,0.1); padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; color: white; font-style: italic; text-transform: uppercase;">Edit Material</h2>
                <p style="font-size: 0.625rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em;">Ubah data bahan baku</p>
            </div>
            <button onclick="closeModal('modalEdit')" style="color: #9ca3af; background: rgba(255,255,255,0.05); width: 2.5rem; height: 2.5rem; border-radius: 50%; border: 1px solid rgba(255,255,255,0.1);">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form id="formEdit" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf @method('PUT')
            
            <!-- NAMA MATERIAL -->
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Nama Bahan</label>
                <input type="text" id="edit_nama_material" name="nama_material" required style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
            </div>

            <!-- SUPPLIER & REF/NOTA -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Supplier</label>
                    <input type="text" id="edit_supplier" name="supplier" placeholder="Contoh: CV. Kulit Jaya" style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Ref / Nota</label>
                    <input type="text" id="edit_ref_no" name="ref_no" placeholder="No Nota / Barcode" style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
            </div>

            <!-- KATEGORI & SATUAN -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Kategori</label>
                    <select id="edit_kategori" name="kategori" style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="Bahan Utama">Bahan Utama</option>
                        <option value="Aksesoris">Aksesoris</option>
                        <option value="Kimia">Kimia</option>
                        <option value="Packaging">Packaging</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Satuan</label>
                    <select id="edit_satuan" name="satuan" required style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="Pcs">Pcs</option>
                        <option value="Lembar">Lembar</option>
                        <option value="Rol">Rol</option>
                        <option value="Kg">Kg</option>
                        <option value="Kaleng">Kaleng</option>
                    </select>
                </div>
            </div>

            <!-- STOK & HARGA -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Jumlah</label>
                    <input type="number" step="any" id="edit_stok" name="stok" required style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem; color: white; font-size: 0.875rem; font-weight: bold; text-align: center;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.25rem;">Harga Beli (Rp)</label>
                    <input type="number" id="edit_harga_satuan" name="harga_satuan" required style="width: 100%; background: #111827; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem; color: white; font-size: 0.875rem; font-weight: bold; text-align: center;">
                </div>
            </div>

            <button type="submit" style="width: 100%; background: #ea580c; color: white; font-weight: bold; padding: 1rem; border-radius: 1rem; font-size: 0.875rem; margin-top: 1rem; box-shadow: 0 10px 15px -3px rgba(234, 88, 12, 0.2);">
                Update Data
            </button>
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
    document.getElementById('edit_nama_material').value = data.nama_material;
    document.getElementById('edit_supplier').value = data.supplier || '';
    document.getElementById('edit_ref_no').value = data.ref_no || '';
    document.getElementById('edit_kategori').value = data.kategori || 'Bahan Utama';
    document.getElementById('edit_satuan').value = data.satuan || 'Pcs';
    document.getElementById('edit_stok').value = data.stok;
    document.getElementById('edit_harga_satuan').value = data.harga_satuan;
}
</script>
@endsection
