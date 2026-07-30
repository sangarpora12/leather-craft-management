<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProdukJadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $produks = ProdukJadi::latest()->paginate(10);
        return view('admin.produk-jadi.index', compact('produks'));
    }

    public function create()
    {
        return view('admin.produk-jadi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'motif' => 'required|string|max:255',
            'warna' => 'required|string|max:255',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('produk', 'public');
        }

        ProdukJadi::create($validated);

        return redirect()->route('admin.produk-jadi.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show($produkJadi)
    {
        $product = ProdukJadi::with('boms.material')->findOrFail($produkJadi);
        $warnaMasterLookup = \App\Models\Warna::all()->keyBy('nama_warna');
        $margin = (float) (\App\Models\Pengaturan::where('nama_pengaturan', 'margin_keuntungan')->value('nilai') ?? 30);
        return view('admin.produk-jadi.show', compact('product', 'warnaMasterLookup', 'margin'));
    }

    public function edit($produkJadi)
    {
        $product = ProdukJadi::findOrFail($produkJadi);
        return view('admin.produk-jadi.edit', compact('product'));
    }

    public function update(Request $request, $produkJadi)
    {
        $product = ProdukJadi::findOrFail($produkJadi);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'motif' => 'required|string|max:255',
            'warna' => 'required|string|max:255',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($product->foto && Storage::disk('public')->exists($product->foto)) {
                Storage::disk('public')->delete($product->foto);
            }
            $validated['foto'] = $request->file('foto')->store('produk', 'public');
        }

        $product->update($validated);

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