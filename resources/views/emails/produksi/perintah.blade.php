@component('mail::message')

{{-- Gambar Produk Utama --}}
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ $fotoProduk }}"
         alt="Foto Produk"
         width="400"
         height="300"
         style="max-width: 400px; width: 100%; height: auto; border-radius: 8px; display: block; margin: 0 auto; object-fit: cover; border: 1px solid #e0e0e0;">
</div>

# Perintah Produksi Baru

Halo,

Anda mendapatkan perintah produksi baru. Mohon untuk segera diproses sesuai detail di bawah ini.

@php $sketsas = $produksi->product->sketsa ?? []; @endphp
@if(count($sketsas) > 0)
<div style="margin-bottom: 20px;">
    <p style="font-size: 12px; color: #777; text-align: center; margin-bottom: 8px;">📏 SKETSA PRODUKSI</p>
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; max-width: 400px; margin: 0 auto;">
        @foreach(array_slice($sketsas, 0, 4) as $sketsaPath)
            <img src="{{ Storage::disk('public')->url($sketsaPath) }}" style="width: 100%; border-radius: 6px; aspect-ratio: 1/1; object-fit: cover; border: 1px solid #e0e0e0;">
        @endforeach
    </div>
</div>
@endif

@php
    $mKategori = is_array($produksi->product->kategori) ? $produksi->product->kategori : (json_decode($produksi->product->kategori, true) ?: [$produksi->product->kategori ?: 'Tidak Ada']);
    $mWarna    = is_array($produksi->product->warna)    ? $produksi->product->warna    : (json_decode($produksi->product->warna, true)    ?: [$produksi->product->warna    ?: 'Tidak Ada']);
    $mMotif    = is_array($produksi->product->jenis_motif) ? $produksi->product->jenis_motif : (json_decode($produksi->product->jenis_motif, true) ?: [$produksi->product->jenis_motif ?: 'Tidak Ada']);
@endphp

@component('mail::panel')
<div style="text-align: center;">
     <h1 style="font-size: 24px; margin: 0; color: #333;">{{ $produksi->product->nama_produk }}</h1>

     <div style="margin: 12px 0;">
        @foreach($mKategori as $item)
        <span style="display: inline-block; background: #e3f2fd; color: #1565c0; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin: 2px;">{{ $item }}</span>
        @endforeach
     </div>

     <p style="font-size: 14px; margin-top: 8px; color: #555;">
         @foreach($mWarna as $item)
         <span style="display: inline-block; background: #fce4ec; color: #c2185b; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; margin: 2px;">{{ $item }}</span>
         @endforeach
         <span style="color: #999; margin: 0 6px;">|</span>
         @foreach($mMotif as $item)
         <span style="display: inline-block; background: #f3e5f5; color: #7b1fa2; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; margin: 2px;">{{ $item }}</span>
         @endforeach
     </p>

     <p style="font-size: 20px; margin-top: 14px;">Jumlah: <strong style="color: #D8361A;">{{ $produksi->jumlah_produksi }} {{ $produksi->product->satuan }}</strong></p>
     <p style="font-size: 13px; margin-top: 8px; color: #777;">No. SPK: <span style="font-family: monospace; background: #f5f5f5; padding: 2px 8px; border-radius: 4px;">{{ $produksi->kode_spk }}</span></p>
</div>
@endcomponent

Berikut adalah bahan baku yang dibutuhkan:

@component('mail::table')
| Nama Material | Kebutuhan | Satuan |
|:--------------|:----------|:-------|
@foreach($produksi->product->boms as $bom)
@if($bom->bahanBaku)
| {{ $bom->bahanBaku->nama_material }} | {{ $bom->jumlah_kebutuhan * $produksi->jumlah_produksi }} | {{ $bom->bahanBaku->satuan }} |
@endif
@endforeach
@endcomponent

@if($showAction)
@component('mail::button', ['url' => $url, 'color' => 'success'])
Lihat Detail Tugas
@endcomponent
@else
> **Status: Hanya Informasi (Tidak ditugaskan)**
@endif

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent