<?php

namespace Modules\OrderSchedule\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;

class OrderScheduleServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('OrderSchedule', 'Database/Migrations'));
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
            module_path('OrderSchedule', 'Config/config.php') => config_path('orderschedule.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('OrderSchedule', 'Config/config.php'), 'orderschedule'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/orderschedule');

        $sourcePath = module_path('OrderSchedule', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/orderschedule';
        }, \Config::get('view.paths')), [$sourcePath]), 'orderschedule');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/orderschedule');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'orderschedule');
        } else {
            $this->loadTranslationsFrom(module_path('OrderSchedule', 'Resources/lang'), 'orderschedule');
        }
    }

    /**
     * Register an additional directory of factories.
     */
    public function registerFactories(): void
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('OrderSchedule', 'Database/factories'));
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
