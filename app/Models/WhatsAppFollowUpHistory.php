<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppFollowUpHistory extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_follow_up_histories';

    protected $fillable = [
        'whatsapp_log_id',
        'user_id',
        'status_sebelum',
        'status_sesudah',
        'catatan',
    ];

    public function whatsappLog(): BelongsTo
    {
        return $this->belongsTo(WhatsAppLog::class, 'whatsapp_log_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function getStatusLabel(?string $status): string
    {
        return match ($status) {
            WhatsAppLog::STATUS_FOLLOW_UP_BELUM => 'Belum ditindaklanjuti',
            WhatsAppLog::STATUS_FOLLOW_UP_MENUNGGU => 'WhatsApp terkirim, belum ada respons',
            WhatsAppLog::STATUS_FOLLOW_UP_MERESPONS => 'Member sudah merespons',
            WhatsAppLog::STATUS_FOLLOW_UP_AKAN_DATANG => 'Member akan datang ke apotek',
            WhatsAppLog::STATUS_FOLLOW_UP_SUDAH_DATANG => 'Member sudah datang ke apotek',
            WhatsAppLog::STATUS_FOLLOW_UP_PERLU_HUBUNGI => 'Perlu dihubungi kembali',
            WhatsAppLog::STATUS_FOLLOW_UP_TIDAK_DAPAT => 'Tidak dapat dihubungi',
            WhatsAppLog::STATUS_FOLLOW_UP_SELESAI => 'Selesai',
            default => $status ? ucwords(str_replace('_', ' ', $status)) : 'Belum ditindaklanjuti',
        };
    }

    public static function getStatusBadgeClass(?string $status): string
    {
        return match ($status) {
            WhatsAppLog::STATUS_FOLLOW_UP_BELUM => 'bg-gray-100 text-gray-700 border border-gray-200',
            WhatsAppLog::STATUS_FOLLOW_UP_MENUNGGU => 'bg-amber-100 text-amber-800 border border-amber-200',
            WhatsAppLog::STATUS_FOLLOW_UP_SUDAH_DATANG => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            WhatsAppLog::STATUS_FOLLOW_UP_TIDAK_DAPAT => 'bg-rose-100 text-rose-800 border border-rose-200',
            WhatsAppLog::STATUS_FOLLOW_UP_MERESPONS => 'bg-sky-100 text-sky-800 border border-sky-200',
            WhatsAppLog::STATUS_FOLLOW_UP_AKAN_DATANG => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
            WhatsAppLog::STATUS_FOLLOW_UP_PERLU_HUBUNGI => 'bg-orange-100 text-orange-800 border border-orange-200',
            WhatsAppLog::STATUS_FOLLOW_UP_SELESAI => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            default => 'bg-gray-100 text-gray-700 border border-gray-200',
        };
    }

    public function getStatusSebelumLabelAttribute(): string
    {
        return self::getStatusLabel($this->status_sebelum);
    }

    public function getStatusSesudahLabelAttribute(): string
    {
        return self::getStatusLabel($this->status_sesudah);
    }

    public function getStatusSesudahBadgeClassAttribute(): string
    {
        return self::getStatusBadgeClass($this->status_sesudah);
    }
}
