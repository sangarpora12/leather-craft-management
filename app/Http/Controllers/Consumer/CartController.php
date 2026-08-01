<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ProdukJadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getCartIdentifier()
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id(), 'session_id' => null];
        }
        return ['user_id' => null, 'session_id' => session()->getId()];
    }

    private function getCartItems()
    {
        $id = $this->getCartIdentifier();
        return Cart::where('user_id', $id['user_id'])
            ->where('session_id', $id['session_id'])
            ->with('produkJadi')
            ->get();
    }

    public function index()
    {
        $cartItems = $this->getCartItems();

        $cartItems = $cartItems->filter(function ($item) {
            return $item->produkJadi->stok > 0;
        });

        $id = $this->getCartIdentifier();
        $outOfStockIds = Cart::where('user_id', $id['user_id'])
            ->where('session_id', $id['session_id'])
            ->whereHas('produkJadi', function ($query) {
                $query->where('stok', '<=', 0);
            })
            ->pluck('id');

        if ($outOfStockIds->isNotEmpty()) {
            Cart::whereIn('id', $outOfStockIds)->delete();
        }

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

        $id = $this->getCartIdentifier();

        $cartItem = Cart::where('user_id', $id['user_id'])
            ->where('session_id', $id['session_id'])
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
                'user_id' => $id['user_id'],
                'session_id' => $id['session_id'],
                'produk_jadi_id' => $produkJadiId,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function buyNow(Request $request, $produkJadiId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = ProdukJadi::findOrFail($produkJadiId);

        if ($product->stok < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $id = $this->getCartIdentifier();
        Cart::where('user_id', $id['user_id'])
            ->where('session_id', $id['session_id'])
            ->delete();

        Cart::create([
            'user_id' => $id['user_id'],
            'session_id' => $id['session_id'],
            'produk_jadi_id' => $produkJadiId,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('consumer.checkout');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $identifier = $this->getCartIdentifier();
        $cartItem = Cart::where('user_id', $identifier['user_id'])
            ->where('session_id', $identifier['session_id'])
            ->findOrFail($id);
        $product = $cartItem->produkJadi;

        if ($request->quantity > $product->stok) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    public function remove($id)
    {
        $identifier = $this->getCartIdentifier();
        $cartItem = Cart::where('user_id', $identifier['user_id'])
            ->where('session_id', $identifier['session_id'])
            ->findOrFail($id);
        $cartItem->delete();

        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function clear()
    {
        $identifier = $this->getCartIdentifier();
        Cart::where('user_id', $identifier['user_id'])
            ->where('session_id', $identifier['session_id'])
            ->delete();

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }
}