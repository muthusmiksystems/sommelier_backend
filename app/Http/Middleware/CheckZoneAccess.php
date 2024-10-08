<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckZoneAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        //if loggedin
        if (Auth::user()) {
            //if not admin
            if (! Auth::user()->hasRole('Admin')) {
                // check if auth user is assigned to a zone.
                $user = Auth::user();
                if ($user->zone_id != null) {
                    //use has zone, now set this zone to the session
                    session(['selectedZone' => $user->zone_id]);
                }
            }
        }

        return $next($request);
    }
}
