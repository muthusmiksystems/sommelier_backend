<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNoUpdateAvailable
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $updateFile = File::exists(storage_path('update'));
        if (! $updateFile) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
