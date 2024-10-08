<?php

namespace Modules\ThermalPrinter\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider;

class ThermalPrinterServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('ThermalPrinter', 'Database/Migrations'));
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
            module_path('ThermalPrinter', 'Config/config.php') => config_path('thermalprinter.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('ThermalPrinter', 'Config/config.php'), 'thermalprinter'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/thermalprinter');

        $sourcePath = module_path('ThermalPrinter', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath,
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path.'/modules/thermalprinter';
        }, \Config::get('view.paths')), [$sourcePath]), 'thermalprinter');
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/thermalprinter');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'thermalprinter');
        } else {
            $this->loadTranslationsFrom(module_path('ThermalPrinter', 'Resources/lang'), 'thermalprinter');
        }
    }

    /**
     * Register an additional directory of factories.
     */
    public function registerFactories(): void
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('ThermalPrinter', 'Database/factories'));
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
