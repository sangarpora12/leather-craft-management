<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Belanja; // Pastikan Model Belanja sudah ada
use Illuminate\Support\Facades\Auth;

class BelanjaController extends Controller
{
    /* =====================================================
     * LIST BELANJA OPERATOR (FULL VERSION)
     * ===================================================== */
    public function index()
    {
        // Mengambil daftar belanja yang belum dibeli
        // Urutkan dari yang URGENT atau terbaru
        $belanjas = DB::table('belanjas') // Sesuaikan dengan nama tabelmu
            ->where('status', 'PENDING')
            ->latest()
            ->get();

        return view('operator.belanja.index', compact('belanjas'));
    }

    /* =====================================================
     * FITUR TANDAI SUDAH DIBELI
     * ===================================================== */
    public function tandaiDibeli($id)
    {
        DB::table('belanjas')->where('id', $id)->update([
            'status' => 'DIBELI',
            'pic_id' => Auth::id(),
            'tanggal_beli' => now()
        ]);

        return back()->with('success', 'Material berhasil ditandai sudah dibeli!');
    }
}