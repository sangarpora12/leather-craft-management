@extends('layouts.operator_app')

@section('content')
<div class="min-h-screen bg-[#0B0E14] pb-8">
    <div class="w-full px-3 py-4">
        <form action="{{ route('operator.scan.save') }}" method="POST" id="scanForm">
            @csrf

            <div id="warnaErrorContainer"></div>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 p-3 mb-4 rounded-xl text-sm text-red-300">
                    ⚠️ @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <!-- HEADER -->
            <div class="bg-[#1C222D] rounded-xl border border-white/5 p-3 mb-4">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <div class="text-[10px] text-blue-400 font-bold uppercase mb-1">Supplier</div>
                        <input type="text" name="supplier" value="{{ session('ocr_result.supplier') ?? '' }}" class="w-full px-3 py-2 bg-[#0B0E14] border border-white/10 rounded-lg text-white text-sm" placeholder="Nama Toko" required>
                    </div>
                    <div>
                        <div class="text-[10px] text-green-400 font-bold uppercase mb-1">No Nota</div>
                        <input type="text" name="no_nota" value="{{ session('ocr_result.metadata.no_nota') ?? '' }}" class="w-full px-3 py-2 bg-[#0B0E14] border border-white/10 rounded-lg text-white text-sm" placeholder="No Nota">
                    </div>
                    <div>
                        <div class="text-[10px] text-purple-400 font-bold uppercase mb-1">Tanggal</div>
                        <input type="text" name="tanggal" value="{{ session('ocr_result.metadata.tanggal') ?? date('Y-m-d') }}" class="w-full px-3 py-2 bg-[#0B0E14] border border-white/10 rounded-lg text-white text-sm">
                    </div>
                    <div>
                        <div class="text-[10px] text-amber-400 font-bold uppercase mb-1">Kontak</div>
                        <input type="text" name="alamat_kontak" value="{{ session('ocr_result.metadata.alamat_kontak') ?? '' }}" class="w-full px-3 py-2 bg-[#0B0E14] border border-white/10 rounded-lg text-white text-sm">
                    </div>
                </div>
            </div>

            <!-- DAFTAR BARANG -->
            <div class="bg-[#1C222D] rounded-xl border border-white/5 p-3 mb-4">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-base font-bold text-white">📦 Daftar Barang</h2>
                </div>

                @if(session('ocr_result.items') && count(session('ocr_result.items')) > 0)
                    <div id="itemsContainer" class="space-y-4">
                        @foreach(session('ocr_result.items') as $index => $item)
                        @php $qtyTotal = (int)($item['qty'] ?? 1); $itemName = $item['nama'] ?? ''; @endphp
                        <div class="item-card bg-[#0B0E14] border border-white/10 rounded-xl p-4" data-index="{{ $index }}" data-total-qty="{{ $qtyTotal }}">
                            <!-- Nama Bahan -->
                            <div class="mb-3">
                                <label class="text-[10px] text-gray-500 uppercase font-bold">Nama Bahan</label>
                                <textarea name="items[{{ $index }}][nama]" rows="1" class="w-full text-base text-white bg-transparent border-0 border-b-2 border-white/20 focus:border-blue-500 outline-none resize-none px-1 py-2" placeholder="Nama Bahan" required>{{ $itemName }}</textarea>
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $index }}">
                            </div>

                            <!-- Satuan, Harga, Jumlah -->
                            <div class="grid grid-cols-3 gap-3 mb-3">
                                <div>
                                    <label class="text-[10px] text-gray-500 uppercase font-bold">Satuan</label>
                                    <input type="text" name="items[{{ $index }}][satuan]" value="{{ $item['satuan'] ?? 'Pcs' }}" class="w-full px-3 py-2 text-sm text-white bg-[#0B0E14] border border-white/20 rounded-lg text-center" placeholder="Stn">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 uppercase font-bold">Harga (Rp)</label>
                                    <input type="number" name="items[{{ $index }}][harga]" value="{{ $item['harga'] ?? 0 }}" class="item-harga w-full px-3 py-2 text-sm text-white bg-[#0B0E14] border border-white/20 rounded-lg text-right {{ ($item['harga'] ?? 0) == 0 ? 'border-red-500/50' : '' }}" min="0" step="100" required>
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500 uppercase font-bold">Jumlah</label>
                                    <input type="number" name="items[{{ $index }}][qty]" value="{{ $qtyTotal }}" class="item-qty w-full px-3 py-2 text-sm text-white bg-[#0B0E14] border border-white/20 rounded-lg text-center" min="1" required>
                                </div>
                            </div>

                            <!-- Subtotal -->
                            <div class="text-right mb-3">
                                <span class="text-xs text-gray-500">Subtotal: </span>
                                <span class="text-base font-bold text-green-400 subtotal-{{ $index }}">Rp {{ number_format(($item['harga'] ?? 0) * $qtyTotal, 0, ',', '.') }}</span>
                            </div>

                            <!-- PECAH WARNA -->
                            <div class="bg-[#0B0E14] border border-blue-500/20 rounded-xl p-3">
                                <label class="text-[10px] text-blue-400 uppercase font-bold mb-2 flex items-center gap-1">
                                    🎨 Pecah Warna 
                                    <span class="text-gray-500 font-normal normal-case">(total: <span class="warna-sum">{{ $qtyTotal }}</span>)</span>
                                </label>
                                <div class="warna-list space-y-2" data-item-index="{{ $index }}" data-total-qty="{{ $qtyTotal }}">
                                    <div class="warna-row bg-[#161A22] rounded-lg p-3 relative">
                                        <button type="button" class="remove-warna-btn absolute -top-2 -right-2 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-lg" title="Hapus warna">×</button>
                                        <select name="items[{{ $index }}][warna][0][nama]" class="warna-select w-full px-3 py-3 text-sm text-white bg-[#0B0E14] border border-white/20 rounded-lg mb-2">
                                            <option value="">-- Pilih Warna --</option>
                                            @foreach($warnas as $w)
                                                <option value="{{ $w->nama_warna }}">{{ $w->nama_warna }}</option>
                                            @endforeach
                                        </select>
                                        <label class="text-[10px] text-gray-500 mb-1 block">Jumlah:</label>
                                        <input type="number" name="items[{{ $index }}][warna][0][qty]" value="{{ $qtyTotal }}" min="0" class="warna-qty w-full px-4 py-3 text-base text-white bg-[#0B0E14] border border-white/20 rounded-lg text-center" placeholder="0">
                                    </div>
                                    <button type="button" class="add-warna-btn w-full py-2 border-2 border-dashed border-green-500/30 rounded-lg text-sm text-green-400 hover:text-green-300 hover:border-green-500 transition-all flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        <span>Tambah Warna</span>
                                    </button>
                                </div>
                                <div class="text-red-400 text-xs mt-2 warna-error hidden">⚠️ Jumlah pecah warna ( <span class="warna-error-sum">0</span> ) tidak sesuai total ( <span class="warna-error-total">0</span> )</div>
                            </div>

                            <!-- Hapus -->
                            <div class="text-right mt-2">
                                <button type="button" class="delete-row text-red-500 hover:text-red-400 text-xs px-2 py-1">✕ Hapus barang ini</button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-row" class="mt-4 w-full py-3 border-2 border-dashed border-white/20 rounded-xl text-sm text-gray-400 hover:text-white hover:border-emerald-500 transition-all">
                        ➕ Tambah Barang
                    </button>

                @else
                    <div class="text-center py-10">
                        <p class="text-gray-400 text-base mb-4">Tidak ada item terdeteksi.</p>
                        <button type="button" id="add-row" class="w-full py-3 border-2 border-dashed border-white/20 rounded-xl text-sm text-gray-400 hover:text-white hover:border-emerald-500 transition-all">
                            ➕ Tambah Barang
                        </button>
                    </div>
                @endif
            </div>

            <!-- TOTAL -->
            <div class="bg-[#1C222D] rounded-xl border border-white/5 p-4 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-400">💰 Total Bayar</span>
                    <span class="text-2xl font-bold text-white" id="total_bayar_display">Rp 0</span>
                    <input type="hidden" id="total_bayar" name="total_bayar" value="0">
                </div>
            </div>

            <!-- TOMBOL -->
            <div class="flex gap-3 justify-end">
                <a href="{{ url('/operator/scan') }}" class="flex-1 text-center px-4 py-3 border border-white/20 rounded-xl text-sm font-medium text-white bg-[#161A22]">🔄 Scan Ulang</a>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl text-sm font-medium text-white bg-emerald-600 disabled:opacity-50">✅ Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function calcItem(card) {
    const idx = card.dataset.index;
    const harga = parseInt(card.querySelector('.item-harga').value) || 0;
    const qty = parseInt(card.querySelector('.item-qty').value) || 0;
    const sub = card.querySelector('.subtotal-' + idx);
    if (sub) sub.textContent = 'Rp ' + (harga * qty).toLocaleString('id-ID');
    calcTotal();
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('[class*="subtotal-"]').forEach(function(el) {
        total += parseInt(el.textContent.replace(/[^0-9]/g, '')) || 0;
    });
    document.getElementById('total_bayar').value = total;
    document.getElementById('total_bayar_display').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function sumWarna(card) {
    const wl = card.querySelector('.warna-list'); if (!wl) return true;
    const totalQty = parseInt(card.dataset.totalQty) || 0;
    let sum = 0;
    wl.querySelectorAll('.warna-qty').forEach(function(i) { sum += parseInt(i.value) || 0; });
    card.querySelector('.warna-sum').textContent = sum;
    
    // Update error message dengan angka
    const err = card.querySelector('.warna-error');
    const errSum = card.querySelector('.warna-error-sum');
    const errTotal = card.querySelector('.warna-error-total');
    if (errSum) errSum.textContent = sum;
    if (errTotal) errTotal.textContent = totalQty;
    
    if (sum !== totalQty) { 
        err.classList.remove('hidden'); 
        return false; 
    } else { 
        err.classList.add('hidden'); 
        return true; 
    }
}

function validateAll() {
    const cards = document.querySelectorAll('.item-card');
    let errors = [];
    cards.forEach(function(card) {
        const nama = (card.querySelector('textarea')?.value || 'Item').trim().substring(0, 25);
        let hasColor = false;
        card.querySelectorAll('.warna-select').forEach(function(s) { if (s.value !== '') hasColor = true; });
        if (!hasColor) errors.push(nama + ' — warna belum dipilih');
        if (!sumWarna(card)) errors.push(nama + ' — jumlah pecah warna ' + card.querySelector('.warna-sum').textContent + ' tidak sesuai total ' + card.dataset.totalQty);
    });
    const btn = document.querySelector('button[type="submit"]');
    const ec = document.getElementById('warnaErrorContainer');
    if (errors.length > 0) {
        btn.disabled = true;
        ec.innerHTML = '<div class="bg-red-500/10 border border-red-500/30 p-3 mb-4 rounded-xl text-sm text-red-300">⚠️ ' + errors.join('<br>') + '</div>';
        ec.classList.remove('hidden');
    } else {
        btn.disabled = false;
        ec.classList.add('hidden');
        ec.innerHTML = '';
    }
}

function tambahWarna(btn) {
    const wl = btn.closest('.warna-list'); if (!wl) return;
    const idx = wl.dataset.itemIndex;
    const newIdx = wl.querySelectorAll('.warna-row').length;
    const row = document.createElement('div');
    row.className = 'warna-row bg-[#161A22] rounded-lg p-3 relative';
    row.innerHTML = `
        <button type="button" class="remove-warna-btn absolute -top-2 -right-2 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-lg">×</button>
        <select name="items[${idx}][warna][${newIdx}][nama]" class="warna-select w-full px-3 py-3 text-sm text-white bg-[#0B0E14] border border-white/20 rounded-lg mb-2">
            <option value="">-- Pilih Warna --</option>
            @foreach($warnas as $w)
                <option value="{{ $w->nama_warna }}">{{ $w->nama_warna }}</option>
            @endforeach
        </select>
        <label class="text-[10px] text-gray-500 mb-1 block">Jumlah:</label>
        <input type="number" name="items[${idx}][warna][${newIdx}][qty]" value="0" min="0" class="warna-qty w-full px-4 py-3 text-base text-white bg-[#0B0E14] border border-white/20 rounded-lg text-center" placeholder="0">
    `;
    // Sisipkan sebelum tombol "Tambah Warna"
    wl.insertBefore(row, btn);
    
    const card = btn.closest('.item-card');
    row.querySelector('.warna-select').addEventListener('change', function() { validateAll(); });
    row.querySelector('.warna-qty').addEventListener('input', function() { if (card) sumWarna(card); validateAll(); });
    row.querySelector('.remove-warna-btn').addEventListener('click', function(e) { 
        e.preventDefault();
        this.closest('.warna-row').remove(); 
        if (card) { sumWarna(card); validateAll(); } 
    });
    validateAll();
}

let nextIdx = {{ count(session('ocr_result.items', [])) }};

function tambahBaris() {
    const idx = nextIdx++;
    const card = document.createElement('div');
    card.className = 'item-card bg-[#0B0E14] border border-white/10 rounded-xl p-4';
    card.dataset.index = idx;
    card.dataset.totalQty = 1;
    card.innerHTML = `
        <div class="mb-3">
            <label class="text-[10px] text-gray-500 uppercase font-bold">Nama Bahan</label>
            <textarea name="items[${idx}][nama]" rows="1" class="w-full text-base text-white bg-transparent border-0 border-b-2 border-white/20 focus:border-blue-500 outline-none resize-none px-1 py-2" placeholder="Nama Bahan" required></textarea>
            <input type="hidden" name="items[${idx}][id]" value="${idx}">
        </div>
        <div class="grid grid-cols-3 gap-3 mb-3">
            <div><label class="text-[10px] text-gray-500 uppercase font-bold">Satuan</label><input type="text" name="items[${idx}][satuan]" value="Pcs" class="w-full px-3 py-2 text-sm text-white bg-[#0B0E14] border border-white/20 rounded-lg text-center"></div>
            <div><label class="text-[10px] text-gray-500 uppercase font-bold">Harga (Rp)</label><input type="number" name="items[${idx}][harga]" value="0" class="item-harga w-full px-3 py-2 text-sm text-white bg-[#0B0E14] border border-red-500/50 rounded-lg text-right" min="0" step="100" required></div>
            <div><label class="text-[10px] text-gray-500 uppercase font-bold">Jumlah</label><input type="number" name="items[${idx}][qty]" value="1" class="item-qty w-full px-3 py-2 text-sm text-white bg-[#0B0E14] border border-white/20 rounded-lg text-center" min="1" required></div>
        </div>
        <div class="text-right mb-3"><span class="text-xs text-gray-500">Subtotal: </span><span class="text-base font-bold text-green-400 subtotal-${idx}">Rp 0</span></div>
        <div class="bg-[#0B0E14] border border-blue-500/20 rounded-xl p-3">
            <label class="text-[10px] text-blue-400 uppercase font-bold mb-2">🎨 Pecah Warna <span class="text-gray-500 font-normal normal-case">(total: <span class="warna-sum">1</span>)</span></label>
            <div class="warna-list space-y-2" data-item-index="${idx}" data-total-qty="1">
                <div class="warna-row bg-[#161A22] rounded-lg p-3 relative">
                    <button type="button" class="remove-warna-btn absolute -top-2 -right-2 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-lg">×</button>
                    <select name="items[${idx}][warna][0][nama]" class="warna-select w-full px-3 py-3 text-sm text-white bg-[#0B0E14] border border-white/20 rounded-lg mb-2"><option value="">-- Pilih Warna --</option>
@foreach($warnas as $w)
<option value="{{ $w->nama_warna }}">{{ $w->nama_warna }}</option>
@endforeach
</select>
                    <label class="text-[10px] text-gray-500 mb-1 block">Jumlah:</label>
                    <input type="number" name="items[${idx}][warna][0][qty]" value="1" min="0" class="warna-qty w-full px-4 py-3 text-base text-white bg-[#0B0E14] border border-white/20 rounded-lg text-center">
                </div>
                <button type="button" class="add-warna-btn w-full py-2 border-2 border-dashed border-green-500/30 rounded-lg text-sm text-green-400 hover:text-green-300 hover:border-green-500 transition-all flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Tambah Warna</span>
                </button>
            </div>
            <div class="text-red-400 text-xs mt-2 warna-error hidden">⚠️ Jumlah pecah warna ( <span class="warna-error-sum">0</span> ) tidak sesuai total ( <span class="warna-error-total">0</span> )</div>
        </div>
        <div class="text-right mt-2"><button type="button" class="delete-row text-red-500 hover:text-red-400 text-xs px-2 py-1">✕ Hapus barang ini</button></div>
    `;
    document.getElementById('itemsContainer').appendChild(card);
    attachEvents(card);
    validateAll();
}

function attachEvents(card) {
    card.querySelector('.item-harga')?.addEventListener('input', function() { calcItem(card); });
    card.querySelector('.item-qty')?.addEventListener('input', function() {
        const q = parseInt(this.value) || 0;
        card.dataset.totalQty = q;
        const wl = card.querySelector('.warna-list');
        if (wl) wl.dataset.totalQty = q;
        // Auto-update qty row pertama jika cuma 1 row
        const wr = card.querySelectorAll('.warna-row');
        if (wr.length === 1) { const qi = wr[0].querySelector('.warna-qty'); if (qi) qi.value = q; }
        calcItem(card);
        validateAll();
    });
    card.querySelector('.delete-row')?.addEventListener('click', function() { card.remove(); calcTotal(); validateAll(); });
    card.querySelector('.add-warna-btn')?.addEventListener('click', function(e) { e.preventDefault(); tambahWarna(this); });
    card.querySelectorAll('.warna-select').forEach(function(s) { s.addEventListener('change', validateAll); });
    card.querySelectorAll('.warna-qty').forEach(function(q) { q.addEventListener('input', function() { sumWarna(card); validateAll(); }); });
    card.querySelectorAll('.remove-warna-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) { 
            e.preventDefault();
            this.closest('.warna-row').remove(); 
            sumWarna(card); 
            validateAll(); 
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.item-card').forEach(attachEvents);
    const ab = document.getElementById('add-row'); if (ab) ab.addEventListener('click', tambahBaris);
    setTimeout(validateAll, 100);
    calcTotal();
});
</script>
@endsection