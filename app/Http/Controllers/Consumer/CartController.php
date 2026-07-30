<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProdukJadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('produkJadi')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->produkJadi->harga_jual * $item->quantity;
        });

        return view('consumer.cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, $produkJadiId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = ProdukJadi::findOrFail($produkJadiId);

        if ($product->stok < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('produk_jadi_id', $produkJadiId)
            ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $product->stok) {
                return back()->withErrors(['quantity' => 'Total quantity melebihi stok tersedia.']);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'produk_jadi_id' => $produkJadiId,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $product = $cartItem->produkJadi;

        if ($request->quantity > $product->stok) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function remove($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }
}