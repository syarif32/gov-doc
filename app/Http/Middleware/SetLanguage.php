<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Priority: User preference from DB if logged in
        if (auth()->check()) {
            App::setLocale(auth()->user()->preferred_lang);
        }
        // 2. Fallback: Session if not logged in (for login/register pages)
        elseif (session()->has('locale')) {
            App::setLocale(session()->get('locale'));
        }

        return $next($request);
    }
}
