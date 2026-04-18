<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil data log dengan relasi user, urutkan dari yang terbaru
        $query = LogAktivitas::with('user')->latest();

        // Filter berdasarkan modul
        if ($request->filled('modul')) {
            $query->modul($request->modul);
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->user($request->user_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->tanggal($request->tanggal);
        }

        // Filter berdasarkan kata kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('modul', 'like', "%{$search}%")
                  ->orWhere('aktivitas', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(15)->withQueryString();

        // Ambil daftar modul unik untuk filter
        $modulList = LogAktivitas::select('modul')
            ->distinct()
            ->pluck('modul');

        // Ambil daftar user untuk filter
        $userList = User::select('id', 'name')->get();

        return view('admin.laporan.log-aktivitas.index', compact('logs', 'modulList', 'userList'));
    }

    /**
     * Hapus log yang sudah lebih dari 30 hari
     */
    public function cleanup()
    {
        $deleted = LogAktivitas::hapusLogLama();
        return back()->with('success', "Berhasil menghapus {$deleted} log yang sudah kadaluarsa.");
    }

    /**
     * Hapus semua log (hanya untuk admin)
     */
    public function destroyAll()
    {
        LogAktivitas::truncate();
        return back()->with('success', 'Semua log aktivitas telah dihapus.');
    }
}