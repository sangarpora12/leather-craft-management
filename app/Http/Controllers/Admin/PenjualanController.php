<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\ProdukKeluar;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    /**
     * Display sales details - customer accounts, orders, and sales summary
     */
    public function index(Request $request)
    {
        // Sales Summary Statistics
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Get all orders with user and items
        $query = Order::with(['user', 'items.produkJadi']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by search (order number or customer name)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->latest()->paginate(10);

        // Get customer accounts with order count and total spent
        $customers = User::where('role', 'customer')
            ->withCount(['orders as total_orders' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }])
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }], 'total_amount')
            ->latest()
            ->get();

        // Recent produk_keluar (stock outflow from sales)
        $recentKeluar = ProdukKeluar::with(['produk_jadi', 'user'])
            ->where('alasan', 'Penjualan')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.penjualan.index', compact(
            'orders',
            'customers',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'completedOrders',
            'totalCustomers',
            'recentKeluar'
        ));
    }

    /**
     * Show order detail
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.produkJadi'])->findOrFail($id);

        return view('admin.penjualan.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        
        if ($request->status === 'completed' && !$order->paid_at) {
            $order->paid_at = now();
        }
        
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!');
    }
}