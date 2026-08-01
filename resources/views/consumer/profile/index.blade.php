@extends('layouts.consumer')

@section('title', 'Profil Saya - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-[#3E2723] font-display">Profil Saya</h1>
            <p class="text-[#8B4513]/60 mt-1">Kelola akun dan lihat riwayat pesanan Anda</p>
        </div>
        <a href="{{ route('consumer.logout') }}" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="flex items-center gap-2 bg-[#8B4513] hover:bg-[#5C3317] text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-12 0v-1m12-11V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2v-1"></path>
            </svg>
            Keluar
        </a>
        <form id="logout-form" action="{{ route('consumer.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>

    <!-- Tabs -->
    <div class="border-b border-[#8B4513]/20 mb-6 flex gap-4">
        <a href="#profil" onclick="showTab('profil'); return false;" id="tab-profil" class="tab-link border-b-2 border-transparent pb-3 px-1 text-[#3E2723] font-medium hover:text-[#8B4513] transition">
            Informasi Akun
        </a>
        <a href="#riwayat" onclick="showTab('riwayat'); return false;" id="tab-riwayat" class="tab-link border-b-2 border-transparent pb-3 px-1 text-[#3E2723] font-medium hover:text-[#8B4513] transition">
            Riwayat Pesanan
        </a>
    </div>

    <!-- Tab: Profil -->
    <div id="tab-content-profil" class="tab-content">
        @if(session('success'))
            <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg mb-4">{{ session('error') }}</div>
        @endif

        <div class="leather-card rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-[#3E2723] mb-4 font-display">Informasi Akun</h2>
            <form method="POST" action="{{ route('consumer.profile.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-[#5C3317] mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ $user->name }}" required
                        class="w-full px-4 py-2 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[#5C3317] mb-1">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" required
                        class="w-full px-4 py-2 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-[#5C3317] mb-1">No. HP</label>
                    <input type="text" name="no_hp" value="{{ $user->no_hp ?? '' }}"
                        class="w-full px-4 py-2 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723]" placeholder="Opsional">
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="btn-primary text-white px-6 py-2.5 rounded-lg font-medium transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="leather-card rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-[#3E2723] font-display">Ganti Password</h2>
                <button type="button" onclick="openPasswordModal()"
                    class="bg-[#8B4513] hover:bg-[#5C3317] text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-7h-2v7H5a2 2 0 00-2 2v7zm10-11V5a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Ganti Password
                </button>
            </div>
            <p class="text-sm text-[#8B4513]/60">Klik tombol di atas untuk membuka form ganti password.</p>
        </div>

        <!-- Hapus Akun Section -->
        <div class="leather-card rounded-lg shadow-sm p-6 mt-6 border-2 border-red-200">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-red-600 font-display">Hapus Akun</h2>
                <button type="button" onclick="openDeleteModal()"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Hapus Akun
                </button>
            </div>
            <p class="text-sm text-red-500/80">Peringatan: Semua data akun Anda akan dihapus permanen dan tidak dapat dikembalikan.</p>
        </div>

        <!-- Delete Account Modal -->
        <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
                <div class="p-6 border-b border-red-200 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-red-600 font-display">Konfirmasi Hapus Akun</h3>
                    <button type="button" onclick="closeDeleteModal()" class="text-[#8B4513]/50 hover:text-[#8B4513]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.163 2.502-2.502V7.102c0-1.54-1.003-2.606-2.503-2.606H5.062c-1.54 0-2.501 1.067-2.501 2.606v13.396c0 1.339.961 2.502 2.501 2.502z"></path>
                            </svg>
                        </div>
                        <p class="text-[#3E2723] font-medium mb-2">Yakin ingin menghapus akun Anda?</p>
                        <p class="text-sm text-[#8B4513]/60">Semua data akun, riwayat pesanan, dan informasi Anda akan dihapus permanen dari sistem.</p>
                    </div>
                    <form method="POST" action="/profile/delete" class="flex justify-end gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-5 py-2.5 border border-[#8B4513]/20 rounded-lg text-[#3E2723] font-medium hover:bg-[#F5F0E1]/50 transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition">
                            Ya, Hapus Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Password Modal Functions
            function openPasswordModal() {
                document.getElementById('password-modal').classList.remove('hidden');
                document.getElementById('password-modal').classList.add('flex');
            }
            function closePasswordModal() {
                document.getElementById('password-modal').classList.add('hidden');
                document.getElementById('password-modal').classList.remove('flex');
            }

            // Delete Modal Functions
            function openDeleteModal() {
                document.getElementById('delete-modal').classList.remove('hidden');
                document.getElementById('delete-modal').classList.add('flex');
            }
            function closeDeleteModal() {
                document.getElementById('delete-modal').classList.add('hidden');
                document.getElementById('delete-modal').classList.remove('flex');
            }

            // Tab Functions
            function showTab(tab) {
                document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('border-[#8B4513]'));
                document.getElementById('tab-content-' + tab).classList.remove('hidden');
                document.getElementById('tab-' + tab).classList.add('border-[#8B4513]');
            }

            // Show first tab by default
            showTab('profil');
        </script>
    </div>
    </div>

    <!-- Tab: Riwayat Pesanan -->
    <div id="tab-content-riwayat" class="tab-content hidden">
        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    @php
                        $statusLabels = [
                            'pending' => 'Pending',
                            'processing' => 'Diproses',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                        ];
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabel = $statusLabels[$order->status] ?? $order->status;
                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <div class="leather-card rounded-lg shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-[#8B4513]/10 flex justify-between items-center">
                            <div>
                                <p class="text-sm text-[#8B4513]/60">No. Pesanan</p>
                                <p class="font-bold text-[#3E2723]">{{ $order->order_number }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColor }}">{{ $statusLabel }}</span>
                                <p class="text-sm text-[#8B4513]/60 mt-1">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                @foreach($order->items as $item)
                                    <div class="flex gap-3">
                                        @if($item->produkJadi && $item->produkJadi->foto)
                                            <img src="{{ asset('storage/' . $item->produkJadi->foto) }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded-lg border border-[#8B4513]/10">
                                        @else
                                            <div class="w-16 h-16 bg-[#F5F0E1] rounded-lg flex items-center justify-center text-[#8B4513]/20 border border-[#8B4513]/10">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <p class="font-medium text-[#3E2723]">{{ $item->product_name }}</p>
                                            @if($item->produkJadi && $item->produkJadi->deskripsi)
                                                <p class="text-xs text-[#8B4513]/50 mt-0.5 line-clamp-1">{{ Str::limit($item->produkJadi->deskripsi, 80) }}</p>
                                            @endif
                                            <p class="text-xs text-[#8B4513]/60 mt-1">Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-[#8B4513]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="border-t border-[#8B4513]/10 pt-3 mt-3 flex justify-between items-center">
                                <span class="text-sm text-[#8B4513]/60">Total</span>
                                <span class="font-bold text-[#3E2723]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="p-3 bg-[#F5F0E1]/30 text-center border-t border-[#8B4513]/10">
                            <a href="{{ route('consumer.profile.order.detail', $order->id) }}" class="text-[#8B4513] hover:text-[#5C3317] text-sm font-medium">
                                Lihat Detail Pesanan
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 leather-card rounded-lg shadow-sm">
                <svg class="w-20 h-20 mx-auto text-[#8B4513]/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-[#8B4513]/50 text-lg">Belum ada riwayat pesanan</p>
                <a href="{{ route('consumer.catalog') }}" class="inline-block mt-4 btn-primary text-white px-6 py-2.5 rounded-lg text-sm font-medium">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Password Change Modal --}}
<div id="password-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-[#8B4513]/10 flex justify-between items-center">
            <h3 class="text-xl font-bold text-[#3E2723] font-display">Ganti Password</h3>
            <button type="button" onclick="closePasswordModal()" class="text-[#8B4513]/50 hover:text-[#8B4513]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('consumer.profile.password') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-[#5C3317] mb-1">Password Saat Ini</label>
                <input type="password" name="current_password" required
                    class="w-full px-4 py-2 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#5C3317] mb-1">Password Baru</label>
                <input type="password" name="password" required minlength="8"
                    class="w-full px-4 py-2 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723]">
            </div>
            <div>
                <label class="block text-sm font-medium text-[#5C3317] mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required minlength="8"
                    class="w-full px-4 py-2 border border-[#8B4513]/20 rounded-lg focus:ring-2 focus:ring-[#A0522D] focus:border-transparent text-[#3E2723]">
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closePasswordModal()"
                    class="px-4 py-2 text-[#8B4513] hover:text-[#5C3317] text-sm font-medium">
                    Batal
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-[#8B4513] hover:bg-[#5C3317] text-white rounded-lg text-sm font-medium transition">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(function(el) {
        el.classList.add('hidden');
    });
    document.getElementById('tab-content-' + tab).classList.remove('hidden');
    document.querySelectorAll('.tab-link').forEach(function(el) {
        el.classList.remove('border-[#8B4513]');
        el.classList.add('border-transparent');
    });
    document.getElementById('tab-' + tab).classList.remove('border-transparent');
    document.getElementById('tab-' + tab).classList.add('border-[#8B4513]');
}

function openPasswordModal() {
    document.getElementById('password-modal').classList.remove('hidden');
    document.getElementById('password-modal').classList.add('flex');
}

function closePasswordModal() {
    document.getElementById('password-modal').classList.add('hidden');
    document.getElementById('password-modal').classList.remove('flex');
}

// Show profil tab by default
showTab('profil');
</script>
@endsection