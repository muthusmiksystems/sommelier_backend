<?php

use App\Http\Controllers\DeliveryAreaProController;
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

Route::prefix('deliveryareapro')->group(function () {
    Route::get('/settings', [DeliveryAreaProController::class, 'settings'])->name('dap.settings');

    Route::post('/area/new', [DeliveryAreaProController::class, 'saveArea'])->name('dap.saveArea');

    Route::get('/area/edit/{id}', [DeliveryAreaProController::class, 'editArea'])->name('dap.editArea');
    Route::post('/area/update', [DeliveryAreaProController::class, 'updateArea'])->name('dap.updateArea');

    //get single store and attach to multiple areas
    Route::get('/assign-areas-to-store/{id}', [DeliveryAreaProController::class, 'assignAreasToStore'])->name('dap.assignAreasToStore');

    //get single area and attach to multiple stores
    Route::get('/assign-stores-to-area/{id}', [DeliveryAreaProController::class, 'assignStoresToArea'])->name('dap.assignStoresToArea');

    Route::post('/update-store-area', [DeliveryAreaProController::class, 'updateStoreArea'])->name('dap.updateStoreArea');

    Route::post('/save-settings', [DeliveryAreaProController::class, 'saveSettings'])->name('dap.saveSettings');
});
