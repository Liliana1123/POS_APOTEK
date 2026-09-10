<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPiutang extends Model
{
    protected $fillable = [
        'penjualan_id',
        'pelanggan_id',
        'user_id',
        'tanggal_bayar',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}