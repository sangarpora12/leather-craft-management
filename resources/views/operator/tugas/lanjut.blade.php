@extends('layouts.operator_app')

@section('title', 'Selesaikan Produksi')

@section('main_class', 'pb-48')

@section('content')
<style>
/* ──────────────────────────────────────────────────────────────
   MODERN UI UPGRADE — Operator Production Completion
   ────────────────────────────────────────────────────────────── */

/* ── Global Styles ───────────────────────────────────────────── */
body {
    background-color: #080a10 !important;
    overflow-x: hidden !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* ── Enhanced Glass Card ─────────────────────────────────────── */
.glass-card {
    background: rgba(30, 30, 30, 0.6) !important;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px; /* Rounded UI */
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

/* ── Smooth Animations ───────────────────────────────────────── */
.slide-up {
    animation: slideUp 0.4s ease-out both;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}
.slide-up:nth-child(2) { animation-delay: .08s; }
.slide-up:nth-child(3) { animation-delay: .16s; }
.slide-up:nth-child(4) { animation-delay: .24s; }

/* ── Page Background ─────────────────────────────────────────── */
.page-bg {
    background-color: #0f172a;
    background-image: radial-gradient(ellipse at top, #1e293b 0%, transparent 60%);
}

/* ── Enhanced Progress Bar ───────────────────────────────────── */
.progress-shimmer {
    background: linear-gradient(90deg, #22c55e 0%, #4ade80 25%, #22c55e 50%, #4ade80 75%, #22c55e 100%);
    background-size: 200% 100%;
    animation: shimmer 2s linear infinite;
    border-radius: 10px;
}
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ── Enhanced Upload Zone ────────────────────────────────────── */
.upload-zone {
    border: 2px dashed rgba(16,185,129,0.6);
    border-radius: 15px;
    background: rgba(16,185,129,0.08);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.upload-zone:hover, .upload-zone.drag-over {
    border-color: rgba(16,185,129,0.9);
    background: rgba(16,185,129,0.12);
    transform: scale(1.02);
}
.upload-zone input[type=file] { display: none; }

/* ── Enhanced Foto Preview Grid ───────────────────────────────── */
.foto-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}
.foto-item {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid rgba(16,185,129,0.5);
    animation: popIn 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
@keyframes popIn {
    from { transform: scale(0.7) rotate(-2deg); opacity: 0; }
    to   { transform: scale(1) rotate(0); opacity: 1; }
}
.foto-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.foto-item .remove-btn {
    position: absolute;
    top: 3px;
    right: 3px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(239, 68, 68, 0.95);
    color: white;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}
.foto-item .remove-btn:hover {
    transform: scale(1.1);
    background: rgba(239, 68, 68, 1);
}

/* ── Enhanced Qty Stepper ─────────────────────────────────────── */
.qty-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255, 255, 255, 0.06);
    border: 2px solid rgba(255, 255, 255, 0.12);
    border-radius: 18px;
    padding: 8px;
    margin-bottom: 12px;
}
.qty-btn {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    font-size: 24px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.12);
    color: white;
    border: none;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
}
.qty-btn:active {
    transform: scale(0.95);
}
.qty-btn.inc {
    background: rgba(16, 185, 129, 0.3);
    color: #4ade80;
}
.qty-btn.dec {
    background: rgba(239, 68, 68, 0.25);
    color: #f87171;
}

/* ── Enhanced Tombol Selesai ─────────────────────────────────── */
.btn-selesai {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 6px 30px rgba(16, 185, 129, 0.5), 0 4px 15px rgba(16, 185, 129, 0.3);
    border: none;
    border-radius: 18px;
    padding: 18px;
    width: 100%;
    font-size: 16px;
    font-weight: 800;
    color: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.btn-selesai:hover {
    box-shadow: 0 8px 40px rgba(16, 185, 129, 0.6), 0 6px 20px rgba(16, 185, 129, 0.4);
    transform: translateY(-2px);
}
.btn-selesai:active {
    transform: scale(0.97);
}

/* ── Enhanced Info Badge ─────────────────────────────────────── */
.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 999px;
}

/* ── Enhanced Header Card ────────────────────────────────────── */
.header-card {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(26, 32, 44, 0.8));
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}
.header-card .product-title {
    font-size: 24px;
    font-weight: 800;
    color: white;
    margin-bottom: 8px;
}
.header-card .product-code {
    font-size: 14px;
    color: #60a5fa;
    font-family: 'Courier New', monospace;
}

/* ── Enhanced Stats Cards ────────────────────────────────────── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}
.stat-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    transition: all 0.3s;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}
.stat-card.blue {
    border-color: rgba(59, 130, 246, 0.3);
    color: #60a5fa;
}
.stat-card.green {
    border-color: rgba(16, 185, 129, 0.3);
    color: #34d399;
}
.stat-card.yellow {
    border-color: rgba(245, 158, 11, 0.3);
    color: #fbbf24;
}
.stat-card .stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    opacity: 0.8;
}
.stat-card .stat-value {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 4px;
}
.stat-card .stat-unit {
    font-size: 12px;
    opacity: 0.7;
}

/* ── Enhanced Progress Bar ───────────────────────────────────── */
.progress-container {
    background: rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 2px;
    margin-top: 12px;
}
.progress-bar {
    height: 12px;
    border-radius: 10px;
    background: linear-gradient(90deg, #22c55e, #4ade80);
    background-size: 200% 100%;
    animation: shimmer 2s linear infinite;
    transition: all 0.4s;
}

/* ── Enhanced Input Container ────────────────────────────────── */
.input-container {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

/* ── Enhanced Foto Upload Area ───────────────────────────────── */
.foto-upload-area {
    background: rgba(16, 185, 129, 0.04);
    border: 2px dashed rgba(16, 185, 129, 0.6);
    border-radius: 15px;
    padding: 40px 20px;
    text-align: center;
    transition: all 0.3s;
}
.foto-upload-area:hover {
    background: rgba(16, 185, 129, 0.06);
    border-color: rgba(16, 185, 129, 0.8);
}
.foto-upload-area .camera-icon {
    width: 60px;
    height: 60px;
    margin-bottom: 16px;
    color: #10b981;
}
.foto-upload-area .upload-text {
    font-size: 14px;
    color: #94a3b4;
    margin-bottom: 4px;
}
.foto-upload-area .upload-subtext {
    font-size: 12px;
    color: #64748b;
}

/* ── Enhanced Action Buttons ─────────────────────────────────── */
.action-buttons {
    display: flex;
    gap: 12px;
    margin-bottom: 100px; /* Padding bottom untuk menghindari tertutup nav */
}
.btn-back {
    background: rgba(100, 116, 139, 0.2);
    border: 1px solid rgba(148, 163, 184, 0.3);
    color: #94a3b4;
    padding: 16px 24px;
    border-radius: 15px;
    font-weight: 600;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-back:hover {
    background: rgba(100, 116, 139, 0.3);
    border-color: rgba(148, 163, 184, 0.5);
    color: #e2e8f0;
}
</style>
<div class="max-w-md mx-auto min-h-screen bg-[#0f172a] shadow-2xl relative page-bg px-4 pt-3">

    @include('components.operator-header', ['title' => 'SELESAIKAN PRODUKSI'])

    @php
        $produkNama   = $produksi->produk_jadi->nama_produk ?? 'Produk';
        $totalProses  = (int) ($produksi->total_proses_gabungan ?? $produksi->jumlah_produksi);
        $totalSelesai = (int) ($produksi->total_selesai_gabungan ?? 0);
        $sisaProses   = (int) ($produksi->total_sisa_proses_gabungan ?? max(0, $totalProses - $totalSelesai));
        $jumlahSpk    = (int) ($produksi->jumlah_spk_proses ?? 1);
        $progressPct  = $totalProses > 0 ? min(100, ($totalSelesai / $totalProses) * 100) : 0;
    @endphp

    <form id="selesaiForm"
          action="{{ route('operator.produksi.selesai', $produksi->id) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        {{-- Hidden: nilai terkonfirmasi yang dikirim controller --}}
        <input type="hidden" name="jumlah_selesai" id="hiddenJumlahSelesai" value="{{ $sisaProses }}">

        <main class="py-3 space-y-3">

            {{-- ── 1. Header Produk dengan detail & kode ─────────────────────────────── --}}
            <div class="header-card slide-up">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="product-title">{{ $produkNama }}</div>
                        <div class="product-code">{{ $produksi->produk_jadi->kode_produk ?? '-' }}</div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="info-badge" style="background:rgba(245,158,11,0.15);color:#fbbf24;border:1px solid rgba(245,158,11,0.3)">
                            ⚙️ {{ $jumlahSpk }} SPK aktif
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── 2. Stats Cards (3 kolom dengan ikon Lucide) ─────────────────────────── --}}
            <div class="stats-grid slide-up">
                <div class="stat-card blue">
                    <div class="stat-label">Total Diproses</div>
                    <div class="stat-value">{{ $totalProses }}</div>
                    <div class="stat-unit">Pcs</div>
                    <div class="mt-2 text-xs text-blue-400/60">
                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-card green">
                    <div class="stat-label">Selesai</div>
                    <div class="stat-value">{{ $totalSelesai }}</div>
                    <div class="stat-unit">Pcs</div>
                    <div class="mt-2 text-xs text-green-400/60">
                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-card yellow">
                    <div class="stat-label">Sisa</div>
                    <div class="stat-value" id="statSisa">{{ $sisaProses }}</div>
                    <div class="stat-unit">Pcs</div>
                    <div class="mt-2 text-xs text-amber-400/60">
                        <svg class="w-4 h-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- ── 3. Progress Bar yang lebih tebal dengan efek animated ───────────────── --}}
            <div class="progress-container slide-up">
                <div class="flex justify-between text-[10px] text-gray-400 mb-1.5">
                    <span>Progress keseluruhan</span>
                    <span id="statPct" class="font-bold text-emerald-300">{{ (int) round($progressPct) }}%</span>
                </div>
                <div class="h-4 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.08)">
                    <div class="h-full rounded-full progress-bar" id="progressBar" style="width:{{ $progressPct }}%"></div>
                </div>
            </div>

            {{-- ── 4. Input Jumlah dengan stepper yang lebih besar ───────────────────────── --}}
            <div class="input-container slide-up">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                         style="background:rgba(16,185,129,0.2)">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-white">Jumlah yang Diselesaikan Kloter Ini</h3>
                </div>

                <div class="qty-stepper mb-3">
                    <button type="button" class="qty-btn dec" onclick="decrementQty()">−</button>
                    {{-- Input ini hanya untuk tampilan; nilai real ada di hiddenJumlahSelesai --}}
                    <input type="number" id="qtyDisplay"
                           min="1" max="{{ $sisaProses }}" value="{{ $sisaProses }}"
                           class="w-24 text-center text-3xl font-black text-white bg-transparent focus:outline-none"
                           oninput="onQtyChange(this.value)">
                    <button type="button" class="qty-btn inc" onclick="incrementQty()">+</button>
                </div>

                <div class="rounded-xl p-3 text-sm text-center"
                     style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08)">
                    <span class="text-gray-400">Akan diselesaikan: </span>
                    <span id="labelQtySelesai" class="text-emerald-300 font-bold">{{ $sisaProses }}</span>
                    <span class="text-gray-400"> / {{ $totalProses }} Pcs</span>
                    &nbsp;·&nbsp;
                    <span class="text-gray-400">Sisa setelah ini: </span>
                    <span id="labelQtySisa" class="text-amber-300 font-bold">0</span>
                    <span class="text-gray-400"> Pcs</span>
                </div>

                {{-- Peringatan jika tidak selesai semua --}}
                <div id="partialWarning" class="mt-2 p-3 rounded-xl text-sm hidden"
                     style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);color:#fbbf24">
                    ⚠️ Kamu hanya menyelesaikan sebagian — sisa SPK tetap di tab <strong>PROSES</strong>.
                </div>
            </div>

            {{-- ── 5. Foto Upload Area yang lebih luas dengan ikon kamera besar ─────────── --}}
            <div class="glass-card slide-up">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                         style="background:rgba(59,130,246,0.2)">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Foto Bukti Produksi</h3>
                        <p class="text-[10px] text-gray-500">Opsional — maks. 10 foto, masing-masing ≤5 MB</p>
                    </div>
                    <div class="ml-auto">
                        <span id="fotoBadge" class="info-badge"
                              style="background:rgba(255,255,255,0.06);color:#94a3b4;border:1px solid rgba(255,255,255,0.1)">
                            0 foto
                        </span>
                    </div>
                </div>

                {{-- Drop zone --}}
                <div class="foto-upload-area p-8 text-center" id="uploadZone" onclick="document.getElementById('fotoInput').click()">
                    <input type="file" id="fotoInput" name="foto_bukti[]"
                           multiple accept="image/jpeg,image/png,image/jpg,image/webp"
                           onchange="handleFotoChange(this)">
                    <div class="camera-icon">
                        <svg class="w-12 h-12 text-emerald-500/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="upload-text">Ketuk untuk ambil foto hasil produksi</p>
                    <p class="upload-subtext">(Bisa banyak sekaligus)</p>
                </div>

                {{-- Preview grid --}}
                <div class="foto-grid" id="fotoGrid"></div>
            </div>

            {{-- ── 6. Tombol Aksi dengan layout yang lebih baik ───────────────────────────── --}}
            <div class="action-buttons slide-up">
                <button type="button" class="btn-selesai" onclick="handleSelesaiClick()">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Selesaikan Produksi
                </button>

                <a href="{{ route('operator.tugas.index') }}"
                   class="btn-back mt-3 w-full py-3 rounded-2xl flex items-center justify-center gap-2 text-sm font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Daftar Tugas
                </a>
            </div>

        </main>
    </form>

</div>

{{-- ══════════════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════════════ --}}
<script>
(function () {
    /* ── Konstanta dari Blade ──────────────────────────────────── */
    var MAX_QTY      = {{ $sisaProses }};   // sisa yang boleh diselesaikan
    var TOTAL_PROSES = {{ $totalProses }};
    var PRODUK_NAMA  = @json($produkNama);

    /* ── State foto ───────────────────────────────────────────── */
    var selectedFiles = [];   // array File objects

    /* ──────────────────────────────────────────────────────────
       QTY STEPPER
    ────────────────────────────────────────────────────────── */
    function getQty()  { return parseInt(document.getElementById('qtyDisplay').value, 10) || 1; }

    function setQty(v) {
        v = Math.max(1, Math.min(v, MAX_QTY));
        document.getElementById('qtyDisplay').value   = v;
        document.getElementById('hiddenJumlahSelesai').value = v;

        // Update label info
        document.getElementById('labelQtySelesai').textContent = v;
        document.getElementById('labelQtySisa').textContent    = MAX_QTY - v;
        document.getElementById('statSisa').textContent        = MAX_QTY - v;

        // Tampilkan warning cicil
        var warn = document.getElementById('partialWarning');
        warn.classList.toggle('hidden', v >= MAX_QTY);
    }

    window.onQtyChange  = function (val) { setQty(parseInt(val, 10) || 1); };
    window.incrementQty = function ()    { setQty(getQty() + 1); };
    window.decrementQty = function ()    { setQty(getQty() - 1); };

    // Inisialisasi
    setQty(MAX_QTY);

    /* ──────────────────────────────────────────────────────────
       FOTO UPLOAD & PREVIEW
    ────────────────────────────────────────────────────────── */
    window.handleFotoChange = function (input) {
        var incoming = Array.from(input.files);

        incoming.forEach(function (file) {
            // Cegah duplikat berdasarkan nama + ukuran
            var dup = selectedFiles.some(function (f) {
                return f.name === file.name && f.size === file.size;
            });
            if (!dup && selectedFiles.length < 10) selectedFiles.push(file);
        });

        renderFotoGrid();
        syncFileInput();
    };

    function renderFotoGrid() {
        var grid  = document.getElementById('fotoGrid');
        var badge = document.getElementById('fotoBadge');
        grid.innerHTML = '';
        badge.textContent = selectedFiles.length + ' foto';
        badge.style.color = selectedFiles.length > 0 ? '#4ade80' : '#94a3b8';

        selectedFiles.forEach(function (file, idx) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var item = document.createElement('div');
                item.className = 'foto-item';
                item.innerHTML =
                    '<img src="' + e.target.result + '" alt="">' +
                    '<button type="button" class="remove-btn" onclick="removeFile(' + idx + ')">✕</button>';
                grid.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }

    window.removeFile = function (idx) {
        selectedFiles.splice(idx, 1);
        renderFotoGrid();
        syncFileInput();
    };

    // Rebuild DataTransfer agar file input native selaras dengan selectedFiles
    function syncFileInput() {
        var dt    = new DataTransfer();
        selectedFiles.forEach(function (f) { dt.items.add(f); });
        document.getElementById('fotoInput').files = dt.files;
    }

    // Drag & drop support
    var zone = document.getElementById('uploadZone');
    zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', function ()  { zone.classList.remove('drag-over'); });
    zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('drag-over');
        var pseudo = { files: e.dataTransfer.files };
        window.handleFotoChange(pseudo);
    });

    /* ──────────────────────────────────────────────────────────
       POPUP KONFIRMASI — SweetAlert2
    ────────────────────────────────────────────────────────── */
    window.handleSelesaiClick = function () {
        var qty       = getQty();
        var sisaAfter = MAX_QTY - qty;

        Swal.fire({
            title : '<span style="color:#e2e8f0;font-size:19px;font-weight:800;">Konfirmasi Selesai</span>',
            html: `
                <div style="text-align:center; padding: 10px;">
                    <div style="font-size: 14px; color: #94a3b8; margin-bottom: 10px;">KONFIRMASI PENYELESAIAN</div>
                    <div style="font-size: 48px; font-weight: 900; color: #10b981; line-height: 1;">${qty} <span style="font-size: 20px;">Pcs</span></div>
                    <div style="font-size: 16px; font-weight: 700; color: #f8fafc; margin: 15px 0;">${PRODUK_NAMA}</div>
                    <div style="background: rgba(255,255,255,0.05); border-radius: 12px; padding: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <div style="font-size: 12px; color: #94a3b8;">Sisa tugas setelah ini:</div>
                        <div style="font-size: 18px; font-weight: 800; color: #fbbf24;">${sisaAfter} Pcs</div>
                    </div>
                </div>
            `,
            showCancelButton  : true,
            confirmButtonText : '✅ Ya, Selesaikan!',
            cancelButtonText  : '← Kembali',
            confirmButtonColor: '#10b981',
            cancelButtonColor : '#475569',
            background        : '#1e293b',
            color             : '#e2e8f0',
            width             : '380px',
            padding           : '24px',
            input             : false,
        }).then(function (result) {
            if (!result.isConfirmed) return;

            /* Loading screen */
            Swal.fire({
                title             : 'Menyimpan...',
                html              : '<div style="text-align:center;padding:24px">'
                                  + '<div style="width:52px;height:52px;border:4px solid rgba(16,185,129,0.2);border-top:4px solid #10b981;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 14px"></div>'
                                  + '<p style="color:#9ca3af;font-size:14px">Memproses dan menyimpan data...</p>'
                                  + '</div>'
                                  + '<style>@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}</style>',
                showConfirmButton : false,
                background        : '#1e293b',
                allowOutsideClick : false,
                allowEscapeKey    : false,
            });

            // Pastikan hidden input sudah sinkron sebelum submit
            document.getElementById('hiddenJumlahSelesai').value = qty;

            setTimeout(function () {
                document.getElementById('selesaiForm').submit();
            }, 500);
        });
    };

})();
</script>
@endsection