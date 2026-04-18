<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\ProdukJadi;
use App\Models\ProdukKeluar;
use App\Models\BahanBaku;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        Carbon::setLocale('id');

        // 1. DATA TERGABUNG (GROUPED): Mengelompokkan berdasarkan Produk & Status
        $logProduksi = Produksi::with(['produk_jadi'])
            ->select('produk_jadi_id', 'status', 
                DB::raw('SUM(jumlah_produksi) as total_qty'), 
                DB::raw('MAX(created_at) as last_update'))
            ->groupBy('produk_jadi_id', 'status')
            ->orderBy('last_update', 'desc')
            ->get();

        // 2. Ringkasan Kartu
        $totalOutput = Produksi::where('status', 'SELESAI')->whereMonth('created_at', now()->month)->sum('jumlah_produksi');
        $produksiPending = Produksi::whereIn('status', ['PENDING', 'PROSES'])->sum('jumlah_produksi');
        $barangKeluar = ProdukKeluar::whereMonth('created_at', now()->month)->sum('jumlah_keluar');
        $stokKritisCount = BahanBaku::whereColumn('stok', '<', 'stok_minimum')->count();
        // 3. Grafik (Hanya yang Selesai)
        $chartLabels = []; $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->isoFormat('dddd');
            $chartData[] = Produksi::whereDate('created_at', $date->format('Y-m-d'))->where('status', 'SELESAI')->sum('jumlah_produksi') ?? 0;
        }

        $totalIncome = Produksi::where('produksis.status', 'SELESAI')
            ->whereMonth('produksis.created_at', now()->month)
            ->join('produk_jadis', 'produksis.produk_jadi_id', '=', 'produk_jadis.id')
            ->sum(DB::raw('produksis.jumlah_produksi * produk_jadis.harga_jual')) ?? 0;

        $totalExpense = 120000000;

        return view('admin.laporan.index', compact(
            'chartLabels', 'chartData', 'totalIncome', 'totalExpense',
            'totalOutput', 'barangKeluar', 'stokKritisCount', 'logProduksi', 'produksiPending'
        ));
    }

    public function fastComplete()
    {
        Produksi::where('status', '!=', 'SELESAI')->update(['status' => 'SELESAI']);
        return redirect()->back()->with('success', 'Semua antrean produksi telah digabungkan ke stok jadi!');
    }
}