<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RestaurantOwnerController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\UpdateController;
use Illuminate\Support\Facades\Route;

/* Setting the locale route */
Route::get('locale/{locale}', function ($locale) {
    Session::put('locale', $locale);

    return redirect()->back();
});

Route::get('error-logs/{hash}', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

/* Installation Routes */
Route::get('install/start', [InstallController::class, 'start'])->name('install.start');
Route::get('install/pre-installation', [InstallController::class, 'preInstallation'])->name('install.preInstallation');
Route::get('install/configuration', [InstallController::class, 'getConfiguration'])->name('install.configuration');
Route::post('install/configuration', [InstallController::class, 'postConfiguration'])->name('install.configurationPost');
Route::get('install/complete', [InstallController::class, 'complete'])->name('install.complete');
/* END Installation Routes */

/* Update Routes */
Route::get('install/update', [UpdateController::class, 'updatePage'])->name('updatePage');
Route::post('install/update', [UpdateController::class, 'update'])->name('updatePost');
/* END Update Routes */

Route::get('/', [PageController::class, 'indexPage'])->name('get.index');

Route::get('/schedule/run/{password}', [SchedulerController::class, 'run']);
Route::get('/files-backup/run/{password}', [BackupController::class, 'filesBackuprun']);
Route::get('/database-backup/run/{password}', [BackupController::class, 'dbBackuprun']);

/* Auth Routes */
Route::get('/auth/login', [PageController::class, 'loginPage'])->name('get.login');
Route::post('/auth/login', [Auth\LoginController::class, 'login'])->name('post.login');
Route::get('auth/logout', [Auth\LoginController::class, 'logout'])->name('logout');
Route::post('/auth/login', [LoginController::class, 'loginotp'])->name('post.loginotp');
Route::get('/auth/verify-otp', [LoginController::class, 'showOtpForm'])->name('get.verifyotp');
Route::post('/auth/verify-otps', [LoginController::class, 'verifyOtp'])->name('post.verifyotp');
Route::get('/auth/store-registration', [PageController::class, 'storeRegistration'])->name('storeRegistration');
Route::get('/auth/delivery-registration', [PageController::class, 'deliveryRegistration'])->name('deliveryRegistration');

Route::get('/auth/forgot-password', [PageController::class, 'forgotPassword'])->name('forgotPassword');
Route::post('/auth/forgot-password-send-email', [PageController::class, 'forgotPasswordSendEmail'])->name('forgotPasswordSendEmail');

Route::get('/auth/change-password', [PageController::class, 'changePassword'])->name('changePassword');
Route::post('/auth/change-password', [PageController::class, 'changePasswordPost'])->name('changePasswordPost');

Route::post('auth/register', [RegisterController::class, 'registerRestaurantDelivery'])->name('registerRestaurantDelivery');
/* END Auth Routes */

/* Custom Routes */

Route::get('auth/register-delivery', [PageController::class, 'registerDelivery'])->name('registerDelivery');
Route::post('auth/register', [RegisterController::class, 'registerRestaurantDelivery'])->name('registerRestaurantDelivery');
Route::post('auth/register-delivery-save', [RegisterController::class, 'registerRestaurantDelivery'])->name('registerDeliverySave');
Route::get('ordering/{user_id}', [PageController::class, 'externalOrdering'])->name('externalOrdering');
Route::post('/auth/verify-deliveryotp', [RegisterController::class,'verifyOTP'])->name('post.deliveryverifyOtp');
Route::get('/auth/verify-deliveryotp', [RegisterController::class,'showotp'])->name('get.deliveryotp');
/* Restaurant Order Routes */
Route::prefix('store-owner')->middleware(['auth', 'storeowner'])->group(function () {
    Route::get('/bookings', [RestaurantOwnerController::class, 'bookings'])->name('restaurant.bookings');
    Route::post('/booking/new/save', [RestaurantOwnerController::class, 'saveNewBooking'])->name('restaurant.saveNewBooking');
    Route::get('/bookings/edit/{id}', [RestaurantOwnerController::class, 'getEditBooking'])->name('restaurant.get.editBooking');
    Route::get('/bookings/cancel/{id}', [RestaurantOwnerController::class, 'cancelBooking'])->name('restaurant.get.cancelBooking');
    Route::get('/bookings/cancelall/{id}', [RestaurantOwnerController::class, 'cancelAllBooking'])->name('restaurant.get.cancelAllBooking');
    Route::post('/booking/edit/save', [RestaurantOwnerController::class, 'updateBooking'])->name('restaurant.updateBooking');
    //Route::get('/booking/searchBooking', 'RestaurantOwnerController@searchBooking')->name('restaurant.post.searchBooking');
    Route::get('/booking/disable/{id}', [RestaurantOwnerController::class, 'disableBooking'])->name('restaurant.disableBooking');
    Route::get('/booking/doneall/', [RestaurantOwnerController::class, 'doneAllBooking'])->name('restaurant.doneAllBooking');

    Route::get('/store/tableshiftinfo/{id}', [RestaurantOwnerController::class, 'getTableShiftRestaurant'])->name('restaurant.get.tableShiftRestaurant');
    Route::post('/store/tableshiftsettings', [RestaurantOwnerController::class, 'saveRestaurantTableShift'])->name('restaurant.saveRestaurantTableShift');

    Route::get('/assign-table', [RestaurantOwnerController::class, 'assignTable'])->name('restaurant.assignTable');
    // Route::post('/store/searchBooking', 'RestaurantOwnerController@restaurantBookingSearch')->name('restaurant.restaurantBookingSearch');
    Route::post('/assignTableToBooking', [RestaurantOwnerController::class, 'assignTableToBooking'])->name('restaurant.assignTableToBooking');
    Route::post('/store/shift-timing/{id}', [RestaurantOwnerController::class, 'getShiftTiming'])->name('restaurant.post.shiftTiming');
    Route::post('/store/shift-timing-for-filter/{id}', [RestaurantOwnerController::class, 'getShiftTimingForFilter'])->name('restaurant.post.shiftTimingForFilter');
    Route::post('/store/get-available-tables', [RestaurantOwnerController::class, 'getAvailableTables'])->name('restaurant.post.availableTables');
    Route::post('/store/restaurant-table-areas/{id}', [RestaurantOwnerController::class, 'getTableAreas'])->name('restaurant.post.getTableAreas');
    Route::get('/store/restaurant-table-areas-ext/{id}', [RestaurantOwnerController::class, 'getTableAreas'])->name('restaurant.get.getTableAreasExt');
    Route::get('/store/restaurant-venue/{email}', [RestaurantOwnerController::class, 'getRestaurantVenue'])->name('restaurant.get.getRestaurantVenue');

    /* Custom linkk for POS settings for store*/
    Route::get('/store/settings/{id}', [RestaurantOwnerController::class, 'getSettingsRestaurant'])->name('restaurant.get.settingsRestaurant');
    Route::get('/restaurantbook/settings/{id}', [RestaurantOwnerController::class, 'getSettingsRestaurantbooking'])
    ->name('restaurant.get.getSettingsRestaurantbooking');
    Route::get('/restaurant/settings/{id}', [RestaurantOwnerController::class, 'getRestaurantbooking'])
    ->name('restaurant.get.getRestaurantbooking');

    Route::post('/store/settings', [RestaurantOwnerController::class, 'saveRestaurantSettings'])->name('restaurant.saveRestaurantSettings');
    /* ENd Custom linkk for POS settings for store*/
    Route::post('/store/checkbepozconnection', [RestaurantOwnerController::class, 'checkBepozConnection'])->name('restaurant.checkBepozConnection');
    Route::get('/bookings-print', [RestaurantOwnerController::class, 'bookingPrint'])->name('restaurant.bookingsPrint');
});

/* Admin Routes */
Route::prefix('admin')->middleware('auth', 'checkzoneaccess')->group(function () {
    /* Custom linkk for POS settings for store*/
    Route::get('/store/settings/{id}', [AdminController::class, 'getSettingsRestaurant'])->name('admin.get.settingsRestaurant');
    Route::post('/store/settings', [AdminController::class, 'saveRestaurantSettings'])->name('admin.saveRestaurantSettings');
    /* ENd Custom linkk for POS settings for store*/

    Route::get('/store/tableshiftinfo/{id}', [AdminController::class, 'tableShiftRestaurant'])->name('admin.get.tableShiftRestaurant');
    Route::post('/store/tableshiftsettings', [AdminController::class, 'saveRestaurantTableShift'])->name('admin.saveRestaurantTableShift');
    Route::post('/store/checkbepozconnection', [AdminController::class, 'checkBepozConnection'])->name('admin.checkBepozConnection');
});

/* End Custom Routes */
/* Force Scheme to https*/
URL::forceScheme('http');