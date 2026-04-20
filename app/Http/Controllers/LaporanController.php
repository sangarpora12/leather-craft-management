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
        
        // Get date range parameters - default to ALL TIME if not specified
        $fromDate = $request->query('from', '');
        $toDate = $request->query('to', '');
        
        // If no date range specified, use all time
        if (empty($fromDate) || empty($toDate)) {
            $fromDate = '2020-01-01'; // Far past date
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

        // Get production data - Grouped by PRODUCT NAME only (not by date)
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
                DB::raw('COUNT(produksis.id) as jumlah_spk'),
                DB::raw('MIN(DATE(produksis.created_at)) as tanggal_mulai'),
                DB::raw('MAX(DATE(produksis.created_at)) as tanggal_terakhir'),
                DB::raw('"Produksi" as jenis_mutasi')
            )
            ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
            ->orderByDesc('jumlah_selesai')
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
                DB::raw('"Keluar" as jenis_mutasi')
            )
            ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
            ->orderByDesc('jumlah')
            ->get();

        // Get bahan masuk dari log_stok (material incoming) - Grouped by MATERIAL NAME only
        // Tampilkan SEMUA transaksi masuk (scan nota, input manual admin/operator)
        $pengadaans = DB::table('log_stok')
            ->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
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
                DB::raw('COUNT(log_stok.id) as jumlah_transaksi'),
                DB::raw('"Masuk" as jenis_mutasi')
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
            ->select(
                'bahan_bakus.nama_material',
                'bahan_bakus.kode_material',
                'bahan_bakus.satuan',
                DB::raw('SUM(produksis.jumlah_selesai * bom.jumlah_kebutuhan) as jumlah'),
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
     * Export data to Excel
     */
    public function exportExcel(Request $request)
    {
        $mode = $request->query('mode', 'lengkap');
        $fromDate = $request->query('from', '');
        $toDate = $request->query('to', '');
        
        if (empty($fromDate) || empty($toDate)) {
            $fromDate = '2020-01-01';
            $toDate = Carbon::now()->format('Y-m-d');
        }
        
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();
        
        // Get data based on mode
        $productions = DB::table('produksis')
            ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->whereBetween('produksis.created_at', [$from, $to])
            ->where('produksis.status', 'SELESAI')
            ->select(
                'produk_jadis.kode_produk',
                'produk_jadis.nama_produk',
                'produk_jadis.harga_jual',
                DB::raw('SUM(produksis.jumlah_selesai) as jumlah_selesai'),
                DB::raw('COUNT(produksis.id) as jumlah_spk')
            )
            ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
            ->orderByDesc('jumlah_selesai')
            ->get();
        
        $produkKeluars = DB::table('produk_keluars')
            ->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
            ->whereBetween('produk_keluars.created_at', [$from, $to])
            ->select(
                'produk_jadis.kode_produk',
                'produk_jadis.nama_produk',
                'produk_jadis.harga_jual',
                DB::raw('SUM(produk_keluars.jumlah_keluar) as jumlah'),
                DB::raw('COUNT(produk_keluars.id) as jumlah_transaksi')
            )
            ->groupBy('produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.kode_produk', 'produk_jadis.harga_jual')
            ->orderByDesc('jumlah')
            ->get();
        
        $pengadaans = DB::table('pengadaans')
            ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
            ->whereBetween('pengadaans.created_at', [$from, $to])
            ->select(
                'bahan_bakus.kode_material',
                'bahan_bakus.nama_material',
                'bahan_bakus.satuan',
                DB::raw('SUM(pengadaans.jumlah) as jumlah'),
                DB::raw('COUNT(pengadaans.id) as jumlah_transaksi')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan')
            ->orderByDesc('jumlah')
            ->get();
        
        $materialUsage = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->whereBetween('produksis.created_at', [$from, $to])
            ->where('produksis.status', 'SELESAI')
            ->select(
                'bahan_bakus.kode_material',
                'bahan_bakus.nama_material',
                'bahan_bakus.satuan',
                DB::raw('SUM(produksis.jumlah_selesai * bom.jumlah_kebutuhan) as jumlah')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.kode_material', 'bahan_bakus.satuan')
            ->orderByDesc('jumlah')
            ->get();
        
        // Create CSV content
        $csv = "Laporan Operasional - Berliano Leather\n";
        $csv .= "Periode: " . Carbon::parse($fromDate)->format('d/m/Y') . " - " . Carbon::parse($toDate)->format('d/m/Y') . "\n\n";
        
        if ($mode == 'lengkap' || $mode == 'produk') {
            $csv .= "=== OUTPUT PRODUKSI ===\n";
            $csv .= "Kode,Nama Produk,Harga Jual,Total Selesai,Jumlah SPK,Nilai Total\n";
            foreach ($productions as $prod) {
                $csv .= "{$prod->kode_produk},{$prod->nama_produk},{$prod->harga_jual},{$prod->jumlah_selesai},{$prod->jumlah_spk}," . ($prod->jumlah_selesai * $prod->harga_jual) . "\n";
            }
            $csv .= "\n";
            
            $csv .= "=== PRODUK KELUAR ===\n";
            $csv .= "Kode,Nama Produk,Harga Jual,Total Keluar,Jumlah Transaksi,Nilai Total\n";
            foreach ($produkKeluars as $keluar) {
                $csv .= "{$keluar->kode_produk},{$keluar->nama_produk},{$keluar->harga_jual},{$keluar->jumlah},{$keluar->jumlah_transaksi}," . ($keluar->jumlah * $keluar->harga_jual) . "\n";
            }
            $csv .= "\n";
        }
        
        if ($mode == 'lengkap' || $mode == 'bahan') {
            $csv .= "=== BAHAN MASUK ===\n";
            $csv .= "Kode,Nama Bahan,Satuan,Total Masuk,Jumlah Transaksi\n";
            foreach ($pengadaans as $bahan) {
                $csv .= "{$bahan->kode_material},{$bahan->nama_material},{$bahan->satuan},{$bahan->jumlah},{$bahan->jumlah_transaksi}\n";
            }
            $csv .= "\n";
            
            $csv .= "=== BAHAN KELUAR ===\n";
            $csv .= "Kode,Nama Bahan,Satuan,Total Dipakai\n";
            foreach ($materialUsage as $bahan) {
                $csv .= "{$bahan->kode_material},{$bahan->nama_material},{$bahan->satuan},{$bahan->jumlah}\n";
            }
        }
        
        $filename = 'laporan_' . $mode . '_' . $fromDate . '_to_' . $toDate . '.csv';
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export data to PDF
     */
    public function exportPdf(Request $request)
    {
        $mode = $request->query('mode', 'lengkap');
        $fromDate = $request->query('from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->query('to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        $productions = DB::table('produksis')
            ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->leftJoin('users', 'produksis.operator_id', '=', 'users.id')
            ->whereBetween('produksis.created_at', [$from, $to])
            ->where('produksis.status', 'SELESAI')
            ->select(
                DB::raw('DATE(produksis.created_at) as tanggal'),
                'produk_jadis.nama_produk',
                'produk_jadis.harga_jual',
                DB::raw('SUM(produksis.jumlah_produksi) as total_target'),
                DB::raw('SUM(produksis.jumlah_selesai) as total_capaian'),
                DB::raw('users.name as operator_name')
            )
            ->groupBy(DB::raw('DATE(produksis.created_at)'), 'produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.harga_jual', 'users.name')
            ->orderByDesc('tanggal')
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

        // ✅ DATA BAHAN BAKU YANG DITERIMA (DIBELI) OLEH OPERATOR INI
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
