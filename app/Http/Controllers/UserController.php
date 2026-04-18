<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Pastikan model User sudah diimport
use Illuminate\Support\Facades\Hash; // Untuk mengenkripsi password

class UserController extends Controller
{
    // Method CREATE untuk menampilkan form
    public function create()
    {
        return view('admin.users.create');
    }

    // Method STORE untuk menyimpan data pengguna baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,operator'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            // Jika tabel Anda menggunakan 'username' sebagai pengganti 'name', 
            // Anda mungkin perlu menambahkan baris di sini: 'username' => $request->name,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil dibuat!');
    }
    
    // Method INDEX untuk menampilkan tabel pengguna (akan kita buat nanti)
    public function index()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    // ... (metode edit, update, destroy lainnya menyusul)
}