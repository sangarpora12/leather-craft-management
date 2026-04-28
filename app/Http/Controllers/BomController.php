<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukJadi;
use App\Models\BahanBaku;
use App\Models\Bom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BomController extends Controller
{
    public function index(Request $request)
    {
        // Start query on ProdukJadi model
        $query = ProdukJadi::query();

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->string('search')->trim();
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_produk', 'like', "%{$search}%");
            });
        }

        // Filter by recipe status
        // Manually trim the input to prevent issues with leading/trailing spaces
        $filter = trim($request->input('filter', 'all'));
        if ($filter === 'has_recipe') {
            $query->whereHas('boms');
        } elseif ($filter === 'no_recipe') {
            $query->whereDoesntHave('boms');
        }

        // Eager load relationships AFTER filtering
        $products = $query->with(['boms.bahanBaku'])
                          ->latest()
                          ->paginate(15)
                          ->withQueryString();

        return view('admin.bom.index', compact('products'));
    }

    public function create($produk_jadi_id)
    {
        $product = ProdukJadi::findOrFail($produk_jadi_id);
        $materials = BahanBaku::select('id', 'nama_material', 'satuan', 'harga_satuan', 'stok', 'is_inventory')
                        ->orderBy('nama_material', 'asc')->get();
        
        return view('admin.bom.create', compact('product', 'materials'));
    }

    public function store(Request $request, $produk_jadi_id)
{
    // 1. Validasi
    $request->validate([
        'material_id'   => 'required|array',
        'material_id.*' => 'required',
        'quantity.*'    => 'required|numeric',
        'unit.*'        => 'required|string',
    ]);

    $product = ProdukJadi::findOrFail($produk_jadi_id);

    // 2. Gunakan DB Query Builder biar lebih pasti masuknya
    DB::beginTransaction();
    try {
        // Hapus resep lama
        DB::table('bom')->where('id_produk', $product->id)->delete();

        $bomsData = [];
        foreach ($request->material_id as $index => $materialId) {
            if (!empty($materialId) && $request->quantity[$index] > 0) {
                $bomsData[] = [
                    'id_produk'        => $product->id,
                    'id_bahan'         => $materialId,
                    'jumlah_kebutuhan' => $request->quantity[$index],
                    'satuan'           => $request->unit[$index],
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];
            }
        }

        if (count($bomsData) > 0) {
            // Kita pakai DB table langsung buat mastiin gak ada masalah di Model
            DB::table('bom')->insert($bomsData);
        }

        DB::commit();
        // Redirect ke halaman edit BOM setelah BOM berhasil disimpan
        return redirect()->route('admin.bom.edit', $product->id)->with('success', '✅ Resep BOM berhasil disimpan! Anda sekarang dapat mengedit detailnya.');
    } catch (\Exception $e) {
        DB::rollback();
        // Kalau error, dia bakal kasih tau kolom mana yang salah
        return "Gagal simpan! Errornya: " . $e->getMessage();
    }
}

public function edit($id)
{
    \Illuminate\Support\Facades\Log::info('BOM Edit Page Request', ['product_id' => $id]);
    $product = ProdukJadi::with(['boms.bahanBaku'])->findOrFail($id);
    
    $existingBoms = [];
    foreach ($product->boms as $index => $bom) {
        if ($bom->bahanBaku) {
            $existingBoms[] = [
                'id'            => $index + 1,
                'material_id'   => $bom->id_bahan,
                'quantity'      => $bom->jumlah_kebutuhan,
                'unit'          => $bom->satuan,
                'name'          => $bom->bahanBaku->nama_material,
                'price'         => $bom->bahanBaku->harga_satuan,
            ];
        }
    }
    
    $materials = BahanBaku::select('id', 'nama_material', 'satuan', 'harga_satuan', 'stok', 'is_inventory')
                    ->orderBy('nama_material', 'asc')->get();
    
    return view('admin.bom.edit', compact('product', 'existingBoms', 'materials'));
}

    public function update(Request $request, $id)
    {
        // Validasi BOM + Spesifikasi Teknis
        $validated = $request->validate([
            'material_id'   => 'sometimes|array',
            'material_id.*' => 'required',
            'quantity.*'    => 'required|numeric',
            'unit.*'        => 'required|string',
            'panjang'       => ['nullable', 'numeric', 'min:0'],
            'lebar'         => ['nullable', 'numeric', 'min:0'],
            'tinggi'        => ['nullable', 'numeric', 'min:0'],
            'detail_pola'   => ['nullable', 'string'],
            'sketsa'        => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        DB::beginTransaction();
        try {
            // === 1. UPDATE SPESIFIKASI TEKNIS PRODUK ===
            $product = ProdukJadi::findOrFail($id);
            
            $pathSketsa = $product->sketsa;
            if ($request->hasFile('sketsa')) {
                // Hapus sketsa lama jika ada
                if ($product->sketsa) {
                    Storage::disk('public')->delete($product->sketsa);
                }
                $pathSketsa = $request->file('sketsa')->store('produk/sketsa', 'public');
            }

            $product->update([
                'panjang' => $validated['panjang'] ?? $product->panjang,
                'lebar' => $validated['lebar'] ?? $product->lebar,
                'tinggi' => $validated['tinggi'] ?? $product->tinggi,
                'detail_pola' => $validated['detail_pola'] ?? $product->detail_pola,
                'sketsa' => $pathSketsa,
            ]);

            // === 2. UPDATE RESEP BOM ===
            // Cek apakah ada data material yang dikirim, jika tidak, lewati update BOM
            if ($request->has('material_id')) {
                DB::table('bom')->where('id_produk', $product->id)->delete();

                $bomsData = [];
                foreach ($request->material_id as $index => $materialId) {
                    if (!empty($materialId) && isset($request->quantity[$index]) && $request->quantity[$index] > 0) {
                        $bomsData[] = [
                            'id_produk'        => $product->id,
                            'id_bahan'         => $materialId,
                            'jumlah_kebutuhan' => $request->quantity[$index],
                            'satuan'           => $request->unit[$index],
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ];
                    }
                }

                if (count($bomsData) > 0) {
                    DB::table('bom')->insert($bomsData);
                }
            }

            DB::commit();
            return redirect()->route('admin.bom.index')->with('success', 'BOM & Spesifikasi untuk produk ' . $product->nama_produk . ' berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', "Gagal memperbarui: " . $e->getMessage());
        }
    }
}