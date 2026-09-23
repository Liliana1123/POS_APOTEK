<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DashboardCacheService
{
    /**
     * Waktu cache wajar (dalam menit), default 5 menit.
     */
    const TTL_MINUTES = 5;

    protected static ?int $currentVersion = null;

    /**
     * Ambil versi cache dashboard saat ini.
     */
    public static function getVersion(): int
    {
        if (static::$currentVersion !== null) {
            return static::$currentVersion;
        }
        $val = Cache::get('dashboard_cache_version');
        if (!$val) {
            $val = time();
            Cache::forever('dashboard_cache_version', $val);
        }
        return static::$currentVersion = (int) $val;
    }

    /**
     * Generate key cache berbasis versi dan parameter.
     */
    public static function key(string $name, array $params = []): string
    {
        $version = self::getVersion();
        $paramHash = !empty($params) ? '_' . md5(json_encode($params)) : '';
        return "dashboard_v{$version}_{$name}{$paramHash}";
    }

    /**
     * Jalankan cache remember dengan key versi dashboard.
     */
    public static function remember(string $name, array $params, \Closure $callback)
    {
        $cacheKey = self::key($name, $params);
        return Cache::remember($cacheKey, now()->addMinutes(self::TTL_MINUTES), $callback);
    }

    /**
     * Invalidate cache dashboard secara instan ketika ada transaksi stok/keuangan baru.
     */
    public static function clear(): void
    {
        static::$currentVersion = null;
        Cache::forever('dashboard_cache_version', time());
    }
}
