<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warna;
use Illuminate\Http\Request;

class WarnaController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.pengaturan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_warna' => 'required|string|max:255|unique:warnas,nama_warna',
        ]);

        Warna::create([
            'nama_warna' => $request->nama_warna,
        ]);

        return back()->with('success', 'Warna berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_warna' => 'required|string|max:255|unique:warnas,nama_warna,' . $id,
        ]);

        $data = Warna::findOrFail($id);
        $data->update([
            'nama_warna' => $request->nama_warna,
        ]);

        return back()->with('success', 'Warna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $data = Warna::findOrFail($id);
            $data->delete();
            return back()->with('success', 'Warna berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Warna tidak dapat dihapus karena masih terikat dengan data lain.');
        }
    }
}