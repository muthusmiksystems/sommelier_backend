<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class PhpVersionCompatibility
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $version = phpversion();
        if (File::exists(storage_path('ignorePhpVersion'))) {
            return $next($request);
        }
        if (! (version_compare($version, '7.2', '>=') && version_compare($version, '7.4', '<'))) {
            print_r('<p>Your PHP version is: <b>'.$version.'</b></p>');
            print_r('<p>Foodomaa only supports <b>PHP version 8.2.4 or 8.2.4.x</b></p>');
            print_r('<p>Kindly set your PHP version to 8.2.4 from your cPanel or contact your Hosting Provider/Server Admin for the same.</p>');
            print_r("<p><a href='https://docs.foodomaa.com/installation/installation-on-server#requirements' target='_blank'>Click here</a> to know more about Foodomaa requirements.</p>");
            exit();
        } else {
            return $next($request);
        }
    }
}
