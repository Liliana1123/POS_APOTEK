<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ['nama', 'telepon', 'alamat'];

    public function penerimaan(): HasMany
    {
        return $this->hasMany(Penerimaan::class);
    }
}
