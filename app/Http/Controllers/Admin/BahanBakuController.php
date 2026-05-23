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
        // Ambil semua bahan dan gabungkan yang memiliki nama_material yang sama
        $allBahans = BahanBaku::all();
        
        // Proses penggabungan bahan - normalisasi nama untuk case-insensitive dan spasi
        $mergedBahans = collect();
        $grouped = $allBahans->groupBy(function($item) {
            // Normalisasi: ubah ke lowercase dan hapus spasi berlebih
            return strtolower(preg_replace('/\s+/', ' ', trim($item->nama_material)));
        });
        
        foreach ($grouped as $key => $items) {
            // Ambil nama material dari item pertama untuk ditampilkan
            $namaMaterial = $items->first()->nama_material;
            // Gabungkan stok dari semua item dengan nama yang sama
            $totalStok = $items->sum('stok');
            // Ambil nilai lain dari item pertama (atau yang terbaru)
            $firstItem = $items->first();
            
            // SEMUA BAHAN: pastikan warnanya cokelat - override semua warna kecuali jika sudah cokelat
            $warna = $firstItem->warna;
            // Jika warna kosong, polos, atau bukan cokelat - ubah menjadi cokelat
            if (empty($warna) || strtolower($warna) === 'polos' || strtolower($warna) !== 'cokelat') {
                $warna = 'cokelat';
            }
            
            $mergedBahans->push((object)[
                'id' => $firstItem->id,
                'nama_material' => $namaMaterial,
                'stok' => $totalStok,
                'stok_minimum' => $firstItem->stok_minimum,
                'harga_satuan' => $firstItem->harga_satuan,
                'satuan' => $firstItem->satuan,
                'warna' => $warna,
                // Pertahankan field lain yang diperlukan
                'kode_material' => $firstItem->kode_material,
                'kategori' => $firstItem->kategori,
                'supplier' => $firstItem->supplier,
            ]);
        }
        
        // Urutkan kembali seperti sebelumnya (terbaru dulu berdasarkan id pertama)
        $bahans = $mergedBahans->sortByDesc('id')->values();
        
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

        // SEMUA BAHAN BARU: paksa warna menjadi cokelat
        $warna = 'cokelat'; // Selalu gunakan cokelat untuk semua bahan baru

        BahanBaku::create([
            'nama_material' => $request->nama_bahan,
            'stok' => $request->stok,
            'harga_satuan' => $request->harga_satuan,
            'satuan' => $request->satuan ?? 'Pcs',
            'stok_minimum' => 10, // Default untuk alert stok kritis
            'warna' => $warna,
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