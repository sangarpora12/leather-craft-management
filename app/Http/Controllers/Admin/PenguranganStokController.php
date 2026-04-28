<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ProdukJadi;
use App\Models\ProdukKeluar;

class PenguranganStokController extends Controller
{
    /**
     * Menampilkan halaman riwayat pengurangan stok.
     */
    public function index(Request $request)
    {
        $query = ProdukJadi::query();

        // ✅ FILTER HANYA PRODUK JADI YANG SUDAH SELESAI PRODUKSI DAN VALID
        $produkSelesaiIds = \App\Models\Produksi::where('status', 'SELESAI')
            ->whereNotNull('produk_jadi_id')
            ->pluck('produk_jadi_id')
            ->unique();
            
        $query->whereIn('id', $produkSelesaiIds)
              ->has('boms'); // Hanya produk yang sudah punya BOM

        // Filter Pencarian Nama
        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->whereJsonContains('kategori', $request->kategori);
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
        
        // ✅ AMBIL KATEGORI DARI TABEL PENGATURAN, JIKA KOSONG FALLBACK DARI PRODUK
        $kategorisRaw = \App\Models\Pengaturan::where('nama_pengaturan', 'kategori_produk')->first();
        $kategoris = collect();
        
        if ($kategorisRaw && $kategorisRaw->nilai) {
            $kategoriArray = is_string($kategorisRaw->nilai) ? json_decode($kategorisRaw->nilai, true) : $kategorisRaw->nilai;
            if (is_array($kategoriArray) && count($kategoriArray) > 0) {
                $kategoris = collect($kategoriArray)->sort()->values();
            }
        }
        
        // ✅ FALLBACK: JIKA DI PENGATURAN KOSONG, AMBIL DARI PRODUK
        if ($kategoris->isEmpty()) {
            $kategorisRaw = ProdukJadi::select('kategori')->distinct()->whereNotNull('kategori')->get();
            
            foreach ($kategorisRaw as $kat) {
                $val = is_string($kat->kategori) ? json_decode($kat->kategori, true) : $kat->kategori;
                if(is_array($val)) {
                    foreach($val as $v) {
                        if(!empty($v) && !$kategoris->contains($v)) $kategoris->push($v);
                    }
                } else {
                    if(!empty($val) && !$kategoris->contains($val)) $kategoris->push($val);
                }
            }
            
            $kategoris = $kategoris->sort()->values();
        }

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
        DB::beginTransaction();
        
        try {
            // 1. Validasi input
            $request->validate([
                'produk_jadi_id' => 'required|exists:produk_jadis,id',
                'jumlah' => 'required|integer|min:1',
                'alasan' => 'required|string|max:255',
            ]);

            // 2. Cari produk dengan LOCK untuk mencegah Race Condition
            $produk = ProdukJadi::lockForUpdate()->findOrFail($request->produk_jadi_id);

            // 3. Validasi Stok
            if ($produk->stok < $request->jumlah) {
                return back()->with('error', 'Gagal mengurangi stok: Stok tersedia hanya ' . $produk->stok . ' unit.');
            }

            // 4. Kurangi Stok Secara Manual
            $produk->stok = $produk->stok - $request->jumlah;
            $produk->save();

            // 5. Catat Riwayat di ProdukKeluar
            \App\Models\ProdukKeluar::create([
                'produk_jadi_id' => $produk->id,
                'jumlah_keluar' => $request->jumlah,
                'alasan' => $request->alasan,
                'tanggal_keluar' => now(),
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            // 6. Beri respon sukses
            return redirect()->route('admin.pakai-produk.index')->with('success', 'Stok ' . $produk->nama_produk . ' berhasil dikurangi sebanyak ' . $request->jumlah . ' unit.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengurangi stok: ' . $e->getMessage())->withInput();
        }
    }
}