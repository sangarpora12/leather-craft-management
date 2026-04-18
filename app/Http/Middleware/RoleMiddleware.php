<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan pengguna sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        // Ambil peran pengguna yang sedang login (Asumsi model User Anda memiliki kolom 'role')
        $userRole = Auth::user()->role; 

        // Cek apakah peran pengguna ada dalam daftar peran yang diizinkan
        if (!in_array($userRole, $roles)) {
            // Jika peran tidak diizinkan, kembalikan response 403 Forbidden
            // [FIX] Jangan return JSON, tapi redirect ke dashboard masing-masing agar tidak bingung di HP
            if ($userRole === 'operator') {
                return redirect()->route('operator.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman Admin.');
            } elseif ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return abort(403, 'Akses Ditolak');
        }

        return $next($request);
    }
}