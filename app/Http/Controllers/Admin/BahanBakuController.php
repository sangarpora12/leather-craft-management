<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BahanBakuController extends Controller
{
    /* =========================
     * OPERATOR FEATURES
     * ========================= */

    // 1. Menampilkan daftar bahan baku untuk operator
    public function indexOperator()
    {
        $bahans = BahanBaku::latest()->get();
        return view('operator.bahan-list', compact('bahans'));
    }

    // 2. Form Input Bahan Manual
    public function createOperator()
    {
        return view('operator.input-bahan');
    }

    // 3. Simpan Pemakaian/Penambahan Bahan
    public function storeOperator(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required',
            'stok' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
        ]);

        BahanBaku::create([
            'nama_material' => $request->nama_bahan,
            'stok' => $request->stok,
            'harga_satuan' => $request->harga_satuan,
            'satuan' => $request->satuan ?? 'Pcs',
            'stok_minimum' => 10, // Default untuk alert stok kritis
        ]);

        return redirect()->route('operator.scan.index')
                         ->with('success', 'Bahan baru berhasil diinput manual!');
    }

    // 4. Update Stok via Operator
    public function updateOperator(Request $request, $id)
    {
        $bahan = BahanBaku::findOrFail($id);
        $bahan->increment('stok', $request->tambah_stok);

        return redirect()->back()->with('success', 'Stok berhasil diperbarui!');
    }
}