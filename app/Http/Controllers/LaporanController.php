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

        $productions = DB::table('produksis')
        ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
        ->leftJoin('users', 'produksis.operator_id', '=', 'users.id') //
        ->whereBetween('produksis.created_at', [$from, $to])
        ->where('produksis.status', 'SELESAI')
        ->select(
            'produk_jadis.nama_produk',
            'produk_jadis.kode_produk',
            'produk_jadis.harga_jual',
            DB::raw('SUM(produksis.jumlah_selesai) as jumlah_selesai'),
            // Ambil nama operator dari tabel users
            DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name"), 
            DB::raw('"Produksi" as jenis_mutasi')
        )
        ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
        ->get();
                
// Get product keluar/sales - Grouped by PRODUCT NAME only
$produkKeluars = DB::table('produk_keluars')
->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
->leftJoin('users', 'produk_keluars.user_id', '=', 'users.id')
->whereBetween('produk_keluars.created_at', [$from, $to])
->select(
    'produk_jadis.nama_produk',
    'produk_jadis.kode_produk',
    'produk_jadis.harga_jual',
    DB::raw('SUM(produk_keluars.jumlah_keluar) as jumlah'),
    DB::raw('COUNT(produk_keluars.id) as jumlah_transaksi'),
    DB::raw('MIN(DATE(produk_keluars.created_at)) as tanggal_mulai'),
    DB::raw('MAX(DATE(produk_keluars.created_at)) as tanggal_terakhir'),
    DB::raw('MAX(DATE(produk_keluars.created_at)) as tanggal_keluar'), 
    
    // ✅ UBAH DI SINI: Menarik data alasan/keterangan dari database
    DB::raw("GROUP_CONCAT(DISTINCT produk_keluars.alasan SEPARATOR ', ') as alasan"), 
    
    DB::raw("'-' as jenis_transaksi"), 
    DB::raw("'-' as keterangan"), 
    DB::raw('"Keluar" as jenis_mutasi'),
    DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as user_name")
)
->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
->orderByDesc('jumlah')
->get();

        
// Get bahan masuk dari log_stok (material incoming) - Grouped by MATERIAL NAME only
$pengadaans = DB::table('log_stok')
->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
->leftJoin('users', 'log_stok.operator_id', '=', 'users.id') // ✅ KEMBALIKAN KE operator_id
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
    DB::raw('SUM(log_stok.jumlah) as jumlah'),
    DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as user_name")
)
->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan')
->orderByDesc('jumlah')
->get();
                
// Get material usage from production (material outgoing) - Grouped by MATERIAL NAME only
$materialUsage = DB::table('produksis')
->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
->leftJoin('users', 'produksis.operator_id', '=', 'users.id')
->whereBetween('produksis.created_at', [$from, $to])
->where('produksis.status', 'SELESAI')
// ->whereNull('bahan_bakus.deleted_at') <--- BARIS INI KITA HAPUS
->select(
    'bahan_bakus.nama_material',
    'bahan_bakus.kode_material',
    'bahan_bakus.satuan',
    DB::raw('SUM(produksis.jumlah_selesai * bom.jumlah_kebutuhan) as jumlah'),
    DB::raw("GROUP_CONCAT(DISTINCT produk_jadis.nama_produk SEPARATOR ', ') as nama_produk"), // Nama produk tetap kita ambil
    DB::raw('MAX(DATE(produksis.created_at)) as tanggal_terakhir'), 
    DB::raw('MAX(DATE(produksis.created_at)) as tanggal_keluar'), 
    DB::raw("'-' as alasan"), 
    DB::raw("'-' as jenis_transaksi"), 
    DB::raw("'-' as keterangan"), 
    DB::raw('"Dipakai" as jenis_mutasi')
)
->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan')
->orderByDesc('jumlah')
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

// Get production data - Grouped by PRODUCT NAME only
$productions = DB::table('produksis')
->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            // ✅ COBA GANTI operator_id MENJADI user_id
            ->leftJoin('users', 'produksis.user_id', '=', 'users.id') 
            ->whereBetween('produksis.created_at', [$from, $to])
->where('produksis.status', 'SELESAI')
->select(
    'produk_jadis.nama_produk',
    'produk_jadis.kode_produk',
    'produk_jadis.harga_jual', // Tetap dipanggil untuk hitung Nilai Total di View
    DB::raw('SUM(produksis.jumlah_selesai) as jumlah_selesai'),
    DB::raw('COUNT(produksis.id) as jumlah_spk'),
    DB::raw('MIN(DATE(produksis.created_at)) as tanggal_mulai'),
    DB::raw('MAX(DATE(produksis.created_at)) as tanggal_terakhir'),
    DB::raw('MAX(DATE(produksis.created_at)) as tanggal_selesai'),
    DB::raw("'-' as alasan"),
    DB::raw("'-' as jenis_transaksi"),
    DB::raw("'-' as keterangan"),
    DB::raw('"Produksi" as jenis_mutasi'),
    // ✅ TAMBAHAN: Tarik nama operator yang menyelesaikan SPK
    DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name") 
)
->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
->orderByDesc('jumlah_selesai')
->get();
        
// Get bahan masuk dari log_stok (material incoming) - Grouped by MATERIAL NAME only
        // Tampilkan SEMUA transaksi masuk (scan nota, input manual admin/operator)
// Get bahan masuk dari log_stok (material incoming) - Grouped by MATERIAL NAME only
$pengadaans = DB::table('log_stok')
->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
// Cek database Anda: jika yang mencatat log_stok bernama "operator_id", ganti "user_id" di bawah menjadi "operator_id"
->leftJoin('users', 'log_stok.user_id', '=', 'users.id') 
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
    DB::raw('SUM(log_stok.jumlah) as jumlah'),
    DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as user_name") // ✅ Panggil nama operator
)
->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan')
->orderByDesc('jumlah')
->get();

        $namaPerusahaan = Pengaturan::where('nama_pengaturan', 'nama_perusahaan')->first()?->nilai ?? 'Berliano Leather';
        
        // Membangun Tabel HTML untuk diekspor ke Excel
        $html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="utf-8"></head><body>';
        $html .= '<table style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">';
        
        // HEADER UTAMA LAPORAN
        $html .= '<tr><td colspan="6" style="font-size: 18px; font-weight: bold; text-align: center; vertical-align: middle; height: 30px;">LAPORAN OPERASIONAL - ' . strtoupper($namaPerusahaan) . '</td></tr>';
        $html .= '<tr><td colspan="6" style="font-size: 12px; text-align: center; vertical-align: middle;">Mode: ' . ucfirst($mode) . ' | Periode: ' . Carbon::parse($fromDate)->format('d/m/Y') . ' - ' . Carbon::parse($toDate)->format('d/m/Y') . '</td></tr>';
        $html .= '<tr><td colspan="6"></td></tr>';

        // GAYA CSS INLINE UNTUK TABEL
        $headStyle = 'background-color: #2F5597; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center; padding: 8px;';
        $subHeadStyle = 'background-color: #D9E1F2; font-weight: bold; border: 1px solid #000000; text-align: left; padding: 8px; font-size: 14px;';
        $cellStyle = 'border: 1px solid #000000; padding: 5px; vertical-align: middle;';
        $numberStyle = 'border: 1px solid #000000; padding: 5px; vertical-align: middle; text-align: right;';
        
        if ($mode == 'lengkap' || $mode == 'produk') {
            // --- OUTPUT PRODUKSI ---
            $html .= '<tr><td colspan="6" style="' . $subHeadStyle . '">📦 HASIL PRODUKSI (SELESAI)</td></tr>';
            $html .= '<tr>';
            $html .= '<th style="' . $headStyle . '">Kode Produk</th>';
            $html .= '<th style="' . $headStyle . '">Nama Produk</th>';
            $html .= '<th style="' . $headStyle . '">Harga Jual (Rp)</th>';
            $html .= '<th style="' . $headStyle . '">Total Selesai</th>';
            $html .= '<th style="' . $headStyle . '">Jumlah SPK</th>';
            $html .= '<th style="' . $headStyle . '">Nilai Estimasi (Rp)</th>';
            $html .= '</tr>';

            $totalSelesai = 0;
            $totalNilaiProduksi = 0;

            foreach ($productions as $prod) {
                $nilaiTotal = $prod->jumlah_selesai * $prod->harga_jual;
                $totalSelesai += $prod->jumlah_selesai;
                $totalNilaiProduksi += $nilaiTotal;

                $html .= '<tr>';
                $html .= '<td style="' . $cellStyle . '">' . $prod->kode_produk . '</td>';
                $html .= '<td style="' . $cellStyle . '">' . $prod->nama_produk . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($prod->harga_jual, 0, ',', '.') . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($prod->jumlah_selesai, 0, ',', '.') . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($prod->jumlah_spk, 0, ',', '.') . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($nilaiTotal, 0, ',', '.') . '</td>';
                $html .= '</tr>';
            }
            // Row Total Produksi
            $html .= '<tr><td colspan="3" style="font-weight: bold; ' . $numberStyle . '">TOTAL PRODUKSI:</td>';
            $html .= '<td style="font-weight: bold; ' . $numberStyle . '">' . number_format($totalSelesai, 0, ',', '.') . '</td>';
            $html .= '<td style="' . $cellStyle . '"></td>';
            $html .= '<td style="font-weight: bold; ' . $numberStyle . '">' . number_format($totalNilaiProduksi, 0, ',', '.') . '</td></tr>';
            $html .= '<tr><td colspan="6"></td></tr>';
            
            // --- PRODUK KELUAR ---
            $html .= '<tr><td colspan="6" style="' . $subHeadStyle . '">📤 PRODUK KELUAR / TERJUAL</td></tr>';
            $html .= '<tr>';
            $html .= '<th style="' . $headStyle . '">Kode Produk</th>';
            $html .= '<th style="' . $headStyle . '">Nama Produk</th>';
            $html .= '<th style="' . $headStyle . '">Harga Jual (Rp)</th>';
            $html .= '<th style="' . $headStyle . '">Total Keluar</th>';
            $html .= '<th style="' . $headStyle . '">Jumlah Transaksi</th>';
            $html .= '<th style="' . $headStyle . '">Total Pemasukan (Rp)</th>';
            $html .= '</tr>';

            $totalKeluar = 0;
            $totalNilaiKeluar = 0;

            foreach ($produkKeluars as $keluar) {
                $nilaiTotal = $keluar->jumlah * $keluar->harga_jual;
                $totalKeluar += $keluar->jumlah;
                $totalNilaiKeluar += $nilaiTotal;

                $html .= '<tr>';
                $html .= '<td style="' . $cellStyle . '">' . $keluar->kode_produk . '</td>';
                $html .= '<td style="' . $cellStyle . '">' . $keluar->nama_produk . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($keluar->harga_jual, 0, ',', '.') . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($keluar->jumlah, 0, ',', '.') . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($keluar->jumlah_transaksi, 0, ',', '.') . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($nilaiTotal, 0, ',', '.') . '</td>';
                $html .= '</tr>';
            }
            // Row Total Keluar
            $html .= '<tr><td colspan="3" style="font-weight: bold; ' . $numberStyle . '">TOTAL KELUAR:</td>';
            $html .= '<td style="font-weight: bold; ' . $numberStyle . '">' . number_format($totalKeluar, 0, ',', '.') . '</td>';
            $html .= '<td style="' . $cellStyle . '"></td>';
            $html .= '<td style="font-weight: bold; ' . $numberStyle . '">' . number_format($totalNilaiKeluar, 0, ',', '.') . '</td></tr>';
            $html .= '<tr><td colspan="6"></td></tr>';
        }
        
        if ($mode == 'lengkap' || $mode == 'bahan') {
            // --- BAHAN MASUK ---
            $html .= '<tr><td colspan="5" style="' . $subHeadStyle . '">📥 BAHAN MASUK / PENGADAAN</td><td style="border:none;"></td></tr>';
            $html .= '<tr>';
            $html .= '<th style="' . $headStyle . '">Kode Bahan</th>';
            $html .= '<th colspan="2" style="' . $headStyle . '">Nama Bahan Baku</th>';
            $html .= '<th style="' . $headStyle . '">Total Masuk</th>';
            $html .= '<th style="' . $headStyle . '">Satuan</th>';
            $html .= '<td style="border:none;"></td>'; // Kosongkan kolom 6
            $html .= '</tr>';

            foreach ($pengadaans as $bahan) {
                $html .= '<tr>';
                $html .= '<td style="' . $cellStyle . '">' . $bahan->kode_material . '</td>';
                $html .= '<td colspan="2" style="' . $cellStyle . '">' . $bahan->nama_material . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($bahan->jumlah, 2, ',', '.') . '</td>';
                $html .= '<td style="' . $cellStyle . '">' . $bahan->satuan . '</td>';
                $html .= '<td style="border:none;"></td>';
                $html .= '</tr>';
            }
            $html .= '<tr><td colspan="6"></td></tr>';
            
            // --- BAHAN KELUAR (DIPAKAI) ---
            $html .= '<tr><td colspan="5" style="' . $subHeadStyle . '">🏭 BAHAN KELUAR / DIPAKAI PRODUKSI</td><td style="border:none;"></td></tr>';
            $html .= '<tr>';
            $html .= '<th style="' . $headStyle . '">Kode Bahan</th>';
            $html .= '<th colspan="2" style="' . $headStyle . '">Nama Bahan Baku</th>';
            $html .= '<th style="' . $headStyle . '">Total Dipakai</th>';
            $html .= '<th style="' . $headStyle . '">Satuan</th>';
            $html .= '<td style="border:none;"></td>'; // Kosongkan kolom 6
            $html .= '</tr>';

            foreach ($materialUsage as $bahan) {
                $html .= '<tr>';
                $html .= '<td style="' . $cellStyle . '">' . $bahan->kode_material . '</td>';
                $html .= '<td colspan="2" style="' . $cellStyle . '">' . $bahan->nama_material . '</td>';
                $html .= '<td style="' . $numberStyle . '">' . number_format($bahan->jumlah, 2, ',', '.') . '</td>';
                $html .= '<td style="' . $cellStyle . '">' . $bahan->satuan . '</td>';
                $html .= '<td style="border:none;"></td>';
                $html .= '</tr>';
            }
        }
        
        $html .= '</table></body></html>';
        
        $filename = 'Laporan_' . ucfirst($mode) . '_' . Carbon::parse($fromDate)->format('dmY') . '_sd_' . Carbon::parse($toDate)->format('dmY') . '.xls';
        
        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
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

// Cari bagian ini di LaporanController.php
$productions = DB::table('produksis')
    ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
    ->leftJoin('users', 'produksis.operator_id', '=', 'users.id') // Pastikan join ke tabel users
    ->whereBetween('produksis.created_at', [$from, $to])
    ->where('produksis.status', 'SELESAI')
    ->select(
        'produk_jadis.nama_produk',
        'produk_jadis.kode_produk',
        'produk_jadis.harga_jual',
        DB::raw('SUM(produksis.jumlah_selesai) as jumlah_selesai'),
        DB::raw('COUNT(produksis.id) as jumlah_spk'),
        DB::raw('MIN(DATE(produksis.created_at)) as tanggal_mulai'),
        DB::raw('MAX(DATE(produksis.created_at)) as tanggal_terakhir'),
        DB::raw('MAX(DATE(produksis.created_at)) as tanggal_selesai'),
        DB::raw("'-' as alasan"),
        DB::raw("'-' as jenis_transaksi"),
        
        // 👈 UBAH BARIS INI: Jangan pakai '-' tapi panggil nama operator
        DB::raw("GROUP_CONCAT(DISTINCT users.name SEPARATOR ', ') as operator_name"), 
        
        DB::raw('"Produksi" as jenis_mutasi')
    )
    ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
    ->orderByDesc('jumlah_selesai')
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
     */
    public function indexOperator(Request $request)
    {
        $user = auth()->user();
        
        // Stats Counter
        $pendingCount = \App\Models\Produksi::where('operator_id', $user->id)
            ->where('status', 'PENDING')
            ->count();
            
        $prosesCount = \App\Models\Produksi::where('operator_id', $user->id)
            ->where('status', 'PROSES')
            ->count();
            
        $selesaiCount = \App\Models\Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->count();

        // Hari ini & Minggu ini stats
        $hariIniSelesaiQty = \App\Models\Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereDate('updated_at', today())
            ->sum('jumlah_selesai');

        $mingguIniSelesaiQty = \App\Models\Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('jumlah_selesai');

        // Riwayat SPK Selesai
        $riwayat = \App\Models\Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->with('product')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // DATA BAHAN BAKU YANG DITERIMA (DIBELI) OLEH OPERATOR INI
        // Grouping berdasarkan bahan_baku_id, jumlahkan total
        $bahanDiterima = DB::table('pengadaans')
            ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
            ->where('pengadaans.operator_id', $user->id)
            ->whereIn('pengadaans.status', ['SELESAI', 'DONE', 'DITERIMA'])
            ->select(
                'bahan_bakus.nama_material',
                'bahan_bakus.satuan',
                DB::raw('SUM(pengadaans.received) as total_diterima'),
                DB::raw('COUNT(pengadaans.id) as jumlah_transaksi'),
                DB::raw('MAX(pengadaans.updated_at) as tanggal_terakhir')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.satuan')
            ->orderByDesc('tanggal_terakhir')
            ->get();

        return view('operator.laporan.index', compact(
            'pendingCount',
            'prosesCount',
            'selesaiCount',
            'hariIniSelesaiQty',
            'mingguIniSelesaiQty',
            'riwayat',
            'bahanDiterima'
        ));
    }

    /**
     * Fast complete
     */
    public function fastComplete(Request $request)
    {
        return back()->with('success', 'Fitur cepat selesai diproses');
    }
}