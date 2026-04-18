@component('mail::message')

# {{ $isAssigned ? 'Tugas Produksi Baru Untuk Anda' : 'Informasi Tugas Produksi Baru' }}

@foreach($spks as $spk)
@if ($spk->product)

{{-- 1. HEADER --}}
<div style="text-align: center; margin-bottom: 20px;">
    <h1 style="color: #2c3e50; font-size: 24px;">{{ $spk->product->nama_produk }}</h1>
    <p style="color: #7f8c8d;">Kode: {{ $spk->product->kode_produk }}</p>
</div>

{{-- 2. FOTO & SKETSA (DAMPINGAN) --}}
<table style="width: 100%; margin-bottom: 20px;">
    <tr>
        <td style="width: 50%; text-align: center; padding: 10px; border: 1px solid #eee;">
            <div style="font-size: 11px; color: #95a5a6;">📸 FOTO PRODUK</div>
            @if ($spk->product->foto)
                <img src="{{ Storage::disk('public')->url($spk->product->foto) }}" width="100%" style="border-radius: 8px; margin-top: 5px;">
            @else
                <div style="padding: 20px; color: #e74c3c; font-size: 12px;">🚫 Tidak Ada Foto Produk</div>
            @endif
        </td>
        <td style="width: 50%; text-align: center; padding: 10px; border: 1px solid #eee;">
            <div style="font-size: 11px; color: #95a5a6;">📏 SKETSA POLA</div>
            @if ($spk->product->sketsa)
                <img src="{{ Storage::disk('public')->url($spk->product->sketsa) }}" width="100%" style="border-radius: 8px; margin-top: 5px;">
            @else
                <div style="padding: 20px; color: #e74c3c; font-size: 12px;">🚫 Tidak Ada Sketsa</div>
            @endif
        </td>
    </tr>
</table>

{{-- 3. DIMENSI --}}
<div style="background: #f8f9fa; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-weight: bold;">
    Tinggi: {{ (int)$spk->product->tinggi }}cm | Panjang: {{ (int)$spk->product->panjang }}cm | Lebar: {{ (int)$spk->product->lebar }}cm
</div>

{{-- 4. SPESIFIKASI --}}
<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
    <tr style="background: #fafafa; font-size: 12px;">
        <th style="border: 1px solid #eee; padding: 8px;">Kategori</th>
        <th style="border: 1px solid #eee; padding: 8px;">Warna</th>
        <th style="border: 1px solid #eee; padding: 8px;">Motif</th>
    </tr>
    <tr style="text-align: center; font-size: 13px;">
        <td style="border: 1px solid #eee; padding: 8px;">{{ str_replace(['[',']','"'], '', $spk->product->kategori) ?: 'Tidak Ada' }}</td>
        <td style="border: 1px solid #eee; padding: 8px;">{{ str_replace(['[',']','"'], '', $spk->product->warna) ?: 'Tidak Ada' }}</td>
        <td style="border: 1px solid #eee; padding: 8px;">{{ str_replace(['[',']','"'], '', $spk->product->jenis_motif) ?: 'Tidak Ada' }}</td>
    </tr>
</table>

{{-- 5. INFO TAMBAHAN (DESKRIPSI, POLA, INTERNAL) --}}
<div style="font-size: 13px; line-height: 1.6; color: #2c3e50;">
    <strong>📝 Detail Pola:</strong> {{ $spk->product->detail_pola ?: 'Tidak Ada' }} <br>
    <strong>📖 Deskripsi Produk:</strong> {{ $spk->product->deskripsi ?: 'Tidak Ada' }} <br>
    <strong>⚠️ Keterangan Internal:</strong> {{ $spk->product->keterangan_internal ?: 'Tidak Ada' }} <br>
</div>

<hr style="margin: 20px 0; border: 1px dashed #eee;">

{{-- 6. TABEL BAHAN --}}
<strong>📦 Daftar Bahan Baku:</strong>
<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px;">
    <tr style="background: #34495e; color: white;">
        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Bahan</th>
        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Total</th>
    </tr>
    @foreach ($spk->product->boms as $bom)
    <tr>
        <td style="padding: 8px; border: 1px solid #ddd;">{{ $bom->bahanBaku->nama_material }}</td>
        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ (int)($bom->jumlah_kebutuhan * $spk->jumlah_produksi) }} {{ str_replace('buah-buahan', 'Pcs', $bom->bahanBaku->satuan) }}</td>
    </tr>
    @endforeach
</table>

<div style="text-align: center; margin: 20px 0; background: #ebf5fb; padding: 10px;">
    Target: <strong>{{ (int)$spk->jumlah_produksi }} Pcs</strong>
</div>

@if(!$loop->last) <hr style="margin: 40px 0; border: 2px solid #3498db;"> @endif

@endif
@endforeach

<div style="text-align: center; margin-top: 20px;">
    <a href="{{ route('login') }}" style="background: #27ae60; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">LOGIN KE SISTEM</a>
</div>

@endcomponent