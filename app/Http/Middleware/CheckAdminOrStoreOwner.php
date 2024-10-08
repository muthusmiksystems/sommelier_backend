<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use Illuminate\Http\Request;

class CheckAdminOrStoreOwner
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()) {
        if ( Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Store Owner')) {
            return $next($request);
        }
    }
        abort(403, 'Unauthorized action.');
    }
}
