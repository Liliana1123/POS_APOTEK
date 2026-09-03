<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $fillable = [
        'kode_apotek', 'kode_kfa', 'nama', 'merk', 'kategori_id', 'satuan_id', 'pabrik_id',
        'barcode', 'butuh_resep', 'stok_minimum', 'aktif',
    ];

    /**
     * Generate Kode Apotek otomatis dengan format HURUF-NOMOR (contoh: P-0001).
     * Huruf diambil dari alfabet pertama pada nama barang.
     * Nomor berupa nomor urut global 4 digit yang tidak reusable.
     */
    public static function generateKodeApotek(string $nama): string
    {
        if (preg_match('/[a-zA-Z]/', $nama, $matches)) {
            $prefix = strtoupper($matches[0]);
        } else {
            $prefix = 'B';
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($prefix) {
            if (\Illuminate\Support\Facades\Schema::hasTable('barang_kode_sequences')) {
                $seq = \Illuminate\Support\Facades\DB::table('barang_kode_sequences')
                    ->where('id', 1)
                    ->lockForUpdate()
                    ->first();

                if (!$seq) {
                    $maxExisting = 0;
                    $codes = self::whereNotNull('kode_apotek')->pluck('kode_apotek');
                    foreach ($codes as $code) {
                        if (preg_match('/^[A-Z]-(\d+)$/', $code, $m)) {
                            $num = (int) $m[1];
                            if ($num > $maxExisting) {
                                $maxExisting = $num;
                            }
                        }
                    }

                    \Illuminate\Support\Facades\DB::table('barang_kode_sequences')->insert([
                        'id' => 1,
                        'last_number' => $maxExisting,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $seq = \Illuminate\Support\Facades\DB::table('barang_kode_sequences')
                        ->where('id', 1)
                        ->lockForUpdate()
                        ->first();
                }

                $nextNumber = $seq->last_number + 1;
                \Illuminate\Support\Facades\DB::table('barang_kode_sequences')
                    ->where('id', 1)
                    ->update([
                        'last_number' => $nextNumber,
                        'updated_at' => now(),
                    ]);

                return sprintf('%s-%04d', $prefix, $nextNumber);
            }

            // Fallback aman jika tabel sequence belum tersedia
            $maxExisting = 0;
            $codes = self::whereNotNull('kode_apotek')->pluck('kode_apotek');
            foreach ($codes as $code) {
                if (preg_match('/^[A-Z]-(\d+)$/', $code, $m)) {
                    $num = (int) $m[1];
                    if ($num > $maxExisting) {
                        $maxExisting = $num;
                    }
                }
            }
            return sprintf('%s-%04d', $prefix, $maxExisting + 1);
        });
    }

    protected $casts = [
        'butuh_resep' => 'boolean',
        'aktif' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    public function pabrik(): BelongsTo
    {
        return $this->belongsTo(Pabrik::class);
    }

    public function detailPenerimaan(): HasMany
    {
        return $this->hasMany(DetailPenerimaan::class);
    }

    // Total stok dari semua batch yang masih aktif
    public function stokTotal(): int
    {
        if (array_key_exists('detail_penerimaan_sum_stok', $this->attributes)) {
            return (int) $this->attributes['detail_penerimaan_sum_stok'];
        }
        return $this->detailPenerimaan()->where('aktif', true)->sum('stok');
    }

    // Batch aktif & masih ada stok, diurutkan FEFO (expired paling dekat duluan)
    public function batchFefo()
    {
        return $this->detailPenerimaan()
            ->where('aktif', true)
            ->where('stok', '>', 0)
            ->orderBy('expired_date', 'asc');
    }

    public function stokMenipis(): bool
    {
        return $this->stokTotal() <= $this->stok_minimum;
    }

    // Harga jual dari batch paling depan (FEFO) - dipakai untuk tampilan estimasi harga di kasir.
    // Harga final tetap dihitung per-batch saat transaksi disimpan.
    public function hargaJualTerkini(): ?float
    {
        return $this->batchFefo()->first()?->harga_jual;
    }

    // Nama supplier terakhir dari riwayat penerimaan barang
    public function supplierNama(): string
    {
        $dp = $this->detailPenerimaan()
            ->with('penerimaan.supplier')
            ->latest('id')
            ->first();

        return $dp?->penerimaan?->supplier?->nama ?? '—';
    }
}
