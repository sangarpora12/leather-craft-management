<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pengaturan; // Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Ambil semua data pengaturan perusahaan
        $config = Pengaturan::all()->pluck('nilai', 'nama_pengaturan');
        
        return view('admin.profile.index', compact('user', 'config'));
    }

    // --- Update Profil Pribadi (Tetap Sama) ---
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->no_hp = $validated['no_hp'];

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::exists('public/' . $user->foto_profil)) {
                Storage::delete('public/' . $user->foto_profil);
            }
            $user->foto_profil = $request->file('foto_profil')->store('profile_photos', 'public');
        }

        $user->save();
        return back()->with('success', 'Profil pribadi diperbarui!');
    }

    // --- FITUR BARU: Update Profil Perusahaan ---
    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'logo_perusahaan' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // <--- Ganti jadi 5120 (5MB)
        ]);

        // Simpan Nama Perusahaan
        Pengaturan::updateOrCreate(
            ['nama_pengaturan' => 'nama_perusahaan'],
            ['nilai' => $validated['nama_perusahaan']]
        );

        // Simpan Logo Perusahaan
        if ($request->hasFile('logo_perusahaan')) {
            $oldLogo = Pengaturan::where('nama_pengaturan', 'logo_perusahaan')->first();
            if ($oldLogo && $oldLogo->nilai && Storage::exists('public/' . $oldLogo->nilai)) {
                Storage::delete('public/' . $oldLogo->nilai);
            }

            $path = $request->file('logo_perusahaan')->store('perusahaan', 'public');
            Pengaturan::updateOrCreate(
                ['nama_pengaturan' => 'logo_perusahaan'],
                ['nilai' => $path]
            );
        }

        return back()->with('success', 'Profil perusahaan berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        // ... kode update password kamu yang lama ...
        $user = Auth::user();
        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();
        return back()->with('success', 'Password berhasil diganti!');
    }
}