<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DangerZoneController extends Controller
{
    public function index()
    {
        return view('admin.pengaturan.danger-zone');
    }

    public function resetData(Request $request)
    {
        $request->validate([
            'confirm' => 'required|accepted'
        ]);

        try {
            DB::transaction(function () {
                // Reset stok semua bahan (set ke 0)
                DB::table('bahan_bakus')->update(['stok' => 0]);
                
                // Hapus pending pengadaan
                DB::table('pengadaans')->where('status', 'PENDING')->delete();
                
                // Hapus unverified bahan masuk
                DB::table('bahan_masuks')->where('status', 'pending')->delete();
                
                // Clear notifications
                DB::table('notifications')->delete();
                
                // Clear sessions & cache
                Artisan::call('cache:clear');
                Artisan::call('config:clear');
                Artisan::call('view:clear');
            });

            return redirect()->back()->with('success', 'Data berhasil direset! Stok = 0, pending items dihapus.');

        } catch (\Exception $e) {
            Log::error('DangerZone Reset Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal reset data: ' . $e->getMessage());
        }
    }

    public function superReset(Request $request)
    {
        $request->validate([
            'confirm_super' => 'required|accepted',
            'password' => 'required'
        ]);

        // Check admin password (simple check - in production use proper auth)
        if (!hash_equals('admin123', sha1($request->password))) { // CHANGE THIS IN PRODUCTION
            return redirect()->back()->with('error', 'Password salah!');
        }

        try {
            DB::transaction(function () {
                Artisan::call('migrate:refresh --seed');
            });

            return redirect()->route('admin.dashboard')->with('success', 'SUPER RESET selesai! Database fresh + seeded.');

        } catch (\Exception $e) {
            Log::error('Super Reset Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Super Reset gagal: ' . $e->getMessage());
        }
    }
}

