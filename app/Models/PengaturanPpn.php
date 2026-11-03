<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengaturanPpn extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_ppns';

    protected $fillable = [
        'nama',
        'tarif_persen',
        'metode_perhitungan',
        'persentase_dpp',
        'tipe_perlakuan_harga',
        'tanggal_mulai',
        'tanggal_akhir',
        'is_aktif',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tarif_persen' => 'decimal:2',
        'persentase_dpp' => 'decimal:4',
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
        'is_aktif' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'ppn_config_id');
    }

    /**
     * Cek apakah fitur PPN aktif secara global di Apotek.
     */
    public static function isPpnAktif(): bool
    {
        return (bool) (InfoApotek::singleton()->ppn_aktif ?? true);
    }

    /**
     * Dapatkan konfigurasi tarif PPN yang aktif untuk tanggal tertentu.
     */
    public static function getActiveForDate($date = null): ?self
    {
        if (! self::isPpnAktif()) {
            return null;
        }

        $date = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();

        return self::where('is_aktif', true)
            ->where('tanggal_mulai', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('tanggal_akhir')
                    ->orWhere('tanggal_akhir', '>=', $date);
            })
            ->orderBy('tanggal_mulai', 'desc')
            ->first();
    }

    /**
     * Validasi periode bertabrakan dengan konfigurasi aktif lain.
     */
    public static function findConflictPeriod($tanggalMulai, $tanggalAkhir = null, $excludeId = null): ?self
    {
        $start = Carbon::parse($tanggalMulai)->toDateString();
        $end = $tanggalAkhir ? Carbon::parse($tanggalAkhir)->toDateString() : '9999-12-31';

        $query = self::where('is_aktif', true);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->where('tanggal_mulai', '<=', $end)
            ->where(function ($q) use ($start) {
                $q->whereNull('tanggal_akhir')
                    ->orWhere('tanggal_akhir', '>=', $start);
            })
            ->first();
    }

    /**
     * Hitung tarif efektif jika menggunakan metode DPP Nilai Lain.
     */
    public function getTarifEfektifAttribute(): float
    {
        if ($this->metode_perhitungan === 'dpp_nilai_lain') {
            return round(((float) $this->tarif_persen * ((float) $this->persentase_dpp / 100)), 2);
        }

        return (float) $this->tarif_persen;
    }

    /**
     * Format rentang tanggal masa berlaku.
     */
    public function getPeriodeTeksAttribute(): string
    {
        $mulai = $this->tanggal_mulai ? $this->tanggal_mulai->translatedFormat('d M Y') : '-';
        $akhir = $this->tanggal_akhir ? $this->tanggal_akhir->translatedFormat('d M Y') : 'Sekarang (Seterusnya)';

        return "{$mulai} — {$akhir}";
    }

    /**
     * Hitung DPP, Nominal PPN, dan Total Faktur berdasarkan subtotal bersih.
     */
    public function hitung(float $subtotalBersih): array
    {
        $tarif = (float) $this->tarif_persen;
        $dppPersen = (float) $this->persentase_dpp;
        $metode = $this->metode_perhitungan;
        $tipeHarga = $this->tipe_perlakuan_harga;

        if ($tipeHarga === 'inklusif') {
            $tarifEfektifRatio = ($tarif / 100) * ($dppPersen / 100);
            $dpp = round($subtotalBersih / (1 + $tarifEfektifRatio), 2);
            $ppn = round($subtotalBersih - $dpp, 2);
            $total = $subtotalBersih;
        } else {
            // Eksklusif
            $dpp = round($subtotalBersih * ($dppPersen / 100), 2);
            $ppn = round($dpp * ($tarif / 100), 2);
            $total = round($subtotalBersih + $ppn, 2);
        }

        return [
            'subtotal_bersih' => round($subtotalBersih, 2),
            'dpp_nominal' => $dpp,
            'ppn_nominal' => $ppn,
            'total' => $total,
            'tarif_ppn_persen' => $tarif,
            'metode_ppn' => $metode,
            'persentase_dpp' => $dppPersen,
            'tipe_perlakuan_harga' => $tipeHarga,
        ];
    }
}

