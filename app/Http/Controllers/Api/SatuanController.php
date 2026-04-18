<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        try {
            $satuans = Satuan::orderBy('tipe', 'asc')
                ->orderBy('nama_satuan', 'asc')
                ->get(['id', 'nama_satuan', 'tipe']);

            // Group by tipe
            $grouped = $satuans->groupBy('tipe');

            return response()->json([
                'success' => true,
                'data' => $grouped
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}