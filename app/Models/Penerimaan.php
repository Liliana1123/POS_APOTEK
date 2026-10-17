<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Penerimaan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'supplier_id', 'telepon_supplier', 'keterangan',
        'tanggal', 'tanggal_faktur', 'no_faktur', 'ppn', 'lunas', 'jatuh_tempo',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_faktur' => 'date',
        'jatuh_tempo' => 'date',
        'lunas' => 'boolean',
        'ppn' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPenerimaan::class);
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesananPenerimaan::class);
    }

    public function riwayatPenerimaan(): HasMany
    {
        return $this->hasMany(RiwayatPenerimaan::class);
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranPenerimaan::class);
    }

    protected $canBeEditedMemo = null;
    protected $alasanTidakBisaDieditMemo = null;
    protected $statusPenerimaanMemo = null;
    protected $totalFakturMemo = null;
    protected $totalTagihanMemo = null;
    protected $totalDibayarMemo = null;
    protected $sisaTagihanMemo = null;

    public function totalFaktur(): float
    {
        if ($this->totalFakturMemo !== null) {
            return $this->totalFakturMemo;
        }
        if ($this->relationLoaded('detail')) {
            $this->totalFakturMemo = (float) $this->detail->sum(fn ($d) => (float) $d->harga_beli * (int) $d->jumlah);
            return $this->totalFakturMemo;
        }
        $this->totalFakturMemo = (float) $this->detail()->sum(DB::raw('harga_beli * jumlah'));
        return $this->totalFakturMemo;
    }

    public function totalTagihan(): float
    {
        if ($this->totalTagihanMemo !== null) {
            return $this->totalTagihanMemo;
        }
        $this->totalTagihanMemo = $this->totalFaktur() + (float) $this->ppn;
        return $this->totalTagihanMemo;
    }
    
    public function kelebihanPembayaran(): float
    {
        return max(0, $this->totalDibayar() - $this->totalTagihan());
    }

    public function totalDibayar(): float
    {
        if ($this->totalDibayarMemo !== null) {
            return $this->totalDibayarMemo;
        }
        // Check if withSum('pembayaran', 'jumlah') was used - it sets 'pembayaran_sum_jumlah' attribute
        if (array_key_exists('pembayaran_sum_jumlah', $this->attributes)) {
            $this->totalDibayarMemo = (float) $this->attributes['pembayaran_sum_jumlah'];
            return $this->totalDibayarMemo;
        }
        if ($this->relationLoaded('pembayaran')) {
            $this->totalDibayarMemo = (float) $this->pembayaran->sum('jumlah');
            return $this->totalDibayarMemo;
        }
        $this->totalDibayarMemo = (float) $this->pembayaran()->sum('jumlah');
        return $this->totalDibayarMemo;
    }

    public function sisaTagihan(): float
    {
        if ($this->sisaTagihanMemo !== null) {
            return $this->sisaTagihanMemo;
        }
        $this->sisaTagihanMemo = max(0, $this->totalTagihan() - $this->totalDibayar());
        return $this->sisaTagihanMemo;
    }

    public function statusPenerimaan(): string
    {
        if ($this->statusPenerimaanMemo !== null) {
            return $this->statusPenerimaanMemo;
        }
        $detailPesanan = $this->detailPesanan;

        $masihKurang = $detailPesanan->contains(function ($detail) {
            return $detail->kekurangan() > 0;
        });

        $this->statusPenerimaanMemo = $masihKurang ? 'BELUM LENGKAP' : 'LENGKAP';
        return $this->statusPenerimaanMemo;
    }

    public function hasBarangTerjual(): bool
    {
        return $this->detail()
            ->whereHas('detailPenjualan')
            ->exists();
    }

    public function hasBarangRusak(): bool
    {
        return $this->detail()
            ->whereHas('rusak')
            ->exists();
    }

    public function hasPembayaranSusulan(): bool
    {
        // Pembayaran susulan adalah pembayaran di luar pembayaran utama / pembayaran pertama
        // Combine into single query: exists susulan OR count > 1
        $susulanCount = $this->pembayaran()
            ->where(function ($q) {
                $q->whereNull('keterangan')
                    ->orWhere('keterangan', '!=', 'Pembayaran pertama');
            })
            ->count();
        return $susulanCount > 0 || $this->pembayaran()->count() > 1;
    }

    public function hasPenerimaanSusulan(): bool
    {
        return $this->riwayatPenerimaan()
            ->where(function ($q) {
                $q->where('jenis', 'pembatalan')
                    ->orWhere(function ($sub) {
                        $sub->where('jenis', 'penerimaan')
                            ->where('keterangan', '!=', 'Penerimaan awal');
                    });
            })
            ->exists();
    }

    public function canBeEdited(): bool
    {
        if ($this->canBeEditedMemo !== null) {
            return $this->canBeEditedMemo;
        }
        $this->canBeEditedMemo = !$this->hasBarangTerjual()
            && !$this->hasBarangRusak()
            && !$this->hasPembayaranSusulan()
            && !$this->hasPenerimaanSusulan();
        return $this->canBeEditedMemo;
    }

    public function alasanTidakBisaDiedit(): ?string
    {
        if ($this->alasanTidakBisaDieditMemo !== null) {
            return $this->alasanTidakBisaDieditMemo;
        }
        $alasan = [];

        if ($this->hasBarangTerjual()) {
            $alasan[] = 'sebagian barang sudah terjual';
        }

        if ($this->hasBarangRusak()) {
            $alasan[] = 'sebagian barang tercatat sebagai barang rusak';
        }

        if ($this->hasPembayaranSusulan()) {
            $alasan[] = 'sudah memiliki pembayaran susulan';
        }

        if ($this->hasPenerimaanSusulan()) {
            $alasan[] = 'sudah memiliki penerimaan susulan';
        }

        if (empty($alasan)) {
            $this->alasanTidakBisaDieditMemo = null;
            return null;
        }

        $this->alasanTidakBisaDieditMemo = 'Penerimaan tidak dapat diedit karena ' . implode(', ', $alasan) . '.';
        return $this->alasanTidakBisaDieditMemo;
    }
}
