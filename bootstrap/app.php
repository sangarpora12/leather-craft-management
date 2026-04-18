<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // --- MIDDLEWARE ALIASES ---
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'checkRole' => \App\Http\Middleware\RoleMiddleware::class, // Jaga-jaga kalau ada yg pake nama ini
        ]);

        // --- TRUST PROXIES FOR CLOUDFLARE TUNNEL ---
        // Penting untuk demo skripsi agar HTTPS dan login berfungsi dengan benar
        $middleware->trustProxies(at: '*'); // Percayai semua proxy (aman untuk tunnel)

    })
    ->withExceptions(function (Exceptions $exceptions) {
        // JURUS ANTI-HTML: Pastikan AJAX requests selalu dapat JSON response
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server Error: ' . $e->getMessage(),
                    'error' => true
                ], 500);
            }
        });
    })->create();
