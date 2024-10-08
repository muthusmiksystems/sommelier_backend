<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;
use View;

class AgentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $agent = new Agent();
        View::share('agent', $agent);
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        //
    }
}
