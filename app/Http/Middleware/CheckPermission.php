<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $slug, string $level = 'lihat'): Response
    {
        if (! $request->user()) {
            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        $punya = $level === 'kelola'
            ? $request->user()->canManage($slug)
            : $request->user()->hasPermission($slug);

        if (! $punya) {
            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}