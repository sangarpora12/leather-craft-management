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

        $products = $query->where('stok', '>', 0)->paginate(12);

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