<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penerimaan extends Model
{
    protected $fillable = ['user_id', 'supplier_id', 'tanggal', 'no_faktur', 'lunas'];

    protected $casts = [
        'tanggal' => 'date',
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
}
