<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        return Kategori::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'tipe' => 'required|in:bahan,produk',
        ]);

        return Kategori::create($request->all());
    }

    public function show($id)
    {
        return Kategori::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->update($request->all());
        return $kategori;
    }

    public function destroy($id)
    {
        Kategori::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}