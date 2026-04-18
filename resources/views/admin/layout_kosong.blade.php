{{-- PENTING: @extends('layouts.admin') berarti file ini menumpang ke layout utama Anda. Pastikan file 'resources/views/layouts/admin.blade.php' memang ada. Jika nama file layout utama Anda beda (misal: 'app.blade.php'), ubah bagian ini. --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title ?? 'Halaman Kosong' }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Info</h6>
                </div>
                <div class="card-body text-center">
                    
                    {{-- Gambar Ilustrasi --}}
                    <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 20rem;" 
                         src="https://via.placeholder.com/400x300.png?text=Under+Construction" 
                         alt="Under Construction">
                    
                    <h4 class="text-gray-800">Fitur Belum Tersedia</h4>
                    <p class="lead text-gray-600 mb-4">
                        Halaman {{ $title ?? 'ini' }} sedang disiapkan untuk menu Skripsi.
                    </p>
                    
                    <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary">
                        &larr; Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
