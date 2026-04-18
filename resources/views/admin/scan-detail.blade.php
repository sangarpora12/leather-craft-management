@extends('layouts.admin')

@section('title', 'Detail Scan Nota - ' . $bahanMasuk->no_transaksi)

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('admin.scan.pending') }}" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Detail Scan Nota</h1>
            </div>
            
            <!-- Status Badge -->
            <div class="flex items-center gap-4">
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    @if($bahanMasuk->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($bahanMasuk->status === 'verified') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    @if($bahanMasuk->status === 'pending')
                        ⏳ Menunggu Verifikasi
                    @elseif($bahanMasuk->status === 'verified')
                        ✅ Terverifikasi
                    @else
                        ❌ Ditolak
                    @endif
                </span>
                <span class="text-gray-600">{{ $bahanMasuk->no_transaksi }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column - Photo Evidence -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gray-800 text-white px-6 py-4">
                    <h2 class="text-lg font-semibold">📸 Bukti Fisik Nota</h2>
                </div>
                <div class="p-6">
                    @if($bahanMasuk->foto_nota)
                        <div class="relative">
                            <img src="{{ $bahanMasuk->foto_nota_url }}" 
                                 alt="Nota Scan" 
                                 class="w-full h-auto rounded-lg shadow-md"
                                 onclick="this.requestFullscreen()">
                            <p class="text-sm text-gray-500 mt-2 text-center">
                                Klik gambar untuk melihat fullscreen
                            </p>
                        </div>
                    @else
                        <div class="bg-gray-100 rounded-lg p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-500">Foto nota tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Digital Data -->
            <div class="space-y-6">
                <!-- Material Details Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-blue-600 text-white px-6 py-4">
                        <h2 class="text-lg font-semibold">📦 Data Digital Bahan</h2>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Material</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $bahanMasuk->nama_material }}</dd>
                            </div>
                            
                            @if($bahanMasuk->kode_material)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kode Material</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $bahanMasuk->kode_material }}</dd>
                            </div>
                            @endif
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Jumlah</dt>
                                    <dd class="mt-1 text-xl font-bold text-blue-600">{{ $bahanMasuk->formatted_jumlah }} {{ $bahanMasuk->satuan }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Harga Satuan</dt>
                                    <dd class="mt-1 text-xl font-bold text-green-600">{{ $bahanMasuk->formatted_harga_satuan }}</dd>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4">
                                <dt class="text-sm font-medium text-gray-500">Total Nilai Transaksi</dt>
                                <dd class="mt-1 text-2xl font-bold text-purple-600">{{ $bahanMasuk->formatted_total_harga }}</dd>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                    <dd class="mt-1 text-gray-900">{{ $bahanMasuk->kategori }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Satuan</dt>
                                    <dd class="mt-1 text-gray-900">{{ $bahanMasuk->satuan }}</dd>
                                </div>
                            </div>
                            
                            @if($bahanMasuk->supplier)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                                <dd class="mt-1 text-gray-900">{{ $bahanMasuk->supplier }}</dd>
                            </div>
                            @endif
                            
                            @if($bahanMasuk->ref_no)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Nota/Ref</dt>
                                <dd class="mt-1 text-gray-900">{{ $bahanMasuk->ref_no }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Additional Info Card -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gray-700 text-white px-6 py-4">
                        <h2 class="text-lg font-semibold">ℹ️ Keterangan Lengkap</h2>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Waktu Input</dt>
                                    <dd class="mt-1 text-gray-900 font-medium">{{ $bahanMasuk->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Lokasi Gudang</dt>
                                    <dd class="mt-1 text-gray-900">{{ $bahanMasuk->lokasi_gudang ?? 'Gudang Utama' }}</dd>
                                </div>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Operator yang Bertugas</dt>
                                <dd class="mt-1 text-gray-900 font-medium">
                                    {{ $bahanMasuk->operator->name ?? 'Unknown' }}
                                    <span class="text-sm text-gray-500">(ID: {{ $bahanMasuk->operator_id }})</span>
                                </dd>
                            </div>
                            
                            @if($bahanMasuk->verified_at)
                            <div class="border-t pt-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Diverifikasi Oleh</dt>
                                        <dd class="mt-1 text-gray-900 font-medium">{{ $bahanMasuk->verifiedBy->name ?? 'Unknown' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Waktu Verifikasi</dt>
                                        <dd class="mt-1 text-gray-900 font-medium">{{ $bahanMasuk->verified_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</dd>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($bahanMasuk->notes)
                            <div class="border-t pt-4">
                                <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                                <dd class="mt-1 text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $bahanMasuk->notes }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Action Buttons (only if pending) -->
                @if($bahanMasuk->status === 'pending')
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">⚡ Aksi Verifikasi</h3>
                    
                    <form action="{{ route('admin.scan.verify', $bahanMasuk->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                        </div>
                        
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-green-700 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Verifikasi & Setujui
                            </button>
                            
                            <button type="button" onclick="showRejectModal()" class="flex-1 bg-red-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-red-700 transition flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tolak
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md mx-4">
        <h3 class="text-xl font-bold mb-4">Tolak Data Scan</h3>
        <form action="{{ route('admin.scan.reject', $bahanMasuk->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan (Wajib)</label>
                <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Jelaskan alasan penolakan..." required></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-red-700 transition">
                    Tolak Data
                </button>
                <button type="button" onclick="hideRejectModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-400 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection