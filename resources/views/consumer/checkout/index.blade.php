@extends('layouts.consumer')

@section('title', 'Checkout - Berliano Leather')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Pengiriman</h2>
                
                <form method="POST" action="{{ route('consumer.checkout.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                        <input type="text" name="shipping_name" id="shipping_name" value="{{ old('shipping_name', Auth::user()->name ?? '') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('shipping_name') border-red-500 @enderror">
                        @error('shipping_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-1">No. HP Penerima</label>
                        <input type="text" name="shipping_phone" id="shipping_phone" value="{{ old('shipping_phone') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('shipping_phone') border-red-500 @enderror">
                        @error('shipping_phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman</label>
                        <textarea name="shipping_address" id="shipping_address" rows="3" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('shipping_address') border-red-500 @enderror">{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                        <select name="payment_method" id="payment_method" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('payment_method') border-red-500 @enderror">
                            <option value="">Pilih Metode Pembayaran</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                        </select>
                        @error('payment_method')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea name="notes" id="notes" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-amber-700 text-white py-3 px-6 rounded-lg hover:bg-amber-800 transition font-medium">
                        Buat Pesanan
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Ringkasan Pesanan</h3>
                
                <div class="space-y-3 mb-4">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $item->produkJadi->nama_produk }} x{{ $item->quantity }}</span>
                            <span class="font-medium">Rp {{ number_format($item->produkJadi->harga_jual * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t pt-3 space-y-2">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Ongkos Kirim</span>
                        <span class="text-green-600">Gratis</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t">
                        <span>Total</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

