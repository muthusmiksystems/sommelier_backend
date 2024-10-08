<?php
/*
 * @ https://CodesOnSale.xyz -- Get More Premium Apps & Scripts
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends \Illuminate\Foundation\Support\Providers\RouteServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            $this->mapApiRoutes();
        });
    }

    protected function mapApiRoutes()
    {
        \Illuminate\Support\Facades\Route::prefix('api')->middleware('api')->group(base_path('routes/api.php'));
    }
}
