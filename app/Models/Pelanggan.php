<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $fillable = ['nama', 'telepon', 'member_id', 'is_member', 'member_since', 'saldo_piutang'];

    protected $casts = [
        'is_member' => 'boolean',
        'member_since' => 'date',
        'saldo_piutang' => 'decimal:2',
    ];

    public static function generateMemberId(): string
    {
        $lastMember = self::whereNotNull('member_id')
            ->orderBy('member_id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastMember) {
            $lastNumber = (int) str_replace('MBR-', '', $lastMember->member_id);
            $nextNumber = $lastNumber + 1;
        }

        return 'MBR-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
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
