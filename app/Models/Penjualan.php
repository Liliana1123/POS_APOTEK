<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $fillable = [
        'user_id',
        'pelanggan_id',
        'tanggal',
        'no_faktur',
        'total',
        'metode_pembayaran',
        'due_date',
        'jenis_transaksi',
        'nama_dokter',
        'id_dokter',
        'alamat_lembaga',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function pembayaranPiutang(): HasMany
    {
        return $this->hasMany(PembayaranPiutang::class);
    }

    public function discountUsages(): HasMany
    {
        return $this->hasMany(DiscountUsage::class);
    }
}
