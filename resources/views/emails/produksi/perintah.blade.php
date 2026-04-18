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

@component('mail::panel')
<div style="text-align: center;">
     <h1 style="font-size: 24px; margin: 0; color: #333;">{{ $produksi->product->nama_produk }}</h1>
     <p style="font-size: 16px; margin-top: 8px; color: #555;">
         <strong>Warna:</strong> {{ is_array(json_decode($produksi->product->warna, true)) ? implode(', ', json_decode($produksi->product->warna, true)) : ($produksi->product->warna ?? '-') }}
     </p>
     <p style="font-size: 16px; margin-top: 2px; color: #555;">
         <strong>Motif:</strong> {{ is_array(json_decode($produksi->product->jenis_motif, true)) ? implode(', ', json_decode($produksi->product->jenis_motif, true)) : ($produksi->product->jenis_motif ?? '-') }}
     </p>
     <p style="font-size: 20px; margin-top: 10px;">Jumlah: <strong style="color: #D8361A;">{{ $produksi->jumlah_produksi }} {{ $produksi->product->satuan }}</strong></p>
     <p style="font-size: 14px; margin-top: 10px; color: #555;">No. SPK: {{ $produksi->kode_spk }}</p>
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