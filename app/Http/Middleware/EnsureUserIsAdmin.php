<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Restrict a route to admin users only. Register the alias in
     * bootstrap/app.php — see README.md, "Peran & Hak Akses".
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'Halaman ini hanya bisa diakses oleh Admin.');
        }

        return $next($request);
    }
}
