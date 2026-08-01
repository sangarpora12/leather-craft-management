@extends('layouts.admin')

@section('page_title', 'Detail Penjualan')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">Detail Penjualan E-Commerce</h2>
            <p class="text-sm text-gray-400 mt-1">Kelola pesanan, akun pelanggan, dan riwayat penjualan</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Total Revenue -->
        <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-wave text-green-500"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mb-1">Total Pendapatan</p>
            <p class="text-lg font-bold text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>

        <!-- Total Orders -->
        <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-shopping-bag text-blue-500"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mb-1">Total Pesanan</p>
            <p class="text-lg font-bold text-white">{{ $totalOrders }}</p>
        </div>

        <!-- Pending -->
        <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-yellow-500"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mb-1">Pesanan Pending</p>
            <p class="text-lg font-bold text-white">{{ $pendingOrders }}</p>
        </div>

        <!-- Completed -->
        <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-check-circle text-green-500"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mb-1">Selesai</p>
            <p class="text-lg font-bold text-white">{{ $completedOrders }}</p>
        </div>

        <!-- Customers -->
        <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                    <i class="fa-solid fa-users text-purple-500"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mb-1">Pelanggan</p>
            <p class="text-lg font-bold text-white">{{ $totalCustomers }}</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl p-4">
        <form method="GET" action="{{ route('admin.penjualan.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor pesanan atau nama pelanggan..."
                        class="w-full bg-[#0F111A] border border-[#2A3140] rounded-xl pl-10 pr-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:border-[#FF6B00]">
                </div>
            </div>
            <div>
                <select name="status" class="bg-[#0F111A] border border-[#2A3140] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#FF6B00]">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="bg-[#FF6B00] text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-orange-600 transition">
                <i class="fa-solid fa-filter mr-1"></i> Filter
            </button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-[#2A3140]">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-receipt text-[#FF6B00]"></i>
                Daftar Pesanan
            </h3>
        </div>
        @if($orders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#0F111A] text-gray-400 text-xs uppercase">
                        <th class="px-4 py-3 text-left">No. Pesanan</th>
                        <th class="px-4 py-3 text-left">Pelanggan</th>
                        <th class="px-4 py-3 text-left">Items</th>
                        <th class="px-4 py-3 text-left">Total</th>
                        <th class="px-4 py-3 text-left">Pembayaran</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr class="border-t border-[#2A3140] hover:bg-[#1F2533] transition">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs text-white">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-[#20232A] flex items-center justify-center text-xs font-bold text-[#FF6B00]">
                                    {{ substr($order->shipping_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">{{ $order->shipping_name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $order->user?->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($order->items->first() && $order->items->first()->produkJadi && $order->items->first()->produkJadi->foto)
                                    <img src="{{ asset('storage/' . $order->items->first()->produkJadi->foto) }}" 
                                         alt="{{ $order->items->first()->product_name }}" 
                                         class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-[#20232A] flex items-center justify-center">
                                        <i class="fa-solid fa-image text-gray-600 text-xs"></i>
                                    </div>
                                @endif
                                <span class="text-xs text-gray-300">{{ $order->items->count() }} item</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-bold text-green-500">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-300 uppercase">{{ $order->payment_method }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-500/10 text-yellow-500',
                                    'processing' => 'bg-blue-500/10 text-blue-500',
                                    'completed' => 'bg-green-500/10 text-green-500',
                                    'cancelled' => 'bg-red-500/10 text-red-500',
                                ];
                                $statusColor = $statusColors[$order->status] ?? 'bg-gray-500/10 text-gray-500';
                            @endphp
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $statusColor }} uppercase">{{ $order->status }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.penjualan.show', $order->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-[#20232A] hover:bg-[#FF6B00] hover:text-white text-gray-400 transition">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[#2A3140]">
            {{ $orders->withQueryString()->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <i class="fa-solid fa-cart-shopping text-4xl text-gray-600 mb-3"></i>
            <p class="text-gray-500 text-sm">Belum ada pesanan</p>
        </div>
        @endif
    </div>

    <!-- Customer Accounts & Recent Stock Outflow -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Customer Accounts -->
        <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-[#2A3140]">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-group text-purple-500"></i>
                    Akun Pelanggan
                </h3>
            </div>
            @if($customers->count() > 0)
            <div class="divide-y divide-[#2A3140]">
                @foreach($customers as $customer)
                <div class="p-4 flex items-center gap-3 hover:bg-[#1F2533] transition">
                    <div class="w-10 h-10 rounded-full bg-[#20232A] flex items-center justify-center text-sm font-bold text-purple-500">
                        {{ substr($customer->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ $customer->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $customer->email }}</p>
                        @if($customer->no_hp)
                        <p class="text-[10px] text-gray-600">{{ $customer->no_hp }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">{{ $customer->total_orders ?? 0 }} pesanan</p>
                        <p class="text-sm font-bold text-green-500">Rp {{ number_format($customer->total_spent ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center">
                <i class="fa-solid fa-user-slash text-3xl text-gray-600 mb-2"></i>
                <p class="text-gray-500 text-sm">Belum ada pelanggan terdaftar</p>
            </div>
            @endif
        </div>

        <!-- Recent Stock Outflow -->
        <div class="bg-[#161B26] border border-[#2A3140] rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-[#2A3140]">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right-from-bracket text-red-500"></i>
                    Stok Keluar (Penjualan)
                </h3>
            </div>
            @if($recentKeluar->count() > 0)
            <div class="divide-y divide-[#2A3140]">
                @foreach($recentKeluar as $keluar)
                <div class="p-4 flex items-center gap-3 hover:bg-[#1F2533] transition">
                    <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center">
                        <i class="fa-solid fa-box text-red-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ $keluar->produk_jadi?->nama_produk ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500">{{ $keluar->catatan }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-red-500">-{{ $keluar->jumlah_keluar }}</p>
                        <p class="text-[10px] text-gray-600">{{ $keluar->tanggal_keluar->format('d/m/Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center">
                <i class="fa-solid fa-box-open text-3xl text-gray-600 mb-2"></i>
                <p class="text-gray-500 text-sm">Belum ada stok keluar dari penjualan</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection