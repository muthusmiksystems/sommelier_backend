<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPartialsController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BulkUploadController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Datatables;
use App\Http\Controllers\DeliveryCollectionController;
use App\Http\Controllers\EagleViewController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\FoodomaaNewsController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RatingReviewController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestaurantCategoryController;
use App\Http\Controllers\RolesAndPermissionController;
use App\Http\Controllers\ServerStatsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TodoNotesController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\ZoneController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:Store Owner|permission:login_as_store_owner')->group(function () {
    Route::impersonate();
});

Route::middleware('auth')->group(function () {
    Route::get('/change-zone-scope/{zone_id}', [ZoneController::class, 'changeZoneScope'])->name('navChangeAreaScope');
});

/* Admin Routes */
Route::prefix('admin')->middleware(['checkAdminOrStoreOwner','auth','checkzoneaccess'])->group(function () {
    Route::get('/processPayment', [AdminController::class, 'processPayment'])->name('admin.processPayment');

    Route::middleware(['checkAdminOrStoreOwner'])->group(function () {
        Route::get('/manage-delivery-guys', [AdminController::class, 'manageDeliveryGuys'])->name('admin.manageDeliveryGuys');
        Route::get('/deliveryGuyUsersDatatable', [Datatables\DeliveryGuyUsersDatatable::class, 'deliveryGuyUsersDatatable'])->name('admin.deliveryGuyUsersDatatable');
        Route::get('/manage-delivery-guys-stores/{id}', [AdminController::class, 'getManageDeliveryGuysRestaurants'])->name('admin.get.manageDeliveryGuysRestaurants');
        Route::get('/store-manage-delivery-guys-stores/{id}', [AdminController::class, 'getManageDeliveryGuysRestaurantstore'])->name('store.get.manageDeliveryGuysRestaurants');
        Route::get('/manage-delivery-guys/eagle-view', [EagleViewController::class, 'deliveryEagleView'])->name('admin.deliveryEagleView');
        Route::get('/manage-delivery-guys/getAllDeliveryInfoEagleView', [EagleViewController::class, 'getAllDeliveryInfoEagleView'])->name('admin.getAllDeliveryInfoEagleView');
        Route::get('/manage-delivery-guys/getDeliveryInfoEagleView/{id}', [EagleViewController::class, 'getDeliveryInfoEagleView'])->name('admin.getDeliveryInfoEagleView');
    });

    Route::middleware(['checkAdminOrStoreOwner'])->group(function () {
        Route::post('/update-delivery-guys-stores', [AdminController::class, 'updateDeliveryGuysRestaurants'])->name('admin.updateDeliveryGuysRestaurants');
    });

    Route::middleware(['checkAdminOrStoreOwner'])->group(function () {
        Route::get('/manage-store-owners', [AdminController::class, 'manageRestaurantOwners'])->name('admin.manageRestaurantOwners');
        Route::get('/storeOwnerUsersDatatable', [Datatables\StoreOwnerUsersDatatable::class, 'storeOwnerUsersDatatable'])->name('admin.storeOwnerUsersDatatable');
        Route::get('/manage-store-owners-stores/{id}', [AdminController::class, 'getManageRestaurantOwnersRestaurants'])->name('admin.get.getManageRestaurantOwnersRestaurants');
        Route::get('/store-manage-store-owners-stores/{id}', [AdminController::class, 'getManageRestaurantOwnersRestaurantstore'])->name('store.get.getManageRestaurantOwnersRestaurants');
    });

    Route::middleware(['checkAdminOrStoreOwner'])->group(function () {
        Route::post('/update-store-owners-stores', [AdminController::class, 'updateManageRestaurantOwnersRestaurants'])->name('admin.updateManageRestaurantOwnersRestaurants');
    });

    Route::middleware(['checkAdminOrStoreOwner'])->group(function () {
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/usersDatatable', [Datatables\UsersDatatable::class, 'usersDatatable'])->name('admin.usersDatatable');

        Route::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
        Route::get('/customerDatatable', [Datatables\CustomersDatatable::class, 'customerDatatable'])->name('admin.customerDatatable');

        Route::get('/staffs', [AdminController::class, 'staffs'])->name('admin.staffs');
        Route::get('/managerDatatable', [Datatables\ManagersDatatable::class, 'managerDatatable'])->name('admin.managerDatatable');

        
    });
   
    Route::middleware(['checkAdminOrStoreOwner'])->group(function () {
         Route::get('/user/edit/{id}', [AdminController::class, 'getEditUser'])->name('admin.get.editUser');
         Route::get('/storeuser/edit/{id}', [AdminController::class, 'getEditUserstore'])->name('store.get.editUser');
        Route::post('/saveNewUser', [AdminController::class, 'saveNewUser'])->name('admin.saveNewUser');
        Route::post('/user/edit/save', [AdminController::class, 'updateUser'])->name('admin.updateUser');
        Route::get('/user/ban/{id}', [AdminController::class, 'banUser'])->name('admin.banUser');
        Route::post('/user/delete-address', [AdminController::class, 'deleteUserAddress'])->name('admin.deleteUserAddress');
        Route::get('/delivery-ratings/{id}', [RatingReviewController::class, 'viewDeliveryReviews'])->name('admin.viewDeliveryReviews');
    });

    Route::middleware(['checkAdminOrStoreOwner'])->group(function () {
        Route::post('/user/add-money-to-wallet', [AdminController::class, 'addMoneyToWallet'])->name('admin.addMoneyToWallet');
        Route::post('/user/substract-money-from-wallet', [AdminController::class, 'substractMoneyFromWallet'])->name('admin.substractMoneyFromWallet');
    });

    Route::middleware(['checkAdminOrStoreOwner'])->group(function () {
        Route::get('/wallet/transactions', [AdminController::class, 'walletTransactions'])->name('admin.walletTransactions');
        Route::get('/wallet/searchWalletTransactions', [AdminController::class, 'searchWalletTransactions'])->name('admin.searchWalletTransactions');
    });

    Route::middleware('permission:settings_manage')->group(function () {
        Route::get('/settings', [SettingController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [SettingController::class, 'saveSettings'])->name('admin.saveSettings');
        Route::post('/settings/send-test-mail', [SettingController::class, 'sendTestmail'])->name('admin.sendTestmail');
        Route::post('/settings/payment-gateways-toggle', [PaymentController::class, 'togglePaymentGateways'])->name('admin.togglePaymentGateways');

        Route::get('/backup/files', [BackupController::class, 'filesBackup'])->name('admin.filesBackup');
        Route::get('/backup/database', [BackupController::class, 'dbBackup'])->name('admin.dbBackup');

        Route::get('/fix-update-issues', [AdminController::class, 'fixUpdateIssues'])->name('admin.fixUpdateIssues');
        Route::post('/force-clear', [SettingController::class, 'forceClear'])->name('admin.forceClear');
        Route::post('/clean-everything', [SettingController::class, 'cleanEverything'])->name('admin.cleanEverything');

        Route::get('/clean-activity-logs', [SettingController::class, 'deleteJunkActivityLogs'])->name('admin.deleteJunkActivityLogs');

        Route::post('/saveSpecificSettings', [SettingController::class, 'saveSpecificSettings'])->name('admin.saveSpecificSettings');
    });

    Route::middleware('permission:order_view')->group(function () {
        Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
        Route::get('/ordersDataTable', [Datatables\OrdersDatatable::class, 'ordersDataTableAdmin'])->name('admin.ordersDataTable');
        Route::get('/order/{order_id}', [AdminController::class, 'viewOrder'])->name('admin.viewOrder');
        Route::get('/printbill/{order_id}', [AdminController::class, 'printThermalBill'])->name('admin.printThermalBill');
        Route::get('/order/{order_id}/get-delivery-guy-info', [AdminController::class, 'getOrderDeliveryGuyInfo'])->name('admin.getOrderDeliveryGuyInfo');
    });

    Route::middleware('permission:order_actions')->group(function () {
        Route::post('/order/cancel-order', [AdminController::class, 'cancelOrderFromAdmin'])->name('admin.cancelOrderFromAdmin');
        Route::post('/order/accept-order', [AdminController::class, 'acceptOrderFromAdmin'])->name('admin.acceptOrderFromAdmin');
        Route::get('/order/confirm-order/{id}', [AdminController::class, 'confirmScheduledOrder'])->name('admin.confirmScheduledOrder');
        Route::post('/order/assign-delivery', [AdminController::class, 'assignDeliveryFromAdmin'])->name('admin.assignDeliveryFromAdmin');
        Route::post('/order/reassign-delivery', [AdminController::class, 'reAssignDeliveryFromAdmin'])->name('admin.reAssignDeliveryFromAdmin');
        Route::get('/approve-payment-of-order/{order_id}', [AdminController::class, 'approvePaymentOfOrder'])->name('admin.approvePaymentOfOrder');
    });

    Route::middleware('permission:promo_sliders_manage')->group(function () {
        Route::get('/sliders', [AdminController::class, 'sliders'])->name('admin.sliders');
        Route::get('/sliders/disable/{id}', [AdminController::class, 'disableSlider'])->name('admin.disableSlider');
        Route::get('/sliders/delete/{id}', [AdminController::class, 'deleteSlider'])->name('admin.deleteSlider');
        Route::get('/sliders/{id}', [AdminController::class, 'getEditSlider'])->name('admin.get.editSlider');
        Route::post('/slider/create', [AdminController::class, 'createSlider'])->name('admin.createSlider');
        Route::post('/slider/save', [AdminController::class, 'saveSlide'])->name('admin.saveSlide');
        Route::post('/sliders/edit/save', [AdminController::class, 'updateSlider'])->name('admin.updateSlider');

        Route::get('/slider/delete/{id}', [AdminController::class, 'deleteSlide'])->name('admin.deleteSlide');
        Route::get('/slider/disable/{id}', [AdminController::class, 'disableSlide'])->name('admin.disableSlide');

        Route::get('/slide/edit/{id}', [AdminController::class, 'editSlide'])->name('admin.editSlide');
        Route::post('/slide/edit/save', [AdminController::class, 'updateSlide'])->name('admin.updateSlide');
        Route::post('/slide/edit/position/save', [AdminController::class, 'updateSlidePosition'])->name('admin.updateSlidePosition');
    });

    Route::middleware('permission:stores_view')->group(function () {
        Route::get('/stores', [AdminController::class, 'restaurants'])->name('admin.restaurants');
        Route::get('/storesDatatable', [Datatables\StoresDatatable::class, 'storesDatatable'])->name('admin.storesDatatable');
    });

    Route::middleware('permission:stores_sort')->group(function () {
        Route::get('/stores/sort', [AdminController::class, 'sortStores'])->name('admin.sortStores');
        Route::post('/stores/sort/save', [AdminController::class, 'updateStorePosition'])->name('admin.updateStorePosition');
    });

    Route::middleware('permission:approve_stores')->group(function () {
        Route::get('/stores/pending-acceptance', [AdminController::class, 'pendingAcceptance'])->name('admin.pendingAcceptance');
        Route::get('/stores/pending-acceptance/accept/{id}', [AdminController::class, 'acceptRestaurant'])->name('admin.acceptRestaurant');
    });

    Route::middleware('permission:stores_edit')->group(function () {
        Route::get('/store/edit/{id}', [AdminController::class, 'getEditRestaurant'])->name('admin.get.editRestaurant');
        Route::get('/store/disable/{id}', [AdminController::class, 'disableRestaurant'])->name('admin.disableRestaurant');
        Route::get('/store/delete/{id}', [AdminController::class, 'deleteRestaurant'])->name('admin.deleteRestaurant');
        Route::post('/store/edit/save', [AdminController::class, 'updateRestaurant'])->name('admin.updateRestaurant');
        Route::post('/store/update-slug', [AdminController::class, 'updateSlug'])->name('admin.updateSlug');
        Route::post('/update-store-payout-details', [AdminController::class, 'updateStorePayoutDetails'])->name('admin.updateStorePayoutDetails');

        Route::get('/store-ratings/{id}', [RatingReviewController::class, 'viewStoreReviews'])->name('admin.viewStoreReviews');
        Route::post('/rating/update', [RatingReviewController::class, 'updateStoreReview'])->name('admin.updateStoreReview');

        Route::post('/store/schedule/save', [AdminController::class, 'updateRestaurantScheduleData'])->name('admin.updateRestaurantScheduleData');
    });

    Route::middleware('permission:stores_add')->group(function () {
        Route::post('/store/new/save', [AdminController::class, 'saveNewRestaurant'])->name('admin.saveNewRestaurant');
        Route::post('/store/bulk/save', [BulkUploadController::class, 'restaurantBulkUpload'])->name('admin.restaurantBulkUpload');
    });

    Route::middleware('permission:addon_categories_view')->group(function () {
        Route::get('/addoncategories', [AdminController::class, 'addonCategories'])->name('admin.addonCategories');
        Route::get('/addoncategories/searchAddonCategories', [AdminController::class, 'searchAddonCategories'])->name('admin.post.searchAddonCategories');
        Route::get('/addoncategory/edit/{id}', [AdminController::class, 'getEditAddonCategory'])->name('admin.editAddonCategory');
        Route::get('/addoncategory/get-addons/{id}', [AdminController::class, 'addonsOfAddonCategory'])->name('admin.addonsOfAddonCategory');
    });

    Route::middleware('permission:addon_categories_edit')->group(function () {
        Route::post('/addoncategory/edit/save', [AdminController::class, 'updateAddonCategory'])->name('admin.updateAddonCategory');
    });

    Route::middleware('permission:addon_categories_add')->group(function () {
        Route::get('/addoncategory/new', [AdminController::class, 'newAddonCategory'])->name('admin.newAddonCategory');
        Route::post('/addoncategory/new/save', [AdminController::class, 'saveNewAddonCategory'])->name('admin.saveNewAddonCategory');
    });

    Route::middleware('permission:addons_view')->group(function () {
        Route::get('/addons', [AdminController::class, 'addons'])->name('admin.addons');
        Route::get('/addons/searchAddons', [AdminController::class, 'searchAddons'])->name('admin.post.searchAddons');
        Route::get('/addon/edit/{id}', [AdminController::class, 'getEditAddon'])->name('admin.editAddon');
    });

    Route::middleware('permission:addons_edit')->group(function () {
        Route::post('/addon/edit/save', [AdminController::class, 'updateAddon'])->name('admin.updateAddon');
    });

    Route::middleware('permission:addons_actions')->group(function () {
        Route::get('/addon/disable/{id}', [AdminController::class, 'disableAddon'])->name('admin.disableAddon');
        Route::get('/addon/delete/{id}', [AdminController::class, 'deleteAddon'])->name('admin.deleteAddon');
    });

    Route::middleware('permission:addons_add')->group(function () {
        Route::post('/addon/new/save', [AdminController::class, 'saveNewAddon'])->name('admin.saveNewAddon');
    });

    Route::middleware('permission:items_view')->group(function () {
        Route::get('/items', [AdminController::class, 'items'])->name('admin.items');
        Route::get('/adminItemsDatatable', [Datatables\AdminItemsDatatable::class, 'itemsDatatable'])->name('admin.adminItemsDatatable');

        Route::get('/items/searchItems', [AdminController::class, 'searchItems'])->name('admin.post.searchItems');
        Route::get('/item/edit/{id}', [AdminController::class, 'getEditItem'])->name('admin.get.editItem');
        Route::get('/item/remove-image/{id}', [AdminController::class, 'removeItemImage'])->name('admin.removeItemImage');

        Route::get('/store/{restaurant_id}/items', [AdminController::class, 'getRestaurantItems'])->name('admin.getRestaurantItems');
    });

    Route::middleware('permission:items_actions')->group(function () {
        Route::get('/item/disable/{id}', [AdminController::class, 'disableItem'])->name('admin.disableItem');
    });

    Route::middleware('permission:items_edit')->group(function () {
        Route::post('/item/edit/save', [AdminController::class, 'updateItem'])->name('admin.updateItem');

        Route::get('/items/sort/{restaurant_id}', [AdminController::class, 'sortMenusAndItems'])->name('admin.sortMenusAndItems');
        Route::post('/items/sort/save', [AdminController::class, 'updateItemPositionForStore'])->name('admin.updateItemPositionForStore');
        Route::post('/itemcategories/sort/save', [AdminController::class, 'updateMenuCategoriesPositionForStore'])->name('admin.updateMenuCategoriesPositionForStore');
    });

    Route::middleware('permission:items_add')->group(function () {
        Route::post('/item/new/save', [AdminController::class, 'saveNewItem'])->name('admin.saveNewItem');
        Route::post('/item/bulk/save', [BulkUploadController::class, 'itemBulkUpload'])->name('admin.itemBulkUpload');
    });

    Route::middleware('permission:menu_categories_view')->group(function () {
        Route::get('/itemcategories', [AdminController::class, 'itemcategories'])->name('admin.itemcategories');
        Route::get('/itemCategoriesDataTable', [Datatables\ItemCategoriesDatatable::class, 'itemCategoriesDataTable'])->name('admin.itemCategoriesDataTable');
    });

    Route::middleware('permission:menu_categories_edit')->group(function () {
        Route::post('/itemcategories/new/save', [AdminController::class, 'createItemCategory'])->name('admin.createItemCategory');
    });

    Route::middleware('permission:menu_categories_actions')->group(function () {
        Route::get('/itemcategory/disable/{id}', [AdminController::class, 'disableCategory'])->name('admin.disableCategory');
    });

    Route::middleware('permission:menu_categories_add')->group(function () {
        Route::post('/itemcategory/edit/save', [AdminController::class, 'updateItemCategory'])->name('admin.updateItemCategory');
    });

    Route::middleware('permission:coupons_manage')->group(function () {
        Route::get('/coupons', [CouponController::class, 'coupons'])->name('admin.coupons');
        Route::post('/coupon/new/save', [CouponController::class, 'saveNewCoupon'])->name('admin.post.saveNewCoupon');
        Route::get('/coupon/edit/{id}', [CouponController::class, 'getEditCoupon'])->name('admin.get.getEditCoupon');
        Route::post('/coupon/edit/save', [CouponController::class, 'updateCoupon'])->name('admin.updateCoupon');
        Route::get('/coupon/delete/{id}', [CouponController::class, 'deleteCoupon'])->name('admin.deleteCoupon');
    });

    Route::middleware('permission:send_notification_manage')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'notifications'])->name('admin.notifications');
        Route::post('/notifications/upload', [NotificationController::class, 'uploadNotificationImage'])->name('admin.uploadNotificationImage');
        Route::post('/notifications/send', [NotificationController::class, 'sendNotifiaction'])->name('admin.sendNotifiaction');
        Route::post('/notification-to-users/send', [NotificationController::class, 'sendNotificationToSelectedUsers'])->name('admin.sendNotificationToSelectedUsers');
        Route::post('/notification-to-non-registered/send', [NotificationController::class, 'sendNotificationToNonRegisteredAppUsers'])->name('admin.sendNotificationToNonRegisteredAppUsers');
        Route::get('/delete-alerts-junk', [NotificationController::class, 'deleteAlertsJunk'])->name('admin.deleteAlertsJunk');
        Route::get('notifications/getUsersToSendNotification', [NotificationController::class, 'getUsersToSendNotification'])->name('admin.getUsersToSendNotification');
    });

    Route::middleware('permission:popular_location_manage')->group(function () {
        Route::get('/popular-geo-locations', [AdminController::class, 'popularGeoLocations'])->name('admin.popularGeoLocations');
        Route::post('/popular-geo-location/new/save', [AdminController::class, 'saveNewPopularGeoLocation'])->name('admin.saveNewPopularGeoLocation');
        Route::get('/popular-geo-location/disable/{id}', [AdminController::class, 'disablePopularGeoLocation'])->name('admin.disablePopularGeoLocation');
        Route::get('/popular-geo-location/delete/{id}', [AdminController::class, 'deletePopularGeoLocation'])->name('admin.deletePopularGeoLocation');
        Route::get('/popular-geo-location/make-default/{id}', [AdminController::class, 'makeDefaultLocation'])->name('admin.makeDefaultLocation');
    });

    Route::middleware('permission:pages_manage')->group(function () {
        Route::get('/pages', [AdminController::class, 'pages'])->name('admin.pages');
        Route::post('/page/new/save', [AdminController::class, 'saveNewpage'])->name('admin.saveNewPage');
        Route::get('/page/edit/{id}', [AdminController::class, 'getEditPage'])->name('admin.getEditPage');
        Route::post('/page/edit/save', [AdminController::class, 'updatePage'])->name('admin.updatePage');
        Route::get('/page/delete/{id}', [AdminController::class, 'deletePage'])->name('admin.deletePage');
    });

    Route::middleware('permission:store_payouts_manage')->group(function () {
        Route::get('/store-payouts', [AdminController::class, 'restaurantpayouts'])->name('admin.restaurantpayouts');
        Route::get('/store-payouts/{id}', [AdminController::class, 'viewRestaurantPayout'])->name('admin.viewRestaurantPayout');
        Route::post('/store-payouts/save', [AdminController::class, 'updateRestaurantPayout'])->name('admin.updateRestaurantPayout');
    });

    Route::middleware('permission:translations_manage')->group(function () {
        Route::get('/translations', [AdminController::class, 'translations'])->name('admin.translations');
        Route::get('/translation/new', [AdminController::class, 'newTranslation'])->name('admin.newTranslation');
        Route::post('/translation/new/save', [AdminController::class, 'saveNewTranslation'])->name('admin.saveNewTranslation');
        Route::get('/translation/edit/{id}', [AdminController::class, 'editTranslation'])->name('admin.editTranslation');
        Route::post('/translation/edit/save', [AdminController::class, 'updateTranslation'])->name('admin.updateTranslation');
        Route::get('/translation/disable/{id}', [AdminController::class, 'disableTranslation'])->name('admin.disableTranslation');
        Route::get('/translation/delete/{id}', [AdminController::class, 'deleteTranslation'])->name('admin.deleteTranslation');
        Route::get('/translation/make-default/{id}', [AdminController::class, 'makeDefaultLanguage'])->name('admin.makeDefaultLanguage');
    });

    Route::middleware('permission:delivery_collection_manage')->group(function () {
        Route::get('/delivery-collections', [DeliveryCollectionController::class, 'deliveryCollections'])->name('admin.deliveryCollections');
        Route::get('/deliveryCollectionDatatable', [Datatables\DeliveryCollectionDatatable::class, 'deliveryCollectionDatatable'])->name('admin.deliveryCollectionDatatable');

        Route::post('/delivery-collection/collect/{id}', [DeliveryCollectionController::class, 'collectDeliveryCollection'])->name('admin.collectDeliveryCollection');
    });

    Route::middleware('permission:delivery_collection_logs_view')->group(function () {
        Route::get('/delivery-collection-logs', [DeliveryCollectionController::class, 'deliveryCollectionLogs'])->name('admin.deliveryCollectionLogs');
        Route::get('/deliveryCollectionLogDatatable', [Datatables\DeliveryCollectionLogDatatable::class, 'deliveryCollectionLogDatatable'])->name('admin.deliveryCollectionLogDatatable');
    });

    Route::middleware('permission:store_category_sliders_manage')->group(function () {
        Route::get('/store-category-slider', [RestaurantCategoryController::class, 'restaurantCategorySlider'])->name('admin.restaurantCategorySlider');
        Route::get('/store-category-slider/delete/{id}', [RestaurantCategoryController::class, 'deleteRestaurantCategorySlide'])->name('admin.deleteRestaurantCategorySlide');
        Route::get('/store-category-slider/disable/{id}', [RestaurantCategoryController::class, 'disableRestaurantCategorySlide'])->name('admin.disableRestaurantCategorySlide');
        Route::post('/store-category-slider/new', [RestaurantCategoryController::class, 'newRestaurantCategory'])->name('admin.newRestaurantCategory');
        Route::post('/store-category-slider/update', [RestaurantCategoryController::class, 'updateRestaurantCategory'])->name('admin.updateRestaurantCategory');
        Route::post('/store-category-slider/save-settings', [RestaurantCategoryController::class, 'saveRestaurantCategorySliderSettings'])->name('admin.saveRestaurantCategorySliderSettings');
        Route::post('/create-store-category-slide', [RestaurantCategoryController::class, 'createRestaurantCategorySlide'])->name('admin.createRestaurantCategorySlide');
        Route::post('/store-category-slider/edit/position/save', [RestaurantCategoryController::class, 'updateCategorySlidePosition'])->name('admin.updateCategorySlidePosition');
    });

    Route::middleware('role:Admin')->group(function () {
        Route::get('/modules', [ModuleController::class, 'modules'])->name('admin.modules');
        Route::post('/module/upload', [ModuleController::class, 'uploadModuleZipFile'])->name('admin.uploadModuleZipFile');
        Route::post('/module/install', [ModuleController::class, 'installModule'])->name('admin.installModule');
        Route::get('/module/disable/{id}', [ModuleController::class, 'disableModule'])->name('admin.disableModule');
        Route::get('/module/enable/{id}', [ModuleController::class, 'enableModule'])->name('admin.enableModule');

        Route::get('/update-foodomaa', [UpdateController::class, 'updateFoodomaa'])->name('admin.updateFoodomaa');
        Route::get('/update-foodomaa-now', [UpdateController::class, 'updateFoodomaaNow'])->name('admin.updateFoodomaaNow');
        Route::post('/update-foodomaa/upload', [UpdateController::class, 'uploadUpdateZipFile'])->name('admin.uploadUpdateZipFile');
    });

    Route::middleware('permission:reports_view')->group(function () {
        Route::get('/reports/top-items', [ReportController::class, 'viewTopItems'])->name('admin.viewTopItems');
    });

    Route::middleware('permission:login_as_store_owner')->group(function () {
        Route::get('/impersonate/{id}', [AdminController::class, 'impersonate'])->name('admin.impersonate');
    });

    Route::middleware('role:Admin')->group(function () {
        Route::get('/roles-and-permission-management', [RolesAndPermissionController::class, 'index'])->name('admin.rolesManagement');
        Route::post('/roles-and-permission-management/save', [RolesAndPermissionController::class, 'createNewRoleWithPermissions'])->name('admin.createNewRoleWithPermissions');
        Route::get('/roles-and-permission-management/edit/{id}', [RolesAndPermissionController::class, 'editRoleAndPermissions'])->name('admin.editRoleAndPermissions');
        Route::post('/roles-and-permission-management/update', [RolesAndPermissionController::class, 'updateRoleAndPermissions'])->name('admin.updateRoleAndPermissions');

        Route::get('/server-stats', [ServerStatsController::class, 'getServerStatsPage'])->name('admin.getServerStatsPage');
        Route::get('/server-stats-data', [ServerStatsController::class, 'getServerStatsData'])->name('admin.getServerStatsData');
    });

    Route::prefix('/utility')->middleware('role:Admin')->group(function () {
        Route::get('/', [UtilityController::class, 'index'])->name('admin.utility.index');
        Route::get('/toggle-all-stores-status/{status}', [UtilityController::class, 'toggleStoreStatus'])->name('admin.utility.toggleStoreStatus');
    });

    Route::middleware('role:Admin')->group(function () {
        Route::get('/zones', [ZoneController::class, 'zones'])->name('admin.zones');
        Route::post('/zone/new', [ZoneController::class, 'saveNewZone'])->name('admin.saveNewZone');

        Route::get('/zone/edit/{id}', [ZoneController::class, 'editZone'])->name('admin.editZone');
        Route::post('/zone/update', [ZoneController::class, 'updateZone'])->name('admin.updateZone');
    });

    Route::get('/manager', [AdminController::class, 'manager'])->name('admin.manager');

    Route::get('/accept-notice', [AdminController::class, 'acceptNotice'])->name('admin.acceptNotice');

    Route::get('/firebase-connection/push', [FirebaseController::class, 'pushNewOrder']);
    Route::get('/firebase-connection/remove', [FirebaseController::class, 'removeOrder']);

    Route::middleware('permission:dashboard_view')->group(function () {
        Route::get('/get-dashboard-stats', [AdminPartialsController::class, 'dashboardStats'])->name('admin.partials.dashboardStats');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/dashboard/save-todo-note', [TodoNotesController::class, 'saveTodoNote'])->name('admin.saveTodoNote');
        Route::get('/dashboard/delete-todo-note', [TodoNotesController::class, 'deleteTodoNote'])->name('admin.deleteTodoNote');
        Route::get('/dashboard/update-todo-note', [TodoNotesController::class, 'updateTodoNote'])->name('admin.updateTodoNote');
        Route::get('/dashboard/getFoodomaaNews', [FoodomaaNewsController::class, 'getFoodomaaNews'])->name('admin.getFoodomaaNews');
        Route::post('/dashboard/makeFoodomaaNewsRead', [FoodomaaNewsController::class, 'makeFoodomaaNewsRead'])->name('admin.makeFoodomaaNewsRead');
    });
});
/* END Admin Routes */
