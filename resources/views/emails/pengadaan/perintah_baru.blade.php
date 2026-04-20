@component('mail::message')

# 📦 Perintah Pengadaan Bahan Baru

Halo **{{ $operatorName }}**,

Admin **{{ $adminName }}** telah mengirimkan permintaan pengadaan bahan baru.

**Prioritas:** {{ $prioritas }}
**Deadline:** {{ $deadline }}

---

## 📋 Daftar Bahan Yang Harus Dipesan:

<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; margin-top: 15px; line-height: 1.2;">
    <thead>
        <tr style="background-color: #374151; color: white;">
            <th width="70" style="padding: 10px; text-align: center; font-size: 11px; font-weight: bold;">Foto</th>
            <th style="padding: 10px; text-align: left; font-size: 11px; font-weight: bold;">Nama Bahan</th>
            <th width="65" style="padding: 10px; text-align: center; font-size: 11px; font-weight: bold;">Jumlah</th>
            <th width="60" style="padding: 10px; text-align: center; font-size: 11px; font-weight: bold;">Satuan</th>
            <th style="padding: 10px; text-align: left; font-size: 11px; font-weight: bold;">Catatan</th>
        </tr>
    </thead>
    <tbody>
    @foreach($pengadaanGroup as $item)
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td width="50" valign="middle" style="padding: 8px; text-align: center; vertical-align: middle;">
                @if(isset($daftarFoto[$item->id]))
                    <img src="{{ $daftarFoto[$item->id] }}" width="48" height="48" style="border-radius: 8px; object-fit: cover; display: block; margin: 0 auto; border: none; max-width:48px; max-height:48px;">
                @else
                    <div style="width:48px; height:48px; background:#374151; border-radius:8px; display:flex; align-items:center; justify-content:center; margin:0 auto; color:white; font-size:18px;">📦</div>
                @endif
            </td>
            <td valign="middle" style="padding: 8px 10px; text-align: left; vertical-align: middle;">
                <div style="font-weight: bold; font-size: 13px; color: #1e293b;">{{ $item->bahan->nama_material }}</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 3px;">{{ $item->bahan->kode_material }}</div>
            </td>
            <td width="65" valign="middle" style="padding: 8px 10px; text-align: center; vertical-align: middle; font-weight: bold; font-size: 13px; color: #059669;">{{ intval($item->jumlah) }}</td>
            <td width="60" valign="middle" style="padding: 8px 10px; text-align: center; vertical-align: middle; font-size: 12px; color: #64748b;">{{ $item->bahan->satuan }}</td>
            <td valign="middle" style="padding: 8px 10px; text-align: left; vertical-align: middle; font-size: 11px; color: #64748b;">{{ $item->catatan ?: '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@component('mail::button', ['url' => $actionUrl, 'color' => 'success'])
🔍 LIHAT DETAIL PESANAN
@endcomponent

Silahkan segera proses pesanan ini.

Salam,<br>
**Sistem Inventory Berliano Leather**

@endcomponent