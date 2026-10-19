<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'pelanggan_id',
        'user_id',
        'penjualan_id',
        'nama_member',
        'nomor_whatsapp',
        'kategori',
        'nama_template',
        'isi_pesan',
        'status',
        'waktu_kirim',
        'waktu_respon',
        'keterangan',
        'faktur_info',
        'total_tagihan',
        'metadata',
    ];

    protected $casts = [
        'waktu_kirim' => 'datetime',
        'waktu_respon' => 'datetime',
        'total_tagihan' => 'float',
        'metadata' => 'array',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            'pengingat_piutang' => 'Pengingat Piutang',
            'promosi_diskon' => 'Promosi / Diskon',
            'ucapan_apresiasi' => 'Ucapan',
            'informasi_produk' => 'Informasi Produk',
            'informasi_pengumuman' => 'Pengumuman',
            default => 'Pesan Custom',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'terkirim' => 'Terkirim',
            'menunggu' => 'Menunggu',
            'gagal' => 'Gagal',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'terkirim' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'menunggu' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'gagal' => 'bg-red-100 text-red-800 border border-red-200',
            'dibatalkan' => 'bg-gray-100 text-gray-700 border border-gray-200',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
