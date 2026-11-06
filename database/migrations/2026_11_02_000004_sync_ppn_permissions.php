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

        $allPermissions = DB::table('permissions')->get();

        foreach ($defaults as $role => $spec) {
            $isWildcard = ($spec === '*');

            foreach ($allPermissions as $p) {
                $level = $isWildcard ? 'kelola' : ($spec[$p->slug] ?? null);
                $manage = ($level === 'kelola');
                $view = $manage || ($level === 'lihat');

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
        $perm = DB::table('permissions')->where('slug', 'sistem.pengaturan-pajak')->first();
        if ($perm) {
            DB::table('role_permissions')->where('permission_id', $perm->id)->delete();
            DB::table('permissions')->where('id', $perm->id)->delete();
        }
    }
};

