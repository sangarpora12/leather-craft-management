@extends('layouts.operator_app')

@section('title', 'Detail Tugas')

@section('content')
{{-- Route operator.tugas.detail sekarang mengarah ke view operator.produksi.detail (lebih lengkap & konsisten).
     File ini dipertahankan untuk kompatibilitas; lakukan redirect di controller jika masih ada pemanggil lama. --}}
<div class="px-5 pt-10">
    <div class="bg-[#151515] border border-white/5 rounded-3xl p-6 text-center">
        <div class="text-sm font-bold text-gray-200">Halaman dipindahkan</div>
        <p class="text-[10px] text-gray-500 mt-2">Detail tugas sudah menggunakan tampilan baru.</p>
        <a href="{{ route('operator.tugas.detail', $produksi->id) }}" class="inline-flex mt-4 px-4 py-2 rounded-2xl bg-blue-600 text-white text-xs font-bold">Buka Detail</a>
    </div>
</div>
@endsection
