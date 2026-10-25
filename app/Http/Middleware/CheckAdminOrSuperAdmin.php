<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOrSuperAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->isAdmin() || $user->isSuperAdmin()),
            403,
            'Halaman ini hanya dapat diakses oleh Admin dan Superadmin.'
        );

        return $next($request);
    }
}
