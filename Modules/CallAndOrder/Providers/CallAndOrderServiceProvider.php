<?php

namespace Modules\CallAndOrder\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;

class CallAndOrderServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('CallAndOrder', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path('CallAndOrder', 'Config/config.php') => config_path('callandorder.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('CallAndOrder', 'Config/config.php'), 'callandorder'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/callandorder');

        $sourcePath = module_path('CallAndOrder', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/callandorder';
        }, \Config::get('view.paths')), [$sourcePath]), 'callandorder');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/callandorder');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'callandorder');
        } else {
            $this->loadTranslationsFrom(module_path('CallAndOrder', 'Resources/lang'), 'callandorder');
        }
    }

    /**
     * Register an additional directory of factories.
     */
    public function registerFactories(): void
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('CallAndOrder', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
