<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $fillable = [
        'nama', 'kategori_id', 'satuan_id', 'pabrik_id',
        'barcode', 'butuh_resep', 'stok_minimum', 'aktif',
    ];

    protected $casts = [
        'butuh_resep' => 'boolean',
        'aktif' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    public function pabrik(): BelongsTo
    {
        return $this->belongsTo(Pabrik::class);
    }

    public function detailPenerimaan(): HasMany
    {
        return $this->hasMany(DetailPenerimaan::class);
    }

    // Total stok dari semua batch yang masih aktif
    public function stokTotal(): int
    {
        if (array_key_exists('detail_penerimaan_sum_stok', $this->attributes)) {
            return (int) $this->attributes['detail_penerimaan_sum_stok'];
        }
        return $this->detailPenerimaan()->where('aktif', true)->sum('stok');
    }

    // Batch aktif & masih ada stok, diurutkan FEFO (expired paling dekat duluan)
    public function batchFefo()
    {
        return $this->detailPenerimaan()
            ->where('aktif', true)
            ->where('stok', '>', 0)
            ->orderBy('expired_date', 'asc');
    }

    public function stokMenipis(): bool
    {
        return $this->stokTotal() <= $this->stok_minimum;
    }

    // Harga jual dari batch paling depan (FEFO) - dipakai untuk tampilan estimasi harga di kasir.
    // Harga final tetap dihitung per-batch saat transaksi disimpan.
    public function hargaJualTerkini(): ?float
    {
        return $this->batchFefo()->first()?->harga_jual;
    }
}
