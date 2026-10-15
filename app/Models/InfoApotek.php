<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoApotek extends Model
{
    protected $fillable = [
        'nama_apotek',
        'nama_pemilik',
        'alamat',
        'alamat_jalan',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'telepon',
        'email',
        'no_izin_sia',
        'nama_apoteker_pj',
        'no_sipa',
        'logo',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            $parts = array_filter([
                $model->alamat_jalan,
                $model->kelurahan ? 'Kel. ' . $model->kelurahan : null,
                $model->kecamatan ? 'Kec. ' . $model->kecamatan : null,
                $model->kota,
                $model->provinsi,
                $model->kode_pos,
            ]);

            if (!empty($parts)) {
                $model->alamat = implode(', ', $parts);
            }
        });
    }

    public function getAlamatLengkapAttribute(): string
    {
        $parts = array_filter([
            $this->alamat_jalan,
            $this->kelurahan ? 'Kel. ' . $this->kelurahan : null,
            $this->kecamatan ? 'Kec. ' . $this->kecamatan : null,
            $this->kota,
            $this->provinsi,
            $this->kode_pos,
        ]);

        if (!empty($parts)) {
            return implode(', ', $parts);
        }

        return (string) ($this->attributes['alamat'] ?? '');
    }

    public static function singleton(): self
    {
        return static::query()->firstOrCreate([], ['nama_apotek' => 'Apotek']);
    }
}