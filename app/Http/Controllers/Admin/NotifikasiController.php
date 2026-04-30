<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Pengaturan::whereIn('nama_pengaturan', [
            'notif_stok_kritis',
            'notif_bahan_masuk', 
            'notif_pengadaan_pending',
            'telegram_bot_token',
            'telegram_channel_id',
            'telegram_admins'
        ])->get()->keyBy('nama_pengaturan');
        
        return view('admin.pengaturan.notifikasi', compact('notifikasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'notif_stok_kritis' => 'required|in:0,1',
            'notif_bahan_masuk' => 'required|in:0,1',
            'notif_pengadaan_pending' => 'required|in:0,1',
            'telegram_bot_token' => 'nullable|string|max:255',
            'telegram_channel_id' => 'nullable|string|max:255',
            'telegram_admins' => 'nullable|string|max:500'
        ]);

        $data = $request->only([
            'notif_stok_kritis',
            'notif_bahan_masuk', 
            'notif_pengadaan_pending',
            'telegram_bot_token',
            'telegram_channel_id',
            'telegram_admins'
        ]);

        foreach ($data as $key => $value) {
            Pengaturan::updateOrCreate(
                ['nama_pengaturan' => $key],
                ['nilai' => $value]
            );
        }

        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Pengaturan notifikasi berhasil disimpan!');
    }
}

