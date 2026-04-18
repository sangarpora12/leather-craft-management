@extends('layouts.admin')

@section('content')

<div class="p-6 text-white">

<div class="flex justify-between items-center mb-6">

<div>
<h1 class="text-2xl font-bold">Monitoring Proses Produksi</h1>
<p class="text-gray-400 text-sm">
Pantau status dan progres setiap SPK
</p>
</div>

<div class="flex gap-3">
<button class="bg-gray-800 px-4 py-2 rounded-lg">
Refresh Data
</button>

<a href="{{ route('admin.produksi.index') }}"
class="bg-orange-500 px-4 py-2 rounded-lg">
Lihat Detail SPK
</a>
</div>

</div>


{{-- STATISTIK --}}
<div class="grid grid-cols-3 gap-6 mb-6">

<div class="bg-[#111] p-6 rounded-xl">
<p class="text-gray-400">Total SPK Aktif</p>
<h2 class="text-3xl font-bold">
{{ $totalSPK }}
</h2>
</div>

<div class="bg-[#111] p-6 rounded-xl">
<p class="text-gray-400">Sedang Dikerjakan</p>
<h2 class="text-3xl font-bold text-blue-400">
{{ $progress->count() }}
</h2>
</div>

<div class="bg-[#111] p-6 rounded-xl">
<p class="text-gray-400">Selesai</p>
<h2 class="text-3xl font-bold text-green-400">
{{ $selesai->count() }}
</h2>
</div>

</div>



{{-- KANBAN BOARD --}}
<div class="grid grid-cols-3 gap-6">

{{-- KONFIRMASI --}}
<div class="bg-[#111] p-4 rounded-xl">

<div class="flex justify-between mb-4">
<h3>Konfirmasi</h3>
<span>{{ $konfirmasi->count() }} SPK</span>
</div>

@foreach($konfirmasi as $spk)

<div class="bg-[#0f0f0f] p-4 rounded-lg mb-4 border border-yellow-600">

<p class="text-xs text-gray-400">
ID SPK : {{ $spk->kode_spk }}
</p>

<h4 class="font-semibold">
{{ $spk->product->nama_produk ?? '-' }}
</h4>

<p class="text-sm text-gray-400">
PIC : {{ $spk->operator->name ?? '-' }}
</p>

<div class="mt-3 h-2 bg-gray-800 rounded">
<div class="h-2 bg-yellow-500 w-1/4 rounded"></div>
</div>

<p class="text-xs text-yellow-500 mt-2">
Menunggu Konfirmasi
</p>

</div>

@endforeach

</div>



{{-- PROSES --}}
<div class="bg-[#111] p-4 rounded-xl">

<div class="flex justify-between mb-4">
<h3>Sedang Dikerjakan</h3>
<span>{{ $progress->count() }} SPK</span>
</div>

@foreach($progress as $spk)

<div class="bg-[#0f0f0f] p-4 rounded-lg mb-4 border border-blue-600">

<p class="text-xs text-gray-400">
ID SPK : {{ $spk->kode_spk }}
</p>

<h4 class="font-semibold">
{{ $spk->product->nama_produk ?? '-' }}
</h4>

<p class="text-sm text-gray-400">
PIC : {{ $spk->operator->name ?? '-' }}
</p>

<div class="mt-3 h-2 bg-gray-800 rounded">
<div class="h-2 bg-green-500 w-3/4 rounded"></div>
</div>

<p class="text-xs text-green-500 mt-2">
Sedang Diproduksi
</p>

</div>

@endforeach

</div>



{{-- SELESAI --}}
<div class="bg-[#111] p-4 rounded-xl">

<div class="flex justify-between mb-4">
<h3>Selesai</h3>
<span>{{ $selesai->count() }} SPK</span>
</div>

@foreach($selesai as $spk)

<div class="bg-[#0f0f0f] p-4 rounded-lg mb-4 border border-green-600">

<p class="text-xs text-gray-400">
ID SPK : {{ $spk->kode_spk }}
</p>

<h4 class="font-semibold">
{{ $spk->product->nama_produk ?? '-' }}
</h4>

<p class="text-xs text-green-500 mt-2">
Selesai QC
</p>

</div>

@endforeach

</div>

</div>

</div>

@endsection