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
        \Carbon\Carbon::setLocale('id');
        config(['app.locale' => 'id']);

        // Blade Directives: Currency & Date Formatting
        \Illuminate\Support\Facades\Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format((float)($expression ?? 0), 0, ',', '.'); ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('tanggalId', function ($expression) {
            return "<?php echo call_user_func(function(\$val, \$fmt = 'd M Y') {
                return !empty(\$val) ? \\Carbon\\Carbon::parse(\$val)->translatedFormat(\$fmt) : '—';
            }, {$expression}); ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('tanggalWaktuId', function ($expression) {
            return "<?php echo call_user_func(function(\$val, \$fmt = 'd M Y, H:i') {
                return !empty(\$val) ? \\Carbon\\Carbon::parse(\$val)->translatedFormat(\$fmt) : '—';
            }, {$expression}); ?>";
        });

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
            \App\Models\User::class,
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
