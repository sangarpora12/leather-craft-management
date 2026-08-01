@extends('layouts.consumer')

@section('title', 'Pengembalian Barang - Berliano Leather')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="text-center mb-12">
        <h1 class="font-display text-4xl font-bold text-[#3E2723] mb-4">Pengembalian Barang</h1>
        <div class="leather-divider w-32 mx-auto mb-6"></div>
        <p class="text-gray-600 text-lg">Cara mudah mengajukan pengembalian produk dengan panduan langkah demi langkah</p>
    </div>

    <!-- Important Notes Banner -->
    <div class="bg-[#FFF3CD] border border-[#FFC107]/50 rounded-xl p-6 mb-12">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-[#FFC107] rounded-full flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-[#856404]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-[#856404] mb-1">Syarat Pengembalian</h3>
                <p class="text-[#856404]/90 text-sm leading-relaxed">Pengembalian hanya dapat diajukan dalam waktu <strong>7 hari kalender</strong> sejak barang diterima. Barang harus dalam kondisi asli, tidak digunakan, dan disertai dengan label serta kemasan asli.</p>
            </div>
        </div>
    </div>

    <!-- Steps Timeline -->
    <div class="relative">
        <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-[#8B4513]/20 hidden md:block"></div>

        @php
            $steps = [
                [
                    'number' => 1,
                    'title' => 'Ajukan Pengajuan Melalui Customer Service',
                    'description' => 'Langkah pertama untuk mengajukan pengembalian adalah menghubungi tim customer service kami melalui WhatsApp di +62 812-3456-7890. Sebutkan nomor pesanan Anda dan alasan mengapa ingin mengembalikan barang.',
                    'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'
                ],
                [
                    'number' => 2,
                    'title' => 'Konfirmasi Tim Berliano Leather',
                    'description' => 'Tim kami akan memverifikasi pengajuan Anda dalam 1x24 jam kerja. Kami akan mengirimkan alamat lengkap warehouse untuk pengiriman retur beserta instruksi packing yang benar.',
                    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                ],
                [
                    'number' => 3,
                    'title' => 'Kirimkan Produk Kembali',
                    'description' => 'Packing barang dengan aman untuk menghindari kerusakan selama pengiriman. Lampirkan nota pembelian di dalam paket dan kirimkan ke alamat yang telah kami berikan. Simpan nomor resi untuk tracking.',
                    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'
                ],
                [
                    'number' => 4,
                    'title' => 'Pengecekan Produk oleh Tim Quality Control',
                    'description' => 'Setelah barang diterima di warehouse kami, tim QC akan melakukan pengecekan menyeluruh terhadap kondisi barang. Proses ini memakan waktu 2-3 hari kerja.',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'
                ],
                [
                    'number' => 5,
                    'title' => 'Penggantian Barang atau Refund Dana',
                    'description' => 'Jika barang diterima dalam kondisi sesuai syarat, kami akan memproses pengiriman barang penggantian atau pengembalian dana (refund) ke rekening Anda. Proses refund memakan waktu 3-5 hari kerja tergantung bank.',
                    'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'
                ]
            ];
        @endphp

        @foreach($steps as $index => $step)
        <div class="relative md:flex items-start gap-8 mb-12 last:mb-0">
            <div class="hidden md:flex w-16 h-16 bg-[#8B4513] text-white rounded-full items-center justify-center text-xl font-bold shrink-0 z-10 relative">
                {{ $step['number'] }}
            </div>
            <div class="md:hidden flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-[#8B4513] text-white rounded-full flex items-center justify-center text-lg font-bold shrink-0">
                    {{ $step['number'] }}
                </div>
                <h3 class="font-display text-xl font-bold text-[#3E2723]">{{ $step['title'] }}</h3>
            </div>
            <div class="md:bg-white md:rounded-2xl md:shadow-lg md:border md:border-[#8B4513]/10 md:p-8 flex-1">
                <div class="hidden md:flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-[#F5F0E1] rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#8B4513]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"></path>
                        </svg>
                    </div>
                    <h3 class="font-display text-xl font-bold text-[#3E2723]">{{ $step['title'] }}</h3>
                </div>
                <p class="text-gray-600 leading-relaxed md:ml-16">{{ $step['description'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- FAQ Section -->
    <div class="mt-16 bg-white rounded-2xl shadow-lg border border-[#8B4513]/10 p-8">
        <h3 class="font-display text-2xl font-bold text-[#3E2723] mb-6 text-center">Pertanyaan Seputar Pengembalian</h3>
        <div class="space-y-4">
            @php
                $faqs = [
                    [
                        'q' => 'Siapa yang menanggung biaya pengiriman retur?',
                        'a' => 'Jika kerusakan adalah kesalahan dari pihak kami (produk cacat, salah kirim), maka biaya pengiriman retur akan kami tanggung. Namun jika pengembalian karena alasan pribadi pelanggan, biaya pengiriman menjadi tanggung jawab pelanggan.'
                    ],
                    [
                        'q' => 'Bisa tidak mengajukan penukaran ukuran saja?',
                        'a' => 'Tentu bisa! Penukaran ukuran masih masuk dalam kebijakan pengembalian kami selama barang yang ingin ditukarkan dalam kondisi baru dan belum digunakan. Silakan ikuti langkah yang sama untuk mengajukan penukaran.'
                    ],
                    [
                        'q' => 'Bagaimana jika barang rusak saat pengiriman kembali?',
                        'a' => 'Kami sangat menyarankan untuk packing barang dengan menggunakan bubble wrap atau kardus tambahan untuk menghindari kerusakan. Jika barang rusak selama pengiriman retur, kami tidak bisa memproses pengembalian dana.'
                    ]
                ];
            @endphp

            @foreach($faqs as $faq)
            <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                <h4 class="font-semibold text-[#3E2723] mb-2">{{ $faq['q'] }}</h4>
                <p class="text-gray-600 text-sm leading-relaxed">{{ $faq['a'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- CTA -->
    <div class="mt-10 bg-[#3E2723] rounded-2xl p-8 text-center">
        <h3 class="font-display text-2xl font-bold text-white mb-4">Siap mengajukan pengembalian?</h3>
        <p class="text-white/70 mb-6">Hubungi customer service kami untuk memproses pengajuan Anda sekarang</p>
        <a href="https://wa.me/6285236706646" target="_blank" class="inline-flex items-center gap-2 bg-green-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-600 transition">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            Chat via WhatsApp
        </a>
    </div>
</div>
@endsection