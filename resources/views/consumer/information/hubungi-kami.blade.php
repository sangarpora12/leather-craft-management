@extends('layouts.consumer')

@section('title', 'Hubungi Kami - Berliano Leather')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="text-center mb-12">
        <h1 class="font-display text-4xl font-bold text-[#3E2723] mb-4">Hubungi Kami</h1>
        <div class="leather-divider w-32 mx-auto mb-6"></div>
        <p class="text-gray-600 text-lg">Kami selalu senang mendengar dari Anda. Hubungi tim Berliano Leather melalui kanal berikut</p>
    </div>

    <!-- Contact Cards Grid -->
    <div class="grid md:grid-cols-4 gap-6 mb-12">
        <!-- WhatsApp Card -->
        <a href="https://wa.me/6285236706646" target="_blank" class="group bg-white rounded-2xl shadow-lg border border-[#8B4513]/10 p-8 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
            <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
            </div>
            <h3 class="font-display text-xl font-bold text-[#3E2723] text-center mb-2">WhatsApp</h3>
            <p class="text-gray-600 text-center text-sm">+62 852-3670-6646</p>
            <p class="text-gray-400 text-center text-xs mt-3">Chat langsung dengan CS kami</p>
        </a>

        <!-- Instagram Card -->
        <a href="https://www.instagram.com/berliano_indonesia?igsh=MXRrMGF0bzlwOXh0aA==" target="_blank" class="group bg-white rounded-2xl shadow-lg border border-[#8B4513]/10 p-8 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
            <div class="w-16 h-16 bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                </svg>
            </div>
            <h3 class="font-display text-xl font-bold text-[#3E2723] text-center mb-2">Instagram</h3>
            <p class="text-gray-600 text-center text-sm">@berliano_indonesia</p>
            <p class="text-gray-400 text-center text-xs mt-3">Follow kami untuk update terbaru</p>
        </a>

        <!-- Email Card -->
        <a href="mailto:zulk61934@gmail.com" class="group bg-white rounded-2xl shadow-lg border border-[#8B4513]/10 p-8 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
            <div class="w-16 h-16 bg-[#8B4513] rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h3 class="font-display text-xl font-bold text-[#3E2723] text-center mb-2">Email</h3>
            <p class="text-gray-600 text-center text-sm">zulk61934@gmail.com</p>
            <p class="text-gray-400 text-center text-xs mt-3">Kirim email untuk kerjasama</p>
        </a>

        <!-- Location Card -->
        <div class="group bg-white rounded-2xl shadow-lg border border-[#8B4513]/10 p-8 hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
            <div class="w-16 h-16 bg-[#C5A059] rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                <svg class="w-8 h-8 text-[#3E2723]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="font-display text-xl font-bold text-[#3E2723] text-center mb-2">Lokasi Workshop</h3>
            <p class="text-gray-600 text-center text-sm">Yogyakarta, Indonesia</p>
            <p class="text-gray-400 text-center text-xs mt-3">Datang langsung ke workshop kami</p>
        </div>
    </div>

    <!-- Workshop Image & Info -->
    <div class="bg-white rounded-2xl shadow-lg border border-[#8B4513]/10 overflow-hidden">
        <div class="md:flex">
            <div class="md:w-1/2">
                <img src="{{ asset('bahan/Kulit Sapi Premium.jpg') }}" alt="Workshop Berliano Leather - Bahan Kulit Sapi Premium" class="h-64 md:h-full w-full object-cover">
            </div>
            <div class="md:w-1/2 p-8 md:p-10">
                <h3 class="font-display text-2xl font-bold text-[#3E2723] mb-4">Workshop Utama Berliano Leather</h3>
                <address class="not-italic text-gray-600 leading-relaxed space-y-3">
                    <p class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[#8B4513] mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>Jl. Nyi Wiji Adhisoro No.1B, Prenggan, <br>Kec. Kotagede, Kota Yogyakarta, <br>Daerah Istimewa Yogyakarta 55172, Indonesia</span>
                    </p>
                    <p class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#8B4513] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Senin - Sabtu: 09.00 - 17.00 WIB</span>
                    </p>
                    <p class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#8B4513] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>Minggu: Tutup (By Appointment Only)</span>
                    </p>
                </address>
                
                <a href="https://maps.app.goo.gl/nm5mGjXV7huKFXM49" target="_blank" class="inline-flex items-center gap-2 mt-6 btn-primary text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    Buka di Google Maps
                </a>
            </div>
        </div>
    </div>
</div>
@endsection