@extends('layouts.consumer')

@section('title', 'Bantuan (FAQ) - Berliano Leather')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="text-center mb-12">
        <h1 class="font-display text-4xl font-bold text-[#3E2723] mb-4">Bantuan (FAQ)</h1>
        <div class="leather-divider w-32 mx-auto mb-6"></div>
        <p class="text-gray-600 text-lg">Temukan jawaban atas pertanyaan yang sering diajukan tentang produk dan layanan kami</p>
    </div>

    <div class="space-y-4">
        @php
            $faqs = [
                [
                    'question' => 'Bagaimana cara memesan produk di Berliano Leather?',
                    'answer' => 'Anda bisa memesan produk dengan menambahkannya ke keranjang belanja, kemudian lanjut ke proses checkout. Kami menerima pembayaran melalui transfer bank, QRIS, maupun COD (Cash on Delivery) untuk wilayah tertentu.'
                ],
                [
                    'question' => 'Berapa lama proses pengiriman pesanan?',
                    'answer' => 'Waktu pengiriman bervariasi tergantung lokasi Anda. Untuk wilayah Jawa, biasanya memakan waktu 2-4 hari kerja. Untuk luar Jawa, membutuhkan waktu 5-7 hari kerja. Semua pesanan akan kami informasikan nomor resinya untuk pelacakan.'
                ],
                [
                    'question' => 'Apakah produk yang dijual benar-benar kulit asli?',
                    'answer' => 'Ya! Semua produk di Berliano Leather dibuat dari 100% kulit asli pilihan terbaik. Kami sangat menjaga kualitas bahan baku untuk memastikan produk yang Anda terima memiliki daya tahan dan kualitas premium.'
                ],
                [
                    'question' => 'Bagaimana cara merawat produk kulit agar awet?',
                    'answer' => 'Untuk merawat produk kulit, hindari paparan air langsung dan sinar matahari berlebih. Bersihkan dengan kain lembut secara rutin, dan gunakan conditioner kulit khusus setiap 3-6 bulan untuk menjaga kelembapan kulit.'
                ],
                [
                    'question' => 'Apakah bisa memesan produk dengan custom desain?',
                    'answer' => 'Kami melayani permintaan custom untuk pembelian dalam jumlah besar. Untuk informasi lebih lanjut tentang custom order, silakan hubungi tim customer service kami melalui WhatsApp yang tertera di halaman Hubungi Kami.'
                ],
                [
                    'question' => 'Bagaimana jika saya menerima produk yang rusak/cacat?',
                    'answer' => 'Jika Anda menerima produk dengan kerusakan atau cacat produksi, silakan ajukan pengembalian dalam waktu 7 hari setelah barang diterima. Kami akan memproses penggantian barang atau pengembalian dana sesuai kebijakan pengembalian yang berlaku.'
                ]
            ];
        @endphp

        @foreach($faqs as $index => $faq)
        <div x-data="{ open: false }" class="bg-white rounded-xl shadow-lg border border-[#8B4513]/10 overflow-hidden">
            <button @click="open = !open" class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-[#F5F0E1]/50 transition">
                <span class="font-semibold text-[#3E2723] pr-4">{{ $faq['question'] }}</span>
                <svg class="w-5 h-5 text-[#8B4513] shrink-0 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" x-transition:enter="transition-all ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition-all ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="px-6 overflow-hidden">
                <p class="text-gray-600 pb-5 leading-relaxed">{{ $faq['answer'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12 bg-[#F5F0E1] rounded-2xl p-8 text-center">
        <h3 class="font-display text-2xl font-bold text-[#3E2723] mb-4">Masih punya pertanyaan lain?</h3>
        <p class="text-gray-600 mb-6">Tim kami siap membantu Anda menjawab pertanyaan lebih lanjut</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('consumer.hubungi-kami') }}" class="inline-block btn-primary text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">Lihat Halaman Kontak</a>
            <a href="https://wa.me/6285236706646" target="_blank" class="inline-flex items-center gap-2 bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Chat WhatsApp Sekarang
            </a>
        </div>
    </div>
</div>
@endsection