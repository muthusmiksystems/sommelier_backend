<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BulkUploadController;
use App\Http\Controllers\Datatables;
use App\Http\Controllers\EagleViewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RestaurantOwnerController;
use Illuminate\Support\Facades\Route;

/* Restaurant Order Routes */
Route::prefix('store-owner')->middleware('auth', 'storeowner')->group(function () {
    Route::get('/orders', [RestaurantOwnerController::class, 'orders'])->name('restaurant.orders');
    Route::get('/notifications', [RestaurantOwnerController::class, 'notifications'])->name('restaurant.notifications');
    Route::get('/sliders', [RestaurantOwnerController::class, 'sliders'])->name('restaurant.sliders');
    Route::post('/restaurant-Create-Slider', [RestaurantOwnerController::class, 'restaurantCreateSlider'])->name('restaurant.createSlider');
    Route::post('/update-storeowner-slider', [RestaurantOwnerController::class, 'updateStoreOwnerSlider'])->name('restaurant.updateStoreOwnerSlider');
    Route::post('/notifications/upload', [RestaurantOwnerController::class, 'uploadNotificationImage'])->name('restaurant.uploadNotificationImage');
    Route::post('/notifications/send', [RestaurantOwnerController::class, 'sendNotifiaction'])->name('restaurant.sendNotifiaction');
    Route::post('/notification-to-users/send', [RestaurantOwnerController::class, 'sendNotificationToSelectedUsers'])->name('restaurant.sendNotificationToSelectedUsers');
    Route::post('/notification-to-non-registered/send', [RestaurantOwnerController::class, 'sendNotificationToNonRegisteredAppUsers'])->name('restaurant.sendNotificationToNonRegisteredAppUsers');
    Route::get('/delete-alerts-junk', [RestaurantOwnerController::class, 'deleteAlertsJunk'])->name('restaurant.deleteAlertsJunk');

    Route::get('notifications/getUsersToSendNotification', [RestaurantOwnerController::class, 'getUsersToSendNotification'])->name('restaurant.getUsersToSendNotification');
    Route::get('/ordersDataTable', [Datatables\OrdersDatatable::class, 'ordersDataTableStoreOwner'])->name('restaurant.ordersDataTable');

    Route::post('/orders/get-new-orders', [RestaurantOwnerController::class, 'getNewOrders'])->name('restaurant.getNewOrders');

    Route::get('/orders/accept-order/{id}', [RestaurantOwnerController::class, 'acceptOrder'])->name('restaurant.acceptOrder');
    Route::get('/orders/confirm-order/{id}', [RestaurantOwnerController::class, 'confirmScheduledOrder'])->name('restaurant.confirmScheduledOrder');
    Route::get('/orders/mark-order-ready/{id}', [RestaurantOwnerController::class, 'markOrderReady'])->name('restaurant.markOrderReady');
    Route::get('/orders/mark-selfpickup-order-completed/{id}', [RestaurantOwnerController::class, 'markSelfPickupOrderAsCompleted'])->name('restaurant.markSelfPickupOrderAsCompleted');

    Route::get('/orders/cancel-order/{id}', [RestaurantOwnerController::class, 'cancelOrder'])->name('restaurant.cancelOrder');

    Route::get('/stores', [RestaurantOwnerController::class, 'restaurants'])->name('restaurant.restaurants');
    Route::get('/store/edit/{id}', [RestaurantOwnerController::class, 'getEditRestaurant'])->name('restaurant.get.editRestaurant');
    Route::post('/store/new/save', [RestaurantOwnerController::class, 'saveNewRestaurant'])->name('restaurant.saveNewRestaurant');
    Route::get('/store/disable/{id}', [RestaurantOwnerController::class, 'disableRestaurant'])->name('restaurant.disableRestaurant');
    Route::post('/store/edit/save', [RestaurantOwnerController::class, 'updateRestaurant'])->name('restaurant.updateRestaurant');
    Route::post('/store/new/save', [RestaurantOwnerController::class, 'saveNewRestaurant'])->name('restaurant.saveNewRestaurant');
    Route::post('/store/schedule/save', [RestaurantOwnerController::class, 'updateRestaurantScheduleData'])->name('restaurant.updateRestaurantScheduleData');
    Route::get('/users', [RestaurantOwnerController::class, 'restaurantusers'])->name('restaurant.users');
    Route::get('/customers', [RestaurantOwnerController::class, 'restaurantcustomers'])->name('restaurant.customers');
    Route::get('/usersDatatable', [Datatables\RestaurantUsersDatatable::class, 'restaurantUsersDatatable'])->name('storeOwner.usersDatatable');
    Route::get('/customerDatatable', [Datatables\RestaurantCustomerDatatable::class, 'RestaurantCustomerDatatable'])->name('storeOwner.customerDatatable');
    Route::post('/saveNewUser', [RestaurantOwnerController::class, 'saveNewUser'])->name('storeOwner.saveNewUser');
    Route::get('/manage-delivery-guys', [RestaurantOwnerController::class, 'restaurantmanageDeliveryGuys'])->name('restaurant.manageDeliveryGuys');
    Route::get('/deliveryGuyUsersDatatable', [Datatables\RestaurantDeliveryGuyUsersDatatable::class, 'RestaurantDeliveryGuyUsersDatatable'])->name('storeOwner.deliveryGuyUsersDatatable');
    Route::get('/staffs', [RestaurantOwnerController::class, 'restaurantstaffs'])->name('restaurant.staffs');
    Route::get('/managerDatatable', [Datatables\RestaurantManagersDatatable::class, 'RestaurantManagersDatatable'])->name('storeOwner.managerDatatable');
    Route::get('/manage-store-owners', [RestaurantOwnerController::class, 'restaurantmanageRestaurantOwners'])->name('restaurant.manageRestaurantOwners');
    Route::get('/storeOwnerUsersDatatable', [Datatables\RestaurantStoreOwnerUsersDatatable::class, 'RestaurantStoreOwnerUsersDatatable'])->name('restaurant.storeOwnerUsersDatatable');



    Route::get('/itemcategories', [RestaurantOwnerController::class, 'itemcategories'])->name('restaurant.itemcategories');
    Route::post('/itemcategories/new/save', [RestaurantOwnerController::class, 'createItemCategory'])->name('restaurant.createItemCategory');
    Route::get('/itemcategory/disable/{id}', [RestaurantOwnerController::class, 'disableCategory'])->name('restaurant.disableCategory');
    Route::post('/itemcategory/edit/save', [AdminController::class, 'updateItemCategory'])->name('restaurant.updateItemCategory');

    Route::get('/addoncategories', [RestaurantOwnerController::class, 'addonCategories'])->name('restaurant.addonCategories');
    Route::get('/addoncategories/searchAddonCategories', [RestaurantOwnerController::class, 'searchAddonCategories'])->name('restaurant.post.searchAddonCategories');
    Route::get('/addoncategory/edit/{id}', [RestaurantOwnerController::class, 'getEditAddonCategory'])->name('restaurant.editAddonCategory');
    Route::post('/addoncategory/edit/save', [RestaurantOwnerController::class, 'updateAddonCategory'])->name('restaurant.updateAddonCategory');
    Route::get('/addoncategory/new', [RestaurantOwnerController::class, 'newAddonCategory'])->name('restaurant.newAddonCategory');
    Route::post('/addoncategory/new/save', [RestaurantOwnerController::class, 'saveNewAddonCategory'])->name('restaurant.saveNewAddonCategory');

    Route::get('/addons', [RestaurantOwnerController::class, 'addons'])->name('restaurant.addons');
    Route::get('/addons/searchAddons', [RestaurantOwnerController::class, 'searchAddons'])->name('restaurant.post.searchAddons');
    Route::get('/addon/edit/{id}', [RestaurantOwnerController::class, 'getEditAddon'])->name('restaurant.editAddon');
    Route::post('/addon/edit/save', [RestaurantOwnerController::class, 'updateAddon'])->name('restaurant.updateAddon');
    Route::post('/addon/new/save', [RestaurantOwnerController::class, 'saveNewAddon'])->name('restaurant.saveNewAddon');
    Route::get('/addon/disable/{id}', [RestaurantOwnerController::class, 'disableAddon'])->name('restaurant.disableAddon');
    Route::get('/addon/delete/{id}', [RestaurantOwnerController::class, 'deleteAddon'])->name('restaurant.deleteAddon');

    Route::get('/items', [RestaurantOwnerController::class, 'items'])->name('restaurant.items');
    Route::get('/stores/searchItems', [RestaurantOwnerController::class, 'searchItems'])->name('restaurant.post.searchItems');
    Route::get('/items/edit/{id}', [RestaurantOwnerController::class, 'getEditItem'])->name('restaurant.get.editItem');
    Route::get('/item/remove-image/{id}', [RestaurantOwnerController::class, 'removeItemImage'])->name('restaurant.removeItemImage');

    Route::get('/item/disable/{id}', [RestaurantOwnerController::class, 'disableItem'])->name('restaurant.disableItem');
    Route::post('/item/edit/save', [RestaurantOwnerController::class, 'updateItem'])->name('restaurant.updateItem');
    Route::post('/item/new/save', [RestaurantOwnerController::class, 'saveNewItem'])->name('restaurant.saveNewItem');
    Route::post('/item/bulk/save', [BulkUploadController::class, 'itemBulkUploadFromRestaurant'])->name('restaurant.itemBulkUpload');

    Route::get('/items/sort/{restaurant_id}', [RestaurantOwnerController::class, 'sortMenusAndItems'])->name('restaurant.sortMenusAndItems');
    Route::post('/items/sort/save', [RestaurantOwnerController::class, 'updateItemPositionForStore'])->name('restaurant.updateItemPositionForStore');
    Route::post('/itemcategories/sort/save', [RestaurantOwnerController::class, 'updateMenuCategoriesPositionForStore'])->name('restaurant.updateMenuCategoriesPositionForStore');

    Route::get('/orders', [RestaurantOwnerController::class, 'orders'])->name('restaurant.orders');
    Route::get('/orders/searchOrders', [RestaurantOwnerController::class, 'postSearchOrders'])->name('restaurant.post.searchOrders');
    Route::get('/order/{order_id}', [RestaurantOwnerController::class, 'viewOrder'])->name('restaurant.viewOrder');

    Route::get('/earnings/{restaurant_id?}', [RestaurantOwnerController::class, 'earnings'])->name('restaurant.earnings');
    Route::post('/earnings/send-payout-request', [RestaurantOwnerController::class, 'sendPayoutRequest'])->name('restaurant.sendPayoutRequest');

    Route::post('/save-store-owner-notification-token', [NotificationController::class, 'saveRestaurantOwnerNotificationToken'])->name('saveRestaurantOwnerNotificationToken');

    Route::get('/ratings/{restaurant_id?}', [RestaurantOwnerController::class, 'ratings'])->name('restaurant.ratings');

    Route::get('zen-mode/{status}', function ($status) {
        Session::put('zenMode', $status);

        return redirect()->route('restaurant.dashboard');
    })->name('restaurant.zenMode');

    Route::post('/check-order-status-new-order', [RestaurantOwnerController::class, 'checkOrderStatusNewOrder'])->name('restaurant.checkOrderStatusNewOrder');
    Route::post('/check-order-status-selfpickup-order', [RestaurantOwnerController::class, 'checkOrderStatusSelfPickupOrder'])->name('restaurant.checkOrderStatusSelfPickupOrder');

    Route::post('/update-store-payout-details', [RestaurantOwnerController::class, 'updateStorePayoutDetails'])->name('restaurant.updateStorePayoutDetails');

    Route::get('/dashboard', [RestaurantOwnerController::class, 'dashboard'])->name('restaurant.dashboard');
});
/* END Restaurant Owner Routes */
