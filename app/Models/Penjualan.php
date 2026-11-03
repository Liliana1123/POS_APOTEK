<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    use Filterable;
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
        'kenakan_ppn',
        'ppn_config_id',
        'tarif_ppn_persen',
        'metode_ppn',
        'persentase_dpp',
        'tipe_perlakuan_harga',
        'subtotal_bersih',
        'dpp_nominal',
        'ppn_nominal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'due_date' => 'date',
        'kenakan_ppn' => 'boolean',
        'tarif_ppn_persen' => 'decimal:2',
        'persentase_dpp' => 'decimal:4',
        'subtotal_bersih' => 'decimal:2',
        'dpp_nominal' => 'decimal:2',
        'ppn_nominal' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function pengaturanPpn(): BelongsTo
    {
        return $this->belongsTo(PengaturanPpn::class, 'ppn_config_id');
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
