<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProdukJadi;
use App\Models\ProdukKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('produkJadi')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('consumer.cart')->withErrors(['cart' => 'Keranjang belanja kosong.']);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->produkJadi->harga_jual * $item->quantity;
        });

        return view('consumer.checkout.index', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:transfer,qris,cod',
            'notes' => 'nullable|string',
        ]);

        $cartItems = Cart::where('user_id', Auth::id())
            ->with('produkJadi')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('consumer.cart')->withErrors(['cart' => 'Keranjang belanja kosong.']);
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            if ($item->produkJadi->stok < $item->quantity) {
                return back()->withErrors(['stock' => "Stok {$item->produkJadi->nama_produk} tidak mencukupi."]);
            }
        }

        DB::beginTransaction();

        try {
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'total_amount' => $cartItems->sum(function ($item) {
                    return $item->produkJadi->harga_jual * $item->quantity;
                }),
                'notes' => $request->notes,
            ]);

            // Create order items and decrement stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'produk_jadi_id' => $item->produk_jadi_id,
                    'product_name' => $item->produkJadi->nama_produk,
                    'price' => $item->produkJadi->harga_jual,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->produkJadi->harga_jual * $item->quantity,
                ]);

                // Decrement stock
                $item->produkJadi->decrement('stok', $item->quantity);

                // Record stock outflow for admin warehouse report
                ProdukKeluar::create([
                    'produk_jadi_id' => $item->produk_jadi_id,
                    'jumlah_keluar' => $item->quantity,
                    'alasan' => 'Penjualan',
                    'catatan' => 'Order: ' . $order->order_number . ' - Customer: ' . Auth::user()->name,
                    'tanggal_keluar' => now(),
                    'user_id' => Auth::id(),
                ]);
            }

            // Clear cart
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            return redirect()->route('consumer.order.success', $order->id)
                ->with('success', 'Pesanan berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal membuat pesanan: ' . $e->getMessage()]);
        }
    }

    public function success($orderId)
    {
        $order = Order::where('user_id', Auth::id())
            ->with('items.produkJadi')
            ->findOrFail($orderId);

        return view('consumer.checkout.success', compact('order'));
    }
}