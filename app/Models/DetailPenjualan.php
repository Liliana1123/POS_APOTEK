<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenjualan extends Model
{
    protected $fillable = [
        'penjualan_id', 'detail_penerimaan_id', 'harga_jual', 'jumlah', 'diskon', 'subtotal',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function detailPenerimaan(): BelongsTo
    {
        return $this->belongsTo(DetailPenerimaan::class);
    }

    // Ambil nama barang lewat batch-nya.
    // Dipakai untuk 1 baris. Untuk list/laporan, pakai eager load:
    // DetailPenjualan::with('detailPenerimaan.barang')->get();
    public function barang()
    {
        return $this->detailPenerimaan?->barang;
    }
}
