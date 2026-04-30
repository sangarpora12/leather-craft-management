<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produks = Produk::latest()->paginate(10);
        return view('admin.produk-jadi.index', compact('produks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.produk-jadi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

        Produk::create($validated);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        return view('admin.produk-jadi.show', compact('produk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        return view('admin.produk-jadi.edit', compact('produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
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
            // Hapus foto lama jika ada
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            
            $validated['foto'] = $request->file('foto')->store('produk', 'public');
        }

        $produk->update($validated);

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produk $produk)
    {
        // Hapus foto jika ada
        if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();

        return redirect()->route('produk.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}