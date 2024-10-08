<?php

namespace Modules\DeliveryAreaPro\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;

class DeliveryAreaProServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('DeliveryAreaPro', 'Database/Migrations'));
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
            module_path('DeliveryAreaPro', 'Config/config.php') => config_path('deliveryareapro.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('DeliveryAreaPro', 'Config/config.php'), 'deliveryareapro'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/deliveryareapro');

        $sourcePath = module_path('DeliveryAreaPro', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/deliveryareapro';
        }, \Config::get('view.paths')), [$sourcePath]), 'deliveryareapro');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/deliveryareapro');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'deliveryareapro');
        } else {
            $this->loadTranslationsFrom(module_path('DeliveryAreaPro', 'Resources/lang'), 'deliveryareapro');
        }
    }

    /**
     * Register an additional directory of factories.
     */
    public function registerFactories(): void
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('DeliveryAreaPro', 'Database/factories'));
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
