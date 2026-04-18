<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HakAksesController extends Controller
{
    /**
     * Display a listing of user roles and permissions.
     */
    public function index()
    {
        // Get all users with their roles
        $users = User::orderBy('role')->orderBy('name')->get();
        
        // Get available roles
        $roles = [
            'admin' => 'Administrator',
            'operator' => 'Operator',
        ];
        
        // Get current user (for checking)
        $currentUser = Auth::user();
        
        return view('admin.hak-akses.index', compact('users', 'roles', 'currentUser'));
    }

    /**
     * Update user role.
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,operator',
        ]);

        // Get the user to be updated
        $user = User::findOrFail($id);
        
        // Get current user
        $currentUser = Auth::user();
        
        // Prevent admin from changing their own role
        if ($user->id === $currentUser->id) {
            return redirect()->route('admin.hak-akses.index')
                ->with('error', 'Anda tidak dapat mengubah role Anda sendiri!');
        }
        
        // Prevent admin from demoting other admins (optional - can be removed if needed)
        if ($user->role === 'admin' && $request->role !== 'admin') {
            // Allow demotion but with warning
        }

        // Update the role
        $user->role = $request->role;
        $user->save();

        return redirect()->route('admin.hak-akses.index')
            ->with('success', 'Role user ' . $user->name . ' berhasil diubah menjadi ' . ($request->role === 'admin' ? 'Administrator' : 'Operator') . '!');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        
        // Get current user
        $currentUser = Auth::user();
        
        // Prevent admin from deactivating themselves
        if ($user->id === $currentUser->id) {
            return redirect()->route('admin.hak-akses.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        // Toggle status (assuming there's an 'active' column, if not we'll add it)
        if (isset($user->active)) {
            $user->active = !$user->active;
            $user->save();
            
            $status = $user->active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->route('admin.hak-akses.index')
                ->with('success', 'User ' . $user->name . ' berhasil ' . $status . '!');
        }
        
        return redirect()->route('admin.hak-akses.index')
            ->with('error', 'Fitur status belum tersedia!');
    }

    /**
     * Show permission management page.
     */
    public function permissions()
    {
        // Define available permissions
        $permissions = [
            'dashboard' => 'Akses Dashboard',
            'bahan_baku' => 'Kelola Bahan Baku',
            'produk_jadi' => 'Kelola Produk Jadi',
            'bom' => 'Kelola Bill of Materials',
            'produksi' => 'Kelola Produksi (SPK)',
            'pengadaan' => 'Kelola Pengadaan',
            'supplier' => 'Kelola Supplier',
            'laporan' => 'Akses Laporan',
            'operator' => 'Kelola Operator',
            'pekerja_master' => 'Kelola Master Pekerja',
            'pengaturan' => 'Kelola Pengaturan Sistem',
            'hak_akses' => 'Kelola Hak Akses',
        ];

        $currentUser = Auth::user();
        
        return view('admin.hak-akses.permissions', compact('permissions', 'currentUser'));
    }
}
