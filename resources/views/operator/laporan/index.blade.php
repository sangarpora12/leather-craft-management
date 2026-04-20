@extends('layouts.operator_app')

@section('title', 'Laporan Operator')

@section('main_class', 'pb-28')

@section('content')
<div class="min-h-screen bg-[#0f172a] bg-[radial-gradient(circle_at_top,#1e293b,transparent)]">
    <div class="max-w-md mx-auto shadow-2xl shadow-black/50 min-h-screen relative">
        @include('components.operator-header', ['title' => 'LAPORAN'])

        <div class="px-5 pt-5 pb-32 space-y-4">

            {{-- ✅ FILTER FORM --}}
            <div class="op-glass rounded-2xl p-4">
                <form method="GET" action="{{ route('operator.laporan.index') }}" id="filterForm">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] text-gray-400 font-bold">Tanggal Mulai</label>
                            <input type="date" name="from" value="{{ $fromDate }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-xs mt-1">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-400 font-bold">Tanggal Selesai</label>
                            <input type="date" name="to" value="{{ $toDate }}" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white text-xs mt-1">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="text-[10px] text-gray-400 font-bold">Kategori</label>
                        <select name="kategori" class="w-full bg-[#1e293b] border border-white/10 rounded-lg px-3 py-2 text-white text-xs mt-1 appearance-none cursor-pointer">
                            <option value="semua" {{ $kategori == 'semua' ? 'selected' : '' }}>Semua (Produk + Bahan)</option>
                            <option value="produk" {{ $kategori == 'produk' ? 'selected' : '' }}>Hanya Produk Jadi</option>
                            <option value="bahan" {{ $kategori == 'bahan' ? 'selected' : '' }}>Hanya Bahan Baku</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full mt-3 bg-emerald-500 hover:bg-emerald-600 text-black font-bold text-xs py-2 rounded-lg">
                        FILTER DATA
                    </button>

                    {{-- ✅ TOMBOL EKSPOR & SHARE --}}
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <a href="{{ route('operator.laporan.export-pdf', ['from' => $fromDate, 'to' => $toDate, 'kategori' => $kategori]) }}" target="_blank" class="flex items-center justify-center gap-1 bg-blue-500 hover:bg-blue-600 text-white font-bold text-xs py-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            CETAK PDF
                        </a>
                        <button id="btnShare" class="flex items-center justify-center gap-1 bg-purple-500 hover:bg-purple-600 text-white font-bold text-xs py-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                            SHARE LAPORAN
                        </button>
                    </div>
                </form>
            </div>

            <style>
                @media print {
                    #filterForm, nav, footer, .no-print { display: none !important; }
                    body { background: white !important; color: black !important; }
                    * { box-shadow: none !important; }
                }
            </style>

            <script>
                document.getElementById('btnShare').addEventListener('click', async () => {
                    const from = '{{ $fromDate }}';
                    const to = '{{ $toDate }}';
                    const totalBahan = '{{ $bahanMingguIni }}';
                    const totalProduk = '{{ $mingguIniSelesaiQty }}';
                    
                    const shareData = {
                        title: 'Laporan Berliano Leather',
                        text: `📊 LAPORAN BERLIANO LEATHER\nPeriode: ${from} s/d ${to}\n\n✅ Bahan Diterima: ${totalBahan} unit\n✅ Produk Selesai: ${totalProduk} pcs\n\nLihat detail lengkap di sistem.`,
                        url: window.location.href
                    };
                    
                    try {
                        if (navigator.share) {
                            await navigator.share(shareData);
                        } else {
                            await navigator.clipboard.writeText(window.location.href);
                            alert('✅ Link laporan berhasil disalin!');
                        }
                    } catch (err) {
                        console.log('Error sharing:', err);
                    }
                });
            </script>

            <div class="grid grid-cols-3 gap-3">
                <div class="op-glass rounded-2xl p-3 text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Pending</div>
                    <div class="text-xl font-extrabold text-amber-300 mt-1">{{ $pendingCount }}</div>
                </div>
                <div class="op-glass rounded-2xl p-3 text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Proses</div>
                    <div class="text-xl font-extrabold text-blue-300 mt-1">{{ $prosesCount }}</div>
                </div>
                <div class="op-glass rounded-2xl p-3 text-center">
                    <div class="text-[10px] text-gray-400 font-bold uppercase">Selesai</div>
                    <div class="text-xl font-extrabold text-emerald-300 mt-1">{{ $selesaiCount }}</div>
                </div>
            </div>

            <div class="op-glass rounded-2xl p-4">
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Ringkasan Hari Ini</div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Produk Selesai</div>
                        <div class="text-lg font-extrabold text-blue-300 mt-1">{{ $hariIniSelesaiQty }}</div>
                        <div class="text-[10px] text-gray-500">pcs</div>
                    </div>
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Bahan Diterima</div>
                        <div class="text-lg font-extrabold text-emerald-300 mt-1">{{ $bahanHariIni }}</div>
                        <div class="text-[10px] text-gray-500">unit</div>
                    </div>
                </div>
            </div>

            <div class="op-glass rounded-2xl p-4">
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Ringkasan Minggu Ini</div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Produk Selesai</div>
                        <div class="text-lg font-extrabold text-blue-300 mt-1">{{ $mingguIniSelesaiQty }}</div>
                        <div class="text-[10px] text-gray-500">pcs</div>
                    </div>
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Bahan Diterima</div>
                        <div class="text-lg font-extrabold text-emerald-300 mt-1">{{ $bahanMingguIni }}</div>
                        <div class="text-[10px] text-gray-500">unit</div>
                    </div>
                </div>
            </div>

            <div class="op-glass rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Riwayat Transaksi</div>
                        <div class="text-[10px] text-gray-500">Produk selesai & Bahan diterima</div>
                    </div>
                </div>

                @forelse($riwayat as $item)
                    <div class="px-4 py-3 border-b border-white/5 hover:bg-white/5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    @if($item->jenis == 'produk')
                                        <span class="inline-flex px-2 py-0.5 bg-blue-500/20 text-blue-300 text-[10px] font-bold rounded">PRODUK</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 bg-emerald-500/20 text-emerald-300 text-[10px] font-bold rounded">BAHAN</span>
                                    @endif
                                </div>
                                <div class="text-xs font-bold text-white mt-1">
                                    {{ $item->jenis == 'produk' ? ($item->product->nama_produk ?? 'Produk') : $item->nama }}
                                </div>
                                <div class="text-[10px] text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($item->tanggal_terakhir)->format('d M Y, H:i') }}
                                </div>
                                @if($item->jumlah_transaksi > 1)
                                    <div class="text-[10px] text-gray-400 mt-1">
                                        ✅ {{ $item->jumlah_transaksi }} x transaksi digabung
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-extrabold {{ $item->jenis == 'produk' ? 'text-blue-300' : 'text-emerald-300' }}">{{ (int) $item->total_qty }}</div>
                                <div class="text-[10px] text-gray-500">{{ $item->jenis == 'produk' ? 'pcs' : ($item->satuan ?? 'unit') }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-gray-500">
                        Belum ada data pada rentang tanggal yang dipilih.
                    </div>
                @endforelse

                <div class="p-3">
                    {{ $riwayat->appends(request()->query())->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection