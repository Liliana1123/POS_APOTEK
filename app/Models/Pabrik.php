<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pabrik extends Model
{
    use Filterable;

    protected $fillable = ['nama', 'telepon', 'alamat'];

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
