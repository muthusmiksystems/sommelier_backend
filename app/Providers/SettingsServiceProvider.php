<?php
/*
 * @ https://CodesOnSale.xyz -- Get More Premium Apps & Scripts
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

namespace App\Providers;

class SettingsServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(\Illuminate\Contracts\Cache\Factory $cache, \App\Setting $settings): void
    {
        $settings = '';
        if (env('APP_INSTALLED') && \Illuminate\Support\Facades\DB::connection()->getDatabaseName() && \Illuminate\Support\Facades\Schema::hasTable('settings')) {
            if (\Cache::has('settings')) {
                $settings = \Cache::get('settings');
                config()->set('setting', $settings);
            } else {
                $settings = $cache->remember('settings', 3600, function () {
                    return \App\Setting::pluck('value', 'key')->all();
                });
                config()->set('setting', $settings);
            }
        }
    }
}
