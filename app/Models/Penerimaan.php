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

    public function pembayaran(): HasMany
    {
        return $this->hasMany(PembayaranPenerimaan::class);
    }

    public function totalFaktur(): float
    {
        return (float) $this->detail()->sum(DB::raw('harga_beli * jumlah'));
    }

    public function totalTagihan(): float
    {
        return $this->totalFaktur() + (float) $this->ppn;
    }
    
    public function kelebihanPembayaran(): float
    {
        return max(0, $this->totalDibayar() - $this->totalTagihan());
    }

    public function totalDibayar(): float
    {
        return (float) $this->pembayaran()->sum('jumlah');
    }

    public function sisaTagihan(): float
    {
        return max(0, $this->totalTagihan() - $this->totalDibayar());
    }
}
