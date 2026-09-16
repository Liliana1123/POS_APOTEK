<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    public const CATEGORY_PENJUALAN = 'penjualan';
    public const CATEGORY_MEMBER = 'member';
    public const CATEGORY_PROMO = 'promo';
    public const CATEGORY_INVENTARIS = 'inventaris';
    public const CATEGORY_KEUANGAN = 'keuangan';
    public const CATEGORY_KEAMANAN = 'keamanan';
    public const CATEGORY_SISTEM = 'sistem';

    public $timestamps = false; // only created_at is used

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'kategori',
        'target',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Resolve category from action name if not provided.
     */
    public static function resolveKategori(string $action): string
    {
        $actionLower = strtolower($action);

        if (str_contains($actionLower, 'penjualan') || str_contains($actionLower, 'transaksi')) {
            return self::CATEGORY_PENJUALAN;
        }
        if (str_contains($actionLower, 'member') || str_contains($actionLower, 'pelanggan')) {
            return self::CATEGORY_MEMBER;
        }
        if (str_contains($actionLower, 'promo') || str_contains($actionLower, 'diskon')) {
            return self::CATEGORY_PROMO;
        }
        if (str_contains($actionLower, 'rusak') || str_contains($actionLower, 'penerimaan') || str_contains($actionLower, 'barang') || str_contains($actionLower, 'obat') || str_contains($actionLower, 'stok')) {
            return self::CATEGORY_INVENTARIS;
        }
        if (str_contains($actionLower, 'piutang') || str_contains($actionLower, 'pembayaran') || str_contains($actionLower, 'kas')) {
            return self::CATEGORY_KEUANGAN;
        }
        if (str_contains($actionLower, 'login') || str_contains($actionLower, 'logout') || str_contains($actionLower, 'user') || str_contains($actionLower, 'password')) {
            return self::CATEGORY_KEAMANAN;
        }

        return self::CATEGORY_SISTEM;
    }

    /**
     * Log a user activity.
     */
    public static function log(string $action, ?string $target = null, ?string $kategori = null): self
    {
        $user = auth()->user();
        return self::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'System',
            'action' => $action,
            'kategori' => $kategori ?? self::resolveKategori($action),
            'target' => $target,
            'created_at' => now(),
        ]);
    }
}
