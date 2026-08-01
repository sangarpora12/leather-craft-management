@extends('layouts.consumer')

@section('title', 'Pesanan Berhasil - Berliano Leather')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        // Show toast alert with order number
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 z-50 bg-green-600 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center gap-3';
        toast.style.animation = 'fadeIn 0.4s ease-out';
        toast.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <div>
                <p class="font-bold text-sm">Pesanan Berhasil!</p>
                <p class="text-xs text-white/90">{{ session('success') }}</p>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s, transform 0.5s';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    @endif
});
</script>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="leather-card rounded-lg shadow-sm p-8 text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-[#3E2723] mb-2 font-display">Pesanan Berhasil!</h1>
        <p class="text-[#8B4513]/60 mb-6">Terima kasih telah berbelanja di Berliano Leather</p>

        <div class="bg-[#F5F0E1] rounded-lg p-6 mb-6 text-left border border-[#8B4513]/10">
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-[#8B4513]/60">Nomor Pesanan:</span>
                    <span class="font-bold text-[#3E2723]">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#8B4513]/60">Tanggal:</span>
                    <span class="font-medium text-[#3E2723]">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#8B4513]/60">Total Pembayaran:</span>
                    <span class="font-bold price-tag text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#8B4513]/60">Metode Pembayaran:</span>
                    <span class="font-medium text-[#3E2723]">
                        @if($order->payment_method == 'transfer')
                            Transfer Bank
                        @elseif($order->payment_method == 'qris')
                            QRIS
                        @elseif($order->payment_method == 'cod')
                            Cash on Delivery (COD)
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#8B4513]/60">Status:</span>
                    <span class="px-3 py-1 bg-[#C5A059]/20 text-[#8B4513] rounded-full text-sm font-medium">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-[#8B4513]/5 border border-[#8B4513]/15 rounded-lg p-4 mb-6">
            <p class="text-sm text-[#5C3317]">
                <strong>Informasi:</strong> Pesanan Anda sedang diproses. Anda akan menerima konfirmasi lebih lanjut melalui kontak yang tersedia.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('consumer.catalog') }}" class="inline-block btn-primary text-white px-6 py-3 rounded-lg transition font-medium">
                Belanja Lagi
            </a>
            <a href="{{ route('consumer.cart') }}" class="inline-block bg-[#F5F0E1] text-[#8B4513] px-6 py-3 rounded-lg hover:bg-[#8B4513] hover:text-white transition font-medium">
                Lihat Keranjang
            </a>
        </div>
    </div>
</div>
@endsection