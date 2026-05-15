<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Display the main laporan page with mode-based filtering
     */
    public function index(Request $request)
    {
        // Get mode parameter
        $mode = $request->query('mode', 'lengkap');
        
        // Get date range parameters - default to 2 years ago if not specified
        $fromDate = $request->query('from', '');
        $toDate = $request->query('to', '');
        
        // If no date range specified, use last 2 years
        if (empty($fromDate) || empty($toDate)) {
            $fromDate = Carbon::now()->subYears(2)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
        }
        
        // Get Pengaturan for Kop Surat
        $pengaturan = Pengaturan::first();
        $namaPerusahaan = Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather';
        $alamat = Pengaturan::where('nama_pengaturan', 'alamat')->first()?->nilai ?? '';
        $noTelepon = Pengaturan::where('nama_pengaturan', 'no_telepon')->first()?->nilai ?? '';
        $email = Pengaturan::where('nama_pengaturan', 'email')->first()?->nilai ?? '';
        $logo = Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first()?->nilai ?? '';

        // Build date range
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

// 1. QUERY PRODUKSI (OUTPUT)
$productions = DB::table('produksis')
->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
->leftJoin('users', 'produksis.operator_id', '=', 'users.id')
->whereBetween('produksis.created_at', [$from, $to])
->where('produksis.status', 'SELESAI')
->select(
    'produk_jadis.nama_produk',
    'produk_jadis.kode_produk',
    'produk_jadis.harga_jual',
    DB::raw('SUM(produksis.jumlah_selesai) as jumlah_selesai'),
    
    // 👇 INI DIA YANG BIKIN ERROR (TADI KETINGGALAN DI FUNGSI INDEX) 👇
    DB::raw('COUNT(produksis.id) as jumlah_spk'), 
    
    DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name"), 
    DB::raw('"Produksi" as jenis_mutasi'),
    DB::raw('MAX(produksis.created_at) as tanggal_terbaru')
)
->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
->orderByDesc('tanggal_terbaru')
->get();
        
// 2. QUERY PRODUK KELUAR
$produkKeluars = DB::table('produk_keluars')
->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
->leftJoin('users', 'produk_keluars.user_id', '=', 'users.id')
->whereBetween('produk_keluars.created_at', [$from, $to])
->select(
    'produk_jadis.nama_produk',
    'produk_jadis.kode_produk',
    'produk_jadis.harga_jual',
    DB::raw('SUM(produk_keluars.jumlah_keluar) as jumlah'),
    DB::raw('MAX(produk_keluars.created_at) as tanggal_terbaru'), // Tambahan untuk sorting
    DB::raw("GROUP_CONCAT(DISTINCT produk_keluars.alasan SEPARATOR ', ') as alasan"), 
    DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name")
)
->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
->orderByDesc('tanggal_terbaru') // Urutkan dari yang terbaru
->get();

// 3. QUERY BAHAN MASUK (PENGADAAN) -> Tambah Harga Satuan di sini
$pengadaans = DB::table('log_stok')
->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
->leftJoin('users', 'log_stok.operator_id', '=', 'users.id') // Sesuaikan jika sebelumnya user_id
->whereBetween('log_stok.created_at', [$from, $to])
->where(function($q) {
    $q->where('log_stok.jenis_transaksi', 'like', '%MASUK%')
      ->orWhere('log_stok.jenis_transaksi', 'like', '%TAMBAH%')
      ->orWhere('log_stok.jenis_transaksi', 'like', '%PENGADAAN%')
      ->orWhere('log_stok.jenis_transaksi', 'like', '%RESTOCK%')
      ->orWhere('log_stok.jenis_transaksi', 'like', '%INPUT%')
      ->orWhere('log_stok.jenis_transaksi', 'like', '%UPDATE OPERATOR%');
})
->select(
    'bahan_bakus.nama_material',
    'bahan_bakus.kode_material',
    'bahan_bakus.satuan',
    'bahan_bakus.warna',
    'bahan_bakus.harga_satuan', // Kunci untuk memisahkan harga
    DB::raw('SUM(log_stok.jumlah) as jumlah'),
    DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name"),
    DB::raw('MAX(log_stok.created_at) as tanggal_terbaru') // Tambahan untuk sorting
)
// GroupBy ditambah 'harga_satuan' agar beda harga = beda baris
->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan', 'bahan_bakus.warna', 'bahan_bakus.harga_satuan')
->orderByDesc('tanggal_terbaru') // Urutkan dari yang terbaru
->get();
        
// 4. QUERY BAHAN KELUAR (PEMAKAIAN SPK)
$materialUsage = DB::table('produksis')
->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
->leftJoin('users', 'produksis.operator_id', '=', 'users.id')
->whereBetween('produksis.created_at', [$from, $to])
->where('produksis.status', 'SELESAI')
->select(
    'bahan_bakus.nama_material',
    'bahan_bakus.kode_material',
    'bahan_bakus.satuan',
    'bahan_bakus.warna',
    DB::raw('SUM(produksis.jumlah_selesai * bom.jumlah_kebutuhan) as jumlah'),
    DB::raw("GROUP_CONCAT(DISTINCT produk_jadis.nama_produk SEPARATOR ', ') as nama_produk"), 
    DB::raw('MAX(produksis.created_at) as tanggal_terbaru') // Tambahan untuk sorting
)
->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan', 'bahan_bakus.warna')
->orderByDesc('tanggal_terbaru') // Urutkan dari yang terbaru
->get();
        // Summary for production/produk
        $totalOutput = DB::table('produksis')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', 'SELESAI')
            ->sum('jumlah_selesai') ?? 0;
            
        $totalIncome = DB::table('produk_keluars')
            ->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
            ->whereBetween('produk_keluars.created_at', [$from, $to])
            ->sum(DB::raw('produk_keluars.jumlah_keluar * produk_jadis.harga_jual')) ?? 0;
            
        $produksiPending = DB::table('produksis')
            ->whereBetween('created_at', [$from, $to])
            ->where('status', '!=', 'SELESAI')
            ->sum('jumlah_produksi') ?? 0;
            
        $barangKeluar = DB::table('produk_keluars')
            ->whereBetween('created_at', [$from, $to])
            ->sum('jumlah_keluar') ?? 0;

        // Summary for materials (BAHAN) - Calculate from log_stok
        $totalBahanMasuk = DB::table('log_stok')
            ->whereBetween('created_at', [$from, $to])
            ->where(function($q) {
                $q->where('jenis_transaksi', 'like', '%MASUK%')
                  ->orWhere('jenis_transaksi', 'like', '%TAMBAH%')
                  ->orWhere('jenis_transaksi', 'like', '%PENGADAAN%')
                  ->orWhere('jenis_transaksi', 'like', '%RESTOCK%')
                  ->orWhere('jenis_transaksi', 'like', '%INPUT%')
                  ->orWhere('jenis_transaksi', 'like', '%UPDATE OPERATOR (TAMBAH)%');
            })
            ->sum('jumlah') ?? 0;
        $totalBahanKeluar = $materialUsage->sum('jumlah') ?? 0;
        
        // Stok tersedia = total stok bahan baku saat ini (sum of all current stock)
        $stokTersedia = BahanBaku::sum('stok') ?? 0;
        
        // Per-material stock for detailed view
        $stokPerBahan = BahanBaku::select('nama_material', 'stok', 'satuan', 'stok_minimum')
            ->orderByDesc('stok')
            ->get();

        // Financial Summary (KEUANGAN)
        // Total Pengeluaran: biaya bahan yang terpakai untuk produksi
        $totalPengeluaran = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->whereBetween('produksis.created_at', [$from, $to])
            ->where('produksis.status', 'SELESAI')
            ->sum(DB::raw('produksis.jumlah_selesai * bom.jumlah_kebutuhan * bahan_bakus.harga_satuan')) ?? 0;

        // Total Pemasukan: dari produk yang terjual
        $totalPemasukan = DB::table('produk_keluars')
            ->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
            ->whereBetween('produk_keluars.created_at', [$from, $to])
            ->sum(DB::raw('produk_keluars.jumlah_keluar * produk_jadis.harga_jual')) ?? 0;

        // Estimasi Laba Kotor
        $estimasiLaba = $totalPemasukan - $totalPengeluaran;

        // 1. ESTIMASI NILAI ASET GUDANG (berdasarkan stok saat ini - tidak terpengaruh filter tanggal)
        // Aset = nilai stok yang ada di gudang saat ini
        $nilaiAsetBahanBaku = BahanBaku::selectRaw('SUM(stok * harga_satuan) as total')->value('total') ?? 0;
        $nilaiAsetProdukJadi = DB::table('produk_jadis')
            ->selectRaw('SUM(stok * harga_jual * 0.7) as total') // HPP = 70% harga jual (dummy logic)
            ->value('total') ?? 0;
        $estimasiNilaiAset = $nilaiAsetBahanBaku + $nilaiAsetProdukJadi;

        // 2. BIAYA OPERASIONAL (Dummy logic - bisa diganti dengan tabel pengeluaran)
        $biayaOperasional = DB::table('keuangan')
            ->where('kategori', 'operasional')
            ->whereBetween('created_at', [$from, $to])
            ->sum('total') ?? 0;
        // Jika tidak ada data, gunakan 10% dari pengeluaran produksi sebagai estimasi
        if ($biayaOperasional == 0) {
            $biayaOperasional = $totalPengeluaran * 0.1;
        }

        // 3. LABA BERSIH
        $labaBersih = $estimasiLaba - $biayaOperasional;

        // 4. TOP 3 PRODUK TERLARIS
        $topProdukTerlaris = DB::table('produk_keluars')
            ->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
            ->whereBetween('produk_keluars.created_at', [$from, $to])
            ->select('produk_jadis.nama_produk', 'produk_jadis.kode_produk', DB::raw('SUM(produk_keluars.jumlah_keluar) as total_terjual'))
            ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk')
            ->orderByDesc('total_terjual')
            ->limit(3)
            ->get();

        // 5. BAHAN PALING CEPAT HABIS (berdasarkan pemakaian SPK)
        $bahanCepatHabis = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->whereBetween('produksis.created_at', [$from, $to])
            ->where('produksis.status', 'SELESAI')
            ->select('bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan', 
                     DB::raw('SUM(produksis.jumlah_selesai * bom.jumlah_kebutuhan) as total_dipakai'))
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan')
            ->orderByDesc('total_dipakai')
            ->limit(3)
            ->get();

        // 6. DATA UNTUK GRAFIK (30 hari terakhir)
        $chartLabels = [];
        $chartPemasukan = [];
        $chartPengeluaran = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('d M');
            
            $pemasukanHarian = DB::table('produk_keluars')
                ->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
                ->whereDate('produk_keluars.created_at', $date)
                ->sum(DB::raw('produk_keluars.jumlah_keluar * produk_jadis.harga_jual')) ?? 0;
            $chartPemasukan[] = round($pemasukanHarian, 0);
            
            $pengeluaranHarian = DB::table('produksis')
                ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
                ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
                ->whereDate('produksis.created_at', $date)
                ->where('produksis.status', 'SELESAI')
                ->sum(DB::raw('produksis.jumlah_selesai * bom.jumlah_kebutuhan * bahan_bakus.harga_satuan')) ?? 0;
            $chartPengeluaran[] = round($pengeluaranHarian, 0);
        }

        // Stok kritis bahan baku - Gunakan nilai dari Pengaturan tabel (dinamis)
        $batasStokGlobal = \App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum')->first()?->nilai ?? 5;
        
        $stokKritisCount = BahanBaku::whereRaw('stok < COALESCE(stok_minimum, ?)', [$batasStokGlobal])->count();

        return view('admin.laporan.index', compact(
            'mode', 'fromDate', 'toDate', 'productions', 'pengadaans', 'produkKeluars', 'materialUsage',
            'totalOutput', 'totalIncome', 'produksiPending', 'barangKeluar',
            'totalBahanMasuk', 'totalBahanKeluar', 'stokTersedia', 'stokPerBahan',
            'totalPengeluaran', 'totalPemasukan', 'estimasiLaba', 'labaBersih', 'biayaOperasional',
            'estimasiNilaiAset', 'topProdukTerlaris', 'bahanCepatHabis',
            'chartLabels', 'chartPemasukan', 'chartPengeluaran',
            'stokKritisCount', 'pengaturan', 'namaPerusahaan', 'alamat', 'noTelepon', 'email', 'logo'
        ));
    }

    /**
     * Export data to Excel (Styled HTML to XLS)
     */
    /**
     * Export data to Excel (Styled HTML to XLS)
     */
    public function exportExcel(Request $request)
    {
        $mode = $request->query('mode', 'lengkap');
        $fromDate = $request->query('from', '');
        $toDate = $request->query('to', '');
        
        if (empty($fromDate) || empty($toDate)) {
            $fromDate = Carbon::now()->subYears(2)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');
        }
        
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        // --- 1. AMBIL DATA (Sama Persis Seperti Tampilan Layar) ---
        $productions = DB::table('produksis')
            ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->leftJoin('users', 'produksis.operator_id', '=', 'users.id') 
            ->whereBetween('produksis.created_at', [$from, $to])
            ->where('produksis.status', 'SELESAI')
            ->select('produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual',
                DB::raw('SUM(produksis.jumlah_selesai) as jumlah_selesai'),
                DB::raw('COUNT(produksis.id) as jumlah_spk'),
                DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name"),
                DB::raw('MAX(produksis.created_at) as tanggal_terbaru')
            )
            ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
            ->orderByDesc('tanggal_terbaru')
            ->get();

        $pengadaans = DB::table('log_stok')
            ->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
            ->leftJoin('users', 'log_stok.operator_id', '=', 'users.id') 
            ->whereBetween('log_stok.created_at', [$from, $to])
            ->where(function($q) {
                $q->where('log_stok.jenis_transaksi', 'like', '%MASUK%')
                  ->orWhere('log_stok.jenis_transaksi', 'like', '%TAMBAH%')
                  ->orWhere('log_stok.jenis_transaksi', 'like', '%PENGADAAN%')
                  ->orWhere('log_stok.jenis_transaksi', 'like', '%RESTOCK%')
                  ->orWhere('log_stok.jenis_transaksi', 'like', '%INPUT%')
                  ->orWhere('log_stok.jenis_transaksi', 'like', '%UPDATE OPERATOR%');
            })
            ->select('bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan', 'log_stok.jenis_transaksi',
                'bahan_bakus.warna', 'bahan_bakus.harga_satuan',
                DB::raw('SUM(log_stok.jumlah) as jumlah'),
                DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name"),
                DB::raw('MAX(log_stok.created_at) as tanggal_terbaru')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan', 'bahan_bakus.warna', 'bahan_bakus.harga_satuan', 'log_stok.jenis_transaksi')
            ->orderByDesc('tanggal_terbaru')
            ->get();

        $produkKeluars = DB::table('produk_keluars')
            ->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
            ->leftJoin('users', 'produk_keluars.user_id', '=', 'users.id')
            ->whereBetween('produk_keluars.created_at', [$from, $to])
            ->select('produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual', 
                     DB::raw('SUM(produk_keluars.jumlah_keluar) as jumlah'), 
                     DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name"),
                     DB::raw("GROUP_CONCAT(DISTINCT produk_keluars.alasan SEPARATOR ', ') as alasan"),
                     DB::raw('MAX(produk_keluars.created_at) as tanggal_terbaru'))
            ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
            ->orderByDesc('tanggal_terbaru')
            ->get();

        $materialUsage = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->whereBetween('produksis.created_at', [$from, $to])
            ->where('produksis.status', 'SELESAI')
            ->select('bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan', 'bahan_bakus.warna',
                     DB::raw('SUM(produksis.jumlah_selesai * bom.jumlah_kebutuhan) as jumlah'),
                     DB::raw("GROUP_CONCAT(DISTINCT produk_jadis.nama_produk SEPARATOR ', ') as nama_produk"),
                     DB::raw('MAX(produksis.created_at) as tanggal_terbaru'))
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan', 'bahan_bakus.warna')
            ->orderByDesc('tanggal_terbaru')
            ->get();

        $namaPerusahaan = Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather';
        $waktuSekarang = Carbon::now()->timezone('Asia/Jakarta');

        // --- 2. BANGUN HTML EXCEL ---
        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="utf-8"></head><body>';
        $html .= '<table style="border-collapse: collapse; width: 100%; font-family: Arial;">';
        $html .= '<tr><td colspan="7" style="font-size: 18px; font-weight: bold; text-align: center;">LAPORAN OPERASIONAL - ' . strtoupper($namaPerusahaan) . '</td></tr>';
        $html .= '<tr><td colspan="7" style="text-align: center;">Periode: ' . Carbon::parse($fromDate)->format('d/m/Y') . ' - ' . Carbon::parse($toDate)->format('d/m/Y') . '</td></tr>';
        $html .= '<tr><td colspan="7"></td></tr>';

        $headStyle = 'background-color: #2F5597; color: #ffffff; font-weight: bold; border: 1px solid #000; text-align: center;';
        $subHeadStyle = 'background-color: #D9E1F2; font-weight: bold; border: 1px solid #000;';
        $cellStyle = 'border: 1px solid #000; padding: 5px;';

        // PRODUKSI
        if ($mode == 'lengkap' || $mode == 'produk') {
            $html .= '<tr><td colspan="7" style="' . $subHeadStyle . '">📦 HASIL PRODUKSI (SELESAI)</td></tr>';
            $html .= '<tr><th style="'.$headStyle.'">Kode</th><th style="'.$headStyle.'">Produk</th><th style="'.$headStyle.'">Operator</th><th style="'.$headStyle.'">Harga</th><th style="'.$headStyle.'">Qty</th><th style="'.$headStyle.'">SPK</th><th style="'.$headStyle.'">Total (Rp)</th></tr>';
            foreach ($productions as $p) {
                $html .= '<tr><td style="'.$cellStyle.'">'.$p->kode_produk.'</td><td style="'.$cellStyle.'">'.$p->nama_produk.'</td><td style="'.$cellStyle.'">'.$p->operator_name.'</td><td style="'.$cellStyle.'">'.number_format($p->harga_jual, 0, ',', '.').'</td><td style="'.$cellStyle.'; text-align:center;">'.number_format($p->jumlah_selesai, 0, ',', '.').'</td><td style="'.$cellStyle.'; text-align:center;">'.$p->jumlah_spk.'</td><td style="'.$cellStyle.'">'.number_format($p->jumlah_selesai * $p->harga_jual, 0, ',', '.').'</td></tr>';
            }
            $html .= '<tr><td colspan="7"></td></tr>';
        }

        // PRODUK KELUAR
        if ($mode == 'lengkap' || $mode == 'produk') {
            $html .= '<tr><td colspan="7" style="' . $subHeadStyle . '">📤 PRODUK KELUAR / TERJUAL</td></tr>';
            $html .= '<tr><th style="'.$headStyle.'">Kode</th><th style="'.$headStyle.'">Produk</th><th style="'.$headStyle.'">Keterangan</th><th style="'.$headStyle.'">User</th><th colspan="2" style="'.$headStyle.'">Jumlah</th><th style="'.$headStyle.'">Total (Rp)</th></tr>';
            foreach ($produkKeluars as $pk) {
                $html .= '<tr><td style="'.$cellStyle.'">'.$pk->kode_produk.'</td><td style="'.$cellStyle.'">'.$pk->nama_produk.'</td><td style="'.$cellStyle.'">'.$pk->alasan.'</td><td style="'.$cellStyle.'">'.$pk->operator_name.'</td><td colspan="2" style="'.$cellStyle.'; text-align:center;">'.number_format($pk->jumlah, 0, ',', '.').'</td><td style="'.$cellStyle.'">'.number_format($pk->jumlah * $pk->harga_jual, 0, ',', '.').'</td></tr>';
            }
            $html .= '<tr><td colspan="7"></td></tr>';
        }

        // BAHAN MASUK
        if ($mode == 'lengkap' || $mode == 'bahan') {
            $html .= '<tr><td colspan="7" style="' . $subHeadStyle . '">📥 BAHAN BAKU MASUK</td></tr>';
            $html .= '<tr><th style="'.$headStyle.'">Kode</th><th style="'.$headStyle.'">Nama Bahan</th><th style="'.$headStyle.'">Harga Satuan</th><th style="'.$headStyle.'">Total Nilai</th><th style="'.$headStyle.'">Jumlah Masuk</th><th style="'.$headStyle.'">Satuan</th><th style="'.$headStyle.'">Operator / User</th></tr>';
            foreach ($pengadaans as $b) {
                $warna = (!empty($b->warna) && strtolower($b->warna) !== 'tidak ada warna') ? " [{$b->warna}]" : " [Tidak Ada Warna]";
                $totalNilai = ($b->harga_satuan ?? 0) * $b->jumlah;
                $html .= '<tr><td style="'.$cellStyle.'">'.$b->kode_material.'</td><td style="'.$cellStyle.'">'.$b->nama_material.$warna.'</td><td style="'.$cellStyle.'; text-align:right;">'.number_format($b->harga_satuan ?? 0, 0, ',', '.').'</td><td style="'.$cellStyle.'; text-align:right;">'.number_format($totalNilai, 0, ',', '.').'</td><td style="'.$cellStyle.'; text-align:center;">'.number_format($b->jumlah, 0, ',', '.').'</td><td style="'.$cellStyle.'; text-align:center;">'.$b->satuan.'</td><td style="'.$cellStyle.'">'.($b->operator_name ?? 'N/A').'</td></tr>'; 
            }
            $html .= '<tr><td colspan="7"></td></tr>';
        }

        // BAHAN KELUAR
        if ($mode == 'lengkap' || $mode == 'bahan') {
            $html .= '<tr><td colspan="7" style="' . $subHeadStyle . '">🏭 BAHAN KELUAR / DIPAKAI PRODUKSI</td></tr>';
            $html .= '<tr><th style="'.$headStyle.'">Kode</th><th style="'.$headStyle.'">Bahan Baku</th><th colspan="3" style="'.$headStyle.'">Digunakan Untuk Produk</th><th style="'.$headStyle.'">Total Pakai</th><th style="'.$headStyle.'">Satuan</th></tr>';
            foreach ($materialUsage as $mu) {
                $warna = (!empty($mu->warna) && strtolower($mu->warna) !== 'tidak ada warna') ? " [{$mu->warna}]" : " [Tidak Ada Warna]";
                $html .= '<tr><td style="'.$cellStyle.'">'.$mu->kode_material.'</td><td style="'.$cellStyle.'">'.$mu->nama_material.$warna.'</td><td colspan="3" style="'.$cellStyle.'">'.$mu->nama_produk.'</td><td style="'.$cellStyle.'; text-align:right;">'.number_format($mu->jumlah, 0, ',', '.').'</td><td style="'.$cellStyle.'">'.$mu->satuan.'</td></tr>';
            }
        }

        // --- 3. TTD & WAKTU JAKARTA (EXCEL) ---
        $html .= '<tr><td colspan="7"></td></tr>';
        $html .= '<tr><td colspan="5"></td><td colspan="2" style="text-align:center;">Yogyakarta, '.$waktuSekarang->translatedFormat('d F Y').'</td></tr>';
        $html .= '<tr><td colspan="5"></td><td colspan="2" style="text-align:center;">Pukul: '.$waktuSekarang->format('H:i').' WIB</td></tr>';
        $html .= '<tr><td colspan="7" style="height:60px;"></td></tr>';
        $html .= '<tr><td colspan="5"></td><td colspan="2" style="text-align:center; font-weight:bold; text-decoration:underline;">'.auth()->user()->name.'</td></tr>';
        $html .= '<tr><td colspan="5"></td><td colspan="2" style="text-align:center; font-size:11px;">Admin / Operator</td></tr>';

        $html .= '</table></body></html>';

        $filename = 'Laporan_' . ucfirst($mode) . '_' . $waktuSekarang->format('dmY_Hi') . '.xls';
        return response($html)->header('Content-Type', 'application/vnd.ms-excel')->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }
    /**
     * Export data to PDF
     */
    public function exportPdf(Request $request)
    {
        $mode = $request->query('mode', 'lengkap');
        $fromDate = $request->query('from', Carbon::now()->subYears(2)->format('Y-m-d'));
        $toDate = $request->query('to', Carbon::now()->format('Y-m-d'));
        
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        $productions = DB::table('produksis')
        ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
        ->leftJoin('users', 'produksis.operator_id', '=', 'users.id')
        ->whereBetween('produksis.created_at', [$from, $to])
        ->where('produksis.status', 'SELESAI')
        ->select(
            'produk_jadis.nama_produk',
            'produk_jadis.kode_produk',
            'produk_jadis.harga_jual',
            DB::raw('SUM(produksis.jumlah_selesai) as jumlah_selesai'),
            
            // 👇 INI BARIS YANG SEMPAT TERTINGGAL 👇
            DB::raw('COUNT(produksis.id) as jumlah_spk'), 
            
            DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name"), 
            DB::raw('"Produksi" as jenis_mutasi'),
            DB::raw('MAX(produksis.created_at) as tanggal_terbaru')
        )
        ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
        ->orderByDesc('tanggal_terbaru')
        ->get();

        $totalTarget = $productions->sum('total_target');
        $totalCapaian = $productions->sum('total_capaian');
        $totalNilai = $productions->sum(function($item) {
            return $item->total_capaian * $item->harga_jual;
        });

        $pengaturan = Pengaturan::first();

        $pdf = Pdf::loadView('admin.laporan.export-pdf', [
            'productions' => $productions,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'totalTarget' => $totalTarget,
            'totalCapaian' => $totalCapaian,
            'totalNilai' => $totalNilai,
            'tanggal_cetak' => Carbon::now()->format('d M Y H:i'),
            'pengaturan' => $pengaturan,
        ]);

        $filename = 'laporan_produksi_' . $fromDate . '_to_' . $toDate . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Legacy redirect
     */
    public function lengkap(Request $request)
    {
        return redirect()->route('admin.laporan.index', array_merge($request->all(), ['mode' => 'lengkap']));
    }

    /**
     * Legacy redirect
     */
    public function cetakLengkap(Request $request)
    {
        return redirect()->route('admin.laporan.index', array_merge($request->all(), ['mode' => 'lengkap']));
    }

    /**
     * Legacy
     */
    public function exportLengkapPdf(Request $request)
    {
        return $this->exportPdf($request);
    }

    /**
     * Legacy
     */
    public function exportLengkapXlsx(Request $request)
    {
        return $this->exportPdf($request);
    }

    /**
     * Legacy
     */
    public function hasilProduksi(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Legacy
     */
    public function harian(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Legacy
     */
    public function analisis(Request $request)
    {
        return $this->index($request);
    }

/**
     * Laporan untuk Operator - Menampilkan Produksi dan Penerimaan Bahan Baku
     */public function indexOperator(Request $request)
{
    $user = auth()->user();
    
    // Filter tanggal (default bulan ini)
    $fromDate = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
    $toDate = $request->query('to', now()->format('Y-m-d'));
    $from = \Carbon\Carbon::parse($fromDate)->startOfDay();
    $to = \Carbon\Carbon::parse($toDate)->endOfDay();

    // Ambil Stok Kritis (Logika Admin: Stok < Stok Minimum)
    $stokKritis = \App\Models\BahanBaku::whereRaw('stok < stok_minimum')->get();

    // Query Gabungan Bahan Masuk & Produk Selesai
    $riwayatProduksi = DB::table('produksis')
        ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
        ->where('produksis.status', 'SELESAI')
        ->whereBetween('produksis.updated_at', [$from, $to])
        ->select(
            'produk_jadis.nama_produk as nama',
            DB::raw("'produk' as jenis"),
            'produksis.jumlah_selesai as qty',
            'produksis.updated_at as tgl'
        );

    $riwayatBahan = DB::table('pengadaans')
        ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
        ->whereIn('pengadaans.status', ['SELESAI', 'DITERIMA'])
        ->whereBetween('pengadaans.updated_at', [$from, $to])
        ->select(
            'bahan_bakus.nama_material as nama',
            DB::raw("'bahan' as jenis"),
            'pengadaans.received as qty',
            'pengadaans.updated_at as tgl'
        );

    $riwayat = $riwayatProduksi->union($riwayatBahan)
        ->orderBy('tgl', 'desc')
        ->paginate(15);

    return view('operator.laporan.index', compact('riwayat', 'stokKritis', 'fromDate', 'toDate', 'user'));
}
public function cetakPdfOperator(Request $request)
    {
        $user = auth()->user();
        $kategori = $request->query('kategori', 'semua');
        $fromDate = $request->query('from', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->query('to', \Carbon\Carbon::now()->format('Y-m-d'));
        
        $from = \Carbon\Carbon::parse($fromDate)->startOfDay();
        $to = \Carbon\Carbon::parse($toDate)->endOfDay();

        $stokKritis = \App\Models\BahanBaku::whereRaw('stok < stok_minimum')->get();

        // 1. Query Produksi (Tambah jumlah_transaksi)
        $riwayatProduksi = \Illuminate\Support\Facades\DB::table('produksis')
            ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->whereBetween('produksis.updated_at', [$from, $to])
            ->where('produksis.status', 'SELESAI')
            ->select(
                'produk_jadis.nama_produk as nama', 
                \Illuminate\Support\Facades\DB::raw("'PRODUK' as jenis"), 
                \Illuminate\Support\Facades\DB::raw('SUM(jumlah_selesai) as total_qty'), 
                \Illuminate\Support\Facades\DB::raw('COUNT(produksis.id) as jumlah_transaksi'), // <-- AMBIL DATA INI
                'produksis.updated_at as tanggal_terakhir'
            )
            ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produksis.updated_at');

        // 2. Query Bahan (Tambah jumlah_transaksi)
        $riwayatBahan = \Illuminate\Support\Facades\DB::table('pengadaans')
            ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
            ->whereBetween('pengadaans.updated_at', [$from, $to])
            ->whereIn('pengadaans.status', ['SELESAI', 'DONE', 'DITERIMA'])
            ->select(
                'bahan_bakus.nama_material as nama', 
                \Illuminate\Support\Facades\DB::raw("'BAHAN MASUK' as jenis"), 
                \Illuminate\Support\Facades\DB::raw('SUM(received) as total_qty'), 
                \Illuminate\Support\Facades\DB::raw('COUNT(pengadaans.id) as jumlah_transaksi'), // <-- AMBIL DATA INI
                'pengadaans.updated_at as tanggal_terakhir'
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'pengadaans.updated_at');

       // ... (bagian query select riwayatProduksi & riwayatBahan sudah benar) ...

        // 3. Gabungkan dan Urutkan
        // PERHATIKAN: Di orderBy cukup tulis nama aliasnya saja tanpa 'as'
        if ($kategori == 'produk') {
            $data = $riwayatProduksi->orderBy('tanggal_terakhir', 'desc')->get();
        } elseif ($kategori == 'bahan') {
            $data = $riwayatBahan->orderBy('tanggal_terakhir', 'desc')->get();
        } else {
            // Jika union, pengurutan dilakukan setelah data digabung
            $data = $riwayatProduksi->union($riwayatBahan)
                ->orderBy('tanggal_terakhir', 'desc') 
                ->get();
        }

        $pengaturan = \App\Models\Pengaturan::first();

        // Load PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('operator.laporan.pdf', [
            'riwayat' => $data,
            'stokKritis' => $stokKritis,
            'user' => $user,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'kategori' => $kategori,
            'totalProduk' => ($kategori == 'bahan') ? 0 : $riwayatProduksi->get()->sum('total_qty'),
            'totalBahan' => ($kategori == 'produk') ? 0 : $riwayatBahan->get()->sum('total_qty'),
            'pengaturan' => $pengaturan
        ]);

        return $pdf->download('Laporan_Operator_'.now()->format('d-m-Y').'.pdf');
    }
    /**
     * Fast complete
     */
    public function fastComplete(Request $request)
    {
        return back()->with('success', 'Fitur cepat selesai diproses');
    }
}