<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motif;
use Illuminate\Http\Request;

class MotifController extends Controller
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
            'nama_motif' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (Motif::whereRaw('LOWER(nama_motif) = ?', [strtolower($value)])->exists()) {
                        $fail("Nama motif '{$value}' sudah ada.");
                    }
                },
            ],
        ], [
            'nama_motif.required' => 'Nama motif tidak boleh kosong.',
        ]);

        Motif::create(['nama_motif' => $request->nama_motif]);

        return back()->with('success', 'Motif berhasil ditambah!');
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
        Motif::findOrFail($id)->delete();
        return back()->with('success', 'Motif Dihapus!');
    }
}