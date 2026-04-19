@extends('layouts.operator_app')

@section('content')
<div class="w-full max-w-md mx-auto bg-gray-900 text-white font-normal" style="font-family: 'Poppins', sans-serif;">
    <!-- Header -->
    @include('components.operator-header', ['title' => 'PENERIMAAN BAHAN'])

    <!-- Filter Deadline -->
    <div class="px-5 py-3">
        <form method="GET" action="{{ route('operator.belanja.index') }}">
            <select name="deadline_filter" onchange="this.form.submit()" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-sm text-white focus:border-emerald-500 focus:outline-none">
                <option value="" {{ !request('deadline_filter') ? 'selected' : '' }}>📅 Semua Deadline</option>
                <option value="hari_ini" {{ request('deadline_filter') == 'hari_ini' ? 'selected' : '' }}>📍 Hari Ini</option>
                <option value="minggu_ini" {{ request('deadline_filter') == 'minggu_ini' ? 'selected' : '' }}>📆 Minggu Ini</option>
                <option value="terlewat" {{ request('deadline_filter') == 'terlewat' ? 'selected' : '' }}>⚠️ Terlewat (Overdue)</option>
            </select>
        </form>
    </div>

    <!-- Stats -->
    <div class="px-5 py-3">
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 font-normal uppercase">Menunggu Penerimaan</span>
                        <div class="text-lg font-normal text-white">{{ $pendingPengadaan->total() }} Item</div>
                    </div>
                </div>
                <span class="text-xs bg-green-500/20 text-green-500 border border-green-500/30 px-3 py-1 rounded-full font-normal uppercase">Pending</span>
            </div>
        </div>
    </div>

    <!-- Pending Procurement Tasks List -->
    <div class="px-5 pb-4">
        @forelse($pendingPengadaan as $pengadaan)
        @php
            $sisa = $pengadaan->jumlah - ($pengadaan->received ?? 0);
            $sudahDiterima = $pengadaan->received ?? 0;
            $deadline = \Carbon\Carbon::parse($pengadaan->tanggal_dibutuhkan);
            $isOverdue = $deadline->isPast() && $sisa > 0;
        @endphp
        <div class="rounded-xl p-5 mb-4 {{ $isOverdue ? 'bg-red-500/10 border border-red-500/40' : 'bg-gray-800 border border-gray-700' }}">
            <div class="flex gap-4 items-start mb-4">
                <!-- FOTO BAHAN -->
                <div class="flex-shrink-0">
                    @if($pengadaan->bahan && $pengadaan->bahan->foto)
                        <img src="{{ asset('storage/' . $pengadaan->bahan->foto) }}" class="w-14 h-14 rounded-xl object-cover border border-gray-700">
                    @else
                        <div class="w-14 h-14 rounded-xl bg-gray-700 flex items-center justify-center border border-gray-700">
                            <i class="fa-solid fa-box text-gray-500 text-xl"></i>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        @if($pengadaan->prioritas === 'URGENT')
                        <div class="bg-red-500/20 border border-red-500/20 rounded px-2 py-1 inline-flex items-center">
                            <div class="w-2 h-2 bg-red-500 rounded mr-2 animate-pulse"></div>
                            <span class="text-xs text-red-500 font-normal uppercase">{{ $pengadaan->prioritas }}</span>
                        </div>
                        @else
                        <div class="bg-blue-500/20 border border-blue-500/20 rounded px-2 py-1 inline-flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                            <span class="text-xs text-blue-500 font-normal uppercase">{{ $pengadaan->prioritas }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="text-base font-normal text-white truncate">{{ $pengadaan->bahan->nama_material ?? 'Material Tidak Ditemukan' }}</div>
                    <div class="text-sm text-gray-500 font-normal">No Ref: {{ $pengadaan->no_referensi }}</div>
                </div>
                <div class="text-right flex-shrink-0">
                    <span class="text-xs text-gray-500 font-normal">Dipesan</span>
                    <div class="text-lg font-normal text-blue-400">{{ $pengadaan->jumlah }} <span class="text-xs">{{ $pengadaan->bahan->satuan ?? 'pcs' }}</span></div>
                </div>
            </div>
            
            <!-- Status Penerimaan -->
            <div class="bg-gray-900/50 rounded-lg p-3 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-400 font-normal">Sudah Diterima</span>
                    <span class="text-sm font-normal text-green-400">{{ $sudahDiterima }} {{ $pengadaan->bahan->satuan ?? 'pcs' }}</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2 mb-2">
                    @php $progress = $pengadaan->jumlah > 0 ? ($sudahDiterima / $pengadaan->jumlah) * 100 : 0; @endphp
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 font-normal">Sisa Belum Datang</span>
                    <span class="text-sm font-normal text-orange-400">{{ $sisa }} {{ $pengadaan->bahan->satuan ?? 'pcs' }}</span>
                </div>
            </div>
            
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 {{ $isOverdue ? 'text-red-400' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-xs font-normal {{ $isOverdue ? 'text-red-400 font-bold' : 'text-gray-400' }}">Deadline: {{ \Carbon\Carbon::parse($pengadaan->tanggal_dibutuhkan)->format('d M Y') }}</span>
                </div>
                <span class="text-xs bg-gray-700 text-gray-300 border border-gray-600 px-3 py-1 rounded font-normal">{{ $pengadaan->bahan->satuan ?? 'Unit' }}</span>
            </div>
            
            @if($pengadaan->catatan)
            <div class="mb-4">
                <span class="text-xs text-gray-400 font-normal">Catatan: {{ $pengadaan->catatan }}</span>
            </div>
            @endif
            
            @if($sisa > 0)
            <div class="w-full py-2 rounded-lg text-sm font-normal flex items-center justify-center gap-2 {{ $isOverdue ? 'bg-red-500/10 border border-red-500 text-red-400' : 'bg-gray-700/50 text-gray-400' }}">
                @if($isOverdue)
                <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>⚠️ DEADLINE TERLEWAT - SEGERA BELI</span>
                @else
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Menunggu Penerimaan</span>
                @endif
            </div>
            @else
            <!-- Sudah lengkap -->
            <div class="w-full bg-green-500/10 border border-green-500/20 text-green-400 py-3 rounded-lg text-sm font-normal uppercase flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Selesai - Otomatis terupdate
            </div>
            @endif
        </div>
        @empty
        <div class="py-12 border-2 border-dashed border-gray-700 rounded-xl text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <span class="text-sm text-gray-500 font-normal">Tidak ada bahan yang menunggu penerimaan</span>
        </div>
        @endforelse

        @if($pendingPengadaan->hasPages())
            <div class="mt-6">
                {{ $pendingPengadaan->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Penerimaan Bahan -->
<div id="receiveModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 rounded-2xl w-full max-w-sm">
        <div class="p-5 border-b border-gray-700">
            <h3 class="text-lg font-bold text-white">Terima Barang</h3>
            <p class="text-sm text-gray-400" id="modalMaterialName">Nama Material</p>
        </div>
        
        <form id="receiveForm" method="POST">
            @csrf
            <div class="p-5">
                <div class="bg-gray-900/50 rounded-lg p-4 mb-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-xs text-gray-400">Jumlah Dipesan</span>
                        <span class="text-sm font-bold text-white" id="modalJumlahDipesan">0</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-xs text-gray-400">Sudah Diterima</span>
                        <span class="text-sm font-bold text-green-400" id="modalSudahDiterima">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs text-gray-400">Sisa</span>
                        <span class="text-sm font-bold text-orange-400" id="modalSisa">0</span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="text-xs text-gray-400 block mb-2">Jumlah Barang Diterima</label>
                    <input type="number" 
                           name="jumlah_diterima" 
                           id="jumlahDiterima"
                           min="1" 
                           class="w-full bg-gray-900 border border-gray-600 rounded-lg px-4 py-3 text-white font-bold text-lg focus:outline-none focus:border-green-500"
                           required>
                    <p class="text-xs text-gray-500 mt-2">Masukkan jumlah barang yang benar-benar datang</p>
                </div>
                
                <input type="hidden" name="pengadaan_id" id="pengadaanId">
            </div>
            
            <div class="p-5 border-t border-gray-700 flex gap-3">
                <button type="button" onclick="closeReceiveModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-lg font-normal uppercase">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-400 text-white py-3 rounded-lg font-bold uppercase">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openReceiveModal(id, nama, jumlah, sudahTerima, sisa, satuan) {
    document.getElementById('pengadaanId').value = id;
    document.getElementById('modalMaterialName').textContent = nama;
    document.getElementById('modalJumlahDipesan').textContent = jumlah + ' ' + satuan;
    document.getElementById('modalSudahDiterima').textContent = sudahTerima + ' ' + satuan;
    document.getElementById('modalSisa').textContent = sisa + ' ' + satuan;
    document.getElementById('jumlahDiterima').max = sisa;
    document.getElementById('jumlahDiterima').value = '';
    document.getElementById('jumlahDiterima').focus();
    
    // Update form action
    const form = document.getElementById('receiveForm');
    form.action = '/operator/pengadaan/' + id + '/receive';
    
    document.getElementById('receiveModal').classList.remove('hidden');
}

function closeReceiveModal() {
    document.getElementById('receiveModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('receiveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReceiveModal();
    }
});

// Handle form submission
document.getElementById('receiveForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const jumlahInput = document.getElementById('jumlahDiterima');
    const jumlah = parseInt(jumlahInput.value);
    const max = parseInt(jumlahInput.max);
    
    if (!jumlah || jumlah < 1) {
        alert('Masukkan jumlah yang valid');
        return;
    }
    
    if (jumlah > max) {
        alert('Jumlah tidak boleh melebihi sisa (' + max + ')');
        return;
    }
    
    // Submit form
    this.submit();
});
</script>
@endsection