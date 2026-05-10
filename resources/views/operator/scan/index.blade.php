@extends('layouts.operator_app')

@section('content')
<div class="min-h-screen bg-[#0D1117] p-4 sm:p-6">
    
    {{-- 
        File ini bertindak sebagai "wadah" untuk form input manual.
        Variabel $pengadaan_id (jika ada) akan datang dari ScanController@showManualInputForm
        dan langsung diteruskan ke partial _input-manual.
    --}}
    @include('operator.partials._input-manual', ['pengadaan_id' => $pengadaan_id ?? null])

</div>
@endsection