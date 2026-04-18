<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warna;
use Illuminate\Http\Request;

class WarnaController extends Controller
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
            'nama_warna' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (Warna::whereRaw('LOWER(nama_warna) = ?', [strtolower($value)])->exists()) {
                        $fail("Nama warna '{$value}' sudah ada.");
                    }
                },
            ],
        ], [
            'nama_warna.required' => 'Nama warna tidak boleh kosong.',
        ]);

        Warna::create(['nama_warna' => $request->nama_warna]);

        return back()->with('success', 'Warna berhasil ditambah!');
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
        Warna::findOrFail($id)->delete();
        return back()->with('success', 'Warna Dihapus!');
    }
}