<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'tanggal_lahir',
        'keterangan',
        'member_id',
        'is_member',
        'member_aktif',
        'member_since',
        'saldo_piutang',
        'status_member',
    ];

    protected $casts = [
        'is_member' => 'boolean',
        'member_aktif' => 'boolean',
        'member_since' => 'date',
        'tanggal_lahir' => 'date',
        'saldo_piutang' => 'decimal:2',
    ];

    public static function generateMemberId(): string
    {
        $lastNumber = self::whereNotNull('member_id')
            ->where('member_id', 'like', 'MBR-%')
            ->pluck('member_id')
            ->map(function ($id) {
                return (int) substr($id, 4);
            })
            ->max();

        return sprintf(
            'MBR-%06d',
            $lastNumber === null ? 1 : $lastNumber + 1
        );
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    public function discountUsages()
    {
        return $this->hasManyThrough(
            DiscountUsage::class,
            Penjualan::class,
            'pelanggan_id',
            'penjualan_id'
        );
    }
}