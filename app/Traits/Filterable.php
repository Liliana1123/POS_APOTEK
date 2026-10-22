<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

trait Filterable
{
    /**
     * Scope pencarian multi-kolom
     *
     * @param Builder $query
     * @param array $columns
     * @param string|null $term
     * @return Builder
     */
    public function scopeSearch(Builder $query, array $columns, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($columns, $term) {
            foreach ($columns as $index => $col) {
                if (str_contains($col, '.')) {
                    // Handle relasi misal: 'kategori.nama'
                    [$relation, $relCol] = explode('.', $col, 2);
                    $q->orWhereHas($relation, function (Builder $rq) use ($relCol, $term) {
                        $rq->where($relCol, 'like', "%{$term}%");
                    });
                } else {
                    $q->orWhere($col, 'like', "%{$term}%");
                }
            }
        });
    }

    /**
     * Scope rentang tanggal (inklusif dari jam 00:00:00 s/d 23:59:59)
     *
     * @param Builder $query
     * @param string $column
     * @param string|null $from
     * @param string|null $to
     * @return Builder
     */
    public function scopeDateRange(Builder $query, string $column, ?string $from, ?string $to): Builder
    {
        if (!empty($from)) {
            $query->where($column, '>=', Carbon::parse($from)->startOfDay());
        }

        if (!empty($to)) {
            $query->where($column, '<=', Carbon::parse($to)->endOfDay());
        }

        return $query;
    }

    /**
     * Scope filter status aktif / nonaktif
     *
     * @param Builder $query
     * @param string $column
     * @param mixed $status
     * @return Builder
     */
    public function scopeFilterStatus(Builder $query, string $column = 'aktif', mixed $status = null): Builder
    {
        if ($status !== null && $status !== '') {
            $query->where($column, (bool) $status);
        }

        return $query;
    }
}
