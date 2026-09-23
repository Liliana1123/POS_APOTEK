<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Auto-invalidate dashboard cache when transactions/stock change
        $modelsToInvalidate = [
            \App\Models\Penjualan::class,
            \App\Models\DetailPenjualan::class,
            \App\Models\Penerimaan::class,
            \App\Models\DetailPenerimaan::class,
            \App\Models\Rusak::class,
            \App\Models\PembayaranPenerimaan::class,
            \App\Models\PembayaranPiutang::class,
            \App\Models\Barang::class,
            \App\Models\Pelanggan::class,
            \App\Models\DiscountUsage::class,
        ];

        foreach ($modelsToInvalidate as $modelClass) {
            $modelClass::saved(function () {
                \App\Services\DashboardCacheService::clear();
            });
            $modelClass::deleted(function () {
                \App\Services\DashboardCacheService::clear();
            });
        }
    }
}
