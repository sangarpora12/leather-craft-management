<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - {{ auth()->user()->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-container { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
            table { font-size: 10pt !important; border: 1px solid black !important; }
            th, td { border: 1px solid black !important; }
            .bg-gray-100 { background-color: #f3f4f6 !important; -webkit-print-color-adjust: exact; }
        }
        @page {
            size: A4;
            margin: 1.5cm;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-gray-200">

    {{-- FILTER BAR (Hanya muncul di layar, melayang di atas) --}}
    <div class="no-print sticky top-0 z-50 bg-[#1a1a1a] shadow-2xl border-b border-white/10 p-4 mb-6">
        <div class="max-w-4xl mx-auto flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('operator.laporan.cetak') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-[10px] text-gray-400 uppercase font-bold">Periode:</label>
                    <input type="date" name="from" value="{{ $fromDate }}" class="bg-black border border-white/20 rounded-lg px-3 py-1.5 text-xs text-white">
                    <span class="text-gray-500 text-xs">s/d</span>
                    <input type="date" name="to" value="{{ $toDate }}" class="bg-black border border-white/20 rounded-lg px-3 py-1.5 text-xs text-white">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i> Update Data
                </button>
            </form>

            <div class="flex gap-2">
                <button onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <a href="{{ route('operator.laporan.index') }}" class="bg-gray-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- CONTAINER LAPORAN --}}
    <div class="print-container max-w-4xl mx-auto bg-white shadow-xl rounded-lg p-8 md:p-12 mb-10">
        
        {{-- KOP SURAT --}}
        <div class="flex items-center justify-between border-b-4 border-black pb-6 mb-8">
            <div class="text-left">
                <h1 class="text-3xl font-black uppercase tracking-tighter">Berliano Leather</h1>
                <p class="text-xs text-gray-600">Production & Inventory System</p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold uppercase">Laporan Operasional</h2>
                <p class="text-xs text-gray-500">Periode: {{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- INFO OPERATOR --}}
        <div class="flex justify-between mb-8 text-sm">
            <div>
                <p class="text-gray-500 uppercase text-[10px] font-bold">Operator Pelaksana</p>
                <p class="font-bold text-lg">{{ auth()->user()->name }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-500 uppercase text-[10px] font-bold">Waktu Cetak</p>
                <p class="font-medium">{{ now()->format('d M Y, H:i') }}</p>
            </div>
        </div>

        {{-- RINGKASAN ANGKA --}}
        <div class="grid grid-cols-3 gap-6 mb-10">
            <div class="border-2 border-yellow-500 rounded-xl p-4 text-center">
                <p class="text-[10px] text-gray-500 uppercase font-black">SPK Pending</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $pendingCount }}</p>
            </div>
            <div class="border-2 border-blue-500 rounded-xl p-4 text-center">
                <p class="text-[10px] text-gray-500 uppercase font-black">Dalam Proses</p>
                <p class="text-3xl font-bold text-blue-600">{{ $prosesCount }}</p>
            </div>
            <div class="border-2 border-green-500 rounded-xl p-4 text-center">
                <p class="text-[10px] text-gray-500 uppercase font-black">Total Selesai</p>
                <p class="text-3xl font-bold text-green-600">{{ $selesaiCount }}</p>
            </div>
        </div>

        {{-- TABEL PRODUKSI --}}
        <div class="mb-10">
            <h3 class="bg-gray-100 px-4 py-2 font-black uppercase text-sm border-l-4 border-black mb-4">I. Ringkasan Hasil Produksi</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 uppercase text-[10px] tracking-widest">
                        <th class="border px-4 py-3 text-left">Nama Produk Jadi</th>
                        <th class="border px-4 py-3 text-center">Total Produksi</th>
                        <th class="border px-4 py-3 text-center">Jumlah SPK</th>
                        <th class="border px-4 py-3 text-right">Update Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                    <tr>
                        <td class="border px-4 py-3 font-bold">{{ $item->product->nama_produk ?? 'N/A' }}</td>
                        <td class="border px-4 py-3 text-center font-black">{{ number_format($item->total_qty) }} Pcs</td>
                        <td class="border px-4 py-3 text-center">{{ $item->jumlah_spk }} Kali</td>
                        <td class="border px-4 py-3 text-right text-gray-500 text-xs">{{ \Carbon\Carbon::parse($item->latest_date)->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="border p-8 text-center text-gray-400 italic">Data produksi tidak ditemukan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TABEL BAHAN MASUK --}}
        <div class="mb-10">
            <h3 class="bg-gray-100 px-4 py-2 font-black uppercase text-sm border-l-4 border-black mb-4">II. Penerimaan Bahan Baku</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 uppercase text-[10px] tracking-widest">
                        <th class="border px-4 py-3 text-left">Nama Material</th>
                        <th class="border px-4 py-3 text-center">Total Masuk</th>
                        <th class="border px-4 py-3 text-center">Satuan</th>
                        <th class="border px-4 py-3 text-right">Frek. Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bahanMasuk as $item)
                    <tr>
                        <td class="border px-4 py-3 font-bold">{{ $item->nama_material }}</td>
                        <td class="border px-4 py-3 text-center font-black text-emerald-600">{{ number_format($item->total_masuk) }}</td>
                        <td class="border px-4 py-3 text-center">{{ $item->satuan ?? 'Unit' }}</td>
                        <td class="border px-4 py-3 text-right">{{ $item->jumlah_transaksi }} Transaksi</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="border p-8 text-center text-gray-400 italic">Tidak ada bahan masuk pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TABEL BAHAN KELUAR --}}
        <div>
            <h3 class="bg-gray-100 px-4 py-2 font-black uppercase text-sm border-l-4 border-black mb-4">III. Pemakaian Bahan Produksi</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 uppercase text-[10px] tracking-widest">
                        <th class="border px-4 py-3 text-left">Nama Material</th>
                        <th class="border px-4 py-3 text-center">Total Dipakai</th>
                        <th class="border px-4 py-3 text-center">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bahanKeluar as $item)
                    <tr>
                        <td class="border px-4 py-3 font-bold">{{ $item->nama_material }}</td>
                        <td class="border px-4 py-3 text-center font-black text-red-600">{{ number_format($item->total_keluar) }}</td>
                        <td class="border px-4 py-3 text-center">{{ $item->satuan ?? 'Unit' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="border p-8 text-center text-gray-400 italic">Belum ada pemakaian bahan pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="mt-16 flex justify-end">
            <div class="text-center w-64">
                <p class="text-sm mb-20 text-gray-600">Yogyakarta, {{ now()->format('d F Y') }}</p>
                <div class="border-b border-black w-full mb-1"></div>
                <p class="font-bold uppercase text-sm">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-tighter">Operator Produksi / Gudang</p>
            </div>
        </div>
    </div>

</body>
</html>