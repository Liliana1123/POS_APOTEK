<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait GeneratesCode
{
    /**
     * Generate sequential code with prefix and padding
     * Contoh: P-0001, TRX-20261022-0001
     *
     * @param string $prefix
     * @param string $column
     * @param int $digits
     * @param string|null $datePrefix format seperti 'Ymd' atau 'ymd'
     * @return string
     */
    public static function generateSequentialCode(
        string $prefix,
        string $column = 'kode',
        int $digits = 4,
        ?string $datePrefix = null
    ): string {
        $fullPrefix = $prefix;
        if ($datePrefix) {
            $fullPrefix .= date($datePrefix) . '-';
        } else {
            $fullPrefix .= '-';
        }

        $lastRecord = static::query()
            ->where($column, 'like', $fullPrefix . '%')
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;
        if ($lastRecord && preg_match('/-(\d+)$/', (string) $lastRecord->{$column}, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return $fullPrefix . str_pad((string) $nextNumber, $digits, '0', STR_PAD_LEFT);
    }
}
