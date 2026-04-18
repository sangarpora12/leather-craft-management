@extends('layouts.admin')

@section('title', 'Detail Produksi: ' . $produksi->kode_spk)
@section('title_header', 'Detail Produksi')

@section('content')
<div class="container-fluid">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.produksi.index') }}" class="p-2 hover:bg-white/5 rounded-xl transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">Detail Produksi</h1>
            </div>
            <p class="text-sm text-gray-400">
                Kode SPK: <strong class="text-amber-400 font-mono">{{ $produksi->kode_spk }}</strong>
            </p>
        </div>
    </div>

    {{-- STATUS BADGE ATAS --}}
    <div class="mb-6">
        <div class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl
            @if($produksi->status === 'SELESAI') bg-green-500/10 border border-green-500/30
            @elseif($produksi->status === 'PROSES') bg-blue-500/10 border border-blue-500/30
            @else bg-yellow-500/10 border border-yellow-500/30
            @endif
        ">
            <div class="w-3 h-3 rounded-full animate-pulse
                @if($produksi->status === 'SELESAI') bg-green-500
                @elseif($produksi->status === 'PROSES') bg-blue-500
                @else bg-yellow-500
                @endif
            "></div>
            <span class="font-bold text-lg
                @if($produksi->status === 'SELESAI') text-green-400
                @elseif($produksi->status === 'PROSES') text-blue-400
                @else text-yellow-400
                @endif
            ">
                {{ $produksi->status }}
            </span>
        </div>
    </div>

    {{-- PROGRESS BAR --}}
    @if($produksi->status === 'PROSES')
    <div class="mb-8 bg-black/30 rounded-2xl p-5 border border-white/10">
        <div class="flex justify-between items-center mb-3">
            <div class="text-sm text-gray-400">Progress Produksi</div>
            <div class="font-bold text-white">{{ number_format($produksi->jumlah_selesai, 0) }} / {{ number_format($produksi->jumlah_produksi, 0) }} Pcs</div>
        </div>
        <div class="w-full bg-white/10 rounded-full h-4 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-4 rounded-full transition-all duration-500" style="width: {{ round(($produksi->jumlah_selesai / $produksi->jumlah_produksi) * 100) }}%"></div>
        </div>
        <div class="text-right mt-2 text-sm font-bold text-emerald-400">{{ round(($produksi->jumlah_selesai / $produksi->jumlah_produksi) * 100) }}%</div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- KIRI 8 KOLOM --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- INFORMASI PRODUK --}}
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                    Detail Produk
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Nama Produk</div>
                        <div class="font-bold text-xl text-white">{{ $produksi->product?->nama_produk ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Kode Produk</div>
                        <div class="font-mono text-amber-400">{{ $produksi->product?->kode_produk ?? '-' }}</div>
                    </div>
                     <div>
                          <div class="text-xs text-gray-400 uppercase tracking-wider mb-1"><i class="fa-solid fa-tags mr-1"></i> Kategori</div>
                          <div class="text-white text-capitalize">
                             {{ $produksi->produk_jadi && is_array(json_decode($produksi->produk_jadi->kategori, true)) ? implode(', ', json_decode($produksi->produk_jadi->kategori, true)) : ($produksi->produk_jadi->kategori ?? '-') }}
                         </div>
                     </div>
                     <div>
                          <div class="text-xs text-gray-400 uppercase tracking-wider mb-1"><i class="fa-solid fa-brush mr-1"></i> Jenis Motif</div>
                          <div class="text-white text-capitalize">
                             {{ $produksi->produk_jadi && is_array(json_decode($produksi->produk_jadi->jenis_motif, true)) ? implode(', ', json_decode($produksi->produk_jadi->jenis_motif, true)) : ($produksi->produk_jadi->jenis_motif ?? '-') }}
                         </div>
                     </div>
                     <div>
                          <div class="text-xs text-gray-400 uppercase tracking-wider mb-1"><i class="fa-solid fa-palette mr-1"></i> Warna</div>
                          <div class="text-white text-capitalize">
                             {{ $produksi->produk_jadi && is_array(json_decode($produksi->produk_jadi->warna, true)) ? implode(', ', json_decode($produksi->produk_jadi->warna, true)) : ($produksi->produk_jadi->warna ?? '-') }}
                         </div>
                     </div>
                </div>

                {{-- DIMENSI --}}
                @if($produksi->product?->panjang || $produksi->product?->lebar || $produksi->product?->tinggi)
                <div class="mt-6 pt-6 border-t border-white/10">
                    <div class="text-xs text-gray-400 uppercase tracking-wider mb-3">📏 Dimensi Produk</div>
                    <div class="flex gap-5">
                        @if($produksi->product?->panjang)
                        <div class="bg-cyan-500/10 border border-cyan-500/30 rounded-xl px-4 py-3">
                            <div class="text-xs text-cyan-400 mb-1">PANJANG</div>
                            <div class="font-bold text-white text-lg">{{ fmod($produksi->product->panjang, 1) !== 0.0 ? number_format($produksi->product->panjang, 2) : (int)$produksi->product->panjang }} cm</div>
                        </div>
                        @endif
                        @if($produksi->product?->lebar)
                        <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl px-4 py-3">
                            <div class="text-xs text-emerald-400 mb-1">LEBAR</div>
                            <div class="font-bold text-white text-lg">{{ fmod($produksi->product->lebar, 1) !== 0.0 ? number_format($produksi->product->lebar, 2) : (int)$produksi->product->lebar }} cm</div>
                        </div>
                        @endif
                        @if($produksi->product?->tinggi)
                        <div class="bg-rose-500/10 border border-rose-500/30 rounded-xl px-4 py-3">
                            <div class="text-xs text-rose-400 mb-1">TINGGI</div>
                            <div class="font-bold text-white text-lg">{{ fmod($produksi->product->tinggi, 1) !== 0.0 ? number_format($produksi->product->tinggi, 2) : (int)$produksi->product->tinggi }} cm</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- RINCIAN BAHAN BAKU / BOM --}}
            @if($produksi->produk_jadi && $produksi->produk_jadi->boms->isNotEmpty())
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <div class="w-2 h-2 bg-amber-400 rounded-full"></div>
                    Rincian Bahan Baku (BOM)
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-400 text-xs uppercase tracking-wider">
                                <th class="pb-4">Nama Bahan</th>
                                <th class="pb-4 text-center">Kebutuhan / Unit</th>
                                <th class="pb-4 text-center">Total Kebutuhan</th>
                                <th class="pb-4 text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach($produksi->produk_jadi->boms as $bom)
                            <tr>
                                <td class="py-4 text-white font-medium">{{ $bom->bahanBaku?->nama_material ?? '-' }}</td>
                                <td class="py-4 text-center text-gray-300">{{ $bom->jumlah_kebutuhan }}</td>
                                <td class="py-4 text-center text-white font-bold">{{ number_format($bom->jumlah_kebutuhan * $produksi->jumlah_produksi, 2) }}</td>
                                <td class="py-4 text-center text-gray-300">{{ $bom->bahanBaku?->satuan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- CATATAN ADMIN --}}
            @if($produksi->catatan)
            <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-5 border-l-4 border-amber-500">
                <div class="text-sm font-bold text-amber-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Catatan Admin
                </div>
                <p class="text-amber-300">{{ $produksi->catatan }}</p>
            </div>
            @endif

        </div>

        {{-- KANAN 4 KOLOM --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- INFORMASI SPK --}}
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">
                <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                    Informasi SPK
                </h3>

                <div class="space-y-4">
                    <div class="flex justify-between">
                        <div class="text-gray-400">Jumlah Produksi</div>
                        <div class="font-bold text-white text-lg">{{ number_format($produksi->jumlah_produksi, 0) }} Pcs</div>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-400">Jumlah Selesai</div>
                        <div class="font-bold text-emerald-400">{{ number_format($produksi->jumlah_selesai, 0) }} Pcs</div>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-400">Operator</div>
                        <div class="font-bold text-white">{{ $produksi->operator?->name ?? 'Belum Ditugaskan' }}</div>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-400">Tanggal Dibuat</div>
                        <div class="text-white">{{ $produksi->created_at?->format('d F Y') }}</div>
                    </div>
                    <div class="flex justify-between">
                        <div class="text-gray-400">Tanggal Target</div>
                        <div class="text-white font-bold">{{ $produksi->tanggal_target?->format('d F Y') ?? '-' }}</div>
                    </div>
                    @if($produksi->tanggal_selesai)
                    <div class="flex justify-between">
                        <div class="text-gray-400">Tanggal Selesai</div>
                        <div class="text-emerald-400 font-bold">{{ $produksi->tanggal_selesai?->format('d F Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- FOTO PRODUK --}}
            @if($produksi->product?->foto)
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">
                <h3 class="text-xl font-bold text-white mb-4">Foto Referensi</h3>
                <div class="rounded-2xl overflow-hidden border border-white/20">
                    <img src="{{ asset('storage/' . $produksi->product->foto) }}" alt="Foto Produk" class="w-full h-auto">
                </div>
            </div>
            @endif

            {{-- FOTO BUKTI --}}
            @if($produksi->foto_bukti)
            <div class="bg-gradient-to-b from-[#1A1A1A] to-[#0F0F0F] border border-white/10 rounded-3xl p-6 shadow-2xl shadow-black/50">
                <h3 class="text-xl font-bold text-white mb-4">📸 Foto Bukti Pengerjaan</h3>
                <div class="rounded-2xl overflow-hidden border border-emerald-500/30">
                    <img src="{{ asset('storage/' . $produksi->foto_bukti) }}" alt="Foto Bukti" class="w-full h-auto">
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection