<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailPesananPenerimaan extends Model
{
    protected $fillable = [
        'penerimaan_id',
        'barang_id',
        'jumlah_dipesan',
    ];

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(Penerimaan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function totalDiterima(): int
    {
        return (int) $this->riwayatPenerimaan()
            ->where('jenis', 'penerimaan')
            ->sum('jumlah');
    }

    public function totalDibatalkan(): int
    {
        return (int) $this->riwayatPenerimaan()
            ->where('jenis', 'pembatalan')
            ->sum('jumlah');
    }

    public function kekurangan(): int
    {
        return max(
            0,
            (int) $this->jumlah_dipesan
            - $this->totalDiterima()
            - $this->totalDibatalkan()
        );
    }

    public function riwayatPenerimaan(): HasMany
    {
        return $this->hasMany(
            RiwayatPenerimaan::class,
            'detail_pesanan_penerimaan_id'
        );
    }
}
