@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-[#1E2129] p-6 sm:p-8 rounded-2xl shadow-lg">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b border-gray-700">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-white">Detail Bahan Baku</h1>
                <p class="text-sm text-gray-400 mt-1">Informasi lengkap mengenai material.</p>
            </div>
            <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                <a href="{{ route('admin.bahan.index') }}" class="bg-gray-600 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out text-sm">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="{{ route('admin.bahan.edit', $bahan->id) }}" class="bg-orange-500 hover:bg-orange-400 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out text-sm">
                    <i class="fa-solid fa-pen-to-square mr-2"></i>Edit
                </a>
                <button type="button" onclick="if(confirm('Yakin ingin menghapus bahan ini?')){document.getElementById('delete-form-{{ $bahan->id }}').submit();}" class="bg-red-500/20 hover:bg-red-500/40 text-red-400 font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out text-sm">
                    <i class="fa-solid fa-trash-alt mr-2"></i>Hapus
                </button>
            </div>
        </div>

        <form id="delete-form-{{ $bahan->id }}" action="{{ route('admin.bahan.destroy', $bahan->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Column: Photo and Identitas -->
            <div class="lg:col-span-1">
                <div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50 sticky top-8">
                    @php
                        $fotoMaster = $bahan->foto;
                    @endphp
                    <div class="aspect-square w-full bg-gray-800 rounded-lg mb-6 overflow-hidden border border-gray-700 flex items-center justify-center">
                        @if($fotoMaster)
                            <img src="{{ asset('storage/' . $fotoMaster) }}" alt="{{ $bahan->nama_material }}" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-box-open text-gray-600 text-6xl"></i>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-xl font-bold text-white tracking-tight">{{ $bahan->nama_material }}</h2>
                        <span class="inline-block px-2 py-1 bg-gray-700 text-gray-400 text-[10px] font-mono rounded uppercase tracking-wider">{{ $bahan->kode_material }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details, Ringkasan, Variants -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Details Card -->
                <div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50">
                    <h3 class="text-lg font-semibold text-white mb-4 border-b border-gray-600 pb-2">Informasi Detail</h3>
                    <div class="grid grid-cols-2 gap-6 text-sm">
                        <div>
                            <p class="text-gray-400 text-xs mb-1 uppercase tracking-tighter">Kategori</p>
                            <p class="text-white font-medium">{{ $bahan->kategori ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs mb-1 uppercase tracking-tighter">Satuan</p>
                            <p class="text-white font-medium">{{ $bahan->satuan }}</p>
                        </div>
                        <div class="col-span-2">
                            @php
                                // $varianWarna SUDAH termasuk $bahan (tidak ada where exclude)
                                // Jadi cukup SUM semua varian, TIDAK boleh ditambah $bahan->stok lagi
                                $totalStokSemuaWarna = isset($varianWarna) ? (float) $varianWarna->sum('stok') : (float) $bahan->stok;
                                // Format cerdas: 150.50 -> 150.5, 150.00 -> 150
                                $formatStok = function($val) {
                                    $val = (float) $val;
                                    if ($val == (int) $val) return number_format($val, 0);
                                    return rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.');
                                };
                            @endphp
                            <p class="text-orange-400 font-bold text-2xl mb-3">{{ $formatStok($totalStokSemuaWarna) }} <span class="text-base text-gray-400 font-normal">{{ $bahan->satuan }}</span></p>
                            <p class="text-gray-400 text-xs mb-1 uppercase tracking-tighter">Varian Warna</p>
                            <div class="space-y-2 mt-2">
                                @if(isset($varianWarna) && $varianWarna->count() > 0)
                                    @foreach($varianWarna as $v)
                                        <div class="flex items-center gap-3">
                                            <span class="w-3 h-3 rounded-full inline-block" style="background-color: {{ $v->warna && $v->warna !== 'Tidak Ada Warna' ? '#' . substr(md5($v->warna), 0, 6) : '#6b7280' }};"></span>
                                            <span class="text-white font-bold text-base">{{ $v->warna ?? 'Tidak Ada Warna' }}</span>
                                            <span class="text-emerald-400 font-bold text-lg">({{ (int) $v->stok }})</span>
                                        </div>
                                    @endforeach
                                @else
                                    <span class="text-gray-500">Hanya varian ini</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-span-2">
                            <p class="text-gray-400 text-xs mb-1 uppercase tracking-tighter">Detail Tambahan</p>
                            <p class="text-gray-300 leading-relaxed">{{ $bahan->detail ?? 'Tidak ada detail tambahan.' }}</p>
                        </div>
                    </div>
                </div>
<!-- Ringkasan Per Harga Beli -->
@if(isset($ringkasanPerHarga) && $ringkasanPerHarga->count() > 0)
<div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50">
    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
        <i class="fa-solid fa-chart-simple text-emerald-400 mr-3"></i>Ringkasan Per Harga Beli
    </h3>
    <div class="space-y-3">
        @foreach($ringkasanPerHarga as $r)
        <div class="flex items-center justify-between p-4 rounded-lg {{ $loop->first ? 'bg-emerald-500/10 border border-emerald-500/20' : 'bg-gray-800/40 border border-white/5' }}">
            <!-- Sisi Kiri: Harga dan Badge Warna -->
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="text-white font-bold text-base">Rp {{ number_format($r->harga_beli, 0, ',', '.') }}</span>
                    @if($loop->first)
                        <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[9px] font-bold rounded border border-emerald-500/30 uppercase">Terakhir</span>
                    @endif
                </div>
                <div>
                    <span class="px-2 py-1 bg-gray-700/50 text-gray-300 text-[10px] font-medium rounded border border-white/10 uppercase tracking-tight">
                        {{ $r->warna ?? 'Tidak Ada Warna' }}
                    </span>
                </div>
            </div>

            <!-- Sisi Kanan: Statistik Stok -->
            <div class="flex items-center gap-4 text-right">
                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-500 uppercase font-bold">Masuk</span>
                    <span class="text-white font-bold text-sm">{{ (float) $r->total_masuk }}</span>
                </div>
                <div class="w-px h-8 bg-gray-700"></div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-gray-500 uppercase font-bold">Sisa</span>
                    <span class="{{ (float) $r->total_sisa > 0 ? 'text-orange-400' : 'text-gray-500' }} font-bold text-sm">
                        {{ (float) $r->total_sisa }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

                <!-- LAYER FIFO STOK -->
@if(isset($semuaLayer) && $semuaLayer->count() > 0)
<div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50">
    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
        <i class="fa-solid fa-layer-group text-purple-400 mr-3"></i>Layer FIFO Stok
    </h3>
    <div class="overflow-x-auto rounded-lg border border-gray-800">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-800/50 text-[10px] text-gray-500 uppercase tracking-widest">
                <tr>
                    <th class="px-4 py-3">Tgl Masuk</th>
                    <th class="px-4 py-3">Warna</th> {{-- Kolom Warna Baru --}}
                    <th class="px-4 py-3 text-right">Qty Masuk</th>
                    <th class="px-4 py-3 text-right">Qty Sisa</th>
                    <th class="px-4 py-3 text-right">Harga Beli</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($semuaLayer as $layer)
                <tr class="hover:bg-gray-800/30 transition">
                    <td class="px-4 py-3 text-white">{{ \Carbon\Carbon::parse($layer->tanggal_masuk)->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="text-[10px] font-medium text-gray-400 uppercase">
                            {{ $layer->material->warna ?? 'Tidak Ada Warna' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-white font-medium">{{ (float) $layer->qty_masuk }}</td>
                    <td class="px-4 py-3 text-right {{ (float) $layer->qty_sisa > 0 ? 'text-orange-400 font-bold' : 'text-gray-600' }}">
                        {{ (float) $layer->qty_sisa }}
                    </td>
                    <td class="px-4 py-3 text-right text-emerald-400 font-bold">Rp {{ number_format($layer->harga_beli, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

                <!-- VARIAN WARNA TABLE -->
                @if(isset($varianWarna) && $varianWarna->count() > 0)
                <div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50">
                    <h3 class="text-lg font-semibold text-white mb-5 flex items-center">
                        <i class="fa-solid fa-palette text-blue-400 mr-3"></i>Varian Warna Lainnya
                    </h3>
                    <div class="overflow-hidden rounded-lg border border-gray-800">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-800/50 text-[10px] text-gray-500 uppercase tracking-widest">
                                <tr>
                                    <th class="px-4 py-3">Nama Material</th>
                                    <th class="px-4 py-3">Warna</th>
                                    <th class="px-4 py-3 text-right">Sisa Stok</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @foreach($varianWarna as $v)
                                <tr class="hover:bg-gray-800/30 transition">
                                    <td class="px-4 py-3 text-white font-medium">{{ $v->nama_material }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 bg-blue-600/20 text-blue-400 text-[10px] rounded border border-blue-600/30">
                                            {{ $v->warna ?? 'Tidak Ada Warna' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold {{ (int) $v->stok > 0 ? 'text-orange-400' : 'text-gray-600' }}">
                                        {{ (int) $v->stok }} <span class="text-gray-500 font-normal text-xs">{{ $v->satuan }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.bahan.edit-varian', $v->id) }}" class="inline-block bg-gray-700/50 hover:bg-orange-500 text-white p-2 rounded-lg transition duration-200" title="Edit Warna">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function deleteBahan(event, id) {
    event.preventDefault();
    event.stopPropagation();
    
    // SweetAlert2 sederhana tapi elegan
    Swal.fire({
        title: 'Hapus Bahan Baku',
        text: 'Anda akan menghapus bahan ini secara permanen. Tindakan tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        background: '#1f2937',
        color: '#ffffff',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    }).catch(error => {
        console.error('SweetAlert error:', error);
        // Fallback ke confirm biasa
        if (confirm('Yakin ingin menghapus bahan ini?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endsection