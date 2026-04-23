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
    public function operatorLaporanCetak()
    {
        $user = auth()->user();

        $pendingCount = Produksi::where('operator_id', $user->id)->where('status', 'PENDING')->count();
        $prosesCount = Produksi::where('operator_id', $user->id)->where('status', 'PROSES')->count();
        $selesaiCount = Produksi::where('operator_id', $user->id)->where('status', 'SELESAI')->count();

        // Group by product - same products are combined
        $riwayat = Produksi::with('product')
            ->where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->selectRaw('produk_jadi_id, SUM(jumlah_produksi) as total_qty, COUNT(*) as jumlah_spk, MAX(updated_at) as latest_date')
            ->groupBy('produk_jadi_id')
            ->orderByDesc('latest_date')
            ->limit(50)
            ->get();

        // Bahan Masuk - Grouped by material
        $bahanMasuk = DB::table('pengadaans')
            ->join('bahan_bakus', 'pengadaans.bahan_baku_id', '=', 'bahan_bakus.id')
            ->whereBetween('pengadaans.created_at', [now()->startOfMonth(), now()])
            ->select(
                'bahan_bakus.nama_material',
                'bahan_bakus.satuan',
                DB::raw('SUM(pengadaans.jumlah) as total_masuk'),
                DB::raw('COUNT(pengadaans.id) as jumlah_transaksi')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.kode_material', 'bahan_bakus.nama_material', 'bahan_bakus.satuan')
            ->orderByDesc('total_masuk')
            ->limit(10)
            ->get();

        // Bahan Keluar - From production usage (BOM)
        $bahanKeluar = DB::table('produksis')
            ->join('bom', 'produksis.produk_jadi_id', '=', 'bom.id_produk')
            ->join('bahan_bakus', 'bom.id_bahan', '=', 'bahan_bakus.id')
            ->whereBetween('produksis.created_at', [now()->startOfMonth(), now()])
            ->where('produksis.status', 'SELESAI')
            ->select(
                'bahan_bakus.nama_material',
                'bahan_bakus.satuan',
                DB::raw('SUM(produksis.jumlah_selesai * bom.jumlah_kebutuhan) as total_keluar')
            )
            ->groupBy('bahan_bakus.id', 'bahan_bakus.kode_material', 'bahan_bakus.nama_material', 'bahan_bakus.satuan')
            ->orderByDesc('total_keluar')
            ->limit(10)
            ->get();

        return view('operator.laporan.cetak', compact(
            'pendingCount',
            'prosesCount',
            'selesaiCount',
            'riwayat',
            'bahanMasuk',
            'bahanKeluar'
        ));
    }

    /**
     * Halaman Utama Dashboard Berliano Leather
     */
    public function index()
    {
        // 1. Ambil Pengaturan Stok Minimum Global (FIX NAMA: stok_minimum_global)
        $globalMinStok = Pengaturan::where('nama_pengaturan', 'stok_minimum_global')->value('nilai') ?? 0;

        // 2. Data Ringkasan (KPI Cards)
        $totalBahan = BahanBaku::count();
        $totalStokBahan = BahanBaku::count();
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
        // FIX LOGIKA SINKRONISASI:
        // Menghitung bahan yang kritis berdasarkan:
        // - Stok <= stok_minimum (individual barang)
        // - Jika stok_minimum barang kosong, pakai globalMinStok dari pengaturan
        // ==============================================================
        // IKUTI PENGATURAN GUDANG: stok_minimum_global adalah acuan utama
        // (kalau belum diset, fallback 5)
        $minGlobal = (int) ($globalMinStok ?: 5);
        $jumlahKritis = BahanBaku::where('stok', '<=', $minGlobal)->count();

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
        
        $pengadaanBahan = (clone $pengadaanPendingQuery)->limit(100)->get();
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
            'produkKeluarBulanIni'
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

        $jumlahSelesai = Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->sum('jumlah_produksi') ?? 0;

        // Total produk selesai hari ini
        $selesaiHariIni = Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereDate('updated_at', now()->toDateString())
            ->sum('jumlah_produksi') ?? 0;

        // Total bulan ini (produk selesai)
        $totalBulanIni = Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->sum('jumlah_produksi') ?? 0;

        $perintahBaruCount = $tugasPendingCount;
        $sedangAktifCount = Produksi::where('operator_id', $user->id)
            ->where('status', 'PROSES')
            ->count();

        // Total tugas pengadaan operator
        $totalPengadaan = \App\Models\Pengadaan::where('operator_id', $user->id)->count();

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
        $totalStokBahan = BahanBaku::count();

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
    public function operatorLaporan(Request $request)
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

        // ✅ STATS COUNTER
        $pendingCount = Produksi::where('operator_id', $user->id)->where('status', 'PENDING')->count();
        $prosesCount = Produksi::where('operator_id', $user->id)->where('status', 'PROSES')->count();
        $selesaiCount = (clone $produksiQuery)->count();

        // ✅ RINGKASAN PRODUKSI
        $hariIniSelesaiQty = Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereDate('updated_at', today())
            ->sum('jumlah_produksi') ?? 0;

        $mingguIniSelesaiQty = Produksi::where('operator_id', $user->id)
            ->where('status', 'SELESAI')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('jumlah_produksi') ?? 0;

        // ✅ RINGKASAN BAHAN DITERIMA
        $bahanHariIni = DB::table('pengadaans')
            ->where('operator_id', $user->id)
            ->whereIn('status', ['SELESAI', 'DONE', 'DITERIMA'])
            ->whereDate('updated_at', today())
            ->sum('received') ?? 0;

        $bahanMingguIni = DB::table('pengadaans')
            ->where('operator_id', $user->id)
            ->whereIn('status', ['SELESAI', 'DONE', 'DITERIMA'])
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('received') ?? 0;

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
                    'bahan_bakus.id',
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

        // Gabungkan dan urutkan semua riwayat
        // Convert ke array biasa untuk menghindari conflict Eloquent Collection
        $riwayat = $riwayatProduksi->toBase()->merge($riwayatBahan)->sortByDesc('tanggal_terakhir')->values();
        
        // Manual pagination untuk collection campuran
        $page = request()->get('page', 1);
        $perPage = 15;
        $riwayat = new \Illuminate\Pagination\LengthAwarePaginator(
            $riwayat->forPage($page, $perPage)->all(),
            $riwayat->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('operator.laporan.index', compact(
            'pendingCount',
            'prosesCount',
            'selesaiCount',
            'hariIniSelesaiQty',
            'mingguIniSelesaiQty',
            'bahanHariIni',
            'bahanMingguIni',
            'riwayat',
            'fromDate',
            'toDate',
            'kategori'
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
            // Gunakan timezone Asia/Jakarta untuk akurasi waktu
            $now = Carbon::now()->timezone('Asia/Jakarta');
            $today = $now->copy()->startOfDay();
            
            // Inisialisasi label jam dari 1 hingga 24
            $labels = range(1, 24);
            foreach ($labels as &$label) {
                $label = str_pad($label, 2, '0', STR_PAD_LEFT);
            }
            
            // Inisialisasi array data dengan nilai 0 untuk 24 jam
            $bMasukData = array_fill(1, 24, 0);
            $bKeluarData = array_fill(1, 24, 0);
            $pMasukData = array_fill(1, 24, 0);
            $pKeluarData = array_fill(1, 24, 0);
            
            // ✅ FIX: Jangan gunakan CONVERT_TZ() MySQL (tidak aktif di kebanyakan server)
            // Ambil semua data hari ini lalu kelompokkan jam menggunakan PHP
            $bahanMasukRaw = DB::table('log_stok')
                ->whereDate('created_at', $today)
                ->where(function($q) {
                    $q->where('jenis_transaksi', 'LIKE', 'MASUK%')
                      ->orWhere('jenis_transaksi', 'INPUT MANUAL OPERATOR')
                      ->orWhere('jenis_transaksi', 'INPUT SCAN OCR');
                })
                ->get(['created_at', 'jumlah']);
            
            foreach ($bahanMasukRaw as $row) {
                $hour = Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->hour + 1;
                if ($hour >= 1 && $hour <= 24) {
                    $bMasukData[$hour] += floatval($row->jumlah);
                }
            }
            
            // Bahan Keluar
            $bahanKeluarRaw = DB::table('log_stok')
                ->whereDate('created_at', $today)
                ->where('jenis_transaksi', 'PENGELUARAN_PRODUKSI')
                ->get(['created_at', 'jumlah']);
            
            foreach ($bahanKeluarRaw as $row) {
                $hour = Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->hour + 1;
                if ($hour >= 1 && $hour <= 24) {
                    $bKeluarData[$hour] += floatval($row->jumlah);
                }
            }
            
            // Produk Masuk (Selesai Produksi)
            $produkMasukRaw = DB::table('produksis')
                ->whereDate('updated_at', $today)
                ->where('status', 'SELESAI')
                ->get(['updated_at', 'jumlah_selesai']);
            
            foreach ($produkMasukRaw as $row) {
                $hour = Carbon::parse($row->updated_at)->timezone('Asia/Jakarta')->hour + 1;
                if ($hour >= 1 && $hour <= 24) {
                    $pMasukData[$hour] += floatval($row->jumlah_selesai);
                }
            }
            
            // Produk Keluar (Penjualan)
            $produkKeluarRaw = DB::table('produk_keluars')
                ->whereDate('created_at', $today)
                ->get(['created_at', 'jumlah_keluar']);
            
            foreach ($produkKeluarRaw as $row) {
                $hour = Carbon::parse($row->created_at)->timezone('Asia/Jakarta')->hour + 1;
                if ($hour >= 1 && $hour <= 24) {
                    $pKeluarData[$hour] += floatval($row->jumlah_keluar);
                }
            }
            
            // Build final arrays for all 24 hours
            // array_values is used to re-index the 1-based array to a 0-based array
            $bMasuk = array_values($bMasukData);
            $bKeluar = array_values($bKeluarData);
            $pMasuk = array_values($pMasukData);
            $pKeluar = array_values($pKeluarData);

            // Labels are already set for 24 hours


        } elseif ($period === 'weekly') {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->isoFormat('ddd');
                $d = $date->format('Y-m-d');

                $bMasuk[] = DB::table('log_stok')->whereDate('created_at', $d)->where('jenis_transaksi', 'LIKE', 'MASUK%')->sum('jumlah');
                $bKeluar[] = DB::table('log_stok')->whereDate('created_at', $d)->where('jenis_transaksi', 'PENGELUARAN_PRODUKSI')->sum('jumlah');
                $pMasuk[] = DB::table('produksis')->whereDate('updated_at', $d)->where('status', 'SELESAI')->sum('jumlah_selesai');
                $pKeluar[] = DB::table('produk_keluars')->whereDate('created_at', $d)->sum('jumlah_keluar');
            }
        } else { // Monthly view for the current year (Jan-Dec)
            $currentYear = Carbon::now()->year;
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::createFromDate($currentYear, $month, 1);
                $labels[] = $date->isoFormat('MMMM');

                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();

                $bMasuk[] = DB::table('log_stok')->whereBetween('created_at', [$start, $end])->where('jenis_transaksi', 'LIKE', 'MASUK%')->sum('jumlah') ?? 0;
                $bKeluar[] = DB::table('log_stok')->whereBetween('created_at', [$start, $end])->where('jenis_transaksi', 'PENGELUARAN_PRODUKSI')->sum('jumlah') ?? 0;
                $pMasuk[] = DB::table('produksis')->whereBetween('updated_at', [$start, $end])->where('status', 'SELESAI')->sum('jumlah_selesai') ?? 0;
                $pKeluar[] = DB::table('produk_keluars')->whereBetween('created_at', [$start, $end])->sum('jumlah_keluar') ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'bahan' => ['masuk' => $bMasuk, 'keluar' => $bKeluar],
            'produk' => ['masuk' => $pMasuk, 'keluar' => $pKeluar]
        ];
    }
}