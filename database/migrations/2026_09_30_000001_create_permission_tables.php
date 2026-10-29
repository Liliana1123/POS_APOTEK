<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('label');
            $table->string('group', 30)->index();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30);
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->boolean('view')->default(false);
            $table->boolean('manage')->default(false);
            $table->unique(['role', 'permission_id']);
        });

        $this->isiHakAksesAwal();
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }

    private function isiHakAksesAwal(): void
    {
        $now = now();
        $halaman = config('permission.pages');
        $default = config('permission.defaults');

        $permissionIds = [];
        $sort = 0;

        foreach ($halaman as $slug => $meta) {
            $permissionIds[$slug] = DB::table('permissions')->insertGetId([
                'slug'   => $slug,
                'label'  => $meta['label'],
                'group'  => $meta['group'],
                'sort'   => ++$sort,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Role dari config + role yang benar-benar dipakai user yang sudah ada,
        // supaya tidak ada user terkunci setelah permission diaktifkan.
        $roles = array_unique(array_merge(
            array_keys($default),
            DB::table('users')->distinct()->pluck('role')->all()
        ));

        $rows = [];

        foreach ($roles as $role) {
            $spec = $default[$role] ?? [];

            foreach ($permissionIds as $slug => $permissionId) {
                $level = $spec === '*' ? 'kelola' : ($spec[$slug] ?? null);
                $manage = $level === 'kelola';

                $rows[] = [
                    'role'          => $role,
                    'permission_id' => $permissionId,
                    'view'          => $manage || $level === 'lihat',
                    'manage'        => $manage,
                ];
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('role_permissions')->insert($chunk);
        }
    }
};
