<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index()
    {
        $this->guardSuperAdmin();

        $permissions = Permission::orderBy('sort')->get();

        return view('permission.index', [
            'roles' => config('permission.roles', []),
            'permissions' => $permissions,
            'matrix' => $this->matrix($permissions),
        ]);
    }

    public function update(Request $request)
    {
        $this->guardSuperAdmin();

        $permissions = Permission::pluck('id', 'slug');
        $roles = config('permission.roles', []);

        $data = $request->validate([
            'view' => ['array'],
            'manage' => ['array'],
        ]);

        $view = $data['view'] ?? [];
        $manage = $data['manage'] ?? [];

        $rows = [];

        foreach ($permissions as $slug => $id) {
            foreach ($roles as $role => $meta) {
                if ($meta['kunci'] ?? false) {
                    continue;
                }

                $manageOn = ! empty($manage[$role][$id]);
                $viewOn = ! empty($view[$role][$id]) || $manageOn;

                $rows[] = [
                    'role' => $role,
                    'permission_id' => $id,
                    'view' => $viewOn,
                    'manage' => $manageOn,
                ];
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('role_permissions')->upsert(
                $chunk,
                ['role', 'permission_id'],
                ['view', 'manage']
            );
        }

        \App\Models\ActivityLog::log(
            'Update Izin Akses',
            count($rows) . ' hak akses per role diperbarui',
            \App\Models\ActivityLog::CATEGORY_KEAMANAN
        );

        return back()->with('success', 'Izin akses berhasil disimpan.');
    }

    public function reset()
    {
        $this->guardSuperAdmin();

        $permissions = Permission::pluck('id', 'slug');
        $defaults = config('permission.defaults');
        $rows = [];

        foreach ($defaults as $role => $spec) {
            foreach ($permissions as $slug => $id) {
                $level = $spec === '*' ? 'kelola' : ($spec[$slug] ?? null);
                $manage = $level === 'kelola';

                $rows[] = [
                    'role' => $role,
                    'permission_id' => $id,
                    'view' => $manage || $level === 'lihat',
                    'manage' => $manage,
                ];
            }
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('role_permissions')->upsert(
                $chunk,
                ['role', 'permission_id'],
                ['view', 'manage']
            );
        }

        \App\Models\ActivityLog::log(
            'Reset Izin Akses',
            'Seluruh hak akses direset ke default sistem',
            \App\Models\ActivityLog::CATEGORY_KEAMANAN
        );

        return back()->with('success', 'Izin akses direset ke default sistem.');
    }

    private function matrix($permissions): array
    {
        $existing = DB::table('role_permissions')
            ->get(['role', 'permission_id', 'view', 'manage'])
            ->keyBy(fn ($r) => $r->role . ':' . $r->permission_id);

        $matrix = [];

        foreach ($permissions as $perm) {
            foreach (array_keys(config('permission.roles', [])) as $role) {
                $row = $existing->get($role . ':' . $perm->id);
                $matrix[$perm->id][$role] = [
                    'view' => (bool) ($row->view ?? false),
                    'manage' => (bool) ($row->manage ?? false),
                ];
            }
        }

        return $matrix;
    }

    private function guardSuperAdmin(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403, 'Hanya Superadmin yang bisa mengelola izin akses.');
    }
}