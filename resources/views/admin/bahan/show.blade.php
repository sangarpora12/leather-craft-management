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
                <button onclick="confirmDelete({{ $bahan->id }})" class="bg-red-500/20 hover:bg-red-500/40 text-red-400 font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out text-sm">
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
                        if (!$fotoMaster && isset($varianWarna)) {
                            $firstWithFoto = $varianWarna->firstWhere('foto', '!=', null);
                            if ($firstWithFoto) $fotoMaster = $firstWithFoto->foto;
                        }
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

            <!-- Right Column: Details, FIFO, Variants -->
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
                                $totalStokSemuaWarna = isset($varianWarna) ? $varianWarna->sum('stok') : $bahan->stok;
                            @endphp
                            <p class="text-orange-400 font-bold text-2xl mb-3">{{ floatval($totalStokSemuaWarna) }} <span class="text-base text-gray-400 font-normal">{{ $bahan->satuan }}</span></p>
                            <p class="text-gray-400 text-xs mb-1 uppercase tracking-tighter">Varian Warna</p>
                            <div class="space-y-2 mt-2">
                                @if(isset($varianWarna) && $varianWarna->count() > 0)
                                    @foreach($varianWarna as $v)
                                        <div class="flex items-center gap-3">
                                            <span class="w-3 h-3 rounded-full inline-block" style="background-color: {{ $v->warna && $v->warna !== 'Tidak Ada Warna' ? '#' . substr(md5($v->warna), 0, 6) : '#6b7280' }};"></span>
                                            <span class="text-white font-bold text-base">{{ $v->warna ?? 'Tidak Ada Warna' }}</span>
                                            <span class="text-emerald-400 font-bold text-lg">({{ floatval($v->stok) }})</span>
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

                <!-- FIFO Stock Layers -->
                <div class="bg-[#2D3139] p-6 rounded-xl border border-gray-700/50">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                        <i class="fa-solid fa-clock-rotate-left text-orange-400 mr-3"></i>Riwayat Stok (FIFO)
                    </h3>

                    @if(isset($ringkasanPerHarga) && $ringkasanPerHarga->count() > 0)
                    <div class="mb-6 bg-gray-800/30 rounded-lg p-4 border border-gray-700">
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-3">Ringkasan Per Harga Beli (FIFO)</p>
                        <div class="space-y-2">
                            @foreach($ringkasanPerHarga as $r)
                            <div class="flex items-center justify-between py-2 px-3 rounded-lg {{ $loop->first ? 'bg-emerald-500/10 border border-emerald-500/20' : '' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-white font-bold text-base">Rp {{ number_format($r->harga_beli, 0, ',', '.') }}</span>
                                    @if($loop->first)
                                        <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-[9px] rounded border border-emerald-500/30">TERAKHIR</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="text-gray-400 text-sm">Total Bahan: <span class="text-white font-bold">{{ floatval($r->total_masuk) }}</span></span>
                                    <span class="mx-2 text-gray-600">|</span>
                                    <span class="text-gray-400 text-sm">Sisa: <span class="{{ floatval($r->total_sisa) > 0 ? 'text-orange-400' : 'text-gray-600' }} font-bold">{{ floatval($r->total_sisa) }}</span></span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Tabel Detail FIFO -->
                    <div class="overflow-hidden rounded-lg border border-gray-800">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-800/50 text-[10px] text-gray-500 uppercase tracking-widest">
                                <tr>
                                    <th class="px-4 py-3">Periode</th>
                                    <th class="px-4 py-3 text-right">Total Bahan</th>
                                    <th class="px-4 py-3 text-right">Sisa Stok</th>
                                    <th class="px-4 py-3 text-right">Harga Beli</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                @php
                                    $layers = isset($semuaLayer) ? $semuaLayer->where('qty_sisa', '>', 0) : $bahan->stockLayers->where('qty_sisa', '>', 0)->sortByDesc('created_at');
                                    $grouped = $layers->groupBy('harga_beli');
                                @endphp
                                @forelse($grouped as $harga => $items)
                                @php
                                    $totalMasuk = $items->sum('qty_masuk');
                                    $totalSisa = $items->sum('qty_sisa');
                                    $tglAwal = $items->min('tanggal_masuk');
                                    $tglAkhir = $items->max('tanggal_masuk');
                                @endphp
                                <tr class="hover:bg-gray-800/30 transition">
                                    <td class="px-4 py-3 text-gray-400 font-mono text-xs">
                                        {{ \Carbon\Carbon::parse($tglAwal)->format('d/m') }} - {{ \Carbon\Carbon::parse($tglAkhir)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-white font-bold">{{ floatval($totalMasuk) }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-lg {{ $totalSisa > 0 ? 'text-orange-400' : 'text-gray-600' }}">
                                        {{ floatval($totalSisa) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-white font-mono">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-gray-500 italic">Data stok belum tersedia.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

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
                                    <td class="px-4 py-3 text-right font-bold {{ floatval($v->stok) > 0 ? 'text-orange-400' : 'text-gray-600' }}">
                                        {{ floatval($v->stok) }} <span class="text-gray-500 font-normal text-xs">{{ $v->satuan }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('admin.bahan.edit', $v->id) }}" class="inline-block bg-gray-700/50 hover:bg-orange-500 text-white p-2 rounded-lg transition duration-200" title="Edit Warna & Foto">
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