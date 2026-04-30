<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Satuan; // Ganti dengan model yang sesuai (Kategori, Warna, Motif)
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.pengaturan.index');
    }

    public function store(Request $request)
    {
        // Sesuaikan nama field dan tabel pada validasi
        $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan',
        ]);

        // Sesuaikan nama field pada create
        Satuan::create([
            'nama_satuan' => $request->nama_satuan,
        ]);

        return back()->with('success', 'Data master berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        // Sesuaikan nama field dan tabel pada validasi
        $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan,' . $id,
        ]);

        $data = Satuan::findOrFail($id);
        // Sesuaikan nama field pada update
        $data->update([
            'nama_satuan' => $request->nama_satuan,
        ]);

        return back()->with('success', 'Data master berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $data = Satuan::findOrFail($id);
            $data->delete();
            return back()->with('success', 'Data master berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Data tidak dapat dihapus karena masih terikat dengan data lain.');
        }
    }
}