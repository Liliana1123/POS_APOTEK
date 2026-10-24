<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    use HasFactory, Filterable;

    protected $table = 'whatsapp_logs';

    public const STATUS_FOLLOW_UP_BELUM = 'belum_ditindaklanjuti';
    public const STATUS_FOLLOW_UP_MENUNGGU = 'menunggu_respons';
    public const STATUS_FOLLOW_UP_SUDAH_DATANG = 'sudah_datang';
    public const STATUS_FOLLOW_UP_TIDAK_DAPAT = 'tidak_dapat_dihubungi';

    // Status tambahan lama untuk kompatibilitas
    public const STATUS_FOLLOW_UP_MERESPONS = 'merespons';
    public const STATUS_FOLLOW_UP_AKAN_DATANG = 'akan_datang';
    public const STATUS_FOLLOW_UP_PERLU_HUBUNGI = 'perlu_dihubungi_kembali';
    public const STATUS_FOLLOW_UP_SELESAI = 'selesai';

    protected $attributes = [
        'status_follow_up' => self::STATUS_FOLLOW_UP_BELUM,
    ];

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
        'status_follow_up',
        'catatan_follow_up',
        'waktu_follow_up',
        'follow_up_user_id',
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
        'waktu_follow_up' => 'datetime',
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

    public function followUpUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_up_user_id');
    }

    public function followUpHistories()
    {
        return $this->hasMany(WhatsAppFollowUpHistory::class, 'whatsapp_log_id')->orderBy('created_at', 'desc')->orderBy('id', 'desc');
    }

    public static function getStatusFollowUpOptions(): array
    {
        return [
            self::STATUS_FOLLOW_UP_BELUM => 'Belum ditindaklanjuti',
            self::STATUS_FOLLOW_UP_MENUNGGU => 'WhatsApp terkirim, belum ada respons',
            self::STATUS_FOLLOW_UP_SUDAH_DATANG => 'Member sudah datang ke apotek',
            self::STATUS_FOLLOW_UP_TIDAK_DAPAT => 'Tidak dapat dihubungi',
        ];
    }

    public function getStatusFollowUpLabelAttribute(): string
    {
        return WhatsAppFollowUpHistory::getStatusLabel($this->status_follow_up ?? self::STATUS_FOLLOW_UP_BELUM);
    }

    public function getStatusFollowUpBadgeClassAttribute(): string
    {
        return WhatsAppFollowUpHistory::getStatusBadgeClass($this->status_follow_up ?? self::STATUS_FOLLOW_UP_BELUM);
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
