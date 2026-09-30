<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role', 'aktif'];

    protected $hidden = ['password', 'remember_token'];

    protected ?array $hakAksesCache = null;

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApoteker(): bool
    {
        return $this->role === 'apoteker';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $row = $this->hakAkses()[$slug] ?? false;

        return $row['view'] || $row['manage'];
    }

    public function canManage(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->hakAkses()[$slug]['manage'] ?? false;
    }

    // ponytail: pivot dibaca 1 query per request, memoized per instance.
    // Naikkan ke Cache::remember keyed role kalau query ini muncul di debugbar.
    protected function hakAkses(): array
    {
        return $this->hakAksesCache ??= DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role', $this->role)
            ->get(['permissions.slug', 'role_permissions.view', 'role_permissions.manage'])
            ->mapWithKeys(fn ($row) => [
                $row->slug => ['view' => (bool) $row->view, 'manage' => (bool) $row->manage],
            ])
            ->all();
    }

    public function penerimaan(): HasMany
    {
        return $this->hasMany(Penerimaan::class);
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }
}
