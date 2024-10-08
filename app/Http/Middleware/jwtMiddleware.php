<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class jwtMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::toUser($request->input('token'));
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return $next($request);

                return response()->json(['error' => 'Token is Invalid']);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return $next($request);

                return response()->json(['error' => 'Token is Expired']);
            } else {
                return $next($request);

                return response()->json(['error' => 'Something is wrong']);
            }
        }

        return $next($request);
    }
}
