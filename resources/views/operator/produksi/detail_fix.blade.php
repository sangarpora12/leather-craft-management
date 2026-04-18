@extends('layouts.operator_app')

@section('title', 'Detail Tugas Produksi')

@section('main_class', 'pb-36')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body { background-color: #080a10 !important; }
.glass-card {
    background: rgba(30, 30, 30, 0.6) !important;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 24px;
}
.slide-up { animation: slideUp 0.4s ease-out; }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.progress-shimmer { background: linear-gradient(90deg, #22c55e 0%, #4ade80 25%, #22c55e 50%, #4ade80 75%, #22c55e 100%); background-size: 200% 100%; animation: shimmer 2s linear infinite; }
@keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
.btn-shimmer { position: relative; overflow: hidden; }
.btn-shimmer::after { content: ''; position: absolute; top: -50%; left: -100%; width: 50%; height: 200%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); animation: btnShimmer 3s infinite; }
@keyframes btnShimmer { 0% { left: -100%; } 50%, 100% { left: 200%; } }
.bom-row:hover { background: rgba(255,255,255,0.05); }
.low-stock-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
.page-bg { background-color: #0f172a; background-image: radial-gradient(circle at top, #1e293b, transparent); }
.modal-body-pb { padding-bottom: 120px !important; }
</style>
@endsection

@section('content')
<div class="max-w-md mx-auto min-h-screen bg-[#0f172a] shadow-2xl relative page-bg px-4 pt-3">
    <header class="flex items-center gap-3 sticky top-0 z-50 py-2 -mx-4 px-4 max-w-md mx-auto" style="background: rgba(15,15,15,0.95); backdrop-filter: blur(10px)">
        <a href="{{ route('operator.tugas.index') }}" class="w-10 h-10 flex items-center justify-center rounded-xl transition-all" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1)">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </a>
        <div class="flex-1">
            <h1 class="text-lg font-bold text-white">Detail Tugas</h1>
            <p class="text-[10px] text-gray-500 font-mono opacity-70">{{ $produksi->kode_spk }}</p>
        </div>
        <div class="px-3 py-1 rounded-full text-xs font-bold 
            @if($produksi->status === 'PENDING') bg-blue-500/20 text-blue-400 border border-blue-500/30
            @elseif($produksi->status === 'PROSES') bg-amber-500/20 text-amber-400 border border-amber-500/30
            @else bg-green-500/20 text-green-400 border border-green-500/30
            @endif">
            @if($produksi->status === 'PENDING') ⏳ PENDING
            @elseif($produksi->status === 'PROSES') 🔄 DIPROSES
            @else ✅ SELESAI
            @endif
        </div>
    </header>

    <main class="py-3 space-y-4">
        @if($errors->any())
        <div class="glass-card rounded-2xl p-4 border-l-4 border-red-500 slide-up">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-red-400 mb-1">⚠️ Gagal Memproses</h4>
                    <ul class="text-xs text-red-300/80 space-y-1 mb-3">
                        @foreach($errors->all() as $err)<li>• {{ $err }}</li>@endforeach
                    </ul>
                    <form method="POST" action="{{ route('operator.produksi.request_bahan', $produksi->id) }}">
                        @csrf<button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-2 rounded-xl transition-all text-sm">📦 Request Bahan Baku</button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="glass-card rounded-2xl p-4 slide-up">
            <div class="flex gap-3">
                <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1)">
                    @if(!empty($produksi->product->foto))<img src="{{ $produksi->product->foto }}" class="w-full h-full object-cover" alt="Produk">
                    @else<div class="w-full h-full flex items-center justify-center"><svg class="w-10 h-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>@endif
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-lg font-bold text-white leading-tight mb-2">{{ $produksi->product->nama_produk ?? 'Produk Dihapus' }}</h2>
                    <div class="flex flex-wrap gap-2">
                        @if($produksi->product->warna ?? false)<span class="text-xs px-3 py-0.5 rounded-full" style="background: rgba(255,255,255,0.1); color: #a1a1aa; border: 1px solid rgba(255,255,255,0.1)">🎨 {{ $produksi->product->warna }}</span>@endif
                        @if($produksi->product->jenis_motif ?? false)<span class="text-xs px-3 py-0.5 rounded-full" style="background: rgba(255,255,255,0.1); color: #a1a1aa; border: 1px solid rgba(255,255,255,0.1)">✨ {{ $produksi->product->jenis_motif }}</span>@endif
                    </div>
                </div>
            </div>
            
            <div class="mt-3 grid grid-cols-2 gap-3">
                <div class="rounded-xl p-3 text-center" style="background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3)">
                    <div class="w-8 h-8 mx-auto mb-2 rounded-lg flex items-center justify-center" style="background: rgba(59,130,246,0.2)">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Target</p>
                    <p class="text-2xl font-bold text-white">{{ $produksi->jumlah_produksi }}</p>
                    <p class="text-xs text-gray-400 mt-1">Pcs</p>
                </div>
                <div class="rounded-xl p-3 text-center" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3)">
                    <div class="w-8 h-8 mx-auto mb-2 rounded-lg flex items-center justify-center" style="background: rgba(239,68,68,0.2)">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Deadline</p>
                    <p class="text-lg font-bold text-red-400 mt-1">{{ $produksi->tanggal_target ? \Carbon\Carbon::parse($produksi->tanggal_target)->format('d M') : '-' }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $produksi->tanggal_target ? \Carbon\Carbon::parse($produksi->tanggal_target)->format('Y') : '' }}</p>
                </div>
            </div>

            @if(($produksi->jumlah_selesai ?? 0) > 0)
            <div class="mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.1)">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-green-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Sudah Diselesaikan
                    </span>
                    <span class="text-lg font-bold text-green-400">{{ $produksi->jumlah_selesai }} / {{ $produksi->jumlah_produksi }} pcs</span>
                </div>
                <div class="h-3 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.1)"><div class="h-full rounded-full progress-shimmer" style="width: {{ ($produksi->jumlah_selesai / $produksi->jumlah_produksi) * 100 }}%"></div></div>
                <p class="text-xs text-gray-400 mt-2 flex items-center justify-between">
                    <span>{{ round(($produksi->jumlah_selesai / $produksi->jumlah_produksi) * 100) }}% selesai</span>
                    <span>{{ $produksi->jumlah_produksi - $produksi->jumlah_selesai }} pcs remaining</span>
                </p>
            </div>
            @endif
        </div>

        @if(in_array($produksi->status, ['PENDING', 'PROSES']))
        <div class="glass-card rounded-2xl p-4 slide-up">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, rgba(16,185,129,0.3), rgba(6,95,70,0.3)); border: 1px solid rgba(16,185,129,0.3)">
                    @if($produksi->status === 'PENDING')<svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                    @else<svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>@endif
                </div>
                <h3 class="text-base font-bold text-white">
                    @if($produksi->status === 'PENDING') Mulai Produksi Baru @else Lanjutkan Produksi @endif
                </h3>
            </div>

            <form id="partialForm" action="{{ route('operator.produksi.partial', $produksi->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="text-sm text-gray-300 block mb-3">Jumlah yang akan diproduksi</label>
                    <div class="flex items-center justify-between rounded-2xl p-3" style="background: rgba(255,255,255,0.05); border: 2px solid rgba(255,255,255,0.1)">
                        <button type="button" onclick="decrementQty()" class="w-12 h-12 rounded-xl font-bold text-2xl transition-all active:scale-95" style="background: rgba(255,255,255,0.1); color: white">−</button>
                        <input type="number" name="jumlah_dikerjakan" id="jumlahInput" min="1" max="{{ $produksi->jumlah_produksi - ($produksi->jumlah_selesai ?? 0) }}" value="1" class="w-16 text-center text-2xl font-bold text-white bg-transparent focus:outline-none" required>
                        <button type="button" onclick="incrementQty()" class="w-12 h-12 rounded-xl font-bold text-2xl transition-all active:scale-95" style="background: rgba(255,255,255,0.1); color: white">+</button>
                    </div>
                    <p class="text-center mt-3 p-2 rounded-xl text-xs" style="background: rgba(255,255,255,0.05); color: #a1a1aa">📦 Sisa: {{ $produksi->jumlah_produksi - ($produksi->jumlah_selesai ?? 0) }} pcs</p>
                </div>

                <div class="mb-4">
                    <h4 class="text-sm text-gray-300 mb-3 flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>Estimasi Material:</h4>
                    <div class="rounded-xl overflow-hidden" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1)">
                        @if($produksi->product && $produksi->product->boms->count() > 0)
                        <table class="w-full text-xs">
                            <thead style="background: rgba(255,255,255,0.05)"><tr><th class="px-2 py-2 text-left text-gray-400 font-semibold">Material</th><th class="px-2 py-2 text-right text-gray-400 font-semibold">Total</th><th class="px-2 py-2 text-right text-gray-400 font-semibold">Stok</th></tr></thead>
                            <tbody id="bomTableBody" style="border-top: 1px solid rgba(255,255,255,0.1)">
                                @foreach($produksi->product->boms as $bom)
                                <tr class="bom-row transition" style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                                    <td class="px-2 py-2 text-gray-300">{{ $bom->material->nama_material ?? 'Material Hilang' }}</td>
                                    <td class="px-2 py-2 text-right text-green-400 font-bold"><span class="bom-qty" data-per-unit="{{ $bom->jumlah_kebutuhan }}">{{ $bom->jumlah_kebutuhan }}</span> {{ $bom->satuan }}</td>
                                    <td class="px-2 py-2 text-right @if(($bom->material->stok ?? 0) >= ($bom->jumlah_kebutuhan * ($produksi->jumlah_produksi - ($produksi->jumlah_selesai ?? 0)))) text-gray-400 @else text-red-400 font-bold low-stock-pulse @endif">{{ $bom->material->stok ?? 0 }}</td>
                                </tr>@endforeach
                            </tbody>
                        </table>
                        @else<div class="p-4 text-center text-gray-500 text-xs">BOM belum tersedia</div>@endif
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="w-full font-bold py-3 rounded-2xl flex items-center justify-center gap-2 transition-all active:scale-[0.98] text-sm btn-shimmer" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 20px rgba(16,185,129,0.3)">
                    @if($produksi->status === 'PENDING')<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>🚀 Mulai Produksi
                    @else<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>✅ Simpan Progres@endif
                </button>
            </form>
        </div>
        @endif

        <div>
            <h3 class="text-base font-bold text-gray-300 mb-3 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.3)"><svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg></div>
                Kebutuhan Material
            </h3>
            <div class="glass-card rounded-2xl overflow-hidden">
                @if($produksi->product && $produksi->product->boms->count() > 0)
                <div style="border-top: 1px solid rgba(255,255,255,0.1)">
                    @foreach($produksi->product->boms as $bom)
                    <div class="p-3 flex items-center justify-between transition bom-row" style="border-bottom: 1px solid rgba(255,255,255,0.05)">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-bold" style="background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(139,92,246,0.2)); color: #60a5fa; border: 1px solid rgba(59,130,246,0.3)">{{ $loop->iteration }}</div>
                            <div><p class="text-sm font-bold text-white">{{ $bom->material->nama_material ?? 'Material Hilang' }}</p><p class="text-xs text-gray-500">Stok: {{ $bom->material->stok ?? 0 }} {{ $bom->material->satuan }}</p></div>
                        </div>
                        <div class="text-right"><p class="text-lg font-bold text-white">{{ $bom->jumlah_kebutuhan * $produksi->jumlah_produksi }}</p><p class="text-xs text-gray-500">{{ $bom->satuan }}</p></div>
                    </div>@endforeach
                </div>
                @else<div class="p-6 text-center text-gray-500"><svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><p class="text-xs">Tidak ada data material</p></div>@endif
            </div>
        </div>
    </main>
    
    @if($produksi->status === 'SELESAI')
    <div class="fixed bottom-0 left-0 right-0 -mx-4 px-4 py-4" style="background: rgba(15,15,15,0.95); backdrop-filter: blur(10px); border-top: 1px solid rgba(255,255,255,0.1)">
        <div class="w-full text-gray-400 font-bold py-3 rounded-2xl flex items-center justify-center gap-2" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1)"><svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Tugas Selesai ✓</div>
    </div>
    @endif
</div>

@if(in_array($produksi->status, ['PENDING', 'PROSES']))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var jumlahInput = document.getElementById('jumlahInput');
    var bomRows = document.querySelectorAll('#bomTableBody tr');
    var maxQty = parseInt(jumlahInput.max) || 0;

    window.incrementQty = function() {
        var currentVal = parseInt(jumlahInput.value) || 0;
        if (currentVal < maxQty) { jumlahInput.value = currentVal + 1; updateBomTable(); }
    };
    window.decrementQty = function() {
        var currentVal = parseInt(jumlahInput.value) || 0;
        if (currentVal > 1) { jumlahInput.value = currentVal - 1; updateBomTable(); }
    };
    function updateBomTable() {
        var qty = parseInt(jumlahInput.value) || 0;
        var clampedQty = Math.min(Math.max(qty, 1), maxQty);
        bomRows.forEach(function(row) {
            var qtySpan = row.querySelector('.bom-qty');
            if (qtySpan) {
                var perUnit = parseFloat(qtySpan.dataset.perUnit);
                var total = perUnit * clampedQty;
                qtySpan.textContent = total.toFixed(2).endsWith('.00') ? Math.round(total) : total.toFixed(2);
            }
        });
    }
    jumlahInput.addEventListener('input', updateBomTable);
    jumlahInput.addEventListener('change', updateBomTable);
    updateBomTable();

    var form = document.getElementById('partialForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var qty = parseInt(jumlahInput.value) || 0;
            var isComplete = qty >= maxQty;
            Swal.fire({
                title: isComplete ? '🎉 Selesaikan Produksi?' : '💾 Simpan Progres?',
                text: isComplete ? 'Produksi akan diselesaikan dengan ' + qty + ' pcs.' : 'Akan menyimpan progres ' + qty + ' pcs.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isComplete ? '#22c55e' : '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: isComplete ? '✅ Ya!' : '💾 Simpan',
                cancelButtonText: 'Batal',
                background: '#1e293b', color: '#fff'
            }).then(function(result) { if (result.isConfirmed) form.submit(); });
        });
    }
});
</script>
@endif
@endsection
