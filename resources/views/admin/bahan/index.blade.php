@extends('layouts.admin')

@section('page_title', 'Bahan Baku')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="space-y-6 animate-fade-in font-sans">

    {{-- HEADER TITLE --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-2">
        <div>
            <p class="text-xs text-gray-500">Kelola database material, stok awal, dan batas reorder point.</p>
        </div>
        
        <div class="flex gap-3">
            {{-- Tombol Tambah Material dihapus karena input bahan hanya dilakukan oleh Operator --}}
        </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#1A1C24] p-6 rounded-2xl border border-[#2D3139] shadow-xl flex justify-between items-center">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2">Total SKU Material</p>
                <h3 class="text-2xl font-extrabold text-white">{{ $bahans->total() }} <span class="text-sm font-normal text-gray-500">Item</span></h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#2D3139] flex items-center justify-center">
                <i class="fa-solid fa-boxes-stacked text-white text-xl"></i>
            </div>
        </div>

        <div class="bg-[#1A1C24] p-6 rounded-2xl border border-[#2D3139] shadow-xl flex justify-between items-center">
            <div>
                <p class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-2">Nilai Aset Material</p>
                <h3 class="text-2xl font-extrabold text-[#00BFA5] whitespace-nowrap">Rp {{ number_format($totalAsetBahan ?? 0, 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#2D3139] border border-[#2D3139] flex items-center justify-center">
                <i class="fa-solid fa-money-bill-wave text-white text-xl"></i>
            </div>
        </div>

        <div class="bg-[#1A1C24] p-6 rounded-2xl border border-[#FF5252]/30 shadow-xl flex justify-between items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-[#FF5252]/5 rounded-bl-full"></div>
            <div class="relative z-10">
                <p class="text-[#FF5252] text-[10px] font-bold uppercase tracking-widest mb-2">Stok Kritis / Habis</p>
                <h3 class="text-2xl font-extrabold text-[#FF5252]">{{ $totalKritis ?? 0 }} <span class="text-sm font-normal text-red-900/50">Item</span></h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#FF5252]/10 border border-[#FF5252]/20 flex items-center justify-center relative z-10">
                <i class="fa-solid fa-clipboard-check text-[#FF5252] text-xl"></i>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <form action="{{ route('admin.bahan.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 mb-2">
        {{-- Kolom Pencarian --}}
        <div class="flex items-center bg-[#1A1C24] border border-[#2D3139] rounded-xl px-4 py-2.5 md:w-64 shadow-lg">
            <i class="fa-solid fa-search text-gray-500 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode, Nama..." class="bg-transparent border-none outline-none text-xs text-white placeholder-gray-500 ml-3 w-full">
        </div>

        {{-- Filter Kategori --}}
        <div class="flex items-center bg-[#1A1C24] border border-[#2D3139] rounded-xl px-4 py-2.5 shadow-lg">
            <i class="fa-solid fa-folder text-gray-400 text-xs mr-2"></i>
            <select name="kategori" onchange="this.form.submit()" class="bg-transparent border-none outline-none text-xs font-bold text-gray-300 w-32 cursor-pointer appearance-none">
                <option value="" class="bg-[#1A1C24]">Semua Kategori</option>
                @forelse($kategories as $kategori)
                <option value="{{ $kategori->nama_kategori }}" class="bg-[#1A1C24]" {{ request('kategori') == $kategori->nama_kategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                @empty
                <option value="" disabled class="bg-[#1A1C24] text-gray-500">Data kategori kosong</option>
                @endforelse
            </select>
            <i class="fa-solid fa-chevron-down text-gray-500 text-[10px] ml-2 pointer-events-none"></i>
        </div>

        {{-- Filter Status Stok --}}
        <div class="flex items-center bg-[#1A1C24] border border-[#2D3139] rounded-xl px-4 py-2.5 shadow-lg">
            <i class="fa-solid fa-layer-group text-gray-400 text-xs mr-2"></i>
            <select name="status_stok" onchange="this.form.submit()" class="bg-transparent border-none outline-none text-xs font-bold text-gray-300 w-32 cursor-pointer appearance-none">
                <option value="" class="bg-[#1A1C24]">Semua Status</option>
                <option value="aman" class="bg-[#1A1C24]" {{ request('status_stok') == 'aman' ? 'selected' : '' }}>Stok Aman</option>
                <option value="kritis" class="bg-[#1A1C24]" {{ request('status_stok') == 'kritis' ? 'selected' : '' }}>Stok Kritis</option>
                <option value="habis" class="bg-[#1A1C24]" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>Habis</option>
            </select>
            <i class="fa-solid fa-chevron-down text-gray-500 text-[10px] ml-2 pointer-events-none"></i>
        </div>

        {{-- Dropdown Urutkan --}}
        <div class="flex items-center bg-[#1A1C24] border border-[#2D3139] rounded-xl px-4 py-2.5 shadow-lg">
            <i class="fa-solid fa-arrow-down-wide-short text-gray-400 text-xs mr-2"></i>
            <select name="sort_by" onchange="this.form.submit()" class="bg-transparent border-none outline-none text-xs font-bold text-gray-300 w-36 cursor-pointer appearance-none">
                <option value="terbaru" class="bg-[#1A1C24]" {{ request('sort_by') == 'terbaru' || !request('sort_by') ? 'selected' : '' }}>Terbaru</option>
                <option value="stok_terbanyak" class="bg-[#1A1C24]" {{ request('sort_by') == 'stok_terbanyak' ? 'selected' : '' }}>Stok Terbanyak</option>
                <option value="stok_sedikit" class="bg-[#1A1C24]" {{ request('sort_by') == 'stok_sedikit' ? 'selected' : '' }}>Stok Sedikit</option>
                <option value="harga_termahal" class="bg-[#1A1C24]" {{ request('sort_by') == 'harga_termahal' ? 'selected' : '' }}>Harga Termahal</option>
                <option value="harga_termurah" class="bg-[#1A1C24]" {{ request('sort_by') == 'harga_termurah' ? 'selected' : '' }}>Harga Termurah</option>
            </select>
            <i class="fa-solid fa-chevron-down text-gray-500 text-[10px] ml-2 pointer-events-none"></i>
        </div>

        {{-- Tombol Reset Filter --}}
        @if(request('search') || request('kategori') || request('status_stok') || (request('sort_by') && request('sort_by') != 'terbaru'))
            <a href="{{ route('admin.bahan.index') }}" class="bg-[#2D3139] hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow-lg flex items-center transition">
                <i class="fa-solid fa-rotate-left mr-2"></i>Reset
            </a>
        @endif
    </form>

    {{-- MAIN TABLE CONTAINER --}}
    <div class="bg-[#1A1C24] rounded-2xl border border-[#2D3139] shadow-xl overflow-hidden">
        
        <div class="px-6 py-5 border-b border-[#2D3139] flex justify-between items-center bg-[#1A1C24]">
            <h2 class="text-sm font-bold text-white tracking-wide">Daftar Bahan Baku</h2>
            <span class="text-[11px] text-gray-500 font-medium">Total: {{ $bahans->total() }} Item</span>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="text-[10px] text-gray-500 uppercase tracking-widest border-b border-[#2D3139] bg-[#0D1117]">
                        <th class="px-6 py-4 font-bold">Foto</th>
                        <th class="px-6 py-4 font-bold">Kode Bahan</th>
                        <th class="px-6 py-4 font-bold">Nama Material</th>
                        <th class="px-6 py-4 font-bold text-center">Stok Saat Ini</th>
                        <th class="px-6 py-4 font-bold text-center">Satuan (Unit)</th>
                        <th class="px-6 py-4 font-bold text-center">Min. Stok</th>
                        <th class="px-6 py-4 font-bold text-right">Harga Beli</th>
                        <th class="px-6 py-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D3139] text-sm">
                    @forelse($bahans as $b)
                    @php
                        // Cek status kritis
                        // IKUTI PENGATURAN GUDANG: stok_minimum_global adalah acuan utama
                        // (fallback 5 bila belum diset)
                        $batasStokItem = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 5);
                        $isKritis = ((float) $b->stok) <= $batasStokItem;
                    @endphp
                    <tr class="hover:bg-[#20232A]/80 transition-colors {{ $isKritis ? 'bg-[#FF5252]/5' : '' }}">
                        
                        <td class="px-6 py-4">
                            @if($b->foto)
                                <img src="{{ asset('storage/' . $b->foto) }}" alt="Foto {{ $b->nama_material }}" class="w-12 h-12 rounded-lg object-cover border border-[#2D3139]">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-[#2D3139] flex items-center justify-center border border-gray-700/50">
                                    <i class="fa-solid fa-image text-gray-500 text-sm"></i>
                                </div>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            <span class="text-[#FD730D] font-extrabold text-xs tracking-wide">{{ $b->kode_material }}</span>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-white">{{ $b->nama_material }}</div>
                            <div class="text-[10px] text-gray-500 mt-1 font-medium">Kategori: {{ $b->kategori }}</div>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="{{ $isKritis ? 'text-[#FF5252]' : 'text-[#00BFA5]' }} font-extrabold text-base">{{ (float) $b->stok }}</span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block bg-[#2D3139] text-gray-300 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-gray-700/50">{{ $b->satuan }}</span>
                        </td>
                        
                        <td class="px-6 py-4 text-center text-gray-500 text-xs font-semibold">
                            {{ $batasStokItem }}
                        </td>
                        
                        <td class="px-6 py-4 text-right text-gray-300 text-xs font-semibold">
                            Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick='openEditModal(@json($b))' class="w-8 h-8 rounded-lg bg-[#2D3139] text-gray-400 hover:text-white hover:bg-gray-600 transition border border-gray-700/50 flex items-center justify-center" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <button onclick="viewStockLayers({{ $b->id }}, '{{ $b->nama_material }}')" class="w-8 h-8 rounded-lg bg-[#2D3139] text-gray-400 hover:text-white hover:bg-gray-600 transition border border-gray-700/50 flex items-center justify-center" title="View Detail / Layer">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                            <form id="delete-form-{{ $b->id }}" action="{{ route('admin.bahan.destroy', $b->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-16 text-gray-500 text-xs italic border-dashed border-t border-[#2D3139]">
                            Data material belum tersedia.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-[#2D3139] bg-[#1A1C24] text-xs text-gray-500">
            {{ $bahans->links() }}
        </div>
    </div>
</div>

@include('admin.bahan._stock-layers-modal')

<div id="modalEdit" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center;">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.8);" onclick="closeModal('modalEdit')"></div>
    <div style="position: relative; background: #1A1C24; width: 100%; max-width: 32rem; border-radius: 1.5rem; border: 1px solid #2D3139; padding: 1.5rem; margin: 1rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: bold; color: white;">Edit Data Bahan</h2>
                <p style="font-size: 0.625rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.1em; mt-1">Perbarui detail material</p>
            </div>
            <button onclick="closeModal('modalEdit')" style="color: #9ca3af; background: #2D3139; width: 2.5rem; height: 2.5rem; border-radius: 50%; border: 1px solid #374151; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form id="editForm" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.25rem;">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Kode Material</label>
                    <input type="text" id="edit-kode_material" name="kode_material" readonly style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: #9ca3af; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Nama Material</label>
                    <input type="text" id="edit-nama_material" name="nama_material" required style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Kategori</label>
                    <select id="edit-kategori" name="kategori" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategories as $kategori)
                            <option value="{{ $kategori->nama_kategori }}">{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Satuan</label>
                    <select id="edit-satuan" name="satuan" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
                        <option value="">-- Pilih Satuan --</option>
                        @foreach($satuans as $satuan)
                            <option value="{{ $satuan->nama_satuan }}">{{ $satuan->nama_satuan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 0.5rem;">Foto Bahan <span class="text-gray-500">(Opsional, biarkan kosong jika tidak ingin ganti)</span></label>
                <input type="file" name="foto" accept="image/*" style="width: 100%; background: #0D1117; border: 1px solid #2D3139; border-radius: 0.75rem; padding: 0.75rem 1rem; color: white; font-size: 0.875rem;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" onclick="closeModal('modalEdit')" style="background: #2D3139; color: white; padding: 0.6rem 1.25rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: bold; border: 1px solid #374151;">Batal</button>
                <button type="submit" style="background: #FD730D; color: white; padding: 0.6rem 1.25rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: bold;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openEditModal(bahan) {
        document.getElementById('editForm').action = '/admin/bahan/' + bahan.id;
        document.getElementById('edit-kode_material').value = bahan.kode_material;
        document.getElementById('edit-nama_material').value = bahan.nama_material;
        document.getElementById('edit-kategori').value = bahan.kategori;
        document.getElementById('edit-satuan').value = bahan.satuan;
        document.getElementById('modalEdit').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Anda yakin?',
            html: `Anda akan menghapus material <strong>${name}</strong> secara permanen. Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            background: '#1A1C24',
            color: '#ffffff'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush