<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProdukJadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = ProdukJadi::query();

        // Search
        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Filter by kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', 'like', '%' . $request->kategori . '%');
        }

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'termurah':
                    $query->orderBy('harga_jual', 'asc');
                    break;
                case 'termahal':
                    $query->orderBy('harga_jual', 'desc');
                    break;
                case 'nama':
                    $query->orderBy('nama_produk', 'asc');
                    break;
                case 'terbaru':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);

        // Get unique categories for filter
        $kategoris = ProdukJadi::select('kategori')->distinct()->get()->pluck('kategori');

        return view('consumer.catalog.index', compact('products', 'kategoris'));
    }

    public function show($id)
    {
        $product = ProdukJadi::findOrFail($id);
        return view('consumer.catalog.show', compact('product'));
    }
}