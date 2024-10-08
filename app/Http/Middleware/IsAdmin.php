<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(route('get.login'));
            }
        } elseif (! Auth::user()->hasRole('Admin')) {
            return redirect()->to('/')->withError('Permission Denied');
        }

        return $next($request);
    }
}
