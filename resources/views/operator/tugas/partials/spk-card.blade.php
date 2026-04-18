@php
    /** @var \App\Models\Produksi $spk */
    $status = $spk->status;
    $isPending = $status === 'PENDING';
    $isProcess = $status === 'PROSES';
    $isDone = $status === 'SELESAI';

    $statusBadge = match(true) {
        $isPending => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
        $isProcess => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
        $isDone => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
        default => 'bg-slate-500/20 text-slate-300 border-slate-500/30'
    };
    $statusText = match(true) {
        $isPending => 'BELUM DIKERJAKAN',
        $isProcess => 'DI PROSES',
        $isDone => 'SELESAI',
        default => $status,
    };
@endphp

<label class="block cursor-pointer">
    @if($isDone)
    <input type="checkbox" name="ids[]" value="{{ $spk->id }}" class="hidden task-checkbox">
    @endif
    <div class="glass-card relative p-5 border-l-4 {{ $isPending ? 'border-l-amber-500' : ($isProcess ? 'border-l-blue-500' : 'border-l-emerald-500') }} transition-all duration-200">
        @if($isDone)
        <div class="absolute top-3 right-3 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center opacity-0 transition-opacity duration-200 check-icon">
            <i class="fas fa-check text-white text-xs"></i>
        </div>
        @endif
        <div class="flex justify-between items-start mb-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-slate-700 border border-slate-600">
                    @if($spk->product && $spk->product->foto)
                        <img src="{{ asset('storage/' . $spk->product->foto) }}" alt="Product" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-box text-slate-400 text-2xl"></i>
                        </div>
                    @endif
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] {{ $statusBadge }} border px-2 py-0.5 rounded-full font-semibold uppercase">
                            {{ $statusText }}
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-white">
                        {{ $spk->product->nama_produk ?? 'Produk Tidak Ditemukan' }}
                    </h3>
                    <p class="text-sm text-slate-400">Kode: {{ $spk->product->kode_produk ?? 'N/A' }}</p>

                    <p class="text-xs text-slate-500">
                        Total {{ $spk->total_spk ?? 1 }} SPK | {{ \Carbon\Carbon::parse($spk->created_at)->format('d M Y') }}
                    </p>
                     @php
                        $notes = $spk->catatan_list ?? collect();
                        if($notes->count() === 0 && !empty($spk->catatan) && $spk->catatan !== '-') {
                            $notes->push($spk->catatan);
                        }
                        $notes = $notes->unique()->values();
                     @endphp
                     @if($notes->count() > 0)
                        <p class="text-xs text-slate-400 mt-1 line-clamp-2">📋 Ada {{ $notes->count() }} catatan khusus</p>
                     @endif
                </div>
            </div>

            <div class="text-right">
                <span class="text-xs text-slate-500">Target</span>
                <div class="text-2xl font-bold text-white">{{ rtrim(rtrim(number_format((float) $spk->jumlah_produksi, 2, ',', '.'), '0'), ',') }}</div>
                <span class="text-xs text-slate-500">Pcs</span>
            </div>
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-slate-700/50">
            <div class="text-[11px] text-slate-500">
                @if($isProcess || $isDone)
                    Mulai: {{ $spk->tanggal_mulai ? \Carbon\Carbon::parse($spk->tanggal_mulai)->format('d M Y') : \Carbon\Carbon::parse($spk->created_at)->format('d M Y') }}
                @else
                    Menunggu dikerjakan
                @endif
            </div>

            <div>
                @if($isPending)
                    <a href="{{ route('operator.tugas.detail', $spk->id) }}"
                        class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-white py-2.5 px-5 rounded-xl font-bold uppercase text-sm shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        🚀 Mulai
                    </a>
                @elseif($isProcess)
                    <a href="{{ route('operator.tugas.lanjut', $spk->id) }}"
                       class="bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white py-2.5 px-5 rounded-xl font-bold uppercase text-sm shadow-lg shadow-orange-500/20 hover:shadow-orange-500/40 transition-all duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        ✅ Selesaikan Produksi
                    </a>
                @else
                    <a href="{{ route('operator.tugas.detail', $spk->id) }}"
                       class="bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl px-5 py-2.5 transition-all duration-200 shadow-sm font-semibold text-slate-200 flex items-center gap-2">
                        <i class="fa-solid fa-eye text-xs"></i>
                        Detail
                    </a>
                @endif
            </div>
        </div>
    </div>
</label>