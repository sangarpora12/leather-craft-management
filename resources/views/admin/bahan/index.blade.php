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
                <p class="text-gray-200 text-[10px] font-bold uppercase tracking-widest mb-2">Total Bahan </p>
                <h3 class="text-2xl font-extrabold text-white">{{ $bahans->total() }} <span class="text-sm font-normal text-gray-200">Item</span></h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#2D3139] flex items-center justify-center">
                <i class="fa-solid fa-boxes-stacked text-white text-xl"></i>
            </div>
        </div>

        <div class="bg-[#1A1C24] p-6 rounded-2xl border border-[#2D3139] shadow-xl flex justify-between items-center">
            <div>
                <p class="text-gray-200 text-[10px] font-bold uppercase tracking-widest mb-2">Nilai Aset Material</p>
                <h3 class="text-2xl font-extrabold text-[#00BFA5] whitespace-nowrap">Rp {{ number_format($totalAsetBahan ?? 0, 0, ',', '.') }}</h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#2D3139] border border-[#2D3139] flex items-center justify-center">
                <i class="fa-solid fa-sack-dollar text-white text-xl"></i>
            </div>
        </div>

        <div class="bg-[#1A1C24] p-6 rounded-2xl border border-[#FF5252]/30 shadow-xl flex justify-between items-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-[#FF5252]/5 rounded-bl-full"></div>
            <div class="relative z-10">
                <p class="text-[#FF5252] text-[10px] font-bold uppercase tracking-widest mb-2">Stok Kritis / Habis</p>
                <h3 class="text-2xl font-extrabold text-[#FF5252]">{{ $totalKritis ?? 0 }} <span class="text-sm font-normal text-red-200">Semua Bahan</span></h3>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-xl bg-[#FF5252]/10 border border-[#FF5252]/20 flex items-center justify-center relative z-10">
                <i class="fa-solid fa-clipboard-check text-[#FF5252] text-xl"></i>
            </div>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <form action="{{ route('admin.bahan.index') }}" method="GET" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Kolom Pencarian --}}
            <div class="lg:col-span-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode atau Nama Material..." class="w-full bg-[#1A1C24] border border-[#2D3139] rounded-lg px-4 py-2.5 text-xs text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all shadow-sm">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fa-solid fa-search text-gray-500 text-sm"></i>
                    </div>
                </div>
            </div>

            {{-- Filter Kategori --}}
            <div>
                <select name="kategori" onchange="this.form.submit()" class="w-full bg-[#1A1C24] border border-[#2D3139] rounded-lg px-4 py-2.5 text-xs font-medium text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all shadow-sm appearance-none">
                    <option value="">Semua Kategori</option>
                    @foreach($kategories as $kategori)
                        <option value="{{ $kategori->nama_kategori }}" {{ request('kategori') == $kategori->nama_kategori ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Warna --}}
            <div>
                <select name="warna" onchange="this.form.submit()" class="w-full bg-[#1A1C24] border border-[#2D3139] rounded-lg px-4 py-2.5 text-xs font-medium text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all shadow-sm appearance-none">
                    <option value="">Semua Warna</option>
                    @foreach($warnas as $warna)
                        <option value="{{ $warna->nama_warna }}" {{ request('warna') == $warna->nama_warna ? 'selected' : '' }}>{{ $warna->nama_warna }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status Stok --}}
            <div>
                <select name="status_stok" onchange="this.form.submit()" class="w-full bg-[#1A1C24] border border-[#2D3139] rounded-lg px-4 py-2.5 text-xs font-medium text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all shadow-sm appearance-none">
                    <option value="">Semua Status</option>
                    <option value="aman" {{ request('status_stok') == 'aman' ? 'selected' : '' }}>Stok Aman</option>
                    <option value="kritis" {{ request('status_stok') == 'kritis' ? 'selected' : '' }}>Stok Kritis</option>
                    <option value="habis" {{ request('status_stok') == 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>

            {{-- Info Stok Minimum --}}
            <div class="bg-[#2D3139] rounded-lg px-4 py-2.5 flex items-center justify-center">
                <span class="text-xs text-gray-100 uppercase tracking-wider font-semibold">Min. Stok: <span class="text-orange-200 font-bold">{{ \App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 5 }}</span></span>
            </div>
        </div>
        
        <div class="flex justify-between items-center mt-4">
            {{-- Dropdown Urutkan --}}
            <div>
                <select name="sort_by" onchange="this.form.submit()" class="bg-[#1A1C24] border border-[#2D3139] rounded-lg px-4 py-2.5 text-xs font-medium text-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all shadow-sm appearance-none">
                    <option value="terbaru" {{ request('sort_by') == 'terbaru' || !request('sort_by') ? 'selected' : '' }}>Urutkan: Terbaru</option>
                    <option value="stok_terbanyak" {{ request('sort_by') == 'stok_terbanyak' ? 'selected' : '' }}>Urutkan: Stok Terbanyak</option>
                    <option value="stok_sedikit" {{ request('sort_by') == 'stok_sedikit' ? 'selected' : '' }}>Urutkan: Stok Sedikit</option>
                    <option value="harga_termahal" {{ request('sort_by') == 'harga_termahal' ? 'selected' : '' }}>Urutkan: Harga Termahal</option>
                    <option value="harga_termurah" {{ request('sort_by') == 'harga_termurah' ? 'selected' : '' }}>Urutkan: Harga Termurah</option>
                </select>
            </div>

            {{-- Tombol Reset Filter --}}
            @if(request()->hasAny(['search', 'kategori', 'warna', 'status_stok', 'sort_by']))
                <a href="{{ route('admin.bahan.index') }}" class="bg-red-500/20 hover:bg-red-500/40 text-red-400 px-4 py-2.5 rounded-lg text-xs font-bold shadow-sm flex items-center transition">
                    <i class="fa-solid fa-xmark mr-2"></i>Reset Filter
                </a>
            @endif
        </div>
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
                        <th class="px-6 py-4 font-bold">Nama Bahan</th>
                        <th class="px-6 py-4 font-bold text-center">Stok</th>
                        <th class="px-6 py-4 font-bold text-center">Satuan (Unit)</th>
                        <th class="px-6 py-4 font-bold text-center">Warna</th>
                        <th class="px-6 py-4 font-bold text-right">Harga Beli</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2D3139] text-sm">
                    @forelse($bahans as $b)
                    @php
                        // Cek status kritis berdasarkan total_stok (SUM dari semua varian)
                        // IKUTI PENGATURAN GUDANG: stok_minimum_global adalah acuan utama
                        // (fallback 5 bila belum diset)
                        $batasStokItem = (int) (\App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 5);
                        $stokItem = (float) ($b->total_stok ?? $b->stok ?? 0);
                        $isKritis = $stokItem <= $batasStokItem;
                    @endphp
                    <tr class="hover:bg-[#20232A]/80 transition-colors cursor-pointer {{ $isKritis ? 'bg-[#FF5252]/5' : '' }}"
    data-href="{{ route('admin.bahan.show', $b->id) }}">
                        
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
                            @php
                                // Format cerdas: 150.50 -> 150.5, 150.00 -> 150
                                $fmtStok = function($v) {
                                    $v = (float) $v;
                                    if ($v == (int) $v) return number_format($v, 0);
                                    return rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');
                                };
                            @endphp
                            <span class="{{ $isKritis ? 'text-[#FF5252]' : 'text-[#00BFA5]' }} font-extrabold text-base">{{ $fmtStok($stokItem) }}</span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block bg-[#2D3139] text-gray-300 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-gray-700/50">{{ $b->satuan }}</span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            @php
                                // Hitung jumlah warna untuk kode_material ini
                                $jumlahWarna = DB::table('bahan_bakus')
                                    ->where('kode_material', $b->kode_material)
                                    ->where('is_inventory', 1)
                                    ->whereNotNull('warna')
                                    ->where('warna', '!=', '')
                                    ->where('warna', '!=', 'Tidak Ada Warna')
                                    ->distinct('warna')
                                    ->count('warna');
                            @endphp
                            @if($jumlahWarna > 0)
                                <span class="inline-block bg-purple-500/20 text-purple-300 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-purple-500/30">
                                    {{ $jumlahWarna }} Warna
                                </span>
                            @else
                                <span class="inline-block bg-gray-500/20 text-gray-400 text-[10px] font-bold px-3 py-1.5 rounded-lg border border-gray-500/30">
                                    Tidak Ada
                                </span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 text-right text-gray-300 text-xs font-semibold">
                            Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}
                        </td>
                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-16 text-gray-500 text-xs italic border-dashed border-t border-[#2D3139]">
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





@endsection



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make table rows clickable
        const rows = document.querySelectorAll('tr[data-href]');
        rows.forEach(row => {
            row.addEventListener('click', function(event) {
                // Prevent click from triggering on interactive elements inside the row (like buttons or links in cells)
                if (event.target.closest('a, button')) {
                    return;
                }
                window.location.href = this.dataset.href;
            });
        });
    });

    function confirmDelete(id) {
        // This function is called by a button, so we need to stop event propagation
        event.stopPropagation();
        Swal.fire({
            title: 'Anda Yakin?',
            text: "Tindakan ini tidak dapat dibatalkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#FF5252',
            cancelButtonColor: '#4A5568',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#1A1C24',
            color: '#FFFFFF'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    function viewStockLayers(bahanId, bahanNama) {
        // This function is called by a button, so we need to stop event propagation
        event.stopPropagation();
        const url = `/admin/bahan/${bahanId}/stock-layers`;

        Swal.fire({
            title: `<i class="fa-solid fa-layer-group text-orange-400"></i> Detail Stok: ${bahanNama}`,
            html: '<div class="text-center text-gray-400">Memuat data...</div>',
            showConfirmButton: false,
            background: '#1A1C24',
            color: '#FFFFFF',
            didOpen: () => {
                Swal.showLoading();
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.layers && data.layers.length > 0) {
                            let tableHtml = `
                                <div class="overflow-x-auto custom-scrollbar mt-4 border border-gray-700 rounded-lg">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-gray-800 text-xs text-gray-400 uppercase">
                                            <tr>
                                                <th class="px-4 py-2">Tgl. Masuk</th>
                                                <th class="px-4 py-2 text-right">Jml. Awal</th>
                                                <th class="px-4 py-2 text-right">Sisa Stok</th>
                                                <th class="px-4 py-2 text-right">Harga Beli</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-700">`;
                            data.layers.forEach(layer => {
                                tableHtml += `
                                    <tr class="hover:bg-gray-700/50">
                                        <td class="px-4 py-2">${new Date(layer.created_at).toLocaleDateString('id-ID')}</td>
                                        <td class="px-4 py-2 text-right">${parseFloat(layer.jumlah_awal)}</td>
                                        <td class="px-4 py-2 text-right font-bold text-orange-400">${parseFloat(layer.sisa_stok)}</td>
                                        <td class="px-4 py-2 text-right">Rp ${new Intl.NumberFormat('id-ID').format(layer.harga_satuan)}</td>
                                    </tr>`;
                            });
                            tableHtml += `</tbody></table></div>`;
                            Swal.update({
                                html: tableHtml,
                                showConfirmButton: true,
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#4A5568',
                            });
                        } else {
                            Swal.update({
                                html: '<div class="text-center text-gray-400 mt-4">Tidak ada detail stok (layer) untuk item ini.</div>',
                                showConfirmButton: true,
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#4A5568',
                            });
                        }
                        Swal.hideLoading();
                    })
                    .catch(error => {
                        console.error('Error fetching stock layers:', error);
                        Swal.update({
                            html: '<div class="text-center text-red-400 mt-4">Gagal memuat data. Silakan coba lagi.</div>',
                            showConfirmButton: true,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#4A5568',
                        });
                        Swal.hideLoading();
                    });
            }
        });
    }
</script>
@endpush