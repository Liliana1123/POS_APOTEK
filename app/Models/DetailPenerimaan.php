<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPenerimaan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'penerimaan_id', 'barang_id', 'no_batch', 'harga_beli', 'harga_jual',
        'expired_date', 'no_rak', 'jumlah', 'stok', 'aktif',
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

    // Batch yang expired_date-nya berada dalam $hari dari hari ini dan belum kadaluarsa
    public function scopeMendekatiExpired(Builder $query, int $hari = 90): Builder
    {
        $today = now()->startOfDay();

        return $query->whereNotNull('expired_date')
            ->whereDate('expired_date', '>', $today)
            ->whereDate('expired_date', '<=', $today->copy()->addDays($hari));
    }

    public function sudahExpired(): bool
    {
        return $this->expired_date->isPast();
    }
}
