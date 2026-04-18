<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if (Kategori::whereRaw('LOWER(nama_kategori) = ?', [strtolower($value)])
                                ->where('tipe', $request->tipe)
                                ->exists()) {
                        $fail("Nama kategori '{$value}' untuk tipe '{$request->tipe}' sudah ada.");
                    }
                },
            ],
            'tipe' => 'required|in:bahan,produk',
        ], [
            'nama_kategori.required' => 'Nama kategori tidak boleh kosong.',
            'tipe.required' => 'Tipe kategori harus dipilih.',
            'tipe.in' => 'Tipe kategori tidak valid.',
        ]);

        Kategori::create([
            'nama_kategori' => $request->nama_kategori,
            'tipe' => $request->tipe,
        ]);

        return back()->with('success', 'Kategori berhasil ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Kategori::findOrFail($id)->delete();
        return back()->with('success', 'Kategori Dihapus!');
    }
}