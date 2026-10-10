<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beban extends Model
{
    use HasFactory;

    protected $table = 'bebans';

    protected $fillable = [
        'user_id',
        'tanggal',
        'kategori',
        'keterangan',
        'nominal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
