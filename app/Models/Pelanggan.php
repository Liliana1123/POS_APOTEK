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
        'custom_discount_percentage',
    ];

    protected $casts = [
        'is_member' => 'boolean',
        'member_aktif' => 'boolean',
        'member_since' => 'date',
        'tanggal_lahir' => 'date',
        'saldo_piutang' => 'decimal:2',
        'custom_discount_percentage' => 'decimal:2',
    ];

    public function getDiskonPercentAttribute(): float
    {
        if (!$this->is_member || !($this->member_aktif ?? false)) {
            return 0;
        }

        return (float) ($this->custom_discount_percentage !== null
            ? $this->custom_discount_percentage
            : config('pos.diskon_member', 10));
    }

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

    public function getJatuhTempoAktifAttribute(): ?\Illuminate\Support\Carbon
    {
        if (($this->saldo_piutang ?? 0) <= 0) {
            return null;
        }

        $penjualanPiutang = $this->relationLoaded('penjualan')
            ? $this->penjualan->filter(fn ($p) => $p->metode_pembayaran === 'piutang')
            : $this->penjualan()->where('metode_pembayaran', 'piutang')->with('pembayaranPiutang')->get();

        $unpaid = $penjualanPiutang->filter(function ($p) {
            $dibayar = $p->relationLoaded('pembayaranPiutang')
                ? $p->pembayaranPiutang->sum('jumlah')
                : $p->pembayaranPiutang()->sum('jumlah');
            return ($p->total - $dibayar) > 0;
        })->sortBy(function ($p) {
            return $p->due_date ? $p->due_date->timestamp : ($p->tanggal ? $p->tanggal->timestamp : PHP_INT_MAX);
        })->first();

        return $unpaid?->due_date;
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