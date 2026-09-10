<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPenerimaan extends Model
{
    protected $fillable = [
        'penerimaan_id',
        'detail_pesanan_penerimaan_id',
        'detail_penerimaan_id',
        'jenis',
        'jumlah',
        'tanggal',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penerimaan(): BelongsTo
    {
        return $this->belongsTo(Penerimaan::class);
    }

    public function detailPesanan(): BelongsTo
    {
        return $this->belongsTo(DetailPesananPenerimaan::class, 'detail_pesanan_penerimaan_id');
    }

    public function detailPenerimaan(): BelongsTo
    {
        return $this->belongsTo(DetailPenerimaan::class);
    }
}
