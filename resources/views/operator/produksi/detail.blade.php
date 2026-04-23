@extends('layouts.operator_app')

@section('title', 'Detail Tugas Produksi')

@section('main_class', 'pb-48')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body { background-color: #080a10 !important; overflow-x: hidden !important; }
.glass-card {
    background: rgba(30, 30, 30, 0.6) !important;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
}
.slide-up { animation: slideUp 0.4s ease-out; }
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
.progress-shimmer {
    background: linear-gradient(90deg, #22c55e 0%, #4ade80 25%, #22c55e 50%, #4ade80 75%, #22c55e 100%);
    background-size: 200% 100%;
    animation: shimmer 2s linear infinite;
}
@keyframes shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.btn-shimmer { position: relative; overflow: hidden; }
.btn-shimmer::after {
    content: '';
    position: absolute; top: -50%; left: -100%;
    width: 50%; height: 200%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: btnShimmer 3s infinite;
}
@keyframes btnShimmer {
    0%      { left: -100%; }
    50%,100%{ left: 200%;  }
}
.bom-row:hover { background: rgba(255,255,255,0.05); }
.page-bg {
    background-color: #0f172a;
    background-image: radial-gradient(circle at top, #1e293b, transparent);
}
.delete-btn { transition: all 0.3s ease; }
.delete-btn:hover { transform: scale(1.05); }

/* ── multi-foto preview ── */
.foto-preview-wrap { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
.foto-preview-wrap img {
    width: 72px; height: 72px;
    object-fit: cover; border-radius: 10px;
    border: 2px solid rgba(16,185,129,0.4);
}
</style>
@endsection

@section('content')
<div class="max-w-md mx-auto min-h-screen bg-[#0f172a] shadow-2xl relative page-bg px-4 pt-3">

    <!-- Header -->
    @include('components.operator-header', ['title' => 'DETAIL TUGAS'])

    <!-- ── Progress Stepper ──────────────────────────────────── -->
    <div class="px-2 py-4">
        <div class="flex items-center justify-between relative">
            <div class="absolute top-1/2 left-0 right-0 h-1 bg-zinc-800 -translate-y-1/2 rounded-full"></div>

            {{-- Step 1: Persiapan --}}
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500
                    @if($produksi->status === 'PENDING') bg-emerald-500 shadow-lg shadow-emerald-500/50 scale-110
                    @elseif($produksi->status === 'PROSES') bg-emerald-600 shadow-md
                    @else bg-emerald-600 @endif">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-[10px] mt-2 font-semibold {{ $produksi->status === 'PENDING' ? 'text-emerald-400' : 'text-gray-500' }}">Persiapan</span>
            </div>

            {{-- Step 2: Produksi --}}
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500
                    @if($produksi->status === 'PROSES') bg-amber-500 shadow-lg shadow-amber-500/50 scale-110 animate-pulse
                    @elseif($produksi->status === 'SELESAI') bg-emerald-600
                    @else bg-zinc-700 @endif">
                    @if($produksi->status === 'PROSES')
                        <svg class="w-5 h-5 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        </svg>
                    @endif
                </div>
                <span class="text-[10px] mt-2 font-semibold {{ $produksi->status === 'PROSES' ? 'text-amber-400' : 'text-gray-500' }}">Produksi</span>
            </div>

            {{-- Step 3: Selesai --}}
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500
                    @if($produksi->status === 'SELESAI') bg-emerald-500 shadow-lg shadow-emerald-500/50 scale-110
                    @else bg-zinc-700 @endif">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-[10px] mt-2 font-semibold {{ $produksi->status === 'SELESAI' ? 'text-emerald-400' : 'text-gray-500' }}">Selesai</span>
            </div>
        </div>
    </div>

    <main class="py-3 space-y-3">
        @php
            $selesai         = (float) ($produksi->jumlah_selesai ?? 0);
            $target          = (float) ($produksi->jumlah_produksi ?? 0);
            $sisa            = max(0, $target - $selesai);
            $progressPercent = $target > 0 ? ($selesai / $target) * 100 : 0;

            $keterangan = null;
            if (!empty($produksi->keterangan) && trim($produksi->keterangan) !== '-') {
                $keterangan = $produksi->keterangan;
            } elseif (!empty($produksi->catatan) && trim($produksi->catatan) !== '-') {
                $keterangan = $produksi->catatan;
            }
        @endphp

        {{-- ── Catatan Admin ───────────────────────────────────── --}}
        @php
            $semuaCatatan = collect();
            if ($keterangan) $semuaCatatan->push($keterangan);

            $semuaSpk = \App\Models\Produksi::where('produk_jadi_id', $produksi->produk_jadi_id)
                ->where('operator_id', auth()->id())
                ->where('status', $produksi->status)
                ->get();

            foreach ($semuaSpk as $spk) {
                foreach (['catatan', 'keterangan'] as $field) {
                    if (!empty($spk->$field) && trim($spk->$field) !== '-') {
                        $semuaCatatan->push($spk->$field);
                    }
                }
            }
            $semuaCatatan = $semuaCatatan->unique()->values();
        @endphp

        @if($semuaCatatan->count() > 0)
        <div class="glass-card rounded-2xl p-4 border-l-4 slide-up"
             style="background:rgba(245,158,11,0.08);border-color:rgba(245,158,11,0.4)">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center"
                     style="background:rgba(245,158,11,0.2)">
                    <svg class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-[10px] text-amber-400/80 uppercase font-bold tracking-wider mb-1">
                        📋 CATATAN DARI ADMIN ({{ $semuaCatatan->count() }})
                    </div>
                    <ul class="m-0 pl-4 text-sm text-amber-100/90 leading-relaxed space-y-1">
                        @foreach($semuaCatatan as $note)
                            <li>{{ $note }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Ringkasan SPK + Progress ─────────────────────────── --}}
        <div class="glass-card rounded-2xl p-4 slide-up space-y-3">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-[10px] text-gray-500 uppercase font-bold tracking-wider">Kode Produk</div>
                    <div class="text-sm font-mono text-blue-300 truncate">{{ $produksi->produk_jadi->kode_produk ?? '-' }}</div>
                </div>
                <div class="px-3 py-1 rounded-full text-[10px] font-bold border
                    @if($produksi->status==='PENDING') bg-blue-500/20 text-blue-300 border-blue-500/30
                    @elseif($produksi->status==='PROSES') bg-amber-500/20 text-amber-300 border-amber-500/30
                    @else bg-emerald-500/20 text-emerald-300 border-emerald-500/30
                    @endif">
                    {{ $produksi->status }}
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                    <div class="text-[10px] text-gray-400 uppercase font-bold">Mulai</div>
                    <div class="text-xs text-white font-semibold mt-1">
                        {{ $produksi->tanggal_mulai ? \Carbon\Carbon::parse($produksi->tanggal_mulai)->format('d M Y') : '-' }}
                    </div>
                </div>
                <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                    <div class="text-[10px] text-gray-400 uppercase font-bold">Target</div>
                    <div class="text-xs text-white font-semibold mt-1">
                        {{ $produksi->tanggal_target ? \Carbon\Carbon::parse($produksi->tanggal_target)->format('d M Y') : '-' }}
                    </div>
                </div>
            </div>

            <div class="rounded-xl p-3 bg-white/5 border border-white/10">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-[10px] text-gray-400 uppercase font-bold">Progress</div>
                    <div class="text-[10px] text-gray-300 font-bold" id="progressPercent">{{ (int) round($progressPercent) }}%</div>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.1)">
                    <div class="h-full rounded-full progress-shimmer" style="width:{{ $progressPercent }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-2 text-[10px] text-gray-400">
                    <span>Selesai: <span class="text-emerald-300 font-bold">{{ rtrim(rtrim(number_format($selesai,2,',','.'), '0'), ',') }}</span></span>
                    <span>Sisa: <span class="text-amber-300 font-bold">{{ rtrim(rtrim(number_format($sisa,2,',','.'), '0'), ',') }}</span></span>
                </div>
            </div>
        </div>

        @if($errors->any())
        <div class="glass-card rounded-2xl p-4 border-l-4 border-red-500 slide-up">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-red-400 mb-1">Gagal Memproses</h4>
                    <ul class="text-xs text-red-300/80 space-y-1">
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- ── Info Produk ───────────────────────────────────────── -->
        <div class="glass-card rounded-2xl p-4 slide-up">
            <h2 class="text-sm font-bold text-white leading-tight mb-3">{{ $produksi->produk_jadi->nama_produk ?? 'Produk Dihapus' }}</h2>

            <div class="grid grid-cols-2 gap-3 mb-3">
                <div class="rounded-2xl overflow-hidden bg-white/5 border border-white/10 shadow-lg flex flex-col">
                    <div class="text-[10px] text-gray-500 uppercase font-bold text-center py-2 border-b border-white/10">📸 FOTO PRODUK</div>
                    <div class="flex-1 flex items-center justify-center min-h-[140px]">
                        @if(!empty($produksi->produk_jadi->foto))
                            <img src="{{ asset('storage/'.$produksi->produk_jadi->foto) }}" class="w-full h-full object-cover" alt="Foto Produk">
                        @else
                            <div class="text-gray-600 text-sm">🚫 Belum tersedia</div>
                        @endif
                    </div>
                </div>
                <div class="rounded-2xl overflow-hidden bg-white/5 border border-white/10 shadow-lg flex flex-col">
                    <div class="text-[10px] text-gray-500 uppercase font-bold text-center py-2 border-b border-white/10">📏 SKETSA POLA</div>
                    @php $opSketsas = $produksi->produk_jadi->sketsa ?? []; @endphp
                    <div class="flex-1 flex items-center justify-center min-h-[140px] p-2">
                        @if(count($opSketsas) > 0)
                            <div class="grid grid-cols-2 gap-1 w-full h-full">
                                @foreach(array_slice($opSketsas, 0, 4) as $skPath)
                                    <img src="{{ asset('storage/'.$skPath) }}" class="w-full h-full object-cover rounded-lg" alt="Sketsa">
                                @endforeach
                            </div>
                        @else
                            <div class="text-gray-600 text-sm">🚫 Belum tersedia</div>
                        @endif
                    </div>
                </div>
            </div>

            @php
                $opKat = is_array($produksi->produk_jadi->kategori) ? $produksi->produk_jadi->kategori : (json_decode($produksi->produk_jadi->kategori, true) ?: [$produksi->produk_jadi->kategori ?: '-']);
                $opWar = is_array($produksi->produk_jadi->warna)    ? $produksi->produk_jadi->warna    : (json_decode($produksi->produk_jadi->warna, true)    ?: [$produksi->produk_jadi->warna    ?: '-']);
                $opMot = is_array($produksi->produk_jadi->jenis_motif) ? $produksi->produk_jadi->jenis_motif : (json_decode($produksi->produk_jadi->jenis_motif, true) ?: [$produksi->produk_jadi->jenis_motif ?: '-']);
            @endphp
            <div class="rounded-xl p-3 bg-white/5 border border-white/10 space-y-3">
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center">
                        <div class="text-[9px] text-gray-500 uppercase font-bold mb-2">KATEGORI</div>
                        <div class="flex flex-wrap justify-center gap-1">
                            @foreach($opKat as $item)
                            <span class="inline-block text-[10px] font-semibold px-2 py-1 rounded-full capitalize" style="background:rgba(59,130,246,0.2);color:#93c5fd;border:1px solid rgba(59,130,246,0.3)">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-[9px] text-gray-500 uppercase font-bold mb-2">WARNA</div>
                        <div class="flex flex-wrap justify-center gap-1">
                            @foreach($opWar as $item)
                            <span class="inline-block text-[10px] font-semibold px-2 py-1 rounded-full capitalize" style="background:rgba(236,72,153,0.2);color:#f9a8d4;border:1px solid rgba(236,72,153,0.3)">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-[9px] text-gray-500 uppercase font-bold mb-2">MOTIF</div>
                        <div class="flex flex-wrap justify-center gap-1">
                            @foreach($opMot as $item)
                            <span class="inline-block text-[10px] font-semibold px-2 py-1 rounded-full capitalize" style="background:rgba(168,85,247,0.2);color:#d8b4fe;border:1px solid rgba(168,85,247,0.3)">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="pt-2 border-t border-white/5 text-center">
                    <div class="text-[9px] text-gray-500 uppercase font-bold mb-1">📐 DIMENSI</div>
                    <div class="font-bold text-white text-sm">
                        <span class="inline-block px-2 py-1 bg-blue-500/10 rounded-lg mx-1">P {{ (int)$produksi->produk_jadi->panjang }} cm</span>
                        <span class="inline-block px-2 py-1 bg-green-500/10 rounded-lg mx-1">L {{ (int)$produksi->produk_jadi->lebar }} cm</span>
                        <span class="inline-block px-2 py-1 bg-red-500/10 rounded-lg mx-1">T {{ (int)$produksi->produk_jadi->tinggi }} cm</span>
                    </div>
                </div>
            </div>

            <div class="mt-3 rounded-xl p-3 bg-blue-900/20 border border-blue-500/30">
                <div class="text-[10px] text-blue-400 uppercase font-bold mb-2">📝 DETAIL POLA / CATATAN POTONG</div>
                <div class="text-xs text-white">{{ $produksi->produk_jadi->detail_pola ?: 'Tidak Ada Catatan' }}</div>
            </div>

            <div class="mt-2 rounded-xl p-3 bg-white/5 border border-white/10">
                <div class="text-[10px] text-gray-500 uppercase font-bold mb-2">📖 DESKRIPSI PRODUK</div>
                <div class="text-xs text-gray-300">{{ $produksi->produk_jadi->deskripsi ?: 'Tidak Ada Deskripsi' }}</div>
            </div>

            @if(!empty($produksi->produk_jadi->keterangan_internal))
            <div class="mt-2 rounded-xl p-3 bg-yellow-500/10 border border-yellow-500/40">
                <div class="text-[10px] text-yellow-400 uppercase font-bold mb-2">⚠️ KETERANGAN INTERNAL</div>
                <div class="text-xs text-yellow-100">{{ $produksi->produk_jadi->keterangan_internal }}</div>
            </div>
            @endif

            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="rounded-xl p-3 text-center" style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.3)">
                    <div class="w-8 h-8 mx-auto mb-2 rounded-lg flex items-center justify-center" style="background:rgba(59,130,246,0.2)">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Total Target Gabungan</p>
                    <p class="text-xl font-bold text-white">{{ (int)$produksi->total_gabungan }}</p>
                    <p class="text-xs text-gray-400 mt-1">Pcs</p>
                </div>
                <div class="rounded-xl p-3 text-center" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3)">
                    <div class="w-8 h-8 mx-auto mb-2 rounded-lg flex items-center justify-center" style="background:rgba(239,68,68,0.2)">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Deadline</p>
                    <p class="text-base font-bold text-red-400 mt-1">{{ $produksi->tanggal_target ? \Carbon\Carbon::parse($produksi->tanggal_target)->format('d M') : '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $produksi->tanggal_target ? \Carbon\Carbon::parse($produksi->tanggal_target)->format('Y') : '' }}</p>
                </div>
            </div>

            @if($selesai > 0)
            <div class="mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,0.1)">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-green-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Sudah Diselesaikan
                    </span>
                    <span class="text-sm font-bold text-green-400">
                        {{ rtrim(rtrim(number_format($selesai,2,',','.'), '0'), ',') }} /
                        {{ rtrim(rtrim(number_format($target,2,',','.'), '0'), ',') }} pcs
                    </span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.1)">
                    <div class="h-full rounded-full progress-shimmer" style="width:{{ $progressPercent }}%"></div>
                </div>
                <p class="text-xs text-gray-400 mt-2 flex items-center justify-between">
                    <span>{{ round($progressPercent) }}% selesai</span>
                    <span>{{ rtrim(rtrim(number_format($sisa,2,',','.'), '0'), ',') }} pcs remaining</span>
                </p>
            </div>
            @endif
        </div>

        <!-- ── Form Produksi (PENDING / PROSES) ──────────────────── -->
        @if(in_array($produksi->status, ['PENDING', 'PROSES']))
        <div class="glass-card rounded-2xl p-4 slide-up mb-16">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background:linear-gradient(135deg,rgba(16,185,129,0.3),rgba(6,95,70,0.3));border:1px solid rgba(16,185,129,0.3)">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($produksi->status === 'PENDING')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-white">
                    {{ $produksi->status === 'PENDING' ? 'Konfirmasi & Kerjakan' : 'Update Progress' }}
                </h3>
            </div>

            {{--
                ✅ PENTING: form ini punya DUA input untuk jumlah:
                  1. #jumlahInput (name="jumlah_selesai")  → yang terlihat, operator ketik di sini
                  2. #hiddenJumlahProduksi (name="jumlah_produksi") → hidden, diisi JS sebelum submit
                Controller membaca 'jumlah_produksi' sebagai prioritas utama.
            --}}
            <form id="partialForm"
                  action="{{ route('operator.produksi.partial', $produksi->id) }}"
                  method="POST">
                @csrf

                {{-- Hidden: nilai konfirmasi akhir yang dikirim ke controller --}}
                <input type="hidden" name="jumlah_produksi" id="hiddenJumlahProduksi" value="1">

                <!-- Quantity Input -->
                <div class="mb-4">
                    <label class="text-xs text-gray-300 block mb-2">Jumlah yang akan diproduksi</label>
                    <div class="flex items-center justify-between rounded-2xl p-2"
                         style="background:rgba(255,255,255,0.05);border:2px solid rgba(255,255,255,0.1)">
                        <button type="button" onclick="decrementQty()"
                                class="w-10 h-10 rounded-xl font-bold text-xl transition-all active:scale-95"
                                style="background:rgba(255,255,255,0.1);color:white">−</button>

                        {{-- name="jumlah_selesai" agar ada fallback di controller --}}
                        <input type="number" name="jumlah_selesai" id="jumlahInput"
                               min="1" max="{{ $produksi->total_sisa_gabungan }}"
                               value="1"
                               class="w-14 text-center text-xl font-bold text-white bg-transparent focus:outline-none"
                               required>

                        <button type="button" onclick="incrementQty()"
                                class="w-10 h-10 rounded-xl font-bold text-xl transition-all active:scale-95"
                                style="background:rgba(255,255,255,0.1);color:white">+</button>
                    </div>

                    <p class="text-center mt-2 p-2 rounded-xl text-xs" style="background:rgba(255,255,255,0.05);color:#a1a1aa">
                        📦 Total Target Gabungan:
                        <span class="text-white font-bold">{{ (int)$produksi->total_gabungan }} Pcs</span>
                        (dari {{ $produksi->jumlah_spk_gabungan }} SPK)<br>
                        Sudah Selesai: <span class="text-emerald-400 font-bold">{{ (int)$produksi->total_selesai_gabungan }} Pcs</span>
                        | Sisa: <span id="sisaDisplay" class="text-amber-400 font-bold">{{ $produksi->total_sisa_gabungan - 1 }} Pcs</span>
                    </p>
                </div>

                <!-- Material Estimation -->
                <div class="mb-4">
                    <h4 class="text-xs text-gray-300 mb-2 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Estimasi Material (sesuai qty input):
                    </h4>

                    @if($produksi->status === 'PENDING')
                    <div class="mb-2 p-2 rounded-xl text-xs"
                         style="background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.3);color:#93c5fd">
                        <strong>INFO:</strong> Stok bahan baku sudah dipotong saat SPK diterbitkan. Tentukan berapa pcs yang akan kamu kerjakan.
                    </div>
                    @endif

                    <div class="rounded-xl overflow-hidden"
                         style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.1)">
                        @if($produksi->produk_jadi && $produksi->produk_jadi->boms->count() > 0)
                        <table class="w-full text-xs">
                            <thead style="background:rgba(255,255,255,0.05)">
                                <tr>
                                    <th class="px-2 py-2 text-left text-gray-400 font-semibold">Material</th>
                                    <th class="px-2 py-2 text-right text-gray-400 font-semibold">Dibutuhkan</th>
                                    <th class="px-2 py-2 text-right text-gray-400 font-semibold">Stok</th>
                                </tr>
                            </thead>
                            <tbody id="bomTableBody" style="border-top:1px solid rgba(255,255,255,0.1)">
                                @foreach($produksi->produk_jadi->boms as $bom)
                                @php
                                    $perUnit = (float)($bom->jumlah_kebutuhan ?? 0);
                                    $stok    = (float)($bom->material->stok ?? 0);
                                    $satuan  = str_replace('buah-buahan', 'Pcs', $bom->satuan);
                                @endphp
                                <tr class="bom-row transition" style="border-bottom:1px solid rgba(255,255,255,0.05)">
                                    <td class="px-2 py-2 text-gray-300">
                                        {{ $bom->bahanBaku->nama_material ?? $bom->material->nama_material ?? 'Material Hilang' }}
                                    </td>
                                    <td class="px-2 py-2 text-right font-bold">
                                        <span class="need-qty text-emerald-300"
                                              data-per-unit="{{ $perUnit }}"
                                              data-stok="{{ $stok }}"
                                              data-satuan="{{ e($satuan) }}">{{ $perUnit }}</span>
                                        <span class="text-gray-400">{{ $satuan }}</span>
                                    </td>
                                    <td class="px-2 py-2 text-right text-gray-400">{{ $stok }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="p-4 text-center text-gray-500 text-xs">BOM belum tersedia</div>
                        @endif
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="button" id="submitBtn" onclick="handleConfirmClick()"
                        class="w-full font-bold py-3 rounded-xl flex items-center justify-center gap-2 transition-all active:scale-[0.98] text-sm btn-shimmer"
                        style="background:linear-gradient(to right,#10b981,#0d9488);box-shadow:0 4px 15px rgba(16,185,129,0.3);">
                    @if($produksi->status === 'PENDING')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                    </svg>
                    🛠️ Konfirmasi & Kerjakan
                    @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Simpan Progres
                    @endif
                </button>
            </form>
        </div>
        @endif

    </main>

    <!-- ── Tombol kembali / lanjut (status PROSES) ────────────── -->
    @if($produksi->status === 'PROSES')
    <div class="mt-6 px-4 mb-24">
        <div class="max-w-sm mx-auto grid grid-cols-2 gap-3">
            <a href="{{ route('operator.tugas.index') }}"
               class="text-center py-3 rounded-2xl flex items-center justify-center gap-2 text-sm font-bold"
               style="background:rgba(100,116,139,0.25);color:#e2e8f0;border:1px solid rgba(148,163,184,0.25)">
                <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
            </a>
            <a href="{{ route('operator.tugas.lanjut', $produksi->id) }}"
               class="text-center py-3 rounded-2xl flex items-center justify-center gap-2 text-sm font-bold"
               style="background:rgba(59,130,246,0.22);color:#93c5fd;border:1px solid rgba(59,130,246,0.3)">
                <i class="fa-solid fa-forward text-xs"></i> Lanjut
            </a>
        </div>
    </div>
    @endif

    <!-- ── Footer status SELESAI ─────────────────────────────── -->
    @if($produksi->status === 'SELESAI')
    <div class="fixed bottom-0 left-0 right-0 w-full max-w-md mx-auto left-1/2 -translate-x-1/2 px-4 py-4 z-[9999]"
         style="background:rgba(15,15,15,0.95);backdrop-filter:blur(10px);border-top:1px solid rgba(255,255,255,0.1);border-radius:24px 24px 0 0">
        <div class="flex gap-3 max-w-[calc(100%-32px)] mx-auto">
            <form action="{{ route('operator.produksi.destroy', $produksi->id) }}" method="POST" class="flex-1"
                  onsubmit="return confirm('Tugas yang sudah selesai akan dihapus. Lanjutkan?')">
                @csrf @method('DELETE')
                <button type="submit" class="delete-btn w-full py-3 rounded-2xl flex items-center justify-center gap-2 text-sm font-bold transition-all"
                        style="background:rgba(239,68,68,0.2);color:#f87171;border:1px solid rgba(239,68,68,0.3)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Tugas
                </button>
            </form>
            <div class="flex-1 py-3 rounded-2xl flex items-center justify-center gap-2 text-sm font-bold"
                 style="background:rgba(16,185,129,0.2);color:#34d399;border:1px solid rgba(16,185,129,0.3)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tugas Selesai
            </div>
        </div>
    </div>
    @endif

</div>

<!-- ══════════════════════════════════════════════════════════════
     JAVASCRIPT
     ══════════════════════════════════════════════════════════════ -->
@if(in_array($produksi->status, ['PENDING', 'PROSES']))
<script>
(function () {
    /* ── Konstanta dari Blade ──────────────────────────────────── */
    var STATUS   = '{{ $produksi->status }}';
    var MAX_QTY  = {{ (int) $produksi->total_sisa_gabungan }};   // sisa gabungan semua SPK

    /* ── Elemen DOM ───────────────────────────────────────────── */
    var inputVisible = document.getElementById('jumlahInput');       // yang operator lihat & ketik
    var inputHidden  = document.getElementById('hiddenJumlahProduksi'); // yang terkirim ke controller
    var sisaDisplay  = document.getElementById('sisaDisplay');

    if (!inputVisible || !inputHidden) return;

    inputVisible.max = MAX_QTY;

    /* ─── Sinkronisasi: setiap kali inputVisible berubah, update
           hidden input DAN estimasi material secara real-time ─── */
    function syncAndUpdate() {
        var qty = parseInt(inputVisible.value, 10) || 1;

        // Clamp
        if (qty < 1)       { qty = 1;       inputVisible.value = qty; }
        if (qty > MAX_QTY) { qty = MAX_QTY; inputVisible.value = qty; }

        // ✅ Selalu sinkronkan hidden input agar nilai terkirim benar
        inputHidden.value = qty;

        // Update label sisa
        if (sisaDisplay) sisaDisplay.textContent = (MAX_QTY - qty) + ' Pcs';

        // Update estimasi material per baris BOM
        document.querySelectorAll('.need-qty').forEach(function (el) {
            var perUnit    = parseFloat(el.dataset.perUnit)  || 0;
            var stok       = parseFloat(el.dataset.stok)     || 0;
            var totalNeed  = Math.round(perUnit * qty * 100) / 100;

            el.textContent = totalNeed % 1 === 0 ? totalNeed.toString() : totalNeed.toString();

            el.classList.toggle('text-emerald-300', totalNeed <= stok);
            el.classList.toggle('text-red-300',     totalNeed >  stok);
        });
    }

    /* ── Tombol ± ─────────────────────────────────────────────── */
    window.incrementQty = function () {
        var v = parseInt(inputVisible.value, 10) || 0;
        if (v < MAX_QTY) { inputVisible.value = v + 1; syncAndUpdate(); }
    };
    window.decrementQty = function () {
        var v = parseInt(inputVisible.value, 10) || 0;
        if (v > 1) { inputVisible.value = v - 1; syncAndUpdate(); }
    };

    inputVisible.addEventListener('input',  syncAndUpdate);
    inputVisible.addEventListener('change', syncAndUpdate);
    syncAndUpdate(); // inisialisasi saat halaman dimuat

    /* ── Kumpulkan daftar bahan untuk ditampilkan di modal ────── */
    function getBahanHtml(qty) {
        var lines = [];
        document.querySelectorAll('.need-qty').forEach(function (el) {
            var perUnit   = parseFloat(el.dataset.perUnit) || 0;
            var satuan    = el.dataset.satuan || 'Pcs';
            var nama      = el.closest('tr').querySelector('td:first-child').textContent.trim();
            var total     = Math.round(perUnit * qty * 100) / 100;
            lines.push('• <strong>' + nama + '</strong>: ' + total + ' ' + satuan);
        });
        return lines.join('<br>') || '<em style="color:#64748b">Tidak ada data BOM</em>';
    }

    /* ══════════════════════════════════════════════════════════
       handleConfirmClick — dipanggil tombol "Konfirmasi / Simpan"
       ══════════════════════════════════════════════════════════ */
    window.handleConfirmClick = function () {
        var qty       = parseInt(inputVisible.value, 10) || 1;
        var remaining = MAX_QTY;

        /* Clamp sekali lagi sebelum modal tampil */
        if (qty > remaining) { qty = remaining; inputVisible.value = qty; syncAndUpdate(); }

        /* ─── Mode PENDING: tampilkan SweetAlert konfirmasi ─── */
        if (STATUS === 'PENDING') {
            if (typeof Swal === 'undefined') {
                /* Fallback tanpa SweetAlert */
                var newVal = prompt('Jumlah yang akan dikerjakan (maks: ' + remaining + '):', qty);
                if (newVal !== null) {
                    var v = parseInt(newVal, 10);
                    if (v >= 1 && v <= remaining) {
                        inputVisible.value = v;
                        inputHidden.value  = v;   // ✅ sync hidden
                        syncAndUpdate();
                        document.getElementById('partialForm').submit();
                    } else {
                        alert('Jumlah harus antara 1 - ' + remaining);
                    }
                }
                return;
            }

            var bahanHtml = getBahanHtml(qty);
            var sisaTarget = MAX_QTY - qty;

            Swal.fire({
                title: '<span style="font-size: 22px; font-weight: 800; color: #e2e8f0;">🛠️ Konfirmasi Produksi</span>',
                html: `
                    <div style="text-align: center; padding: 16px 0;">
                        <p style="margin-bottom: 8px; font-size:14px; color: #94a3b8;">Anda akan memulai produksi sebanyak:</p>
                        <div style="font-size: 48px; font-weight: 900; color: #22c55e; line-height: 1;">
                            ${qty}
                            <span style="font-size: 24px; font-weight: 500; opacity: .8;">Pcs</span>
                        </div>

                        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.1);">
                            ${bahanHtml}
                        </div>

                        <div style="margin-top: 16px; padding: 12px; background: rgba(251, 191, 36, 0.1); border-radius: 12px; border: 1px solid rgba(251, 191, 36, 0.2);">
                            <p style="font-size: 12px; color: #fcd34d;">Sisa target gabungan setelah ini:</p>
                            <p style="font-size: 18px; font-weight: 800; color: #fbbf24;">${sisaTarget} Pcs</p>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '✅ Konfirmasi & Kerjakan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#475569',
                background: '#1e293b',
                color: '#e2e8f0',
                width: '420px',
                padding: '24px',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memulai Produksi...',
                        html: '<div style="text-align:center;padding:24px"><div style="width:52px;height:52px;border:4px solid rgba(34,197,94,0.2);border-top:4px solid #22c55e;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 14px"></div><p style="color:#9ca3af;font-size:14px">Mempersiapkan sesi produksi Anda...</p></div><style>@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}</style>',
                        showConfirmButton: false,
                        background: '#1e293b',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                    setTimeout(() => {
                        document.getElementById('partialForm').submit();
                    }, 500);
                }
            });

        /* ─── Mode PROSES: konfirmasi sederhana ─────────────────── */
        } else {
            if (confirm('Simpan progres ' + qty + ' pcs? Aksi ini tidak dapat diundur.')) {
                /* ✅ Pastikan hidden input terisi sebelum submit */
                inputHidden.value = qty;

                Swal.fire({
                    title : 'Menyimpan...',
                    html  : '<div style="text-align:center;padding:20px">'
                          + '<div style="width:50px;height:50px;border:4px solid rgba(16,185,129,0.2);border-top:4px solid #10b981;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 16px"></div>'
                          + '<p style="color:#9ca3af;font-size:14px">Menyimpan progres...</p></div>'
                          + '<style>@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}</style>',
                    showConfirmButton : false,
                    background        : '#1e293b',
                    color             : '#e2e8f0',
                    allowOutsideClick : false,
                    allowEscapeKey    : false,
                });

                setTimeout(function () {
                    document.getElementById('partialForm').submit();
                }, 800);
            }
        }
    };

})();
</script>
@endif
@endsection