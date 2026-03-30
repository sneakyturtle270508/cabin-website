<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableCpAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('cp/*') || $request->is('cp')) {
            // Skip authentication for all CP routes
            return $next($request);
        }

        return $next($request);
    }
}
