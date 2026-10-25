<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $pages = config('permission.pages');
        $defaults = config('permission.defaults');
        $roles = config('permission.roles');

        // 1. Hapus permission dashboard lama yang digantikan granularitas baru
        $oldSlugs = [
            'dashboard.omzet-grafik',
            'dashboard.laba-margin',
            'dashboard.keuangan-piutang',
        ];
        DB::table('permissions')->whereIn('slug', $oldSlugs)->delete();

        // 2. Upsert seluruh permission dari config('permission.pages')
        $sort = 0;
        foreach ($pages as $slug => $meta) {
            $sort++;
            $perm = DB::table('permissions')->where('slug', $slug)->first();

            if ($perm) {
                DB::table('permissions')->where('id', $perm->id)->update([
                    'label' => $meta['label'],
                    'group' => $meta['group'],
                    'sort' => $sort,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('permissions')->insert([
                    'slug' => $slug,
                    'label' => $meta['label'],
                    'group' => $meta['group'],
                    'sort' => $sort,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Hapus permission yang sudah tidak ada di config('permission.pages')
        DB::table('permissions')->whereNotIn('slug', array_keys($pages))->delete();

        // 3. Sinkronisasi default role_permissions
        $allPermissions = DB::table('permissions')->get();

        foreach ($defaults as $role => $spec) {
            $isWildcard = ($spec === '*');

            foreach ($allPermissions as $p) {
                $level = $isWildcard ? 'kelola' : ($spec[$p->slug] ?? null);
                $manage = ($level === 'kelola');
                $view = $manage || ($level === 'lihat');

                // Khusus dashboard widget, view=true jika ada di defaults atau wildcard
                DB::table('role_permissions')->updateOrInsert(
                    ['role' => $role, 'permission_id' => $p->id],
                    [
                        'view' => $view,
                        'manage' => $manage,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        // Reversible jika diperlukan
    }
};
