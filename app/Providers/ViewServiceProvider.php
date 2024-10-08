<?php

namespace App\Providers;

use App\Zone;
use Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer(['admin.*'], function ($view) {
            $elFlag = File::exists(base_path('vendor/bin/elflag'));
            if ($elFlag) {
                session()->put('elFlag', true);
            } else {
                session()->put('elFlag', false);
            }
        });

        if (env('APP_INSTALLED')) {
            if (DB::connection()->getDatabaseName()) {
                if (Schema::hasTable('zones')) {
                    if (Cache::has('zonesCache')) {
                        $zones = Cache::get('zonesCache');
                    } else {
                        $zones = Cache::remember('zonesCache', 3600, function () {
                            $data = Zone::get();

                            return $data;
                        });
                    }
                    view()->composer(['*'], function ($view) use ($zones) {
                        $view->with('navZones', $zones);
                    });
                }
            }
        }
    }
}
