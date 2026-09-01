<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class CustomDiscount extends Model
{
    protected $fillable = [
        'nama',
        'persentase',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
        'cakupan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
    ];

    public function kategoris(): BelongsToMany
    {
        return $this->belongsToMany(Kategori::class, 'custom_discount_categories', 'custom_discount_id', 'kategori_id');
    }

    public function barangs(): BelongsToMany
    {
        return $this->belongsToMany(Barang::class, 'custom_discount_barangs', 'custom_discount_id', 'barang_id');
    }

    // Scope for active today
    public function scopeAktifHariIni(Builder $query): Builder
    {
        $today = now()->format('Y-m-d');
        return $query->where('aktif', true)
            ->where('tanggal_mulai', '<=', $today)
            ->where('tanggal_selesai', '>=', $today);
    }

    /**
     * Get the active custom discount percentage for a specific barang.
     * Since validation prevents overlaps, at most one active promo will match.
     */
    public static function getPercentForBarang(Barang $barang): int
    {
        $activePromos = self::aktifHariIni()->with(['kategoris', 'barangs'])->get();

        foreach ($activePromos as $promo) {
            if ($promo->cakupan === 'semua') {
                return $promo->persentase;
            }

            if ($promo->cakupan === 'kategori') {
                if ($promo->kategoris->contains($barang->kategori_id)) {
                    return $promo->persentase;
                }
            }

            if ($promo->cakupan === 'barang') {
                if ($promo->barangs->contains($barang->id)) {
                    return $promo->persentase;
                }
            }

            if ($promo->cakupan === 'kombinasi') {
                // UNION logic: category match OR individual barang match
                if ($promo->kategoris->contains($barang->kategori_id) || $promo->barangs->contains($barang->id)) {
                    return $promo->persentase;
                }
            }
        }

        return 0;
    }
}
