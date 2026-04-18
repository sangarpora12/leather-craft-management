<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        // PERBAIKAN KRITIS: Ubah peran user dan peran yang diminta menjadi huruf kecil semua.
        // Ini mengatasi masalah jika di DB tersimpan 'Operator' atau 'operator'.
        if (strtolower(Auth::user()->role) !== strtolower($role)) {
            abort(403, 'AKSES DITOLAK: Anda bukan ' . $role);
        }

        return $next($request);
    }
}