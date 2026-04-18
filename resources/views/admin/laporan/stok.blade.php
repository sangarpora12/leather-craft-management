@extends('layouts.admin')

@section('title','Laporan Stok')

@section('content')
<h2 class="text-xl font-bold text-white mb-4">Laporan Stok & Kerusakan</h2>

<table class="w-full text-sm text-white">
<tr class="bg-[#111]">
    <th class="p-2">Produk</th>
    <th>Stok</th>
    <th>Keluar</th>
</tr>

@foreach($produk as $p)
<tr class="border-b border-white/5">
    <td class="p-2">{{ $p->nama_produk }}</td>
    <td>{{ $p->stok }}</td>
    <td>{{ $p->produk_keluars_sum_jumlah_keluar ?? 0 }}</td>
</tr>
@endforeach
</table>
@endsection
