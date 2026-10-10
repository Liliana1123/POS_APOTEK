<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Middleware parameter parser. Laravel's Pipeline::parsePipeString() splits
     * route middleware only by `:` (limit 2), so "permission:slug:kelola" arrives
     * here as a single arg "slug:kelola". We re-split it ourselves.
     *
     * @param  string  $spec  either "slug" (level defaults 'lihat') or "slug:kelola"
     */
    public function handle(Request $request, Closure $next, string $spec): Response
    {
        [$slug, $level] = array_pad(explode(':', $spec, 2), 2, 'lihat');

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
