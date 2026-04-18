<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\ProdukJadi;
use App\Models\ProdukKeluar;
use App\Models\BahanBaku;
use App\Models\Bom;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Display the main laporan page with grouped production data
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $produkId = $request->produk_id;
        $bahanId = $request->bahan_id;
        $periode = $request->periode ?? 'bulanan';
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        
        // Get lists for dropdowns
        $produkList = ProdukJadi::orderBy('nama_produk')->get();
        $bahanList = BahanBaku::orderBy('nama_material')->get();
        
        // Calculate date range based on periode
        switch ($periode) {
            case 'harian':
                $fromDate = $tanggal;
                $toDate = $fromDate;
                $from = Carbon::parse($fromDate)->startOfDay();
                $to = Carbon::parse($toDate)->endOfDay();
                break;
            case 'mingguan':
                $from = Carbon::now()->subDays(6)->startOfDay();
                $to = Carbon::now()->endOfDay();
                $fromDate = $from->format('Y-m-d');
                $toDate = $to->format('Y-m-d');
                break;
            case 'bulanan':
            default:
                $from = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
                $to = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
                $fromDate = $from->format('Y-m-d');
                $toDate = $to->format('Y-m-d');
                break;
        }
        
        // ==================== GROUPED DATA (with filters) ====================
        $productions = DB::table('produksis')
            ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->when($bahanId, function($q) use ($bahanId) {
                return $q->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
                         ->where('bom.id_bahan', $bahanId);
            })
            ->when($produkId, function($q) use ($produkId) {
                return $q->where('produksis.produk_jadi_id', $produkId);
            })
            ->when($periode === 'harian', function($q) use ($fromDate) {
                return $q->whereDate('produksis.created_at', $fromDate);
            })
            ->when($periode === 'mingguan', function($q) use ($from, $to) {
                return $q->whereBetween('produksis.created_at', [$from, $to]);
            })
            ->when($periode === 'bulanan', function($q) use ($bulan, $tahun) {
                return $q->whereMonth('produksis.created_at', $bulan)
                             ->whereYear('produksis.created_at', $tahun);
            })
            ->select(
                DB::raw('DATE(produksis.created_at) as tanggal'),
                'produk_jadis.id as produk_jadi_id',
                'produk_jadis.nama_produk',
                'produk_jadis.harga_jual',
                DB::raw('SUM(produksis.jumlah_produksi) as total_target'),
                DB::raw('SUM(produksis.jumlah_selesai) as total_capaian')
            )
            ->groupBy(DB::raw('DATE(produksis.created_at)'), 'produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.harga_jual')
            ->orderByDesc('tanggal')
            ->orderBy('produk_jadis.nama_produk')
            ->get();

        // ==================== CARD VARIABLES (with filters) ====================
        
        // Total Output (Pcs) - sum of all jumlah_selesai with filters
        $totalOutput = DB::table('produksis')
            ->when($bahanId, function($q) use ($bahanId) {
                return $q->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
                         ->where('bom.id_bahan', $bahanId);
            })
            ->when($produkId, function($q) use ($produkId) {
                return $q->where('produksis.produk_jadi_id', $produkId);
            })
            ->when($periode === 'harian', function($q) use ($fromDate) {
                return $q->whereDate('produksis.created_at', $fromDate);
            })
            ->when($periode === 'mingguan', function($q) use ($from, $to) {
                return $q->whereBetween('produksis.created_at', [$from, $to]);
            })
            ->when($periode === 'bulanan', function($q) use ($bulan, $tahun) {
                return $q->whereMonth('produksis.created_at', $bulan)
                             ->whereYear('produksis.created_at', $tahun);
            })
            ->where('produksis.status', 'SELESAI')
            ->sum('jumlah_selesai') ?? 0;

        // Total Omzet/Pemasukan (Rp) - from produk_keluars
        $totalIncome = DB::table('produk_keluars')
            ->join('produk_jadis', 'produk_keluars.produk_jadi_id', '=', 'produk_jadis.id')
            ->when($produkId, function($q) use ($produkId) {
                return $q->where('produk_keluars.produk_jadi_id', $produkId);
            })
            ->when($periode === 'harian', function($q) use ($fromDate) {
                return $q->whereDate('produk_keluars.created_at', $fromDate);
            })
            ->when($periode === 'mingguan', function($q) use ($from, $to) {
                return $q->whereBetween('produk_keluars.created_at', [$from, $to]);
            })
            ->when($periode === 'bulanan', function($q) use ($bulan, $tahun) {
                return $q->whereMonth('produk_keluars.created_at', $bulan)
                             ->whereYear('produk_keluars.created_at', $tahun);
            })
            ->sum(DB::raw('produk_keluars.jumlah_keluar * produk_jadis.harga_jual')) ?? 0;

        // Total Pengeluaran/Biaya Produksi (Rp)
        $totalExpense = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->when($bahanId, function($q) use ($bahanId) {
                return $q->where('bom.id_bahan', $bahanId);
            })
            ->when($produkId, function($q) use ($produkId) {
                return $q->where('produksis.produk_jadi_id', $produkId);
            })
            ->when($periode === 'harian', function($q) use ($fromDate) {
                return $q->whereDate('produksis.created_at', $fromDate);
            })
            ->when($periode === 'mingguan', function($q) use ($from, $to) {
                return $q->whereBetween('produksis.created_at', [$from, $to]);
            })
            ->when($periode === 'bulanan', function($q) use ($bulan, $tahun) {
                return $q->whereMonth('produksis.created_at', $bulan)
                             ->whereYear('produksis.created_at', $tahun);
            })
            ->where('produksis.status', 'SELESAI')
            ->sum(DB::raw('produksis.jumlah_selesai * bom.jumlah_kebutuhan * bahan_bakus.harga_satuan')) ?? 0;

        // Stok Kritis - materials below minimum (no filter)
        // Stok kritis bahan baku - Gunakan nilai dari Pengaturan tabel (dinamis)
        $batasStokGlobal = \App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum')->first()?->nilai ?? 5;
        
        $stokKritisCount = BahanBaku::whereRaw('stok < COALESCE(stok_minimum, ?)', [$batasStokGlobal])->count();

        // Produksi Pending - all non-SELESAI productions
        $produksiPending = DB::table('produksis')
            ->when($bahanId, function($q) use ($bahanId) {
                return $q->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
                         ->where('bom.id_bahan', $bahanId);
            })
            ->when($produkId, function($q) use ($produkId) {
                return $q->where('produksis.produk_jadi_id', $produkId);
            })
            ->when($periode === 'harian', function($q) use ($fromDate) {
                return $q->whereDate('produksis.created_at', $fromDate);
            })
            ->when($periode === 'mingguan', function($q) use ($from, $to) {
                return $q->whereBetween('produksis.created_at', [$from, $to]);
            })
            ->when($periode === 'bulanan', function($q) use ($bulan, $tahun) {
                return $q->whereMonth('produksis.created_at', $bulan)
                             ->whereYear('produksis.created_at', $tahun);
            })
            ->where('produksis.status', '!=', 'SELESAI')
            ->sum('jumlah_produksi') ?? 0;

        // Barang Keluar - total products shipped
        $barangKeluar = DB::table('produk_keluars')
            ->when($produkId, function($q) use ($produkId) {
                return $q->where('produk_jadi_id', $produkId);
            })
            ->when($periode === 'harian', function($q) use ($fromDate) {
                return $q->whereDate('created_at', $fromDate);
            })
            ->when($periode === 'mingguan', function($q) use ($from, $to) {
                return $q->whereBetween('created_at', [$from, $to]);
            })
            ->when($periode === 'bulanan', function($q) use ($bulan, $tahun) {
                return $q->whereMonth('created_at', $bulan)
                             ->whereYear('created_at', $tahun);
            })
            ->sum('jumlah_keluar') ?? 0;

        // ==================== CHART DATA (7 days) - with filters ====================
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('d M');
            
            $dayProduction = DB::table('produksis')
                ->when($bahanId, function($q) use ($bahanId) {
                    return $q->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
                             ->where('bom.id_bahan', $bahanId);
                })
                ->when($produkId, function($q) use ($produkId) {
                    return $q->where('produksis.produk_jadi_id', $produkId);
                })
                ->whereDate('produksis.created_at', $date)
                ->where('produksis.status', 'SELESAI')
                ->sum('produksis.jumlah_selesai');
            
            $chartData[] = $dayProduction ?? 0;
        }

        // ==================== TOTALS FOR TABLE ====================
        $totalTargetSemua = $productions->sum('total_target');
        $totalCapaianSemua = $productions->sum('total_capaian');
        $totalNilaiOutput = $productions->sum(function($item) {
            return $item->total_capaian * $item->harga_jual;
        });

        return view('admin.laporan.index', compact(
            'fromDate', 'toDate', 'from', 'to',
            'productions',
            'totalOutput', 'totalIncome', 'totalExpense',
            'stokKritisCount', 'produksiPending', 'barangKeluar',
            'totalTargetSemua', 'totalCapaianSemua', 'totalNilaiOutput',
            'chartLabels', 'chartData',
            'produkList', 'bahanList',
            'produkId', 'bahanId', 'periode', 'tanggal', 'bulan', 'tahun'
        ));
    }

    /**
     * Export data to PDF
     */
    public function exportPdf(Request $request)
    {
        // Get filter parameters (same as index)
        $produkId = $request->produk_id;
        $bahanId = $request->bahan_id;
        $periode = $request->periode ?? 'bulanan';
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        
        // Calculate date range
        switch ($periode) {
            case 'harian':
                $fromDate = $tanggal;
                $toDate = $fromDate;
                $from = Carbon::parse($fromDate)->startOfDay();
                $to = Carbon::parse($toDate)->endOfDay();
                break;
            case 'mingguan':
                $from = Carbon::now()->subDays(6)->startOfDay();
                $to = Carbon::now()->endOfDay();
                $fromDate = $from->format('Y-m-d');
                $toDate = $to->format('Y-m-d');
                break;
            case 'bulanan':
            default:
                $from = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
                $to = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
                $fromDate = $from->format('Y-m-d');
                $toDate = $to->format('Y-m-d');
                break;
        }

        // Get grouped data (with filters)
        $productions = DB::table('produksis')
            ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->when($bahanId, function($q) use ($bahanId) {
                return $q->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
                         ->where('bom.id_bahan', $bahanId);
            })
            ->when($produkId, function($q) use ($produkId) {
                return $q->where('produksis.produk_jadi_id', $produkId);
            })
            ->when($periode === 'harian', function($q) use ($fromDate) {
                return $q->whereDate('produksis.created_at', $fromDate);
            })
            ->when($periode === 'mingguan', function($q) use ($from, $to) {
                return $q->whereBetween('produksis.created_at', [$from, $to]);
            })
            ->when($periode === 'bulanan', function($q) use ($bulan, $tahun) {
                return $q->whereMonth('produksis.created_at', $bulan)
                             ->whereYear('produksis.created_at', $tahun);
            })
            ->select(
                DB::raw('DATE(produksis.created_at) as tanggal'),
                'produk_jadis.nama_produk',
                'produk_jadis.harga_jual',
                DB::raw('SUM(produksis.jumlah_produksi) as total_target'),
                DB::raw('SUM(produksis.jumlah_selesai) as total_capaian')
            )
            ->groupBy(DB::raw('DATE(produksis.created_at)'), 'produk_jadis.id', 'produk_jadis.nama_produk', 'produk_jadis.harga_jual')
            ->orderByDesc('tanggal')
            ->orderBy('produk_jadis.nama_produk')
            ->get();

        // Calculate totals
        $totalTarget = $productions->sum('total_target');
        $totalCapaian = $productions->sum('total_capaian');
        $totalNilai = $productions->sum(function($item) {
            return $item->total_capaian * $item->harga_jual;
        });

        // Get filter info for PDF
        $filterInfo = [];
        if ($produkId) {
            $produk = ProdukJadi::find($produkId);
            $filterInfo[] = 'Produk: ' . ($produk ? $produk->nama_produk : 'Semua');
        }
        if ($bahanId) {
            $bahan = BahanBaku::find($bahanId);
            $filterInfo[] = 'Bahan: ' . ($bahan ? $bahan->nama_material : 'Semua');
        }
        $filterInfo[] = 'Periode: ' . ucfirst($periode);

        // Generate PDF
        $pdf = Pdf::loadView('admin.laporan.export-pdf', [
            'productions' => $productions,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'totalTarget' => $totalTarget,
            'totalCapaian' => $totalCapaian,
            'totalNilai' => $totalNilai,
            'tanggal_cetak' => Carbon::now()->format('d M Y H:i'),
            'filterInfo' => $filterInfo,
        ]);

        $filename = 'laporan_produksi_' . $fromDate . '_to_' . $toDate . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Display the analisis & efficiency page
     */
    public function analisis(Request $request)
    {
        Carbon::setLocale('id');
        
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        
        // Default: bulan ini
        $from = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $to = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        // ==================== KARTU RINGKASAN ====================
        
        // Total Produksi bulan ini (pcs)
        $totalProduksi = DB::table('produksis')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'SELESAI')
            ->sum('jumlah_selesai') ?? 0;
        
        // Total produksi bulan lalu untuk comparison
        $lastMonth = Carbon::createFromDate($tahun, $bulan, 1)->subMonth();
        $totalProduksiLastMonth = DB::table('produksis')
            ->whereMonth('created_at', $lastMonth->format('m'))
            ->whereYear('created_at', $lastMonth->format('Y'))
            ->where('status', 'SELESAI')
            ->sum('jumlah_selesai') ?? 0;
        
        // Hitung persentase perubahan
        $produksiChange = 0;
        if ($totalProduksiLastMonth > 0) {
            $produksiChange = round((($totalProduksi - $totalProduksiLastMonth) / $totalProduksiLastMonth) * 100, 1);
        }
        
        // Efisiensi Operator (%) - rata-rata pencapaian target
        $operatorEfficiencyData = DB::table('produksis')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'SELESAI')
            ->select('operator_id', 
                DB::raw('SUM(jumlah_produksi) as total_target'),
                DB::raw('SUM(jumlah_selesai) as total_selesai')
            )
            ->groupBy('operator_id')
            ->get();
        
        $avgEfficiency = 0;
        if ($operatorEfficiencyData->count() > 0) {
            $totalTarget = $operatorEfficiencyData->sum('total_target');
            $totalSelesai = $operatorEfficiencyData->sum('total_selesai');
            if ($totalTarget > 0) {
                $avgEfficiency = round(($totalSelesai / $totalTarget) * 100, 1);
            }
        }
        
        // Biaya Overhead (BOM材料)
        $totalBiayaOverhead = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->whereMonth('produksis.created_at', $bulan)
            ->whereYear('produksis.created_at', $tahun)
            ->where('produksis.status', 'SELESAI')
            ->sum(DB::raw('produksis.jumlah_selesai * bom.jumlah_kebutuhan * bahan_bakus.harga_satuan')) ?? 0;
        
        // Biaya bulan lalu untuk comparison
        $totalBiayaOverheadLastMonth = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->whereMonth('produksis.created_at', $lastMonth->format('m'))
            ->whereYear('produksis.created_at', $lastMonth->format('Y'))
            ->where('produksis.status', 'SELESAI')
            ->sum(DB::raw('produksis.jumlah_selesai * bom.jumlah_kebutuhan * bahan_bakus.harga_satuan')) ?? 0;
        
        $overheadChange = 0;
        if ($totalBiayaOverheadLastMonth > 0) {
            $overheadChange = round((($totalBiayaOverhead - $totalBiayaOverheadLastMonth) / $totalBiayaOverheadLastMonth) * 100, 1);
        }
        
        // Material Kritis - Gunakan nilai dari Pengaturan tabel (dinamis)
        $batasStokGlobalAnalisis = \App\Models\Pengaturan::where('nama_pengaturan', 'stok_minimum')->first()?->nilai ?? 5;
        
        $materialKritis = BahanBaku::whereRaw('stok <= COALESCE(stok_minimum, ?)', [$batasStokGlobalAnalisis])
            ->orderBy('stok', 'asc')
            ->get();
        $jumlahMaterialKritis = $materialKritis->count();
        
        // ==================== CHART DATA (7 hari terakhir) ====================
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->isoFormat('D MMM');
            
            $dayProduction = DB::table('produksis')
                ->whereDate('created_at', $date)
                ->where('status', 'SELESAI')
                ->sum('jumlah_selesai');
            
            $chartData[] = $dayProduction ?? 0;
        }
        
        // Max value untuk height chart
        $chartMax = !empty($chartData) ? max($chartData) : 100;
        if ($chartMax == 0) $chartMax = 100;
        
        // ==================== PROGRESS STATUS SPK ====================
        $totalSPK = DB::table('produksis')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->count();
        
        $spkSelesai = DB::table('produksis')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'SELESAI')
            ->count();
        
        $spkProses = DB::table('produksis')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'PROSES')
            ->count();
        
        $spkPending = DB::table('produksis')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'PENDING')
            ->count();
        
        $percentSelesai = $totalSPK > 0 ? round(($spkSelesai / $totalSPK) * 100) : 0;
        $percentProses = $totalSPK > 0 ? round(($spkProses / $totalSPK) * 100) : 0;
        $percentPending = $totalSPK > 0 ? round(($spkPending / $totalSPK) * 100) : 0;
        
        // ==================== RANKING OPERATOR ====================
        $operatorRanking = DB::table('produksis')
            ->join('users', 'produksis.operator_id', '=', 'users.id')
            ->whereMonth('produksis.created_at', $bulan)
            ->whereYear('produksis.created_at', $tahun)
            ->where('produksis.status', 'SELESAI')
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(produksis.jumlah_selesai) as total_selesai'),
                DB::raw('SUM(produksis.jumlah_produksi) as total_target')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_selesai')
            ->limit(10)
            ->get()
            ->map(function($op) {
                $op->efficiency = $op->total_target > 0 
                    ? round(($op->total_selesai / $op->total_target) * 100, 1) 
                    : 0;
                return $op;
            });
        
        // ==================== DAFTAR OPERATOR UNTUK FILTER ====================
        $operatorList = \App\Models\User::where('role', 'operator')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Filter operator jika dipilih
        $operatorId = $request->operator_id;
        
        if ($operatorId) {
            $operatorRanking = $operatorRanking->where('id', $operatorId);
        }
        
        return view('admin.laporan-analisis', compact(
            'totalProduksi', 'produksiChange',
            'avgEfficiency',
            'totalBiayaOverhead', 'overheadChange',
            'materialKritis', 'jumlahMaterialKritis',
            'chartLabels', 'chartData', 'chartMax',
            'spkSelesai', 'spkProses', 'spkPending', 'totalSPK',
            'percentSelesai', 'percentProses', 'percentPending',
            'operatorRanking',
            'operatorList', 'operatorId',
            'bulan', 'tahun'
        ));
    }
}
