<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
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
            'nama_satuan' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (Satuan::whereRaw('LOWER(nama_satuan) = ?', [strtolower($value)])->exists()) {
                        $fail("Nama satuan '{$value}' sudah ada.");
                    }
                },
            ],
        ], [
            'nama_satuan.required' => 'Nama satuan tidak boleh kosong.',
        ]);

        Satuan::create([
            'nama_satuan' => $request->nama_satuan,
        ]);

        return back()->with('success', 'Satuan berhasil ditambah!');
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
        Satuan::findOrFail($id)->delete();
        return back()->with('success', 'Satuan Dihapus!');
    }
}