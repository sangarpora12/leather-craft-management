<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * LIST NOTIFIKASI (DENGAN FILTER + PAGINATION)
     */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $filter = $request->query('filter', 'all');

        // QUERY DASAR (JANGAN ->get())
        $query = $user->notifications()
            ->orderBy('created_at', 'desc');

        // FILTER
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // PAGINATION (PAKAI NAMA AMAN, TIDAK BENTROK DENGAN LAYOUT)
        $paginatedNotifications = $query
            ->paginate(15)
            ->withQueryString();

        return view('admin.notifications.index', [
            'paginatedNotifications' => $paginatedNotifications,
            'filter' => $filter,
        ]);
    }

    /**
     * DATA NOTIFIKASI UNTUK LONCENG (AJAX)
     */
    public function getNotifications()
    {
        $user = Auth::user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
            'notifications' => $user->unreadNotifications()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ]);
    }

    /**
     * BACA 1 NOTIFIKASI & REDIRECT
     */
    public function markAsReadAndRedirect($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();

            return redirect(
                $notification->data['url'] ?? route('admin.dashboard')
            );
        }

        return back();
    }

    /**
     * BACA SEMUA NOTIFIKASI
     */
    public function markAllRead()
    {
        Auth::user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi sudah dibaca.');
    }

    /**
     * HAPUS 1 NOTIFIKASI
     */
    public function destroy($id)
    {
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->delete();
            return back()->with('success', 'Notifikasi dihapus.');
        }

        return back();
    }

    /**
     * MARK READ NOTIFIKASI (AJAX)
     */
    public function markRead(Request $request)
    {
        $id = $request->input('id');
        $notification = Auth::user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * HAPUS SEMUA NOTIFIKASI
     */
    public function deleteAll()
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'Semua notifikasi dihapus.');
    }
}
