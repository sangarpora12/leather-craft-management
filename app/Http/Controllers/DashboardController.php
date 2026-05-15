<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BahanBaku;
use App\Models\ProdukJadi;
use App\Models\Produksi;
use App\Models\Pengaturan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Cetak laporan untuk operator (PDF/print view).
     */
    public function operatorLaporanCetak(Request $request)
    {
        $user = auth()->user();
    
        // 1. AMBIL PARAMETER FILTER
        $fromDate = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->query('to', now()->format('Y-m-d'));
        $kategori = $request->query('kategori', 'semua');
        
        $from = \Carbon\Carbon::parse($fromDate)->startOfDay();
        $to = \Carbon\Carbon::parse($toDate)->endOfDay();
    
        // 2. QUERY RINGKASAN PRODUKSI
        $riwayat = Produksi::with('product')
            ->where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereBetween('updated_at', [$from, $to])
            ->selectRaw('produk_jadi_id, SUM(jumlah_selesai) as total_qty, COUNT(*) as jumlah_spk, MAX(updated_at) as latest_date')
            ->groupBy('produk_jadi_id')
            ->orderByDesc('latest_date')
            ->get();
    
        // 3. QUERY BAHAN MASUK (Penerimaan)
        $bahanMasuk = DB::table('pengadaans')
            ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
            ->where('pengadaans.operator_id', $user->id)
            ->whereIn('pengadaans.status', ['SELESAI', 'DONE', 'DITERIMA'])
            ->whereBetween('pengadaans.updated_at', [$from, $to])
            ->select(
                'bahan_bakus.nama_material',
                'bahan_bakus.satuan',
                DB::raw('SUM(pengadaans.received) as total_masuk'),
                DB::raw('COUNT(pengadaans.id) as jumlah_transaksi')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.satuan')
            ->get();
    
        // 4. QUERY BAHAN KELUAR (Pemakaian Produksi) - INI YANG TADI KURANG
        $bahanKeluar = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->where('produksis.operator_id', $user->id)
            ->where('produksis.status', 'SELESAI')
            ->whereBetween('produksis.updated_at', [$from, $to])
            ->select(
                'bahan_bakus.nama_material',
                'bahan_bakus.satuan',
                DB::raw('SUM(produksis.jumlah_selesai * bom.jumlah_kebutuhan) as total_keluar')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.satuan')
            ->orderByDesc('total_keluar')
            ->get();
    
        // Statistik Counter
        $pendingCount = Produksi::where('operator_id', $user->id)->where('status', 'PENDING')->count();
        $prosesCount = Produksi::where('operator_id', $user->id)->where('status', 'PROSES')->count();
        $selesaiCount = Produksi::where('operator_id', $user->id)->where('status', 'SELESAI')->whereBetween('updated_at', [$from, $to])->count();
    
        // ✅ PASTIIN SEMUA VARIABEL MASUK KE COMPACT
        return view('operator.laporan.cetak', compact(
            'riwayat', 
            'bahanMasuk', 
            'bahanKeluar', // Tambahkan ini
            'fromDate', 
            'toDate', 
            'kategori', 
            'user', 
            'pendingCount', 
            'prosesCount', 
            'selesaiCount'
        ));
    }

    /**
     * Halaman Utama Dashboard Berliano Leather
     */
    public function index()
    {
        // 1. Ambil Pengaturan Stok Minimum Global dari nama_pengaturan 'stok_minimum_bahan'
        $globalMinStok = Pengaturan::where('nama_pengaturan', 'stok_minimum_bahan')->value('nilai') ?? 0;

        // 2. Data Ringkasan (KPI Cards)
        $totalBahan = BahanBaku::distinct('kode_material')->count('kode_material');
        $totalStokBahan = BahanBaku::distinct('kode_material')->count('kode_material');
        // Produk jadi yang valid: sudah ada BOM & sudah pernah produksi selesai
        $produkSelesaiIds = Produksi::query()
            ->where('status', 'SELESAI')
            ->whereNotNull('produk_jadi_id')
            ->pluck('produk_jadi_id')
            ->unique();

        $produkValidQuery = ProdukJadi::query()
            ->whereIn('id', $produkSelesaiIds)
            ->has('boms');

        $totalProduk = (clone $produkValidQuery)->count();
        $stokProduk = (clone $produkValidQuery)->sum('stok') ?? 0;

        // Hitung Live Nilai Aset (Stok * Harga)
        $totalAsetBahan = BahanBaku::all()->sum(function($bahan) {
            return ($bahan->stok ?? 0) * ($bahan->harga_satuan ?? 0);
        });

        // ==============================================================
        // FINAL FIX LOGIC:
        // Menghitung bahan yang kritis berdasarkan stok per-varian (per-item),
        // bukan total gabungan. Setiap warna adalah item inventaris terpisah.
        // ==============================================================
        $minGlobal = (int) $globalMinStok;

        $criticalQuery = \App\Models\BahanBaku::where('is_inventory', 1);

        if ($minGlobal === 0) {
            // Jika setting 0, hanya item dengan stok tepat 0 yang kritis.
            $criticalQuery->where('stok', '=', 0);
        } else {
            // Jika setting > 0, item dengan stok <= setting adalah kritis.
            $criticalQuery->where('stok', '<=', $minGlobal);
        }
        $jumlahKritis = $criticalQuery->count();

        // Hitung Produk Jadi KRITIS (Stok <= Stok Minimum Global)
        $stokMinProduk = (int) (Pengaturan::where('nama_pengaturan', 'stok_minimum_produk')->value('nilai') ?? 2);
        $jumlahProdukKritis = (clone $produkValidQuery)->where('stok', '<=', $stokMinProduk)->count();

        // 3. Daftar Produksi Aktif (Status PENDING/PROSES saja) - Grouped by product
        $produksiAktif = Produksi::with('product')
            ->whereIn('status', ['PENDING', 'PROSES'])
            ->selectRaw('produk_jadi_id, SUM(jumlah_produksi) as total_qty, COUNT(*) as jumlah_spk, 
                         GROUP_CONCAT(DISTINCT status) as statuses, MAX(created_at) as latest_date')
            ->groupBy('produk_jadi_id')
            ->orderByDesc('latest_date')
            ->limit(5)
            ->get();

        // 4. Pengadaan Bahan yang Pending (belum selesai) - GROUP BY BAHAN DAN SUM JUMLAH
        $pengadaanPendingQuery = DB::table('pengadaans')
            ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
            ->leftJoin('users', 'pengadaans.operator_id', '=', 'users.id')
            ->where('pengadaans.status', '!=', 'Selesai/Dibeli')
            ->where('pengadaans.status', '!=', 'DITERIMA')
            ->select(
                'bahan_bakus.id',
                'bahan_bakus.kode_material',
                'bahan_bakus.nama_material', 
                'bahan_bakus.satuan', 
                DB::raw('SUM(pengadaans.jumlah) as total_jumlah'),
                DB::raw('MAX(pengadaans.created_at) as terakhir_dibuat'),
                DB::raw('GROUP_CONCAT(DISTINCT users.name SEPARATOR ", ") as operator_names')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.kode_material', 'bahan_bakus.nama_material', 'bahan_bakus.satuan')
            ->orderByDesc(DB::raw('MAX(pengadaans.created_at)'));
        
        $pengadaanBahan = (clone $pengadaanPendingQuery)->limit(00)->get();
        $totalPengadaanPending = $pengadaanPendingQuery->count();

        // ✅ FIX GRAFIK KOSONG: Ambil data statistik untuk Chart.js
        $chartData = $this->buildChartData('daily');
        $chartLabels = $chartData['labels'];
        $bahanMasuk = $chartData['bahan']['masuk'];
        $bahanKeluar = $chartData['bahan']['keluar'];
        $produkMasuk = $chartData['produk']['masuk'];
        $produkKeluar = $chartData['produk']['keluar'];
        
        // Hitung total produk keluar bulan ini
        $produkKeluarBulanIni = DB::table('transaksi_stok')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('jenis_transaksi', 'PRODUK_KELUAR')
            ->sum('jumlah') ?? 0;

        // Ambil semua pengadaan PENDING secara individual untuk ditampilkan di dashboard
        // Hanya tampilkan pengadaan yang bahannya masih ada (tidak dihapus)
        $pengadaanPendingIndividual = \App\Models\Pengadaan::with('bahan', 'operator')
            ->where('status', 'PENDING')
            ->whereHas('bahan') // hanya pengadaan yang bahannya masih ada
            ->orderBy('tanggal_dibutuhkan', 'asc')
            ->get();

        return view('admin.dashboard', compact(
            'totalBahan', 
            'totalProduk', 
            'totalAsetBahan', 
            'totalStokBahan',
            'stokProduk',
            'jumlahKritis',
            'jumlahProdukKritis',
            'produksiAktif',
            'pengadaanBahan',
            'totalPengadaanPending',
            'chartLabels',
            'bahanMasuk',
            'bahanKeluar',
            'produkMasuk',
            'produkKeluar',
            'produkKeluarBulanIni',
            'pengadaanPendingIndividual'
        ));
    }

    /**
     * Dashboard khusus operator
     */
    public function operatorDashboard()
    {
        $user = auth()->user();

        $unreadCount = $user?->unreadNotifications()?->count() ?? 0;

        // Ringkasan tugas operator
        $tugasAktifCount = Produksi::where('operator_id', $user->id)
            ->whereIn('status', ['PENDING', 'PROSES'])
            ->count();

        $tugasPendingCount = Produksi::where('operator_id', $user->id)
            ->where('status', 'PENDING')
            ->count();

        $jumlahSelesai = (int) Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->sum(DB::raw('CEIL(jumlah_produksi)')) ?? 0;

        // Total produk selesai hari ini
        $selesaiHariIni = (int) Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereDate('updated_at', now()->toDateString())
            ->sum(DB::raw('CEIL(jumlah_produksi)')) ?? 0;

        // Total bulan ini (produk selesai)
        $totalBulanIni = (int) Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->sum(DB::raw('CEIL(jumlah_produksi)')) ?? 0;

        $perintahBaruCount = $tugasPendingCount;
        $sedangAktifCount = Produksi::where('operator_id', $user->id)
            ->where('status', 'PROSES')
            ->count();

        // Total tugas pengadaan operator (hitung yang bahannya masih ada)
        $totalPengadaan = \App\Models\Pengadaan::where('operator_id', $user->id)
            ->where('status', 'PENDING')
            ->whereHas('bahan')
            ->count();

        // Bahan masuk bulan ini (dari log_stok - termasuk scan OCR)
        $bahanMasukBulanIni = DB::table('log_stok')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('jenis_transaksi', 'LIKE', 'MASUK%')
            ->sum('jumlah') ?? 0;

        // Bahan keluar bulan ini (dari log_stok)
        $bahanKeluarBulanIni = DB::table('log_stok')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('jenis_transaksi', 'PENGELUARAN_PRODUKSI')
            ->sum('jumlah') ?? 0;

        // Total harga beli (bahan masuk dari scan OCR)
        $totalHargaBeli = DB::table('log_stok')
            ->join('bahan_bakus', 'log_stok.bahan_baku_id', '=', 'bahan_bakus.id')
            ->whereYear('log_stok.created_at', now()->year)
            ->whereMonth('log_stok.created_at', now()->month)
            ->where('log_stok.jenis_transaksi', 'LIKE', 'MASUK%')
            ->selectRaw('SUM(log_stok.jumlah * bahan_bakus.harga_satuan) as total')
            ->value('total') ?? 0;
            
        // ✅ Total JENIS BAHAN (jumlah kode produk bahan yang berbeda)
        $totalStokBahan = BahanBaku::distinct('kode_material')->count('kode_material');

        return view('operator.dashboard', compact(
            'unreadCount',
            'tugasAktifCount',
            'tugasPendingCount',
            'jumlahSelesai',
            'selesaiHariIni',
            'totalBulanIni',
            'perintahBaruCount',
            'sedangAktifCount',
            'totalPengadaan',
            'bahanMasukBulanIni',
            'bahanKeluarBulanIni',
            'totalHargaBeli',
            'totalStokBahan'
        ));
    }

    // ==============================================================
    // OPERATOR NOTIFICATIONS
    // ==============================================================

    /**
     * Halaman daftar notifikasi operator (jika suatu saat dibutuhkan halaman khusus).
     * Saat ini operator-header sudah menampilkan dropdown notifikasi.
     */
    public function operatorNotifications()
    {
        $user = auth()->user();
        $paginatedNotifications = $user->notifications()->latest()->paginate(20);

        return view('operator.notifications.index', [
            'paginatedNotifications' => $paginatedNotifications,
        ]);
    }

    /**
     * AJAX/POST: tandai 1 notifikasi operator sebagai dibaca.
     */
    public function markOperatorNotificationRead(Request $request)
    {
        $id = $request->input('id');
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * POST: tandai semua notifikasi operator sebagai dibaca.
     */
    public function markAllOperatorNotificationsRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return back()->with('success', 'Semua notifikasi sudah dibaca.');
    }

    /**
     * GET: klik notifikasi operator -> tandai dibaca lalu redirect ke URL yang disimpan.
     * Ini membuat badge merah berkurang saat notif diklik.
     */
    public function markOperatorNotificationReadAndRedirect(string $id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();

            return redirect(
                $notification->data['url'] ?? route('operator.tugas.index')
            );
        }

        return redirect()->route('operator.tugas.index');
    }

    /**
     * Search global untuk operator (bahan + produk + tugas)
     */
    public function operatorSearch(Request $request)
    {
        $q = $request->query('q', '');
        if (empty($q)) {
            return redirect()->route('operator.dashboard');
        }

        $keyword = '%' . $q . '%';

        // Cari di bahan baku
        $bahanResults = \App\Models\BahanBaku::where('nama_material', 'LIKE', $keyword)
            ->orWhere('kode_material', 'LIKE', $keyword)
            ->limit(5)
            ->get();

        // Cari di produk jadi
        $produkResults = \App\Models\ProdukJadi::where('nama_produk', 'LIKE', $keyword)
            ->orWhere('kode_produk', 'LIKE', $keyword)
            ->limit(5)
            ->get();

        // Cari di produksi (SPK)
        $spkResults = Produksi::where('operator_id', auth()->id())
            ->where(function ($query) use ($keyword) {
                $query->where('kode_spk', 'LIKE', $keyword)
                      ->orWhereHas('product', function ($q) use ($keyword) {
                          $q->where('nama_produk', 'LIKE', $keyword);
                      });
            })
            ->limit(5)
            ->get();

        // Kalau hanya 1 hasil bahan → langsung ke detail bahan
        if ($bahanResults->count() === 1 && $produkResults->isEmpty() && $spkResults->isEmpty()) {
            return redirect()->route('operator.bahan.show', $bahanResults->first()->id);
        }

        // Kalau hanya 1 hasil SPK → langsung ke detail tugas
        if ($spkResults->count() === 1 && $bahanResults->isEmpty() && $produkResults->isEmpty()) {
            return redirect()->route('operator.tugas.detail', $spkResults->first()->id);
        }

        // Kalau hasil bahan dominan → ke halaman bahan dengan filter search
        if ($bahanResults->count() > 0) {
            return redirect()->route('operator.bahan.index', ['search' => $q]);
        }

        // Kalau hasil SPK dominan → ke halaman tugas dengan filter search
        if ($spkResults->count() > 0) {
            return redirect()->route('operator.tugas.index', ['search' => $q]);
        }

        // Default: ke halaman bahan (karena operator paling sering cari bahan)
        return redirect()->route('operator.bahan.index', ['search' => $q]);
    }

    /**
     * Halaman scan nota untuk operator.
     */
    public function scanIndex()
    {
        return view('operator.scan-nota');
    }

    /**
     * Laporan sederhana untuk operator (ringkasan + riwayat produksi).
     */
/**
     * Laporan operasional untuk kepala gudang (operator).
     * Fitur: Filter tanggal, Kategori, Foto Asli, dan Info Warna.
     */
    public function operatorLaporan(Request $request)
    {
        $user = auth()->user();

        $fromDate = $request->query('from', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->query('to', now()->format('Y-m-d'));
        $kategori = $request->query('kategori', 'semua');
        
        $from = \Carbon\Carbon::parse($fromDate)->startOfDay();
        $to = \Carbon\Carbon::parse($toDate)->endOfDay();

        $stokKritis = \App\Models\BahanBaku::whereRaw('stok < stok_minimum')->get();

        // QUERY PRODUKSI (PRODUK)
        $riwayatProduksi = DB::table('produksis')
        ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->leftJoin('users', 'produksis.operator_id', '=', 'users.id')
            ->where('produksis.operator_id', $user->id)
            ->where('produksis.status', 'SELESAI')
            ->whereBetween('produksis.updated_at', [$from, $to])
            ->select(
                'produk_jadis.nama_produk as nama',
                DB::raw("'produk' as jenis"),
                DB::raw("CEIL(produksis.jumlah_selesai) as total_qty"), 
                'produksis.updated_at as tanggal_terakhir',
                'users.name as nama_operator',
                'produk_jadis.foto as foto_item', 
                DB::raw('NULL as warna'),
                DB::raw("'pcs' as satuan_item")
            );

        // QUERY PENGADAAN (BAHAN)
        $riwayatBahan = DB::table('pengadaans')
            ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
            ->leftJoin('users', 'pengadaans.operator_id', '=', 'users.id')
            ->where('pengadaans.operator_id', $user->id)
            ->whereIn('pengadaans.status', ['SELESAI', 'DONE', 'DITERIMA'])
            ->whereBetween('pengadaans.updated_at', [$from, $to])
            ->select(
                'bahan_bakus.nama_material as nama',
                DB::raw("'bahan' as jenis"),
                DB::raw("CEIL(pengadaans.received) as total_qty"),
                'pengadaans.updated_at as tanggal_terakhir',
                'users.name as nama_operator',
                'bahan_bakus.foto as foto_item',
                'bahan_bakus.warna as warna',
                'bahan_bakus.satuan as satuan_item'
            );

        if ($kategori == 'produk') {
            $query = $riwayatProduksi;
        } elseif ($kategori == 'bahan') {
            $query = $riwayatBahan;
        } else {
            $query = $riwayatProduksi->union($riwayatBahan);
        }

        $riwayat = DB::table(DB::raw("({$query->toSql()}) as combined"))
            ->mergeBindings($query)
            ->orderByDesc('tanggal_terakhir')
            ->paginate(15)
            ->withQueryString();

        return view('operator.laporan.index', compact(
            'stokKritis', 'riwayat', 'fromDate', 'toDate', 'kategori'
        ));
    }
    /**
     * Export Laporan Operator ke PDF
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();

        // ✅ AMBIL PARAMETER FILTER
        $fromDate = $request->query('from', '');
        $toDate = $request->query('to', '');
        $kategori = $request->query('kategori', 'semua');

        // Default jika tidak ada filter tanggal
        if (empty($fromDate)) $fromDate = now()->startOfMonth()->format('Y-m-d');
        if (empty($toDate)) $toDate = now()->format('Y-m-d');

        $from = Carbon::parse($fromDate)->startOfDay();
        $to = Carbon::parse($toDate)->endOfDay();

        // ✅ QUERY UTAMA DENGAN FILTER TANGGAL
        $produksiQuery = Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereBetween('updated_at', [$from, $to]);

        $pengadaanQuery = DB::table('pengadaans')
            ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
            ->where('pengadaans.operator_id', $user->id)
            ->whereIn('pengadaans.status', ['SELESAI', 'DONE', 'DITERIMA'])
            ->whereBetween('pengadaans.updated_at', [$from, $to]);

        // ✅ DATA RIWAYAT (GROUPING)
        $riwayatProduksi = collect();
        $riwayatBahan = collect();

        if ($kategori == 'semua' || $kategori == 'produk') {
            $riwayatProduksi = (clone $produksiQuery)
                ->with('product')
                ->selectRaw('produk_jadi_id, SUM(jumlah_selesai) as total_qty, COUNT(*) as jumlah_transaksi, MAX(updated_at) as tanggal_terakhir')
                ->groupBy('produk_jadi_id')
                ->orderByDesc('tanggal_terakhir')
                ->get()
                ->map(function($item) {
                    $item->jenis = 'produk';
                    return $item;
                });
        }

        if ($kategori == 'semua' || $kategori == 'bahan') {
            $riwayatBahan = (clone $pengadaanQuery)
                ->select(
                    'bahan_bakus.nama_material as nama',
                    'bahan_bakus.satuan',
                    DB::raw('SUM(pengadaans.received) as total_qty'),
                    DB::raw('COUNT(pengadaans.id) as jumlah_transaksi'),
                    DB::raw('MAX(pengadaans.updated_at) as tanggal_terakhir')
                )
                ->groupBy('bahan_bakus.id', 'bahan_bakus.nama_material', 'bahan_bakus.satuan')
                ->orderByDesc('tanggal_terakhir')
                ->get()
                ->map(function($item) {
                    $item->jenis = 'bahan';
                    return $item;
                });
        }

        // Gabungkan semua data
        $riwayat = $riwayatProduksi->toBase()->merge($riwayatBahan)->sortByDesc('tanggal_terakhir')->values();

        // Hitung total
        $totalProduk = $riwayatProduksi->sum('total_qty');
        $totalBahan = $riwayatBahan->sum('total_qty');
        $pengaturan = Pengaturan::first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('operator.laporan.pdf', compact(
            'riwayat',
            'fromDate',
            'toDate',
            'kategori',
            'totalProduk',
            'totalBahan',
            'pengaturan',
            'user'
        ))->setPaper('a4', 'portrait');

        $filename = 'laporan_operator_'.$fromDate.'_'.$toDate.'.pdf';
        return $pdf->download($filename);
    }

    /**
     * API untuk data Chart JS
     */
    public function chartData(Request $request)
    {
        $period = $request->query('period', 'daily');
        $chart = $this->buildChartData($period);

        return response()->json([
            'labels' => $chart['labels'],
            'bahan' => $chart['bahan'],
            'produk' => $chart['produk'],
        ]);
    }

    /**
     * Helper untuk memproses data statistik grafik
     */
    private function buildChartData(string $period = 'daily'): array
    {
        Carbon::setLocale('id');
        $labels = []; 
        $bMasuk = []; $bKeluar = [];
        $pMasuk = []; $pKeluar = [];

        if ($period === 'daily') {
            $labels = [];
             for ($i = 0; $i < 24; $i++) {
                 $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
             }

            $bMasukData = array_fill(0, 24, 0);
            $bKeluarData = array_fill(0, 24, 0);
            $pMasukData = array_fill(0, 24, 0);
            $pKeluarData = array_fill(0, 24, 0);

            $now = now()->timezone('Asia/Jakarta');
 
             // Bahan Masuk: Menggunakan logika dari LaporanController
             $bahanMasukRaw = DB::table('log_stok')
                 ->where('created_at', '>=', $now->copy()->subHours(24))
                 ->where(function($q) {
                     $q->where('jenis_transaksi', 'like', '%MASUK%')
                       ->orWhere('jenis_transaksi', 'like', '%TAMBAH%')
                       ->orWhere('jenis_transaksi', 'like', '%PENGADAAN%')
                       ->orWhere('jenis_transaksi', 'like', '%RESTOCK%')
                       ->orWhere('jenis_transaksi', 'like', '%INPUT%')
                       ->orWhere('jenis_transaksi', 'like', '%UPDATE OPERATOR%');
                 })
                 ->get(['created_at', 'jumlah']);
 
             foreach ($bahanMasukRaw as $row) {
                 $hour = Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->hour;
                 $bMasukData[$hour] += floatval($row->jumlah);
             }
 
             // Bahan Keluar: Disederhanakan, semua yang bukan 'masuk' dianggap 'keluar'
             $bahanKeluarRaw = DB::table('log_stok')
                 ->where('created_at', '>=', $now->copy()->subHours(24))
                 ->where(function($q) {
                     $q->where('jenis_transaksi', 'like', '%KELUAR%')
                       ->orWhere('jenis_transaksi', 'like', '%KURANG%')
                       ->orWhere('jenis_transaksi', 'like', '%PRODUKSI%')
                       ->orWhere('jenis_transaksi', 'like', '%DIPAKAI%');
                 })
                 ->get(['created_at', 'jumlah']);
 
             foreach ($bahanKeluarRaw as $row) {
                 $hour = Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->hour;
                 $bKeluarData[$hour] += floatval($row->jumlah);
             }
 
             // Produk Masuk (Selesai Produksi)
             $produkMasukRaw = DB::table('produksis')
                 ->where('updated_at', '>=', $now->copy()->subHours(24))
                 ->where('status', 'SELESAI')
                 ->get(['updated_at as created_at', 'jumlah_selesai as jumlah']);
 
             foreach ($produkMasukRaw as $row) {
                 $hour = Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->hour; // 0-23
                 $pMasukData[$hour] += floatval($row->jumlah);
             }
 
             // Produk Keluar (Penjualan)
             $produkKeluarRaw = DB::table('produk_keluars')
                 ->where('created_at', '>=', $now->copy()->subHours(24))
                 ->get(['created_at', 'jumlah_keluar as jumlah']);
 
             foreach ($produkKeluarRaw as $row) {
                 $hour = Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->hour; // 0-23
                 $pKeluarData[$hour] += floatval($row->jumlah);
             }
 
             return [
                 'labels' => $labels,
                 'bahan' => ['masuk' => array_values($bMasukData), 'keluar' => array_values($bKeluarData)],
                 'produk' => ['masuk' => array_values($pMasukData), 'keluar' => array_values($pKeluarData)]
             ];
        } elseif ($period === 'weekly') {
            $pMasukData = [];
            $pKeluarData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->isoFormat('dddd, D MMM');
                $d = $date->format('Y-m-d');

                $bMasuk[] = DB::table('log_stok')->whereDate('created_at', $d)
                    ->where(function($q) {
                        $q->where('jenis_transaksi', 'like', '%MASUK%')
                          ->orWhere('jenis_transaksi', 'like', '%TAMBAH%')
                          ->orWhere('jenis_transaksi', 'like', '%PENGADAAN%')
                          ->orWhere('jenis_transaksi', 'like', '%RESTOCK%')
                          ->orWhere('jenis_transaksi', 'like', '%INPUT%');
                    })->sum('jumlah') ?? 0;
                
                $bKeluar[] = DB::table('produksis')
                     ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
                     ->whereDate('produksis.updated_at', $d)
                     ->where('produksis.status', 'SELESAI')
                     ->sum(DB::raw('produksis.jumlah_selesai * bom.jumlah_kebutuhan')) ?? 0;

                $pMasukData[] = DB::table('produksis')->whereDate('updated_at', $d)->where('status', 'SELESAI')->sum('jumlah_selesai') ?? 0;
                $pKeluarData[] = DB::table('produk_keluars')->whereDate('created_at', $d)->sum('jumlah_keluar') ?? 0;
            }
            $pMasuk = $pMasukData;
            $pKeluar = $pKeluarData;
        } else { // monthly
            $pMasukData = [];
            $pKeluarData = [];
            $currentYear = Carbon::now()->year;
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::createFromDate($currentYear, $month, 1);
                $labels[] = $date->isoFormat('MMMM');

                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();

                $bMasuk[] = DB::table('log_stok')->whereBetween('created_at', [$start, $end])
                    ->where(function($q) {
                        $q->where('jenis_transaksi', 'like', '%MASUK%')
                          ->orWhere('jenis_transaksi', 'like', '%TAMBAH%')
                          ->orWhere('jenis_transaksi', 'like', '%PENGADAAN%')
                          ->orWhere('jenis_transaksi', 'like', '%RESTOCK%')
                          ->orWhere('jenis_transaksi', 'like', '%INPUT%');
                    })->sum('jumlah') ?? 0;

                $bKeluar[] = DB::table('produksis')
                     ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
                     ->whereBetween('produksis.updated_at', [$start, $end])
                     ->where('produksis.status', 'SELESAI')
                     ->sum(DB::raw('produksis.jumlah_selesai * bom.jumlah_kebutuhan')) ?? 0;

                $pMasukData[] = DB::table('produksis')->whereBetween('updated_at', [$start, $end])->where('status', 'SELESAI')->sum('jumlah_selesai') ?? 0;
                $pKeluarData[] = DB::table('produk_keluars')->whereBetween('created_at', [$start, $end])->sum('jumlah_keluar') ?? 0;
            }
            $pMasuk = $pMasukData;
            $pKeluar = $pKeluarData;
        }

        return [
            'labels' => $labels,
            'bahan' => ['masuk' => $bMasuk, 'keluar' => $bKeluar],
            'produk' => ['masuk' => $pMasuk, 'keluar' => $pKeluar]
        ];
    }
}