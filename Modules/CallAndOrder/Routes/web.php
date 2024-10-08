<?php

use App\Http\Controllers\CallAndOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
 */

Route::prefix('callandorder')->group(function () {
    Route::middleware('role:Admin')->group(function () {
        Route::get('/settings', [CallAndOrderController::class, 'settings'])->name('cao.settings');
        Route::post('/save-settings', [CallAndOrderController::class, 'saveSettings'])->name('cao.saveSettings');
    });

    Route::middleware('permission:login_as_customer')->group(function () {
        Route::get('/users', [CallAndOrderController::class, 'users'])->name('cao.usersPage');
        Route::get('/usersDatatable', [CallAndOrderController::class, 'usersDatatable'])->name('cao.usersDatatable');
        Route::post('login-as-customer', [CallAndOrderController::class, 'loginAsCustomer'])->name('cao.loginAsCustomer');
        Route::post('register-guest-user', [CallAndOrderController::class, 'registerGuestUser'])->name('cao.registerGuestUser');
    });
});
