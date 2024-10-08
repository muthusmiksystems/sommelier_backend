<?php

use App\Http\Controllers\OrderScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::prefix('orderschedule')->group(function () {
    Route::get('/settings', [OrderScheduleController::class, 'settings'])->name('orderschedule.settings');
    Route::post('/save-settings', [OrderScheduleController::class, 'saveSettings'])->name('orderschedule.saveSettings');
});
