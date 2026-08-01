@extends('layouts.consumer')

@section('title', 'Detail Pesanan ' . $order->order_number . ' - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('consumer.profile') }}" class="text-[#8B4513] hover:text-[#5C3317] font-medium text-sm flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali ke Profil
        </a>
        <h1 class="text-3xl font-bold text-[#3E2723] font-display">Detail Pesanan</h1>
        <p class="text-[#8B4513]/60 mt-1">No. Pesanan: {{ $order->order_number }}</p>
    </div>

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

    <!-- Order Status -->
    <div class="leather-card rounded-lg shadow-sm p-4 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
            <span class="text-sm text-[#8B4513]/60">Tanggal: {{ $order->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="text-right">
            <span class="text-sm text-[#8B4513]/60">Metode Pembayaran:</span>
            <span class="font-medium text-[#3E2723]">{{ ucfirst($order->payment_method) }}</span>
        </div>
    </div>

    <!-- Order Items -->
    <div class="leather-card rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-xl font-bold text-[#3E2723] mb-4 font-display">Item Pesanan</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[#8B4513]/10">
                        <th class="text-left py-3 text-sm font-medium text-[#3E2723]">Produk</th>
                        <th class="text-center py-3 text-sm font-medium text-[#3E2723]">Qty</th>
                        <th class="text-right py-3 text-sm font-medium text-[#3E2723]">Harga</th>
                        <th class="text-right py-3 text-sm font-medium text-[#3E2723]">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b border-[#8B4513]/5">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    @if($item->produkJadi && $item->produkJadi->foto)
                                        <img src="{{ asset('storage/' . $item->produkJadi->foto) }}" alt="{{ $item->product_name }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-[#F5F0E1] rounded-lg flex items-center justify-center text-[#8B4513]/20">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-[#3E2723]">{{ $item->product_name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-center text-sm text-[#8B4513]/70">{{ $item->quantity }}</td>
                            <td class="py-3 text-right text-sm text-[#8B4513]/70">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="py-3 text-right text-sm font-bold text-[#8B4513]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Shipping & Total -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="leather-card rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-[#3E2723] mb-4 font-display">Informasi Pengiriman</h2>
            <div class="space-y-2 text-sm">
                <p><span class="text-[#8B4513]/60">Nama:</span> <span class="text-[#3E2723] font-medium">{{ $order->shipping_name }}</span></p>
                <p><span class="text-[#8B4513]/60">No. HP:</span> <span class="text-[#3E2723]">{{ $order->shipping_phone ?? '-' }}</span></p>
                <p><span class="text-[#8B4513]/60">Alamat:</span> <span class="text-[#3E2723]">{{ $order->shipping_address }}</span></p>
            </div>
        </div>
        <div class="leather-card rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-[#3E2723] mb-4 font-display">Ringkasan Pembayaran</h2>
            <div class="space-y-2 text-sm border-b border-[#8B4513]/10 pb-3 mb-3">
                <div class="flex justify-between">
                    <span class="text-[#8B4513]/60">Subtotal</span>
                    <span class="text-[#3E2723]">Rp {{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#8B4513]/60">Ongkir</span>
                    <span class="text-[#3E2723]">-</span>
                </div>
            </div>
            <div class="flex justify-between text-lg font-bold">
                <span class="text-[#3E2723]">Total</span>
                <span class="text-[#8B4513]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
