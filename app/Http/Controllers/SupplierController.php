<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource (Katalog Rekomendasi dari Data Bahan Baku).
     * Mengambil data ASLI dari tabel bahan_bakus berdasarkan nama_toko.
     */
    public function index(Request $request)
    {
        // Ambil semua bahan baku yang punya nama supplier
        $bahanBakus = BahanBaku::whereNotNull('supplier')
                               ->where('supplier', '!=', '')
                               ->get();
                               
        // Kelompokkan berdasarkan nama supplier (untuk card grid)
        $suppliers = $bahanBakus->groupBy('supplier')->sortKeys();

        // Cari rekomendasi termurah per nama material
        $rekomendasiTermurah = $bahanBakus
            ->groupBy('nama_material')
            ->map(function ($items) {
                return $items->sortBy('harga_satuan')->first();
            })
            ->sortBy('harga_satuan')
            ->values();

        return view('admin.supplier.index', compact('suppliers', 'rekomendasiTermurah'));
    }

    /**
     * Store a newly created resource in storage.
     * NOTE: Tidak digunakan lagi karena data diambil otomatis dari bahan_bakus
     */
    public function store(Request $request)
    {
        // Fitur CRUD dihentikan karena data sekarang diambil dari Bahan Baku
        return redirect()->route('admin.supplier.index')->with('info', 'Data supplier sekarang diambil secara otomatis dari inputan Bahan Baku.');
    }

    /**
     * Update the specified resource in storage.
     * NOTE: Tidak digunakan lagi
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('admin.supplier.index')->with('info', 'Data supplier sekarang diambil secara otomatis dari inputan Bahan Baku.');
    }

    /**
     * Remove the specified resource from storage.
     * NOTE: Tidak digunakan lagi
     */
    public function destroy($id)
    {
        return redirect()->route('admin.supplier.index')->with('info', 'Data supplier sekarang diambil secara otomatis dari inputan Bahan Baku.');
    }
    
    /**
     * Get materials for a specific store (AJAX)
     */
    public function getMaterials($namaToko)
    {
        $materials = BahanBaku::where('supplier', $namaToko)
            ->whereNotNull('nama_material')
            ->where('nama_material', '!=', '')
            ->get();
            
        return response()->json([
            'success' => true,
            'supplier' => $namaToko,
            'materials' => $materials
        ]);
    }
    
    /**
     * Get price comparison for all materials (AJAX)
     */
    public function getPriceComparison()
    {
        $bahanBakus = BahanBaku::whereNotNull('supplier')
            ->where('supplier', '!=', '')
            ->whereNotNull('nama_material')
            ->where('nama_material', '!=', '')
            ->get();
            
        $priceComparison = $bahanBakus
            ->groupBy('nama_material')
            ->map(function ($items) {
                $sorted = $items->sortBy('harga_satuan');
                return [
                    'nama_material' => $items->first()->nama_material,
                    'cheapest_supplier' => $sorted->first()->supplier,
                    'cheapest_price' => $sorted->first()->harga_satuan,
                    'satuan' => $sorted->first()->satuan,
                    'materials' => $sorted->values()
                ];
            })
            ->sortBy('cheapest_price')
            ->values();
            
        return response()->json([
            'success' => true,
            'data' => $priceComparison
        ]);
    }
}
