<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $fillable = ['nama', 'telepon', 'member_id', 'is_member', 'member_aktif', 'member_since', 'saldo_piutang'];

    protected $casts = [
        'is_member' => 'boolean',
        'member_aktif' => 'boolean',
        'member_since' => 'date',
        'saldo_piutang' => 'decimal:2',
    ];

    public static function generateMemberId(): string
    {
        $lastNumber = self::whereNotNull('member_id')
            ->where('member_id', 'like', 'MBR-%')
            ->pluck('member_id')
            ->map(fn (string $memberId): int => (int) substr($memberId, 4))
            ->max();

        return sprintf('MBR-%06d', $lastNumber === null ? 0 : $lastNumber + 1);
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    public function discountUsages()
    {
        return $this->hasManyThrough(DiscountUsage::class, Penjualan::class, 'pelanggan_id', 'penjualan_id');
    }
}
