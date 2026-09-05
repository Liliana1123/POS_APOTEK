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
        'tanggal', 'no_faktur', 'lunas', 'jatuh_tempo',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jatuh_tempo' => 'date',
        'lunas' => 'boolean',
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

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranPenerimaan::class);
    }

    public function totalFaktur(): float
    {
        return (float) $this->detail()->sum(DB::raw('harga_beli * jumlah'));
    }

    public function totalDibayar(): float
    {
        return (float) $this->pembayaran()->sum('jumlah');
    }

    public function sisaTagihan(): float
    {
        return max(0, $this->totalFaktur() - $this->totalDibayar());
    }
}
