<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rusak extends Model
{
    use Filterable;

    protected $fillable = ['detail_penerimaan_id', 'tanggal', 'jumlah', 'keterangan'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function detailPenerimaan(): BelongsTo
    {
        return $this->belongsTo(DetailPenerimaan::class);
    }
}
