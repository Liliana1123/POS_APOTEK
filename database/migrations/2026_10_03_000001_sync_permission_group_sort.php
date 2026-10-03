<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sort = 0;

        foreach (config('permission.pages') as $slug => $meta) {
            DB::table('permissions')
                ->where('slug', $slug)
                ->update([
                    'group' => $meta['group'],
                    'sort' => ++$sort,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Kolom group/sort sepenuhnya diturunkan dari config('permission.pages'),
        // jadi tidak ada nilai lama yang perlu dipulihkan.
    }
};
