<?php

use App\Http\Controllers\SuperCacheController;
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

Route::prefix('supercache')->group(function () {
    Route::get('/settings', [SuperCacheController::class, 'settings'])->name('supercache.settings');
    Route::get('/purge', [SuperCacheController::class, 'purgeCache'])->name('supercache.purgeCache');
});
