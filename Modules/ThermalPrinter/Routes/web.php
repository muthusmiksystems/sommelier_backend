<?php

use App\Http\Controllers\ThermalPrinterController;
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

Route::prefix('thermalprinter')->middleware('web', 'auth')->group(function () {
    Route::get('/settings', [ThermalPrinterController::class, 'settings'])->name('thermalprinter.settings');
    Route::post('/save-settings', [ThermalPrinterController::class, 'saveSettings'])->name('thermalprinter.saveSettings');

    Route::get('/print/full-invoice/{order_id}', [ThermalPrinterController::class, 'printInvoice'])->name('thermalprinter.printInvoice');
    Route::post('/print/get-order-data', [ThermalPrinterController::class, 'getOrderDataForPrinting'])->name('thermalprinter.getOrderData');
});
