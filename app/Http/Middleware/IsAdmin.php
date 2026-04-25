<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role_level === 'admin') {
            return $next($request); // Silakan lewat
        }

        // Jika bukan admin, tendang ke halaman 403 Forbidden
        abort(403, 'Akses Ditolak!');
    }
}