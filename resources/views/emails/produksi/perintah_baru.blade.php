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
@php 
    $rawSketsa = $spk->product->sketsa;
    $sketsas = is_array($rawSketsa) ? $rawSketsa : (json_decode($rawSketsa, true) ?: []);
@endphp
<table style="width: 100%; margin-bottom: 20px;">
    <tr>
        <td style="width: 50%; text-align: center; padding: 10px; border: 1px solid #eee; vertical-align: top;">
            <div style="font-size: 11px; color: #95a5a6;">📸 FOTO PRODUK</div>
            @if ($spk->product->foto)
                <img src="{{ Storage::disk('public')->url($spk->product->foto) }}" width="100%" style="border-radius: 8px; margin-top: 5px;">
            @else
                <div style="padding: 20px; color: #e74c3c; font-size: 12px;">🚫 Tidak Ada Foto Produk</div>
            @endif
        </td>
        <td style="width: 50%; text-align: center; padding: 10px; border: 1px solid #eee; vertical-align: top;">
            <div style="font-size: 11px; color: #95a5a6;">📏 SKETSA POLA</div>
            @if (count($sketsas) > 0)
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5px; margin-top: 5px;">
                    @foreach(array_slice($sketsas, 0, 4) as $sketsaPath)
                        <img src="{{ Storage::disk('public')->url($sketsaPath) }}" style="width: 100%; border-radius: 6px; object-fit: cover; aspect-ratio: 1/1;">
                    @endforeach
                </div>
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

@php
    $eKategori = is_array($spk->product->kategori) ? $spk->product->kategori : (json_decode($spk->product->kategori, true) ?: [$spk->product->kategori ?: 'Tidak Ada']);
    $eWarna    = is_array($spk->product->warna)    ? $spk->product->warna    : (json_decode($spk->product->warna, true)    ?: [$spk->product->warna    ?: 'Tidak Ada']);
    $eMotif    = is_array($spk->product->jenis_motif) ? $spk->product->jenis_motif : (json_decode($spk->product->jenis_motif, true) ?: [$spk->product->jenis_motif ?: 'Tidak Ada']);
@endphp

{{-- 4. SPESIFIKASI --}}
<div style="background: #f8f9fa; border-radius: 10px; padding: 16px; margin-bottom: 20px; border: 1px solid #e9ecef;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 6px 0; vertical-align: top; width: 90px;">
                <span style="font-size: 11px; color: #6c757d; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Kategori</span>
            </td>
            <td style="padding: 6px 0;">
                @foreach($eKategori as $item)
                <span style="display: inline-block; background: #e3f2fd; color: #1565c0; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin: 2px;">{{ $item }}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 0; vertical-align: top;">
                <span style="font-size: 11px; color: #6c757d; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Warna</span>
            </td>
            <td style="padding: 6px 0;">
                @foreach($eWarna as $item)
                <span style="display: inline-block; background: #fce4ec; color: #c2185b; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin: 2px;">{{ $item }}</span>
                @endforeach
            </td>
        </tr>
        <tr>
            <td style="padding: 6px 0; vertical-align: top;">
                <span style="font-size: 11px; color: #6c757d; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px;">Motif</span>
            </td>
            <td style="padding: 6px 0;">
                @foreach($eMotif as $item)
                <span style="display: inline-block; background: #f3e5f5; color: #7b1fa2; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin: 2px;">{{ $item }}</span>
                @endforeach
            </td>
        </tr>
    </table>
</div>

{{-- 5. INFO TAMBAHAN (DESKRIPSI, POLA, INTERNAL) --}}
<div style="font-size: 13px; line-height: 1.6; color: #2c3e50;">
    <strong>📝 Detail Pola:</strong> {{ $spk->product->detail_pola ?: 'Tidak Ada' }} <br>
    <strong>📖 Deskripsi Produk:</strong> {{ $spk->product->deskripsi ?: 'Tidak Ada' }} <br>
    <strong>⚠️ Keterangan Internal:</strong> {{ $spk->product->keterangan_internal ?: 'Tidak Ada' }} <br>
</div>

<hr style="margin: 20px 0; border: 1px dashed #eee;">

{{-- 6. TABEL BAHAN --}}{{-- 6. TABEL BAHAN BAKU --}}
<strong>📦 Daftar Bahan Baku:</strong>

<table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; border: 1px solid #ddd;">
    <tr style="background: #34495e; color: white;">
        <th style="padding: 8px; border: 1px solid #ddd; text-align: center; width: 50px;">Foto</th>
        <th style="padding: 8px; border: 1px solid #ddd; text-align: left;">Bahan</th>
        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">Total</th>
    </tr>
    @isset($spk->product->boms)
        @foreach ($spk->product->boms as $bom)
        <tr>
            <td style="padding: 5px; border: 1px solid #ddd; text-align: center;">
                @php
                    $fotoUrl = null;
                    if (isset($bom->bahanBaku->foto) && $bom->bahanBaku->foto) {
                        $fotoUrl = $bom->bahanBaku->foto;
                    } else {
                        $fallback = \App\Models\BahanBaku::where('nama_material', $bom->bahanBaku->nama_material)
                            ->whereNotNull('foto')
                            ->where('foto', '!=', '')
                            ->first();
                        if ($fallback) {
                            $fotoUrl = $fallback->foto;
                        }
                    }
                @endphp

                @if($fotoUrl)
                    <img src="{{ Storage::disk('public')->url($fotoUrl) }}" width="40" height="40" style="border-radius: 4px;">
                @else
                    <span style="color: #ccc; font-size: 8px;">No Pic</span>
                @endif
            </td>
            <td style="padding: 8px; border: 1px solid #ddd;">
                <strong>{{ $bom->bahanBaku->nama_material ?? 'Bahan Tidak Diketahui' }}</strong>
                <br><small style="color: #7f8c8d;">Warna: {{ $bom->bahanBaku->warna ?? '-' }}</small>
            </td>
            <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                {{ (int)($bom->jumlah_kebutuhan * $spk->jumlah_produksi) }} 
                {{ $bom->bahanBaku->satuan ?? 'Unit' }}
            </td>
        </tr>
        @endforeach
    @endisset
</table>

<div style="text-align: center; margin: 20px 0; background: #ebf5fb; padding: 10px;">
    Target: <strong>{{ (int)$spk->jumlah_produksi }} Pcs</strong>
</div>

@if(!$loop->last) <hr style="margin: 40px 0; border: 2px solid #3498db;"> @endif

@endif
@endforeach

<div style="text-align: center; margin-top: 20px;">
    @if($isKepalaGudang)
        <a href="{{ route('login') }}" style="background: #27ae60; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">LOGIN DASHBOARD</a>
    @else
        <p style="color: #7f8c8d; font-size: 12px;">Anda menerima instruksi ini sebagai Pekerja. Silakan hubungi Kepala Gudang untuk validasi.</p>
    @endif
</div>

@endcomponent