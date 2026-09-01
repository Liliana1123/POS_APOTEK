<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pabrik extends Model
{
    protected $fillable = ['nama'];

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
