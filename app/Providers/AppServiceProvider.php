<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\BahanBaku;
use App\Models\Pengaturan;
use App\Observers\ProdukJadiObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS URLs to fix mixed content issues
        // Always force HTTPS for ngrok/cloudflared tunnels and production
        if (app()->environment('production') ||
            str_contains(request()->getHost(), 'ngrok') ||
            str_contains(request()->getHost(), 'trycloudflare.com') ||
            request()->header('X-Forwarded-Proto') === 'https' ||
            request()->isSecure()) {
            \URL::forceScheme('https');
        }

        // Register Observer for Dynamic Pricing
        // Auto recalculate harga produk saat harga bahan atau pengaturan berubah
        BahanBaku::observe(ProdukJadiObserver::class);
        Pengaturan::observe(ProdukJadiObserver::class);
    }
}
