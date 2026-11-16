<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app('installer')->isInstalled()) {
            return redirect()->route('install.welcome');
        }

        return $next($request);
    }
}
