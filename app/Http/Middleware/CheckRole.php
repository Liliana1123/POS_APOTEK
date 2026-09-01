<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Penggunaan di routes/web.php:
     *
     * Route::middleware(['auth', 'role:admin'])->group(function () {
     *     // hanya admin
     * });
     *
     * Route::middleware(['auth', 'role:admin,apoteker'])->group(function () {
     *     // admin ATAU apoteker
     * });
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Kamu tidak punya akses ke halaman ini.');
        }

        return $next($request);
    }
}
