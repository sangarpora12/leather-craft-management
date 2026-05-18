PHP
<div style="margin-bottom: 20px;">
    <p style="color: #475569; font-size: 14px;">
        Admin <strong>{{ $pengadaan->admin->name }}</strong> telah mengirimkan permintaan pengadaan bahan baru.
    </p>
    <p style="font-size: 14px;">
        <strong>Prioritas:</strong> 
        <span style="color: {{ $pengadaan->prioritas == 'URGENT' ? '#ef4444' : '#f59e0b' }}; font-weight: bold;">
            {{ $pengadaan->prioritas }}
        </span> 
        | <strong>Deadline:</strong> {{ \Carbon\Carbon::parse($pengadaan->deadline)->format('d M Y') }}
    </p>
</div>

<table style="width: 100%; border-collapse: collapse; font-family: sans-serif; font-size: 12px;">
    <thead style="background-color: #334155; color: white;">
        <tr>
            <th style="padding: 10px;">Foto</th>
            <th style="padding: 10px;">Nama Bahan</th>
            <th style="padding: 10px;">Jumlah</th>
            <th style="padding: 10px;">Satuan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pengadaan->detailBahan as $item)
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px;"><img src="{{ $message->embed(public_path($item->bahan->foto)) }}" width="50" style="border-radius: 4px;"></td>
            <td style="padding: 10px;">
                <strong>{{ $item->bahan->nama_bahan }}</strong><br>
                <span style="color: #f97316;">Warna: {{ $item->warna }}</span><br>
                <span style="color: #64748b;">{{ $item->bahan->kode_material }}</span>
            </td>
            <td style="padding: 10px; color: #10b981; font-weight: bold;">{{ number_format($item->jumlah, 2) }}</td>
            <td style="padding: 10px; color: #64748b;">{{ $item->bahan->satuan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div style="text-align: center; margin-top: 20px;">
    <a href="{{ route('operator.belanja.detail', $pengadaan->id) }}" style="background-color: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">
        🔍 LIHAT DETAIL PESANAN
    </a>
</div>
