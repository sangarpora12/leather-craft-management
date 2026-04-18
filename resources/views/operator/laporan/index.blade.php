@extends('layouts.operator_app')

@section('title', 'Laporan Operator')

@section('main_class', 'pb-28')

@section('content')
<div class="min-h-screen bg-[#0f172a] bg-[radial-gradient(circle_at_top,#1e293b,transparent)]">
    <div class="max-w-md mx-auto shadow-2xl shadow-black/50 min-h-screen relative">
        @include('components.operator-header', ['title' => 'LAPORAN'])

        <div class="px-5 pt-5 pb-32 space-y-4">
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
                <div class="text-xs text-gray-400 font-bold uppercase tracking-wider">Ringkasan Produksi</div>
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Hari ini</div>
                        <div class="text-lg font-extrabold text-white mt-1">{{ $hariIniSelesaiQty }}</div>
                        <div class="text-[10px] text-gray-500">pcs selesai</div>
                    </div>
                    <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                        <div class="text-[10px] text-gray-400 font-bold uppercase">Minggu ini</div>
                        <div class="text-lg font-extrabold text-white mt-1">{{ $mingguIniSelesaiQty }}</div>
                        <div class="text-[10px] text-gray-500">pcs selesai</div>
                    </div>
                </div>
            </div>

            <div class="op-glass rounded-2xl overflow-hidden">
                <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white">Riwayat SPK Selesai</div>
                        <div class="text-[10px] text-gray-500">10 terakhir</div>
                    </div>
                    <a href="{{ route('operator.tugas.index') }}" class="text-[10px] text-emerald-300 hover:text-emerald-200 font-bold">Lihat Tugas</a>
                </div>

                @forelse($riwayat as $r)
                    <a href="{{ route('operator.tugas.detail', $r->id) }}" class="block px-4 py-3 border-b border-white/5 hover:bg-white/5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-white truncate">{{ $r->product->nama_produk ?? 'Produk' }}</div>
                                <div class="text-[10px] text-gray-500 font-mono truncate">{{ $r->kode_spk }}</div>
                                <div class="text-[10px] text-gray-500 mt-1">{{ $r->updated_at?->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-extrabold text-emerald-300">{{ $r->jumlah_produksi }}</div>
                                <div class="text-[10px] text-gray-500">pcs</div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-6 text-center text-xs text-gray-500">
                        Belum ada riwayat produksi selesai.
                    </div>
                @endforelse

                <div class="p-3">
                    {{ $riwayat->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
