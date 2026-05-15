@extends('layouts.operator_app')

@section('content')
<div class="w-full max-w-md mx-auto bg-[#0B0E14] text-white font-normal" style="font-family: 'Poppins', sans-serif;">
    
    @include('components.operator-header', ['title' => 'Rincian Belanja', 'url' => route('operator.belanja.index')])

    <div class="p-5">
        <div class="bg-[#1C222D] border border-white/5 rounded-xl p-5 mb-5">
            <div class="flex gap-4 items-center">
                @if($firstItem->bahan && $firstItem->bahan->foto)
                    <img src="{{ asset('storage/' . $firstItem->bahan->foto) }}" alt="{{ $nama_material_decoded }}" class="w-16 h-16 rounded-lg object-cover border border-white/5">
                @else
                    <div class="w-16 h-16 rounded-lg bg-[#0B0E14] flex items-center justify-center border border-dashed border-white/10">
                        <i class="fa-solid fa-image text-gray-600 text-2xl"></i>
                    </div>
                @endif
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-white">{{ $nama_material_decoded }}</h2>
                    <p class="text-sm text-gray-400">
                        Total: <span class="font-bold text-blue-400">{{ (float)$totalJumlah }}</span>
                        @if($totalReceived > 0)
                            | Sudah dibeli: <span class="font-bold text-green-400">{{ (float)$totalReceived }}</span>
                            | Sisa: <span class="font-bold text-orange-400">{{ (float)$totalSisa }}</span>
                        @endif
                        {{ $firstItem->bahan->satuan }}
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-sm text-gray-500 font-normal uppercase px-2">Daftar Permintaan ({{ $items->count() }})</h3>
            
            @php
                // 1. KELOMPOKKAN DATA BERDASARKAN WARNA & STATUS
                $rincianGrouped = $items->groupBy(function($item) {
                    $warnaRaw = strtoupper(trim($item->warna ?? $item->bahan->warna ?? 'TIDAK ADA WARNA'));
                    $status = strtoupper(trim($item->prioritas ?? 'BIASA'));
                    return $warnaRaw . '|' . $status;
                });
            @endphp

            @foreach($rincianGrouped as $kunci => $grupRincian)
                @php
                    $pecah = explode('|', $kunci);
                    $warnaRaw = $pecah[0];
                    $statusPrioritas = $pecah[1];

                    // ✅ Logika Filter Warna: Ubah Polos/NA/- jadi "TIDAK ADA WARNA"
                    $sampahWarna = ['POLOS', 'N/A', 'NULL', '-', '', 'TIDAK ADA WARNA'];
                    $namaWarnaFix = in_array(strtoupper($warnaRaw), $sampahWarna) ? 'TIDAK ADA WARNA' : $warnaRaw;

                    $grupJumlah = (float) $grupRincian->sum('jumlah');
                    $grupReceived = (float) $grupRincian->sum('received');
                    $grupSisa = $grupJumlah - $grupReceived;
                @endphp

                <div class="bg-[#1C222D] border border-white/5 rounded-xl overflow-hidden shadow-lg mb-4">
                    
                    <div class="bg-white/5 px-4 py-3 border-b border-white/5 flex flex-wrap justify-between items-center gap-2">
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-white">
                                🎨 {{ $namaWarnaFix }}
                            </h4>
                            @if($statusPrioritas == 'URGENT')
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500/20 text-red-400 flex-shrink-0">
                                    URGENT
                                </span>
                            @endif
                        </div>
                        
                        <div class="text-right flex flex-col items-end">
                            @if($grupReceived > 0)
                                <span class="text-[10px] text-gray-400 line-through">Awal: {{ (float)$grupJumlah }}</span>
                                <span class="text-xs font-bold text-orange-400 bg-orange-500/10 px-2 py-1 rounded mt-0.5">
                                    Sisa: {{ (float)$grupSisa }} {{ $grupRincian->first()->bahan->satuan ?? 'pcs' }}
                                </span>
                            @else
                                <span class="text-xs font-medium text-blue-400 bg-blue-500/10 px-2 py-1 rounded">
                                    Total: {{ (float)$grupJumlah }} {{ $grupRincian->first()->bahan->satuan ?? 'pcs' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="divide-y divide-white/5">
                        @foreach($grupRincian as $detail)
                            @php
                                $detailJumlah = (float) $detail->jumlah;
                                $detailReceived = (float) $detail->received;
                                $detailSisa = $detailJumlah - $detailReceived;
                            @endphp

                            <div class="p-4 hover:bg-white/5 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[11px] text-gray-500 font-mono">{{ $detail->no_referensi }}</span>
                                    
                                    <div class="text-right">
                                        @if($detailReceived > 0)
                                            <div class="text-[10px] text-gray-400 line-through">Awal: {{ $detailJumlah }}</div>
                                            <div class="text-sm font-bold text-orange-400">
                                                Sisa: {{ $detailSisa }} <span class="text-[10px] text-orange-400/70 font-normal">{{ $detail->bahan->satuan }}</span>
                                            </div>
                                        @else
                                            <div class="text-sm font-bold text-white">
                                                {{ $detailJumlah }} <span class="text-[10px] text-gray-500 font-normal">{{ $detail->bahan->satuan }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mb-2">
                                    <i class="fa-regular fa-calendar-alt mr-1"></i> Butuh sblm: <span class="text-gray-300">{{ \Carbon\Carbon::parse($detail->tanggal_dibutuhkan)->format('d M Y') }}</span>
                                </p>
                                @if($detail->catatan)
                                    <div class="bg-[#0B0E14] rounded p-2.5 text-xs italic text-gray-400 border-l-2 border-blue-500/40">
                                        "{{ $detail->catatan }}"
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection