@extends('layouts.admin')

@section('page_title', 'Detail Pesanan')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.penjualan.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white transition">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Penjualan
    </a>

    <!-- Order Header -->
    <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-xl font-bold text-white">Pesanan {{ $order->order_number }}</h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-500/10 text-yellow-500',
                            'processing' => 'bg-blue-500/10 text-blue-500',
                            'completed' => 'bg-green-500/10 text-green-500',
                            'cancelled' => 'bg-red-500/10 text-red-500',
                        ];
                        $statusColor = $statusColors[$order->status] ?? 'bg-gray-500/10 text-gray-500';
                    @endphp
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full {{ $statusColor }} uppercase">{{ $order->status }}</span>
                </div>
                <p class="text-sm text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>

            <!-- Status Update Form -->
            <form method="POST" action="{{ route('admin.penjualan.updateStatus', $order->id) }}" class="flex items-center gap-2">
                @csrf
                @method('PUT')
                <select name="status" class="bg-[#0F111A] border border-[#2A3140] rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-[#FF6B00]">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="bg-[#FF6B00] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-orange-600 transition">
                    Update
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2">
            <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl overflow-hidden">
                <div class="p-4 border-b border-[#2A3140]">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-box text-[#FF6B00]"></i>
                        Item Pesanan ({{ $order->items->count() }})
                    </h3>
                </div>
                <div class="divide-y divide-[#2A3140]">
                    @foreach($order->items as $item)
                    <div class="p-4 flex items-center gap-4">
                        <!-- Product Image -->
                        <div class="w-16 h-16 rounded-lg bg-[#0F111A] flex-shrink-0 overflow-hidden">
                            @if($item->produkJadi && $item->produkJadi->foto)
                                <img src="{{ asset('storage/' . $item->produkJadi->foto) }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-600"></i>
                                </div>
                            @endif
                        </div>
                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-white truncate">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</p>
                        </div>
                        <!-- Subtotal -->
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-500">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!-- Total -->
                <div class="p-4 border-t border-[#2A3140] bg-[#0F111A]">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-white">Total Pembayaran</span>
                        <span class="text-lg font-bold text-green-500">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer & Shipping Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Customer Info -->
            <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-5">
                <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user text-purple-500"></i>
                    Informasi Pelanggan
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">Nama</p>
                        <p class="text-sm text-white font-medium">{{ $order->user?->name ?? $order->shipping_name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">Email</p>
                        <p class="text-sm text-white font-medium">{{ $order->user?->email ?? '-' }}</p>
                    </div>
                    @if($order->user?->no_hp)
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">No. HP</p>
                        <p class="text-sm text-white font-medium">{{ $order->user->no_hp }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-5">
                <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-truck text-blue-500"></i>
                    Informasi Pengiriman
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">Penerima</p>
                        <p class="text-sm text-white font-medium">{{ $order->shipping_name }}</p>
                    </div>
                    @if($order->shipping_phone)
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">No. HP Penerima</p>
                        <p class="text-sm text-white font-medium">{{ $order->shipping_phone }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">Alamat</p>
                        <p class="text-sm text-white font-medium">{{ $order->shipping_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Info -->
            <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-5">
                <h3 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-green-500"></i>
                    Informasi Pembayaran
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">Metode</p>
                        <p class="text-sm text-white font-medium uppercase">{{ $order->payment_method }}</p>
                    </div>
                    @if($order->paid_at)
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">Dibayar Pada</p>
                        <p class="text-sm text-white font-medium">{{ $order->paid_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                    @if($order->notes)
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase">Catatan</p>
                        <p class="text-sm text-white font-medium">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection