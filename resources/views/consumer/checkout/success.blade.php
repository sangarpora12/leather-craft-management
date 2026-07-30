@extends('layouts.consumer')

@section('title', 'Pesanan Berhasil - Berliano Leather')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Berhasil!</h1>
        <p class="text-gray-600 mb-6">Terima kasih telah berbelanja di Berliano Leather</p>

        <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Nomor Pesanan:</span>
                    <span class="font-bold text-gray-900">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Pembayaran:</span>
                    <span class="font-bold text-amber-700 text-lg">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode Pembayaran:</span>
                    <span class="font-medium">
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
                    <span class="text-gray-600">Status:</span>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                <strong>Informasi:</strong> Pesanan Anda sedang diproses. Anda akan menerima konfirmasi lebih lanjut melalui kontak yang tersedia.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('consumer.catalog') }}" class="inline-block bg-amber-700 text-white px-6 py-3 rounded-lg hover:bg-amber-800 transition font-medium">
                Belanja Lagi
            </a>
            <a href="{{ route('consumer.cart') }}" class="inline-block bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition font-medium">
                Lihat Keranjang
            </a>
        </div>
    </div>
</div>
@endsection

