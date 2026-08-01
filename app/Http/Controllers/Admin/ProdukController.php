<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProdukJadi;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $produk_jadi = ProdukJadi::latest()->paginate(10);
        $allCategories = ProdukJadi::select('kategori')->distinct()->get()->pluck('kategori');
        return view('admin.produk-jadi.index', compact('produk_jadi', 'allCategories'));
    }

    public function create()
    {
        return view('admin.produk-jadi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|array|min:1',
            'kategori.*' => 'string|max:100',
            'warna' => 'required|array|min:1',
            'warna.*' => 'string|max:100',
            'jenis_motif' => 'required|array|min:1',
            'jenis_motif.*' => 'string|max:100',
            'tipe_harga' => 'nullable|string|in:manual,otomatis',
            'harga_jual' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $hargaJual = 0;
        if (!empty($request->harga_jual)) {
            $hargaJual = (int) str_replace(['.', ',', 'Rp', ' '], '', $request->harga_jual);
        }

        $pathFoto = null;
        if ($request->hasFile('foto')) {
            $pathFoto = $request->file('foto')->store('produk-jadi', 'public');
        }

        ProdukJadi::create([
            'nama_produk'  => $validated['nama_produk'],
            'kategori'     => $validated['kategori'],
            'warna'        => $validated['warna'],
            'jenis_motif'  => $validated['jenis_motif'],
            'harga_jual'   => $hargaJual,
            'tipe_harga'   => $validated['tipe_harga'] ?? 'manual',
            'deskripsi'    => $validated['deskripsi'] ?? null,
            'stok'         => 0,
            'foto'         => $pathFoto,
        ]);

        return redirect()->route('admin.produk-jadi.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show($produkJadi)
    {
        $product = ProdukJadi::with('boms.material')->findOrFail($produkJadi);
        $warnaMasterLookup = \App\Models\Warna::all()->keyBy('nama_warna');
        $margin = (float) (Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 30);
        return view('admin.produk-jadi.show', compact('product', 'warnaMasterLookup', 'margin'));
    }

    public function edit($produkJadi)
    {
        $product = ProdukJadi::findOrFail($produkJadi);

        // Decode JSON array fields
        $product->kategori = is_string($product->kategori) ? json_decode($product->kategori, true) : $product->kategori;
        $product->warna = is_string($product->warna) ? json_decode($product->warna, true) : $product->warna;
        $product->jenis_motif = is_string($product->jenis_motif) ? json_decode($product->jenis_motif, true) : $product->jenis_motif;

        if (!is_array($product->kategori)) $product->kategori = [];
        if (!is_array($product->warna)) $product->warna = [];
        if (!is_array($product->jenis_motif)) $product->jenis_motif = [];

        $margin = (float) (Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 30);
        $kategories = \App\Models\Kategori::where('tipe', 'produk')->get();
        $warnas = \App\Models\Warna::all();
        $motifs = \App\Models\Motif::all();
        return view('admin.produk-jadi.edit', compact('product', 'margin', 'kategories', 'warnas', 'motifs'));
    }

    public function update(Request $request, $produkJadi)
    {
        $product = ProdukJadi::findOrFail($produkJadi);

        $validated = $request->validate([
            'nama_produk'  => 'required|string|max:255',
            'kategori'     => 'required|array|min:1',
            'kategori.*'   => 'string|max:100',
            'warna'        => 'required|array|min:1',
            'warna.*'      => 'string|max:100',
            'jenis_motif'  => 'required|array|min:1',
            'jenis_motif.*'=> 'string|max:100',
            'tipe_harga'   => 'nullable|string|in:manual,otomatis',
            'harga_jual'   => 'nullable|string',
            'deskripsi'    => 'nullable|string',
            'keterangan'   => 'nullable|string',
            'foto'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'delete_foto'  => 'nullable|string|in:1',
            'panjang'      => 'nullable|numeric|min:0',
            'lebar'        => 'nullable|numeric|min:0',
            'tinggi'       => 'nullable|numeric|min:0',
            'detail_pola'  => 'nullable|string',
            'stok_minimum' => 'nullable|integer|min:0',
        ]);

        // Parse harga_jual dari format "150.000" ke integer
        $hargaJual = 0;
        if ($request->filled('tipe_harga') && $request->tipe_harga === 'otomatis') {
            $margin = (float) (Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 30);
            $totalHpp = 0;
            $product->load('boms.material');
            foreach ($product->boms as $bom) {
                if ($bom->material) {
                    $hargaSatuan = (float) str_replace(',', '.', $bom->material->harga_satuan);
                    $jumlahKebutuhan = (float) str_replace(',', '.', $bom->jumlah_kebutuhan);
                    $totalHpp += $hargaSatuan * $jumlahKebutuhan;
                }
            }
            $hargaJual = ceil($totalHpp + ($totalHpp * ($margin / 100)));
        } else {
            if (!empty($request->harga_jual)) {
                $hargaJual = (int) str_replace(['.', ',', 'Rp', ' '], '', $request->harga_jual);
            }
        }

        // Handle foto
        $pathFoto = $product->foto;
        if ($request->hasFile('foto')) {
            if ($product->foto && Storage::disk('public')->exists($product->foto)) {
                Storage::disk('public')->delete($product->foto);
            }
            $pathFoto = $request->file('foto')->store('produk-jadi', 'public');
        } elseif ($request->has('delete_foto') && $request->delete_foto == '1') {
            if ($product->foto) {
                Storage::disk('public')->delete($product->foto);
                $pathFoto = null;
            }
        }

        $product->update([
            'nama_produk'  => $validated['nama_produk'],
            'kategori'     => $validated['kategori'],
            'warna'        => $validated['warna'],
            'jenis_motif'  => $validated['jenis_motif'],
            'harga_jual'   => $hargaJual,
            'tipe_harga'   => $validated['tipe_harga'] ?? $product->tipe_harga ?? 'manual',
            'deskripsi'    => $validated['deskripsi'] ?? $product->deskripsi,
            'keterangan'   => $validated['keterangan'] ?? $product->keterangan,
            'panjang'      => $validated['panjang'] ?? $product->panjang,
            'lebar'        => $validated['lebar'] ?? $product->lebar,
            'tinggi'       => $validated['tinggi'] ?? $product->tinggi,
            'detail_pola'  => $validated['detail_pola'] ?? $product->detail_pola,
            'foto'         => $pathFoto,
        ]);

        return redirect()->route('admin.produk-jadi.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($produkJadi)
    {
        $product = ProdukJadi::findOrFail($produkJadi);

        if ($product->foto && Storage::disk('public')->exists($product->foto)) {
            Storage::disk('public')->delete($product->foto);
        }

        $product->delete();

        return redirect()->route('admin.produk-jadi.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}