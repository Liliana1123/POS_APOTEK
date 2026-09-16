<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoApotek extends Model
{
    protected $fillable = [
        'nama_apotek',
        'nama_pemilik',
        'alamat',
        'telepon',
        'email',
        'no_izin_sia',
        'nama_apoteker_pj',
        'no_sipa',
        'logo',
    ];

    public static function singleton(): self
    {
        return static::query()->firstOrCreate([], ['nama_apotek' => 'Apotek']);
    }
}