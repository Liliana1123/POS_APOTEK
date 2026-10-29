<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use Filterable;

    protected $fillable = ['nama', 'telepon', 'alamat', 'pic'];

    public function penerimaan(): HasMany
    {
        return $this->hasMany(Penerimaan::class);
    }
}
