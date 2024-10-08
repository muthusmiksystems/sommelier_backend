<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\FilesChecksumController;
use App\Http\Controllers\GeocoderController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PromoSliderController;
use App\Http\Controllers\RatingReviewController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\RestaurantCategoryController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\RestaurantOwnerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StoreOwner;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/* API ROUTES */

Route::post('files-checksum', [FilesChecksumController::class, 'filesCheck']);

Route::post('/coordinate-to-address', [GeocoderController::class, 'coordinatesToAddress']);

Route::post('/address-to-coordinate', [GeocoderController::class, 'addressToCoordinates']);
Route::post('/get_address_lat_lng', [GeocoderController::class, 'get_address_lat_lng']);

Route::post('/get-settings', [SettingController::class, 'getSettings']);

Route::get('/get-setting/{key}', [SettingController::class, 'getSettingByKey']);

Route::post('/search-location/{query}', [LocationController::class, 'searchLocation']);

Route::post('/popular-locations', [LocationController::class, 'popularLocations']);

Route::post('/popular-geo-locations', [LocationController::class, 'popularGeoLocations']);

Route::post('/promo-slider', [PromoSliderController::class, 'promoSlider']);
Route::post('/promo-slider-new', [PromoSliderController::class, 'promoSliderNew']);

Route::post('/promo-slider-app', [PromoSliderController::class, 'promoSliderapp']);

Route::post('/get-delivery-restaurants', [RestaurantController::class, 'getDeliveryRestaurants']);

Route::post('/get-selfpickup-restaurants', [RestaurantController::class, 'getSelfPickupRestaurants']);

Route::post('/get-restaurant-info/{slug}', [RestaurantController::class, 'getRestaurantInfo']);

Route::post('/get-restaurant-info-by-id/{id}', [RestaurantController::class, 'getRestaurantInfoById']);

Route::post('/get-restaurant-info-and-operational-status', [RestaurantController::class, 'getRestaurantInfoAndOperationalStatus']);

Route::post('/get-restaurant-items/{slug}', [RestaurantController::class, 'getRestaurantItems']);

Route::post('/get-pages', [PageController::class, 'getPages']);

Route::post('/get-single-page', [PageController::class, 'getSinglePage']);

Route::post('/search-restaurants', [RestaurantController::class, 'searchRestaurants']);

Route::post('/send-otp', [SmsController::class, 'sendOtp']);
Route::post('/verify-otp', [SmsController::class, 'verifyOtp']);
Route::post('/check-restaurant-operation-service', [RestaurantController::class, 'checkRestaurantOperationService']);

Route::post('/get-single-item', [RestaurantController::class, 'getSingleItem']);

Route::post('/get-all-languages', [LanguageController::class, 'getAllLanguages']);

Route::post('/get-single-language', [LanguageController::class, 'getSingleLanguage']);

Route::post('/get-restaurant-category-slides', [RestaurantCategoryController::class, 'getRestaurantCategorySlider']);

Route::post('/get-all-restaurants-categories', [RestaurantCategoryController::class, 'getAllRestaurantsCategories']);

Route::post('/get-filtered-restaurants', [RestaurantController::class, 'getFilteredRestaurants']);

Route::post('/send-password-reset-mail', [PasswordResetController::class, 'sendPasswordResetMail']);

Route::post('/verify-password-reset-otp', [PasswordResetController::class, 'verifyPasswordResetOtp']);

Route::post('/change-user-password', [PasswordResetController::class, 'changeUserPassword']);

Route::post('/check-cart-items-availability', [RestaurantController::class, 'checkCartItemsAvailability']);

Route::get('/stripe-redirect-capture', [PaymentController::class, 'stripeRedirectCapture'])->name('stripeRedirectCapture');

/* Paytm */
Route::get('/payment/paytm/{order_id}', [PaymentController::class, 'payWithPaytm']);
Route::post('/payment/process-paytm', [PaymentController::class, 'processPaytm']);
/* END Paytm */

Route::get('/get-store-reviews/{slug}', [RatingReviewController::class, 'getRatingAndReview']);

Route::get('/payment/verify-khalti-payment', [PaymentController::class, 'verifyKhaltiPayment']);

Route::post('/save-notification-token-no-user', [NotificationController::class, 'saveTokenNoUser']);
Route::post('/save-notification-token', [NotificationController::class, 'saveToken']);
/* Custom API's Developed */

Route::post('/get-delivery-restaurants-by-owner-id', [RestaurantController::class, 'getDeliveryRestaurantsByOwnerId']);

/* End Custom API's Developed */

/* Protected Routes for Loggedin users */
Route::middleware('jwt.auth')->group(function () {
    Route::post('/get-ratable-order', [RatingReviewController::class, 'getRatableOrder']);

    Route::post('/rate-order', [RatingReviewController::class, 'rateOrder']);

    // Route::post('/get-store-reviews', [
    //     'uses' => 'RatingReviewController@getStoreReviews',
    // ]);

    Route::post('/get-restaurant-info-with-favourite/{slug}', [RestaurantController::class, 'getRestaurantInfoWithFavourite']);

    Route::post('/apply-coupon', [CouponController::class, 'applyCoupon']);



    Route::post('/update-app-token-for-user', [NotificationController::class, 'updateAppTokenForUser']);

    Route::post('/get-payment-gateways', [PaymentController::class, 'getPaymentGateways']);


    Route::post('/delete-address', [AddressController::class, 'deleteAddress']);

    Route::post('/check-running-order', [UserController::class, 'checkRunningOrder']);

    Route::middleware('isactiveuser')->group(function () {
        Route::post('/place-order', [OrderController::class, 'placeOrder']);
    });

    Route::post('/accept-stripe-payment', [PaymentController::class, 'acceptStripePayment']);

    Route::post('/set-default-address', [AddressController::class, 'setDefaultAddress']);
    Route::post('/get-orders', [OrderController::class, 'getOrders']);
    Route::post('/get-order-items', [OrderController::class, 'getOrderItems']);

    Route::post('/cancel-order', [OrderController::class, 'cancelOrder']);

    Route::post('/get-wallet-transactions', [UserController::class, 'getWalletTransactions']);

    Route::post('/mark-all-notifications-read', [NotificationController::class, 'markAllNotificationsRead']);
    Route::post('/mark-one-notification-read', [NotificationController::class, 'markOneNotificationRead']);

    Route::post('/delivery/update-user-info', [DeliveryController::class, 'updateDeliveryUserInfo']);

    Route::post('/delivery/get-delivery-orders', [DeliveryController::class, 'getDeliveryOrders']);

    Route::post('/delivery/get-single-delivery-order', [DeliveryController::class, 'getSingleDeliveryOrder']);

    Route::post('/delivery/set-delivery-guy-gps-location', [DeliveryController::class, 'setDeliveryGuyGpsLocation']);

    Route::post('/delivery/get-delivery-guy-gps-location', [DeliveryController::class, 'getDeliveryGuyGpsLocation']);

    Route::post('/delivery/accept-to-deliver', [DeliveryController::class, 'acceptToDeliver']);

    Route::post('/delivery/pickedup-order', [DeliveryController::class, 'pickedupOrder']);

    Route::post('/delivery/deliver-order', [DeliveryController::class, 'deliverOrder']);

    Route::post('/delivery/toggle-delivery-guy-status', [DeliveryController::class, 'updateDeliveryUserInfo']);

    Route::post('/delivery/get-completed-orders', [DeliveryController::class, 'getCompletedOrders']);

    Route::post('/conversation/chat', [ChatController::class, 'deliveryCustomerChat']);

    Route::post('/change-avatar', [UserController::class, 'changeAvatar']);

    Route::post('/check-ban', [UserController::class, 'checkBan']);

    Route::post('/toggle-favorite', [UserController::class, 'toggleFavorite']);

    Route::post('/get-favorite-stores', [RestaurantController::class, 'getFavoriteStores']);

    Route::post('/update-tax-number', [UserController::class, 'updateTaxNumber']);
});
/* END Protected Routes */
Route::post('/get-addresses', [AddressController::class, 'getAddresses']);
Route::post('/save-address', [AddressController::class, 'saveAddress']);
Route::post('/update-user-info', [UserController::class, 'updateUserInfo']);
Route::post('/update-user-profile', [UserController::class, 'updateUserProfile']);
Route::post('/get-user-notifications', [NotificationController::class, 'getUserNotifications']);
Route::post('/send-notifications', [NotificationController::class, 'sendPushNotifications']);
Route::post('/apply-coupon-app', [CouponController::class, 'applyCouponApp']);
Route::post('/accept-stripe-payment-app', [PaymentController::class, 'acceptStripePaymentApp']);
Route::post('/place-order-app', [OrderController::class, 'placeOrderApp']);
Route::post('/check-running-order-app', [UserController::class, 'checkRunningOrderApp']);
Route::get('/restaurant-venue/{id}', [RestaurantOwnerController::class, 'restaurantVenue']);
Route::get('/restaurantbook/settings/{id}', [RestaurantOwnerController::class, 'getSettingsRestaurantbooking']);

/*Razorpay APIs*/
Route::post('/payment/razorpay/create-order', [RazorpayController::class, 'razorPayCreateOrder']);
Route::post('/payment/razorpay/process', [RazorpayController::class, 'processRazorpayPayment']);
Route::post('/payment/razorpay/webhook', [RazorpayController::class, 'webhook']);
/*END Razorpay APIs*/

Route::post('/payment/process-razor-pay', [PaymentController::class, 'processRazorpay']);

Route::get('/payment/process-mercado-pago/{id}', [PaymentController::class, 'processMercadoPago']);
Route::get('/payment/return-mercado-pago', [PaymentController::class, 'returnMercadoPago']);

Route::post('/payment/process-paymongo', [PaymentController::class, 'processPaymongo']);
Route::get('/payment/handle-process-paymongo/{id}', [PaymentController::class, 'handlePayMongoRedirect']);

/* Auth Routes */
Route::post('/login', [UserController::class, 'login']);
Route::post('/login/google', [GoogleController::class, 'login']);
Route::post('/user/fingerprint', [UserController::class, 'addfingerprintuser']);

Route::post('/login-with-otp', [UserController::class, 'loginWithOtp']);
Route::post('/get-by-userId', [UserController::class, 'getbyuserid']);
Route::post('/generate-otp-for-login', [SmsController::class, 'generateOtpForLogin']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login-otp', [UserController::class, 'loginotp']);


Route::post('/delivery/login', [DeliveryController::class, 'login']);
Route::post('/app/bepozconnections', [RestaurantOwnerController::class, 'getrestaurantbepozcconnection']);
/* END Auth Routes */

/*Store App Routes */

Route::post('/store-owner/login', [StoreOwner\StoreOwnerAppController::class, 'login']);

Route::get('/store-owner/get-all-language', [StoreOwner\StoreOwnerAppController::class, 'getAllLanguage']);
Route::get('/store-owner/get-single-language/{language_code}', [StoreOwner\StoreOwnerAppController::class, 'getSingleLanguage']);

Route::post('/check-max-pax', [RestaurantOwnerController::class, 'checkMaxPax']);
Route::post('/new-booking/{slug}', [RestaurantOwnerController::class, 'bookingFromAnotherSites']);

Route::get('/store/shift-timing-ext/{id}/{date}', [RestaurantOwnerController::class, 'getShiftTimingExt'])->name('restaurant.get.shiftTimingExt');

Route::middleware('jwt.auth')->group(function () {
    Route::post('/store-owner/dashboard', [StoreOwner\StoreOwnerAppController::class, 'dashboard']);

    Route::post('/store-owner/toggle-store-status', [StoreOwner\StoreOwnerAppController::class, 'toggleStoreStatus']);

    Route::post('/store-owner/get-orders', [StoreOwner\StoreOwnerAppController::class, 'getOrders']);

    Route::post('/store-owner/get-single-order', [StoreOwner\StoreOwnerAppController::class, 'getSingleOrder']);

    Route::post('/store-owner/cancel-order', [StoreOwner\StoreOwnerAppController::class, 'cancelOrder']);

    Route::post('/store-owner/accept-order', [StoreOwner\StoreOwnerAppController::class, 'acceptOrder']);

    Route::post('/store-owner/mark-selfpickup-order-ready', [StoreOwner\StoreOwnerAppController::class, 'markSelfpickupOrderReady']);

    Route::post('/store-owner/mark-selfpickup-order-completed', [StoreOwner\StoreOwnerAppController::class, 'markSelfpickupOrderCompleted']);
    Route::post('/store-owner/confirm-scheduled-order', [StoreOwner\StoreOwnerAppController::class, 'confirmScheduledOrder']);

    Route::post('/store-owner/get-menu', [StoreOwner\StoreOwnerAppController::class, 'getMenu']);

    Route::post('/store-owner/toggle-item-status', [StoreOwner\StoreOwnerAppController::class, 'toggleItemStatus']);

    Route::post('/store-owner/search-items', [StoreOwner\StoreOwnerAppController::class, 'searchItems']);

    Route::post('/store-owner/edit-item', [StoreOwner\StoreOwnerAppController::class, 'editItem']);

    Route::post('/store-owner/update-item', [StoreOwner\StoreOwnerAppController::class, 'updateItem']);
    Route::post('/store-owner/get-past-orders', [StoreOwner\StoreOwnerAppController::class, 'getPastOrders']);
    Route::post('/store-owner/search-orders', [StoreOwner\StoreOwnerAppController::class, 'searchOrders']);

    Route::post('/store-owner/update-item-image', [StoreOwner\StoreOwnerAppController::class, 'updateItemImage']);

    Route::post('/store-owner/get-ratings', [StoreOwner\StoreOwnerAppController::class, 'getRatings']);

    Route::post('/store-owner/get-earnings', [StoreOwner\StoreOwnerAppController::class, 'getEarnings']);

    Route::post('/store-owner/send-payout-request', [StoreOwner\StoreOwnerAppController::class, 'sendPayoutRequest']);

    Route::post('/store-owner/get-inactive-items', [StoreOwner\StoreOwnerAppController::class, 'getInactiveItems']);

    Route::post('/store-owner/get-store-page', [StoreOwner\StoreOwnerAppController::class, 'getStorePage']);

    Route::post('/store-owner/toggle-category-status', [StoreOwner\StoreOwnerAppController::class, 'toggleCategoryStatus']);

    /* Custom API's Developed */
    Route::post('/get-by-id', [UserController::class, 'maitredeGetUser']);

    Route::post('/create-booking', [RestaurantOwnerController::class, 'maitredeAddBooking']);

    Route::post('/update-booking', [RestaurantOwnerController::class, 'maitredeUpdateBooking']);

    Route::get('/get-booking/{id}', [RestaurantOwnerController::class, 'maitredeGetBooking']);

    Route::get('/cancel-booking/{id}', [RestaurantOwnerController::class, 'maitredeCancelBooking']);

    Route::get('/get-restaurant-bookings/{id}', [RestaurantOwnerController::class, 'maitredeGetRestaurantBooking']);
    Route::get('/get-restaurant-tables/{id}', [RestaurantOwnerController::class, 'maitredeGetRestaurantTables']);
    Route::post('/get-user-by-restaurant-details/{id}', [UserController::class, 'getuserbyrestaurantdetails']);
    /* End Custom API's Developed */
});
