<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Kategori::orderBy('nama_kategori', 'asc');

            // Filter by tipe jika diminta
            if ($request->filled('tipe')) {
                $query->where('tipe', $request->tipe);
            }

            $kategoris = $query->get(['id', 'nama_kategori', 'tipe']);

            return response()->json([
                'success' => true,
                'data' => $kategoris
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}