<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\GeneratesCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    use Filterable, GeneratesCode;
    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'tanggal_lahir',
        'keterangan',
        'member_id',
        'is_member',
        'member_aktif',
        'member_since',
        'saldo_piutang',
        'status_member',
        'custom_discount_percentage',
    ];

    protected $casts = [
        'is_member' => 'boolean',
        'member_aktif' => 'boolean',
        'member_since' => 'date',
        'tanggal_lahir' => 'date',
        'saldo_piutang' => 'decimal:2',
        'custom_discount_percentage' => 'decimal:2',
    ];

    public function getDiskonPercentAttribute(): float
    {
        if (!$this->is_member || !($this->member_aktif ?? false)) {
            return 0;
        }

        return (float) ($this->custom_discount_percentage !== null
            ? $this->custom_discount_percentage
            : config('pos.diskon_member', 10));
    }

    public static function generateMemberId(): string
    {
        return self::generateSequentialCode('MBR', 'member_id', 6);
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }

    public function getJatuhTempoAktifAttribute(): ?\Illuminate\Support\Carbon
    {
        if (($this->saldo_piutang ?? 0) <= 0) {
            return null;
        }

        $penjualanPiutang = $this->relationLoaded('penjualan')
            ? $this->penjualan->filter(fn ($p) => $p->metode_pembayaran === 'piutang')
            : $this->penjualan()->where('metode_pembayaran', 'piutang')->with('pembayaranPiutang')->get();

        $unpaid = $penjualanPiutang->filter(function ($p) {
            $dibayar = $p->relationLoaded('pembayaranPiutang')
                ? $p->pembayaranPiutang->sum('jumlah')
                : $p->pembayaranPiutang()->sum('jumlah');
            return ($p->total - $dibayar) > 0;
        })->sortBy(function ($p) {
            return $p->due_date ? $p->due_date->timestamp : ($p->tanggal ? $p->tanggal->timestamp : PHP_INT_MAX);
        })->first();

        return $unpaid?->due_date;
    }

    public function discountUsages()
    {
        return $this->hasManyThrough(
            DiscountUsage::class,
            Penjualan::class,
            'pelanggan_id',
            'penjualan_id'
        );
    }

    /**
     * Mengambil informasi tagihan piutang yang memenuhi syarat pengingat WhatsApp.
     * Mengembalikan null jika member lunas, belum masuk periode H-3, atau nomor telepon kosong.
     */
    public function getPengingatPiutangAttribute(): ?array
    {
        if (!$this->is_member || !$this->member_aktif) {
            return null;
        }

        if (($this->saldo_piutang ?? 0) <= 0) {
            return null;
        }

        $telepon = trim((string) $this->telepon);
        if ($telepon === '') {
            return null;
        }

        $today = \Illuminate\Support\Carbon::today();

        $penjualanPiutang = $this->relationLoaded('penjualan')
            ? $this->penjualan->filter(fn ($p) => $p->metode_pembayaran === 'piutang')
            : $this->penjualan()->where('metode_pembayaran', 'piutang')->with('pembayaranPiutang')->get();

        $tagihanEligible = $penjualanPiutang->map(function ($p) use ($today) {
            $dueDate = $p->due_date ?? ($p->tanggal ? $p->tanggal->copy()->addMonth() : null);
            if (!$dueDate) {
                return null;
            }

            $dibayar = (float) ($p->relationLoaded('pembayaranPiutang')
                ? $p->pembayaranPiutang->sum('jumlah')
                : $p->pembayaranPiutang()->sum('jumlah'));

            $sisa = max(0, (float) $p->total - $dibayar);
            if ($sisa <= 0) {
                return null;
            }

            $hariMenujuJatuhTempo = (int) $today->diffInDays($dueDate->copy()->startOfDay(), false);

            return [
                'penjualan_id' => $p->id,
                'no_faktur' => $p->no_faktur,
                'sisa' => $sisa,
                'due_date' => $dueDate,
                'hari' => $hariMenujuJatuhTempo,
            ];
        })->filter()->sortBy(fn ($t) => $t['due_date']->timestamp)->values();

        if ($tagihanEligible->isEmpty()) {
            return null;
        }

        $totalSisa = $tagihanEligible->sum('sisa');
        $earliestDue = $tagihanEligible->first()['due_date'];

        return [
            'total_sisa' => $totalSisa,
            'due_date' => $earliestDue,
            'jatuh_tempo_formatted' => $earliestDue->format('d M Y'),
            'jatuh_tempo_pesan' => $earliestDue->format('d-m-Y'),
            'primary_penjualan_id' => $tagihanEligible->first()['penjualan_id'],
            'total_tagihan_count' => $tagihanEligible->count(),
        ];
    }
}