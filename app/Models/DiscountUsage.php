<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountUsage extends Model
{
    protected $fillable = [
        'penjualan_id',
        'detail_penjualan_id',
        'barang_id',
        'barang_nama',
        'jenis',
        'custom_discount_id',
        'custom_discount_nama',
        'persentase',
        'nominal',
    ];

    protected $casts = [
        'persentase' => 'integer',
        'nominal' => 'decimal:2',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function customDiscount(): BelongsTo
    {
        return $this->belongsTo(CustomDiscount::class);
    }
}
