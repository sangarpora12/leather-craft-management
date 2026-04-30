<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Motif;
use Illuminate\Http\Request;

class MotifController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.pengaturan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_motif' => 'required|string|max:255|unique:motifs,nama_motif',
        ]);

        Motif::create([
            'nama_motif' => $request->nama_motif,
        ]);

        return back()->with('success', 'Motif berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_motif' => 'required|string|max:255|unique:motifs,nama_motif,' . $id,
        ]);

        $data = Motif::findOrFail($id);
        $data->update([
            'nama_motif' => $request->nama_motif,
        ]);

        return back()->with('success', 'Motif berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $data = Motif::findOrFail($id);
            $data->delete();
            return back()->with('success', 'Motif berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Motif tidak dapat dihapus karena masih terikat dengan data lain.');
        }
    }
}