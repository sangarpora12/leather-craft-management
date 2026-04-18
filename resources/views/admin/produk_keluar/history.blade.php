@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-[#1A1C24] border border-[#343842] rounded-2xl p-6 shadow-xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-white">Riwayat Penjualan Produk</h2>
            <a href="{{ route('admin.produk-keluar.index') }}" class="bg-[#FD730D] hover:bg-[#E6670B] text-white font-bold py-2 px-4 rounded-lg transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Kembali ke Penjualan
            </a>
        </div>

        <!-- Success & Error Messages -->
        @if (session('success'))
            <div class="bg-green-500 bg-opacity-20 text-green-300 border border-green-500 rounded-lg p-4 mb-6">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-500 bg-opacity-20 text-red-300 border border-red-500 rounded-lg p-4 mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Tabel Riwayat Penjualan -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-400">
                <thead class="text-xs text-gray-400 uppercase bg-[#12141A]">
                    <tr>
                        <th scope="col" class="px-6 py-3">Tanggal</th>
                        <th scope="col" class="px-6 py-3">Nama Produk</th>
                        <th scope="col" class="px-6 py-3">Kuantitas</th>
                        <th scope="col" class="px-6 py-3">Nama Pembeli</th>
                        <th scope="col" class="px-6 py-3">Metode Pembayaran</th>
                        <th scope="col" class="px-6 py-3">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatPenjualan as $item)
                    <tr class="border-b border-[#343842] hover:bg-[#242731]">
                        <td class="px-6 py-4 font-medium text-white whitespace-nowrap">
                            {{ $item->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->produkJadi->nama_produk ?? 'Produk Dihapus' }}
                        </td>
                        <td class="px-6 py-4 text-green-500 font-bold">
                            {{ $item->kuantitas }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->nama_pembeli ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $item->metode_pembayaran ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-green-400 font-bold">
                            Rp {{ number_format($item->total_harga ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-500">
                            Belum ada riwayat penjualan produk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $riwayatPenjualan->links() }}
        </div>
    </div>
</div>
@endsection