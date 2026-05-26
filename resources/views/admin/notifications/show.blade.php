@extends('layouts.admin')

@section('page_title', 'Detail Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-[#1A1C24] border border-[#2D3139] rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-white">{{ $notification->data['judul'] ?? 'Detail Notifikasi' }}</h2>
            <a href="{{ route('admin.notifications.index') }}" class="text-sm text-gray-400 hover:text-white transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="space-y-4">
            @if(isset($notification->data['detail']))
            <div class="bg-[#0F111A] rounded-xl p-4 border border-[#2D3139]">
                <h3 class="text-xs font-bold text-blue-400 uppercase mb-3">📋 Informasi Nota</h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Operator:</span>
                        <span class="text-white ml-1">{{ $notification->data['detail']['operator'] ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Supplier:</span>
                        <span class="text-white ml-1">{{ $notification->data['detail']['supplier'] ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">No. Nota:</span>
                        <span class="text-white ml-1">{{ $notification->data['detail']['no_nota'] ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Tanggal:</span>
                        <span class="text-white ml-1">{{ $notification->data['detail']['tanggal'] ?? '-' }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Daftar Item dengan Harga -->
            @if(isset($notification->data['detail']['items']) && count($notification->data['detail']['items']) > 0)
            <div class="bg-[#0F111A] rounded-xl p-4 border border-[#2D3139]">
                <h3 class="text-xs font-bold text-green-400 uppercase mb-3">📦 Daftar Barang Masuk</h3>
                <div class="space-y-2">
                    @php $grandTotal = 0; @endphp
                    @foreach($notification->data['detail']['items'] as $i => $item)
                    @php
                        $hargaSatuan = $item['harga_satuan'] ?? $item['harga'] ?? 0;
                        $qty = $item['qty'] ?? 0;
                        $subtotal = $item['subtotal'] ?? ($hargaSatuan * $qty);
                        $grandTotal += $subtotal;
                    @endphp
                    <div class="bg-[#161A22] rounded-lg px-4 py-3">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center gap-3">
                                <span class="w-6 h-6 rounded-full bg-blue-600/20 text-blue-400 flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $item['nama'] ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">🎨 {{ $item['warna'] ?? 'Tidak Ada Warna' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-400">{{ $qty }}</p>
                                <p class="text-xs text-gray-500">{{ $item['satuan'] ?? 'Pcs' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-[#2D3139]/50 text-xs">
                            <span class="text-gray-500">💰 @if($hargaSatuan > 0) Rp {{ number_format($hargaSatuan, 0, ',', '.') }} / {{ $item['satuan'] ?? 'Pcs' }} @else <span class="text-gray-600 italic">Harga tidak diisi</span> @endif</span>
                            <span class="text-yellow-400 font-semibold">@if($hargaSatuan > 0) Rp {{ number_format($subtotal, 0, ',', '.') }} @else <span class="text-gray-600 italic">-</span> @endif</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Grand Total -->
                <div class="flex items-center justify-between bg-[#1C222D] rounded-lg px-4 py-3 mt-3 border @if($grandTotal > 0) border-yellow-500/30 @else border-gray-500/30 @endif">
                    <span class="text-sm font-bold @if($grandTotal > 0) text-yellow-400 @else text-gray-400 @endif">💰 Total Keseluruhan</span>
                    <span class="text-lg font-bold @if($grandTotal > 0) text-yellow-400 @else text-gray-400 @endif">@if($grandTotal > 0) Rp {{ number_format($grandTotal, 0, ',', '.') }} @else - @endif</span>
                </div>
            </div>
            @endif

            <!-- Foto Nota -->
            @if(isset($notification->data['foto_nota_url']))
            <div class="bg-[#0F111A] rounded-xl p-4 border border-[#2D3139]">
                <h3 class="text-xs font-bold text-orange-400 uppercase mb-3">📸 Foto Nota</h3>
                <img src="{{ $notification->data['foto_nota_url'] }}" alt="Foto Nota" class="w-full rounded-lg border border-[#2D3139]" onerror="this.style.display='none'">
            </div>
            @endif

            <!-- Tombol -->
            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.bahan.index') }}" class="flex-1 text-center px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition">
                    📋 Lihat Stok Bahan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection