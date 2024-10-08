<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Localization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (\Session::has('locale')) {
            \App::setlocale(\Session::get('locale'));
        }

        return $next($request);
    }
}
