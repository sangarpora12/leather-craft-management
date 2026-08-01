@extends('layouts.consumer')

@section('title', 'Garansi Produk - Berliano Leather')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="text-center mb-12">
        <h1 class="font-display text-4xl font-bold text-[#3E2723] mb-4">Garansi Produk</h1>
        <div class="leather-divider w-32 mx-auto mb-6"></div>
        <p class="text-gray-600 text-lg">Kami menjamin kualitas setiap produk kulit premium dari Berliano Leather</p>
    </div>

    <div class="bg-[#F5F0E1] rounded-2xl p-8 mb-10 border-l-4 border-[#8B4513]">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-[#8B4513] rounded-full flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-display text-2xl font-bold text-[#3E2723] mb-2">Garansi 1 Tahun Penuh</h3>
                <p class="text-gray-600 leading-relaxed">Semua produk Berliano Leather mendapatkan garansi resmi selama 1 tahun sejak tanggal pembelian. Garansi mencakup cacat produksi dan masalah material yang tidak disebabkan oleh penggunaan yang tidak wajar.</p>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @php
            $garanties = [
                [
                    'title' => 'Apa yang dicover oleh garansi?',
                    'content' => '<ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed">
                        <li>Cacat pada jahitan yang lepas sebelum penggunaan normal</li>
                        <li>Masalah pada material kulit yang rusak akibat proses produksi</li>
                        <li>Kerusakan pada aksesoris bawaan (resleting, kancing) yang tidak normal</li>
                        <li>Masalah pada struktur produk yang patah dalam penggunaan normal</li>
                    </ul>'
                ],
                [
                    'title' => 'Apa yang tidak dicover oleh garansi?',
                    'content' => '<ul class="list-disc list-inside space-y-2 text-gray-600 leading-relaxed">
                        <li>Kerusakan akibat penggunaan yang tidak wajar atau penyalahgunaan</li>
                        <li>Kerusakan akibat paparan cairan, api, atau benda tajam</li>
                        <li>Keausan normal akibat penggunaan sehari-hari yang berkepanjangan</li>
                        <li>Perbaikan yang dilakukan oleh pihak ketiga tanpa persetujuan kami</li>
                        <li>Kehilangan atau pencurian produk setelah diterima pelanggan</li>
                    </ul>'
                ],
                [
                    'title' => 'Bagaimana cara mengklaim garansi?',
                    'content' => '<p class="text-gray-600 leading-relaxed mb-3">Untuk mengklaim garansi produk, silakan ikuti langkah berikut:</p>
                    <ol class="list-decimal list-inside space-y-2 text-gray-600 leading-relaxed">
                        <li>Hubungi customer service kami melalui WhatsApp di <a href="https://wa.me/6285236706646" target="_blank" class="text-[#8B4513] underline">+62 852-3670-6646</a></li>
                        <li>Sertakan nomor pesanan dan foto bukti kerusakan</li>
                        <li>Tunggu konfirmasi dari tim kami dalam 1x24 jam kerja</li>
                        <li>Kirimkan produk kembali ke alamat workshop kami (biaya pengiriman ditanggung pelanggan)</li>
                        <li>Tim kami akan memproses perbaikan atau penggantian dalam 7-14 hari kerja</li>
                    </ol>'
                ],
                [
                    'title' => 'Proses perbaikan garansi',
                    'content' => '<p class="text-gray-600 leading-relaxed">Setelah kami menerima produk yang diklaim garansi, tim artisan kami akan melakukan pemeriksaan menyeluruh. Jika kerusakan tercover oleh garansi, kami akan melakukan perbaikan secara gratis. Jika produk tidak dapat diperbaiki, kami akan menggantinya dengan produk baru yang sama atau melakukan refund sesuai dengan ketentuan yang berlaku.</p>'
                ],
                [
                    'title' => 'Garansi internasional',
                    'content' => '<p class="text-gray-600 leading-relaxed">Untuk pelanggan internasional, garansi tetap berlaku namun biaya pengiriman pulang-pergi menjadi tanggung jawab pelanggan. Kami merekomendasikan untuk menghubungi kami terlebih dahulu sebelum mengirimkan produk dari luar negeri untuk menghindari kendala bea cukai.</p>'
                ]
            ];
        @endphp

        @foreach($garanties as $index => $garansi)
        <div x-data="{ open: false }" class="bg-white rounded-xl shadow-lg border border-[#8B4513]/10 overflow-hidden">
            <button @click="open = !open" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-[#F5F0E1]/50 transition">
                <span class="font-semibold text-[#3E2723] pr-4">{{ $garansi['title'] }}</span>
                <svg class="w-5 h-5 text-[#8B4513] shrink-0 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition-all ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="px-6 overflow-hidden">
                <div class="pb-5">{!! $garansi['content'] !!}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection