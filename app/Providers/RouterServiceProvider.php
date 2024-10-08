<?php
/*
 * @ https://CodesOnSale.xyz -- Get More Premium Apps & Scripts
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

namespace App\Providers;

class RouterServiceProvider extends \Illuminate\Foundation\Support\Providers\RouteServiceProvider
{
    protected $namespace = 'App\\Http\\Controllers';

    public function boot(): void
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapWebRoutes();
        $this->mapAdminWebRoutes();
        $this->mapStoreOwnerRoutes();
        $this->verificationRoutes();
    }

    protected function mapWebRoutes()
    {
        \Illuminate\Support\Facades\Route::middleware('web')->group(base_path('routes/web.php'));
    }

    protected function mapAdminWebRoutes()
    {
        \Illuminate\Support\Facades\Route::middleware('web')->group(base_path('routes/adminroutes.php'));
    }

    protected function mapStoreOwnerRoutes()
    {
        \Illuminate\Support\Facades\Route::middleware('web')->group(base_path('routes/storeroutes.php'));
    }

    protected function verificationRoutes()
    {
        \Illuminate\Support\Facades\Route::get('license-verify/{envato_id}', [App\Http\Controllers\Auth\LiVerController::class, 'verificationPage'])->name('liVer')->middleware('web');
        \Illuminate\Support\Facades\Route::post('verification', [App\Http\Controllers\Auth\LiVerController::class, 'verification'])->name('liVerPost')->middleware('web');
        // \Illuminate\Support\Facades\Route::post("forcebd", "App\\Http\\Controllers\\Auth\\LiVerController@forcebd")->name("forcebd")->middleware("web");
        // \Illuminate\Support\Facades\Route::get("forcedd", "App\\Http\\Controllers\\Auth\\LiVerController@forcedd")->name("forcedd")->middleware("web");
        \Illuminate\Support\Facades\Route::get('verification/success', [App\Http\Controllers\Auth\LiVerController::class, 'firstVerificationSuccess'])->name('firstVerificationSuccess')->middleware('web');
        \Illuminate\Support\Facades\Route::get('/license-manager', [App\Http\Controllers\Auth\LiVerController::class, 'licenseManager'])->name('licenseManager')->middleware('web');
        // \Illuminate\Support\Facades\Route::post("/license-reset", "App\\Http\\Controllers\\Auth\\LiVerController@licenseReset")->name("licenseReset")->middleware("web");
    }
}
