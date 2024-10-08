<?php

namespace Modules\SuperCache\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;

class SuperCacheServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('SuperCache', 'Database/Migrations'));
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
            module_path('SuperCache', 'Config/config.php') => config_path('supercache.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('SuperCache', 'Config/config.php'), 'supercache'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/supercache');

        $sourcePath = module_path('SuperCache', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/supercache';
        }, \Config::get('view.paths')), [$sourcePath]), 'supercache');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/supercache');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'supercache');
        } else {
            $this->loadTranslationsFrom(module_path('SuperCache', 'Resources/lang'), 'supercache');
        }
    }

    /**
     * Register an additional directory of factories.
     */
    public function registerFactories(): void
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('SuperCache', 'Database/factories'));
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
