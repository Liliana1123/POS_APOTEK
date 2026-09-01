<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailPenerimaan extends Model
{
    protected $fillable = [
        'penerimaan_id', 'barang_id', 'no_batch', 'harga_beli', 'harga_jual',
        'expired_date', 'jumlah', 'stok', 'aktif',
    ];

    protected $casts = [
        'expired_date' => 'date',
        'aktif' => 'boolean',
    ];

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(Penerimaan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function rusak(): HasMany
    {
        return $this->hasMany(Rusak::class);
    }

    // Batch yang expired_date-nya <= $hari dari sekarang
    public function scopeMendekatiExpired(Builder $query, int $hari = 90): Builder
    {
        return $query->where('expired_date', '<=', now()->addDays($hari));
    }

    public function sudahExpired(): bool
    {
        return $this->expired_date->isPast();
    }
}
