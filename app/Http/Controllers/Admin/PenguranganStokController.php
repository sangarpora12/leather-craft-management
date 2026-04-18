<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProdukJadi;
use App\Models\RiwayatPenguranganStok;

class PenguranganStokController extends Controller
{
    /**
     * Menampilkan halaman riwayat pengurangan stok.
     */
    public function index(Request $request)
    {
        $query = ProdukJadi::query();

        // Filter Pencarian Nama
        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Status Stok
        if ($request->filled('status_stok')) {
            if ($request->status_stok == 'habis') {
                $query->where('stok', '<=', 0);
            } elseif ($request->status_stok == 'kritis') {
                $query->where('stok', '>', 0)->where('stok', '<', 5);
            } elseif ($request->status_stok == 'aman') {
                $query->where('stok', '>=', 5);
            }
        }

        // Pengurutan
        $sortBy = $request->get('sort_by', 'nama_asc'); // Default sort
        switch ($sortBy) {
            case 'nama_desc':
                $query->orderBy('nama_produk', 'desc');
                break;
            case 'stok_asc':
                $query->orderBy('stok', 'asc');
                break;
            case 'stok_desc':
                $query->orderBy('stok', 'desc');
                break;
            default:
                $query->orderBy('nama_produk', 'asc');
                break;
        }

        $produkJadis = $query->paginate(12)->withQueryString();
        $kategoris = ProdukJadi::select('kategori')->distinct()->whereNotNull('kategori')->orderBy('kategori')->get();

        return view('admin.pakai-produk.index', compact('produkJadis', 'kategoris'));
    }

    /**
     * Menampilkan halaman form untuk mengurangi stok produk.
     */
    public function create()
    {
        // Ambil semua produk jadi untuk ditampilkan di dropdown
        $produkJadis = ProdukJadi::orderBy('nama_produk')->get();
        return view('admin.pakai-produk.create', compact('produkJadis'));
    }

    /**
     * Memproses pengurangan stok dari form.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'produk_jadi_id' => 'required|exists:produk_jadis,id',
            'kuantitas' => 'required|integer|min:1',
            'alasan' => 'required|string|max:255',
        ]);

        // 2. Cari produk
        $produk = ProdukJadi::findOrFail($request->produk_jadi_id);

        // 3. Panggil metode kurangiStok
        $berhasil = $produk->kurangiStok($request->kuantitas, $request->alasan);

        // 4. Beri respon
        if ($berhasil) {
            // DIUBAH: Langsung arahkan ke halaman riwayat untuk melihat hasilnya.
            return redirect()->route('admin.pakai-produk.index')->with('success', 'Stok berhasil dikurangi. Riwayat baru telah dicatat.');
        } else {
            // Cek apakah masalahnya adalah stok tidak cukup
            if ($produk->stok < $request->kuantitas) {
                return back()->with('error', 'Gagal mengurangi stok. Kuantitas melebihi stok yang tersedia (' . $produk->stok . ').');
            }
            // Jika bukan karena stok, berarti ada masalah lain saat transaksi
            return back()->with('error', 'Terjadi kesalahan internal saat mencoba mengurangi stok. Silakan coba lagi.');
        }
    }
}