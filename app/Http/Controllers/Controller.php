<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // FUNGSI GLOBAL UNTUK NOTIFIKASI
    public function createNotification($message, $type = 'info')
    {
        try {
            DB::table('notifications')->insert([
                'type' => $type,
                'message' => $message,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Abaikan jika tabel belum ada
        }
    }
}