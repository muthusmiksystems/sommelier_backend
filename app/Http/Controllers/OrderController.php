<?php

namespace App\Http\Controllers;

use App\Addon;
use App\Coupon;
use App\Helpers\TranslationHelper;
use App\Item;
use App\Order;
use App\Orderitem;
use App\OrderItemAddon;
use App\PushNotify;
use App\Restaurant;
use App\RestaurantCustomerModel;
use App\RestaurantSettings;
use App\User;
use Hashids;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function placeOrder(Request $request, TranslationHelper $translationHelper): JsonResponse
    {
        $user = auth()->user();
        return response()->json($user);

        if ($user) {
            $keys = ['orderPaymentWalletComment', 'orderPartialPaymentWalletComment'];
            $translationData = $translationHelper->getDefaultLanguageValuesForKeys($keys);

            $newOrder = new Order();

            $lastOrder = Order::orderBy('id', 'desc')->first();

            if ($lastOrder) {
                $lastOrderId = $lastOrder->id;
                $newId = $lastOrderId + 1;
                $uniqueId = Hashids::encode($newId);
            } else {
                //first order
                $newId = 1;
            }

            $uniqueId = Hashids::encode($newId);
            $unique_order_id = 'OD' . '-' . date('m-d') . '-' . strtoupper(Str::random(4)) . '-' . strtoupper($uniqueId);
            $newOrder->unique_order_id = $unique_order_id;

            $restaurant_id = $request['order'][0]['restaurant_id'];
            $restaurant = Restaurant::where('id', $restaurant_id)->first();
            $restaurant_settings = RestaurantSettings::where('restaurant_id', $restaurant_id)->first();

            $newOrder->user_id = $user->id;
            if (isset($restaurant_id)) {
                try {
                    $restaurantuser = User::findOrFail($newOrder->user_id);

                    // Retrieve the role_id for 'Customer'
                    $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                    // Check if the record already exists
                    $existingRecord = RestaurantCustomerModel::where('user_id', $newOrder->user_id)
                        ->where('restaurant_id', $restaurant_id)
                        ->where('role_id', $role_id)
                        ->first();

                    // If the record does not exist, create it
                    if (!$existingRecord) {
                        RestaurantCustomerModel::create([
                            'role_id' => $role_id,
                            'user_id' => $newOrder->user_id,
                            'restaurant_id' => $restaurant_id,
                        ]);
                    }
                } catch (\Throwable $e) {
                    // Log the error for debugging
                    \Log::error('Error registering user: ' . $e->getMessage());

                    // Return detailed error response
                    $response = ['success' => false, 'data' => 'Could not register user. ' . $e->getMessage()];
                    return response()->json($response, 500);
                }
            }

            $newOrder->zone_id = $restaurant->zone_id ? $restaurant->zone_id : null;

            if ($request['pending_payment'] || $request['method'] == 'MERCADOPAGO' || $request['method'] == 'PAYTM' || $request['method'] == 'RAZORPAY') {
                $newOrder->orderstatus_id = '8';

                if (Module::find('OrderSchedule') && Module::find('OrderSchedule')->isEnabled()) {
                    if (isset($request->schedule_date) && $request->schedule_date != null && isset($request->schedule_slot) && $request->schedule_slot != null) {
                        $newOrder->is_scheduled = true;
                        $newOrder->schedule_date = json_encode($request->schedule_date);
                        $newOrder->schedule_slot = json_encode($request->schedule_slot);
                    }
                }
            } elseif ($restaurant->auto_acceptable) {
                $newOrder->orderstatus_id = '2';

                if (Module::find('OrderSchedule') && Module::find('OrderSchedule')->isEnabled()) {
                    if (isset($request->schedule_date) && $request->schedule_date != null && isset($request->schedule_slot) && $request->schedule_slot != null) {
                        $newOrder->orderstatus_id = '10';
                        $newOrder->is_scheduled = true;
                        $newOrder->schedule_date = json_encode($request->schedule_date);
                        $newOrder->schedule_slot = json_encode($request->schedule_slot);
                    }
                }

                if ($request->delivery_type == 1) {
                    sendSmsToDelivery($restaurant_id);
                }
                if (config('setting.enablePushNotificationOrders') == 'true') {
                    //to user
                    $notify = new PushNotify();
                    $notify->sendPushNotification('2', $newOrder->user_id, $newOrder->unique_order_id);
                }
            } else {
                $newOrder->orderstatus_id = '1';

                if (Module::find('OrderSchedule') && Module::find('OrderSchedule')->isEnabled()) {
                    if (isset($request->schedule_date) && $request->schedule_date != null && isset($request->schedule_slot) && $request->schedule_slot != null) {
                        $newOrder->orderstatus_id = '10';
                        $newOrder->is_scheduled = true;
                        $newOrder->schedule_date = json_encode($request->schedule_date);
                        $newOrder->schedule_slot = json_encode($request->schedule_slot);
                    }
                }
            }

            $newOrder->location = json_encode($request['location']);

            if ($request->delivery_type == 2) {
                $full_address = 'NA';
            } else {
                $full_address = $request['user']['data']['default_address']['house'] . ', ' . $request['user']['data']['default_address']['address'];
            }
            $newOrder->address = $full_address;

            //get restaurant charges
            $newOrder->restaurant_charge = $restaurant->restaurant_charges;

            $newOrder->transaction_id = $request->payment_token;

            $orderTotal = 0;
            foreach ($request['order'] as $oI) {
                $originalItem = Item::where('id', $oI['id'])->first();
                $orderTotal += ($originalItem->price * $oI['quantity']);

                if (isset($oI['selectedaddons'])) {
                    foreach ($oI['selectedaddons'] as $selectedaddon) {
                        $addon = Addon::where('id', $selectedaddon['addon_id'])->first();
                        if ($addon) {
                            $orderTotal += $addon->price * $oI['quantity'];
                        }
                    }
                }
            }
            $newOrder->sub_total = $orderTotal;

            if ($request->coupon) {
                $coupon = Coupon::where('code', strtoupper($request['coupon']['code']))->first();
                if ($coupon) {
                    $newOrder->coupon_name = $request['coupon']['code'];
                    if ($coupon->discount_type == 'PERCENTAGE') {
                        $percentage_discount = (($coupon->discount / 100) * $orderTotal);
                        if ($coupon->max_discount) {
                            if ($percentage_discount >= $coupon->max_discount) {
                                $percentage_discount = $coupon->max_discount;
                            }
                        }
                        $newOrder->coupon_amount = $percentage_discount;
                        $orderTotal = $orderTotal - $percentage_discount;
                    }
                    if ($coupon->discount_type == 'AMOUNT') {
                        $newOrder->coupon_amount = $coupon->discount;
                        $orderTotal = $orderTotal - $coupon->discount;
                    }
                    $coupon->count = $coupon->count + 1;
                    $coupon->save();
                }
            }

            if ($request->delivery_type == 1) {
                if (config('setting.enGDMA') == 'true') {
                    $distance = (float) $request->dis;
                } else {
                    $distance = getDistance($request['user']['data']['default_address']['latitude'], $request['user']['data']['default_address']['longitude'], $restaurant->latitude, $restaurant->longitude);
                }

                if ($restaurant->delivery_charge_type == 'DYNAMIC') {
                    //get distance between user and restaurant,

                    if ($distance > $restaurant->base_delivery_distance) {
                        $extraDistance = $distance - $restaurant->base_delivery_distance;
                        $extraCharge = ($extraDistance / $restaurant->extra_delivery_distance) * $restaurant->extra_delivery_charge;
                        $dynamicDeliveryCharge = $restaurant->base_delivery_charge + $extraCharge;

                        if (config('setting.enDelChrRnd') == 'true') {
                            $dynamicDeliveryCharge = ceil($dynamicDeliveryCharge);
                        }

                        $newOrder->delivery_charge = $dynamicDeliveryCharge;
                        $newOrder->actual_delivery_charge = $dynamicDeliveryCharge;
                    } else {
                        $newOrder->delivery_charge = $restaurant->base_delivery_charge;
                        $newOrder->actual_delivery_charge = $restaurant->base_delivery_charge;
                    }
                } else {
                    $newOrder->delivery_charge = $restaurant->delivery_charges;
                    $newOrder->actual_delivery_charge = $restaurant->delivery_charges;
                }

                $newOrder->distance = $distance;
            } else {
                //for selfpickup...
                $newOrder->delivery_charge = 0;
                $newOrder->actual_delivery_charge = 0;
            }

            //for free delivery above x subtotal
            if ($restaurant->free_delivery_subtotal > 0) {
                if ($newOrder->sub_total >= $restaurant->free_delivery_subtotal) {
                    $newOrder->delivery_charge = 0;
                }
            }

            $orderTotal = $orderTotal + $newOrder->delivery_charge;

            $orderTotal = $orderTotal + $restaurant->restaurant_charges;

            if (config('setting.taxApplicable') == 'true') {
                $taxAmount = $newOrder->tax = (($orderTotal - $restaurant->delivery_charges) / 11);
                //$newOrder->tax = config('setting.taxPercentage');

                //$taxAmount = (float) (((float) config('setting.taxPercentage') / 100) * $orderTotal);
            } else {
                $taxAmount = 0;
            }

            $newOrder->tax_amount = $taxAmount;

            $orderTotal = $orderTotal + $taxAmount;

            if (isset($request['tipAmount']) && !empty($request['tipAmount'])) {
                $orderTotal = $orderTotal + $request['tipAmount'];
            }

            //this is the final order total

            if ($request['method'] == 'COD') {
                if ($request->partial_wallet == true) {
                    //deduct all user amount and add
                    $newOrder->payable = $orderTotal - $user->balanceFloat;
                }
                if ($request->partial_wallet == false) {
                    $newOrder->payable = $orderTotal;
                }
            }

            $newOrder->total = $orderTotal;

            $newOrder->order_comment = $request['order_comment'];

            $newOrder->payment_mode = $request['method'];

            $newOrder->restaurant_id = $request['order'][0]['restaurant_id'];

            $newOrder->tip_amount = $request['tipAmount'];

            if ($request->delivery_type == 1) {
                //delivery
                $newOrder->delivery_type = 1;
            } else {
                //selfpickup
                $newOrder->delivery_type = 2;
            }

            $newOrder->cash_change_amount = $request['cash_change_amount'] ? $request['cash_change_amount'] : null;

            $newOrder->delivery_pin = substr(str_shuffle('123456789'), 0, 5);

            //process paypal payment
            if ($request['method'] == 'PAYPAL' || $request['method'] == 'PAYSTACK' || $request['method'] == 'RAZORPAY' || $request['method'] == 'STRIPE' || $request['method'] == 'PAYMONGO' || $request['method'] == 'MERCADOPAGO' || $request['method'] == 'PAYTM' || $request['method'] == 'FLUTTERWAVE' || $request['method'] == 'KHALTI') {
                //successfuly received payment
                $newOrder->save();
                if ($request->partial_wallet == true) {
                    $userWalletBalance = $user->balanceFloat;
                    $newOrder->wallet_amount = $userWalletBalance;
                    $newOrder->save();
                    //deduct all user amount and add
                    $user->withdraw($userWalletBalance * 100, ['description' => $translationData->orderPartialPaymentWalletComment . $newOrder->unique_order_id]);
                }
                foreach ($request['order'] as $orderItem) {
                    $item = new Orderitem();
                    $item->order_id = $newOrder->id;
                    $item->item_id = $orderItem['id'];
                    $item->name = $orderItem['name'];
                    $item->quantity = $orderItem['quantity'];
                    $item->price = $orderItem['price'];
                    $item->save();
                    if (isset($orderItem['selectedaddons'])) {
                        foreach ($orderItem['selectedaddons'] as $selectedaddon) {
                            $addon = new OrderItemAddon();
                            $addon->orderitem_id = $item->id;
                            $addon->addon_category_name = $selectedaddon['addon_category_name'];
                            $addon->addon_name = $selectedaddon['addon_name'];
                            $addon->addon_price = $selectedaddon['price'];
                            $addon->save();
                        }
                    }
                }

                //sms to Store Owner
                if (!$restaurant->auto_acceptable && $newOrder->orderstatus_id == '1' && config('setting.smsRestaurantNotify') == 'true') {
                    $restaurant_id = $request['order'][0]['restaurant_id'];
                    sendSmsToStoreOwner($restaurant_id, $orderTotal);
                }

                //push notification to Delivery Guy
                if ($restaurant->auto_acceptable && config('setting.enablePushNotification') && config('setting.enablePushNotificationOrders') == 'true') {
                    $restaurant_id = $request['order'][0]['restaurant_id'];
                    sendPushNotificationToDelivery($restaurant_id, $newOrder);
                }

                //push notification to Store Owner
                if (in_array($newOrder->orderstatus_id, ['1', '10'])) {
                    sendPushNotificationToStoreOwner($restaurant_id, $unique_order_id);
                }

                activity()
                    ->performedOn($newOrder)
                    ->causedBy($user)
                    ->withProperties(['type' => 'Order_Placed'])->log('Order placed');

                if ($newOrder->orderstatus_id == '2') {
                    activity()
                        ->performedOn($newOrder)
                        ->causedBy(User::find(1))
                        ->withProperties(['type' => 'Order_Accepted_Auto'])->log('Order auto accepted');
                }

                $response = [
                    'success' => true,
                    'data' => $newOrder,
                ];

                return response()->json($response);
            } else {
                $newOrder->save();
                if ($request['method'] == 'COD') {
                    if ($request->partial_wallet == true) {
                        $userWalletBalance = $user->balanceFloat;
                        $newOrder->wallet_amount = $userWalletBalance;
                        $newOrder->save();
                        //deduct all user amount and add
                        $user->withdraw($userWalletBalance * 100, ['description' => $translationData->orderPartialPaymentWalletComment . $newOrder->unique_order_id]);
                    }
                }

                //if method is WALLET, then deduct amount with appropriate description
                if ($request['method'] == 'WALLET') {
                    $userWalletBalance = $user->balanceFloat;
                    $newOrder->wallet_amount = $orderTotal;
                    $newOrder->save();
                    $user->withdraw($orderTotal * 100, ['description' => $translationData->orderPaymentWalletComment . $newOrder->unique_order_id]);
                }

                $bepoz_data = [];

                foreach ($request['order'] as $orderItem) {
                    $item = new Orderitem();
                    $item->order_id = $newOrder->id;
                    $item->item_id = $orderItem['id'];
                    $item->name = $orderItem['name'];
                    $item->quantity = $orderItem['quantity'];
                    $item->price = $orderItem['price'];
                    $item->save();
                    if (isset($orderItem['selectedaddons'])) {
                        foreach ($orderItem['selectedaddons'] as $selectedaddon) {
                            $addon = new OrderItemAddon();
                            $addon->orderitem_id = $item->id;
                            $addon->addon_category_name = $selectedaddon['addon_category_name'];
                            $addon->addon_name = $selectedaddon['addon_name'];
                            $addon->addon_price = $selectedaddon['price'];
                            $addon->save();
                        }
                    }

                    $bepoz_data[] = [
                        'bepoz_product_id' => $orderItem['bepoz_pid'],
                        'qty' => $orderItem['quantity'],
                        'rate' => $orderItem['price'],
                        'pos_size' => (!empty($orderItem['bepoz_psize'])) ? $orderItem['bepoz_psize'] : 0,
                    ];
                }

                //sms to Store Owner
                if (!$restaurant->auto_acceptable && $newOrder->orderstatus_id == '1' && config('setting.smsRestaurantNotify') == 'true') {
                    $restaurant_id = $request['order'][0]['restaurant_id'];
                    sendSmsToStoreOwner($restaurant_id, $orderTotal);
                }

                //push notification to Delivery Guy
                if ($restaurant->auto_acceptable && config('setting.enablePushNotification') && config('setting.enablePushNotificationOrders') == 'true') {
                    $restaurant_id = $request['order'][0]['restaurant_id'];
                    sendPushNotificationToDelivery($restaurant_id, $newOrder);
                }

                //push notification to Store Owner
                if (in_array($newOrder->orderstatus_id, ['1', '10'])) {
                    sendPushNotificationToStoreOwner($restaurant_id, $unique_order_id);
                }

                activity()
                    ->performedOn($newOrder)
                    ->causedBy($user)
                    ->withProperties(['type' => 'Order_Placed'])->log('Order placed');

                if ($newOrder->orderstatus_id == '2') {
                    activity()
                        ->performedOn($newOrder)
                        ->causedBy(User::find(1))
                        ->withProperties(['type' => 'Order_Accepted_Auto'])->log('Order auto accepted');
                }

                $response = [
                    'success' => true,
                    'data' => $newOrder,
                ];

                $this->bepozIntegration($request, $unique_order_id, $restaurant_settings, $bepoz_data, $newOrder->address);

                return response()->json($response);
            }
        }
    }
    public function placeOrderApp(Request $request, TranslationHelper $translationHelper): JsonResponse
    {
        $user = User::where('id', $request->user_id)->first();
        Log::info('Stripe Payment Request: ', $request->all());
        if ($user) {
            $keys = ['orderPaymentWalletComment', 'orderPartialPaymentWalletComment'];
            $translationData = $translationHelper->getDefaultLanguageValuesForKeys($keys);

            $newOrder = new Order();

            $lastOrder = Order::orderBy('id', 'desc')->first();

            if ($lastOrder) {
                $lastOrderId = $lastOrder->id;
                $newId = $lastOrderId + 1;
                $uniqueId = Hashids::encode($newId);
            } else {
                //first order
                $newId = 1;
            }

            $uniqueId = Hashids::encode($newId);
            $unique_order_id = 'OD' . '-' . date('m-d') . '-' . strtoupper(Str::random(4)) . '-' . strtoupper($uniqueId);
            $newOrder->unique_order_id = $unique_order_id;

            $restaurant_id = $request['order'][0]['restaurant_id'];
            $restaurant = Restaurant::where('id', $restaurant_id)->first();
            $restaurant_settings = RestaurantSettings::where('restaurant_id', $restaurant_id)->first();

            $newOrder->user_id = $user->id;
            if (isset($restaurant_id)) {
                try {
                    $restaurantuser = User::findOrFail($newOrder->user_id);

                    // Retrieve the role_id for 'Customer'
                    $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                    // Check if the record already exists
                    $existingRecord = RestaurantCustomerModel::where('user_id', $newOrder->user_id)
                        ->where('restaurant_id', $restaurant_id)
                        ->where('role_id', $role_id)
                        ->first();

                    // If the record does not exist, create it
                    if (!$existingRecord) {
                        RestaurantCustomerModel::create([
                            'role_id' => $role_id,
                            'user_id' => $newOrder->user_id,
                            'restaurant_id' => $restaurant_id,
                        ]);
                    }
                } catch (\Throwable $e) {
                    // Log the error for debugging
                    \Log::error('Error registering user: ' . $e->getMessage());

                    // Return detailed error response
                    $response = ['success' => false, 'data' => 'Could not register user. ' . $e->getMessage()];
                    return response()->json($response, 500);
                }
            }

            $newOrder->zone_id = $restaurant->zone_id ? $restaurant->zone_id : null;

            if ($request['pending_payment'] || $request['method'] == 'MERCADOPAGO' || $request['method'] == 'PAYTM' || $request['method'] == 'RAZORPAY') {
                $newOrder->orderstatus_id = '8';

                if (Module::find('OrderSchedule') && Module::find('OrderSchedule')->isEnabled()) {
                    if (isset($request->schedule_date) && $request->schedule_date != null && isset($request->schedule_slot) && $request->schedule_slot != null) {
                        $newOrder->is_scheduled = true;
                        $newOrder->schedule_date = json_encode($request->schedule_date);
                        $newOrder->schedule_slot = json_encode($request->schedule_slot);
                    }
                }
            } elseif ($restaurant->auto_acceptable) {
                $newOrder->orderstatus_id = '2';

                if (Module::find('OrderSchedule') && Module::find('OrderSchedule')->isEnabled()) {
                    if (isset($request->schedule_date) && $request->schedule_date != null && isset($request->schedule_slot) && $request->schedule_slot != null) {
                        $newOrder->orderstatus_id = '10';
                        $newOrder->is_scheduled = true;
                        $newOrder->schedule_date = json_encode($request->schedule_date);
                        $newOrder->schedule_slot = json_encode($request->schedule_slot);
                    }
                }

                if ($request->delivery_type == 1) {
                    sendSmsToDelivery($restaurant_id);
                }
                if (config('setting.enablePushNotificationOrders') == 'true') {
                    //to user
                    $notify = new PushNotify();
                    $notify->sendPushNotification('2', $newOrder->user_id, $newOrder->unique_order_id);
                }
            } else {
                $newOrder->orderstatus_id = '1';

                if (Module::find('OrderSchedule') && Module::find('OrderSchedule')->isEnabled()) {
                    if (isset($request->schedule_date) && $request->schedule_date != null && isset($request->schedule_slot) && $request->schedule_slot != null) {
                        $newOrder->orderstatus_id = '10';
                        $newOrder->is_scheduled = true;
                        $newOrder->schedule_date = json_encode($request->schedule_date);
                        $newOrder->schedule_slot = json_encode($request->schedule_slot);
                    }
                }
            }

            $newOrder->location = json_encode($request['location']);

            if ($request->delivery_type == 2) {
                $full_address = 'NA';
            } else {
                $full_address = ($request['user']['data']['default_address']['house']
                    ? $request['user']['data']['default_address']['house'] : '')
                    . ', '
                    . ($request['user']['data']['default_address']['address']
                        ? $request['user']['data']['default_address']['address'] : '');
            }
            $newOrder->address = $full_address;

            //get restaurant charges
            $newOrder->restaurant_charge = $restaurant->restaurant_charges;

            $newOrder->transaction_id = $request->payment_token;

            $orderTotal = 0;
            foreach ($request['order'] as $oI) {
                $originalItem = Item::where('id', $oI['id'])->first();
                $orderTotal += ($originalItem->price * $oI['quantity']);

                if (isset($oI['selectedaddons'])) {
                    foreach ($oI['selectedaddons'] as $selectedaddon) {
                        $addon = Addon::where('id', $selectedaddon['addon_id'])->first();
                        if ($addon) {
                            $orderTotal += $addon->price * $oI['quantity'];
                        }
                    }
                }
            }
            $newOrder->sub_total = $orderTotal;

            if ($request->coupon) {
                $coupon = Coupon::where('code', strtoupper($request['coupon']['code']))->first();
                if ($coupon) {
                    $newOrder->coupon_name = $request['coupon']['code'];
                    if ($coupon->discount_type == 'PERCENTAGE') {
                        $percentage_discount = (($coupon->discount / 100) * $orderTotal);
                        if ($coupon->max_discount) {
                            if ($percentage_discount >= $coupon->max_discount) {
                                $percentage_discount = $coupon->max_discount;
                            }
                        }
                        $newOrder->coupon_amount = $percentage_discount;
                        $orderTotal = $orderTotal - $percentage_discount;
                    }
                    if ($coupon->discount_type == 'AMOUNT') {
                        $newOrder->coupon_amount = $coupon->discount;
                        $orderTotal = $orderTotal - $coupon->discount;
                    }
                    $coupon->count = $coupon->count + 1;
                    $coupon->save();
                }
            }

            if ($request->delivery_type == 1) {
                if (config('setting.enGDMA') == 'true') {
                    $distance = (float) $request->dis;
                } else {
                    $distance = getDistance($request['user']['data']['default_address']['latitude'], $request['user']['data']['default_address']['longitude'], $restaurant->latitude, $restaurant->longitude);
                }

                if ($restaurant->delivery_charge_type == 'DYNAMIC') {
                    //get distance between user and restaurant,

                    if ($distance > $restaurant->base_delivery_distance) {
                        $extraDistance = $distance - $restaurant->base_delivery_distance;
                        $extraCharge = ($extraDistance / $restaurant->extra_delivery_distance) * $restaurant->extra_delivery_charge;
                        $dynamicDeliveryCharge = $restaurant->base_delivery_charge + $extraCharge;

                        if (config('setting.enDelChrRnd') == 'true') {
                            $dynamicDeliveryCharge = ceil($dynamicDeliveryCharge);
                        }

                        $newOrder->delivery_charge = $dynamicDeliveryCharge;
                        $newOrder->actual_delivery_charge = $dynamicDeliveryCharge;
                    } else {
                        $newOrder->delivery_charge = $restaurant->base_delivery_charge;
                        $newOrder->actual_delivery_charge = $restaurant->base_delivery_charge;
                    }
                } else {
                    $newOrder->delivery_charge = $restaurant->delivery_charges;
                    $newOrder->actual_delivery_charge = $restaurant->delivery_charges;
                }

                $newOrder->distance = $distance;
            } else {
                //for selfpickup...
                $newOrder->delivery_charge = 0;
                $newOrder->actual_delivery_charge = 0;
            }

            //for free delivery above x subtotal
            if ($restaurant->free_delivery_subtotal > 0) {
                if ($newOrder->sub_total >= $restaurant->free_delivery_subtotal) {
                    $newOrder->delivery_charge = 0;
                }
            }

            $orderTotal = $orderTotal + $newOrder->delivery_charge;

            $orderTotal = $orderTotal + $restaurant->restaurant_charges;

            if (config('setting.taxApplicable') == 'true') {
                $taxAmount = $newOrder->tax = (($orderTotal - $restaurant->delivery_charges) / 11);
                //$newOrder->tax = config('setting.taxPercentage');

                //$taxAmount = (float) (((float) config('setting.taxPercentage') / 100) * $orderTotal);
            } else {
                $taxAmount = 0;
            }

            $newOrder->tax_amount = $taxAmount;

            $orderTotal = $orderTotal + $taxAmount;

            if (isset($request['tipAmount']) && !empty($request['tipAmount'])) {
                $orderTotal = $orderTotal + $request['tipAmount'];
            }

            //this is the final order total

            if ($request['method'] == 'COD') {
                if ($request->partial_wallet == true) {
                    //deduct all user amount and add
                    $newOrder->payable = $orderTotal - $user->balanceFloat;
                }
                if ($request->partial_wallet == false) {
                    $newOrder->payable = $orderTotal;
                }
            }

            $newOrder->total = $orderTotal;

            $newOrder->order_comment = $request['order_comment'];

            $newOrder->payment_mode = $request['method'];

            $newOrder->restaurant_id = $request['order'][0]['restaurant_id'];

            $newOrder->tip_amount = $request['tipAmount'];

            if ($request->delivery_type == 1) {
                //delivery
                $newOrder->delivery_type = 1;
            } else {
                //selfpickup
                $newOrder->delivery_type = 2;
            }

            $newOrder->cash_change_amount = $request['cash_change_amount'] ? $request['cash_change_amount'] : null;

            $newOrder->delivery_pin = substr(str_shuffle('123456789'), 0, 5);

            //process paypal payment
            if ($request['method'] == 'PAYPAL' || $request['method'] == 'PAYSTACK' || $request['method'] == 'RAZORPAY' || $request['method'] == 'STRIPE' || $request['method'] == 'PAYMONGO' || $request['method'] == 'MERCADOPAGO' || $request['method'] == 'PAYTM' || $request['method'] == 'FLUTTERWAVE' || $request['method'] == 'KHALTI') {
                //successfuly received payment
                $newOrder->save();
                if ($request->partial_wallet == true) {
                    $userWalletBalance = $user->balanceFloat;
                    $newOrder->wallet_amount = $userWalletBalance;
                    $newOrder->save();
                    //deduct all user amount and add
                    $user->withdraw($userWalletBalance * 100, ['description' => $translationData->orderPartialPaymentWalletComment . $newOrder->unique_order_id]);
                }
                foreach ($request['order'] as $orderItem) {
                    $item = new Orderitem();
                    $item->order_id = $newOrder->id;
                    $item->item_id = $orderItem['id'];
                    $item->name = $orderItem['name'];
                    $item->quantity = $orderItem['quantity'];
                    $item->price = $orderItem['price'];
                    $item->save();
                    if (isset($orderItem['selectedaddons'])) {
                        foreach ($orderItem['selectedaddons'] as $selectedaddon) {
                            $addon = new OrderItemAddon();
                            $addon->orderitem_id = $item->id;
                            $addon->addon_category_name = $selectedaddon['addon_category_name'];
                            $addon->addon_name = $selectedaddon['addon_name'];
                            $addon->addon_price = $selectedaddon['price'];
                            $addon->save();
                        }
                    }
                }

                //sms to Store Owner
                if (!$restaurant->auto_acceptable && $newOrder->orderstatus_id == '1' && config('setting.smsRestaurantNotify') == 'true') {
                    $restaurant_id = $request['order'][0]['restaurant_id'];
                    sendSmsToStoreOwner($restaurant_id, $orderTotal);
                }

                //push notification to Delivery Guy
                if ($restaurant->auto_acceptable && config('setting.enablePushNotification') && config('setting.enablePushNotificationOrders') == 'true') {
                    $restaurant_id = $request['order'][0]['restaurant_id'];
                    sendPushNotificationToDelivery($restaurant_id, $newOrder);
                }

                //push notification to Store Owner
                if (in_array($newOrder->orderstatus_id, ['1', '10'])) {
                    sendPushNotificationToStoreOwner($restaurant_id, $unique_order_id);
                }

                // activity()
                //     ->performedOn($newOrder)
                //     ->causedBy($user)
                //     ->withProperties(['type' => 'Order_Placed'])->log('Order placed');

                if ($newOrder->orderstatus_id == '2') {
                    activity()
                        ->performedOn($newOrder)
                        ->causedBy(User::find(1))
                        ->withProperties(['type' => 'Order_Accepted_Auto'])->log('Order auto accepted');
                }

                $response = [
                    'success' => true,
                    'data' => $newOrder,
                ];
                $bepoz_data[] = [
                    'bepoz_product_id' => $orderItem['bepoz_pid'],
                    'qty' => $orderItem['quantity'],
                    'rate' => $orderItem['price'],
                    'pos_size' => (!empty($orderItem['bepoz_psize'])) ? $orderItem['bepoz_psize'] : 0,
                ];
                $this->bepozIntegration($request, $unique_order_id, $restaurant_settings, $bepoz_data, $newOrder->address);
                return response()->json($response);
            } else {
                $newOrder->save();
                if ($request['method'] == 'COD') {
                    if ($request->partial_wallet == true) {
                        $userWalletBalance = $user->balanceFloat;
                        $newOrder->wallet_amount = $userWalletBalance;
                        $newOrder->save();
                        //deduct all user amount and add
                        $user->withdraw($userWalletBalance * 100, ['description' => $translationData->orderPartialPaymentWalletComment . $newOrder->unique_order_id]);
                    }
                }

                //if method is WALLET, then deduct amount with appropriate description
                if ($request['method'] == 'WALLET') {
                    $userWalletBalance = $user->balanceFloat;
                    $newOrder->wallet_amount = $orderTotal;
                    $newOrder->save();
                    $user->withdraw($orderTotal * 100, ['description' => $translationData->orderPaymentWalletComment . $newOrder->unique_order_id]);
                }

                $bepoz_data = [];

                foreach ($request['order'] as $orderItem) {
                    $item = new Orderitem();
                    $item->order_id = $newOrder->id;
                    $item->item_id = $orderItem['id'];
                    $item->name = $orderItem['name'];
                    $item->quantity = $orderItem['quantity'];
                    $item->price = $orderItem['price'];
                    $item->save();
                    if (isset($orderItem['selectedaddons'])) {
                        foreach ($orderItem['selectedaddons'] as $selectedaddon) {
                            $addon = new OrderItemAddon();
                            $addon->orderitem_id = $item->id;
                            $addon->addon_category_name = $selectedaddon['addon_category_name'];
                            $addon->addon_name = $selectedaddon['addon_name'];
                            $addon->addon_price = $selectedaddon['price'];
                            $addon->save();
                        }
                    }

                    $bepoz_data[] = [
                        'bepoz_product_id' => $orderItem['bepoz_pid'],
                        'qty' => $orderItem['quantity'],
                        'rate' => $orderItem['price'],
                        'pos_size' => (!empty($orderItem['bepoz_psize'])) ? $orderItem['bepoz_psize'] : 0,
                    ];
                }

                //sms to Store Owner
                if (!$restaurant->auto_acceptable && $newOrder->orderstatus_id == '1' && config('setting.smsRestaurantNotify') == 'true') {
                    $restaurant_id = $request['order'][0]['restaurant_id'];
                    sendSmsToStoreOwner($restaurant_id, $orderTotal);
                }

                //push notification to Delivery Guy
                if ($restaurant->auto_acceptable && config('setting.enablePushNotification') && config('setting.enablePushNotificationOrders') == 'true') {
                    $restaurant_id = $request['order'][0]['restaurant_id'];
                    sendPushNotificationToDelivery($restaurant_id, $newOrder);
                }

                //push notification to Store Owner
                if (in_array($newOrder->orderstatus_id, ['1', '10'])) {
                    sendPushNotificationToStoreOwner($restaurant_id, $unique_order_id);
                }

                // activity()
                //     ->performedOn($newOrder)
                //     ->causedBy($user)
                //     ->withProperties(['type' => 'Order_Placed'])->log('Order placed');

                if ($newOrder->orderstatus_id == '2') {
                    activity()
                        ->performedOn($newOrder)
                        ->causedBy(User::find(1))
                        ->withProperties(['type' => 'Order_Accepted_Auto'])->log('Order auto accepted');
                }

                $response = [
                    'success' => true,
                    'data' => $newOrder,
                ];

                $this->bepozIntegration($request, $unique_order_id, $restaurant_settings, $bepoz_data, $newOrder->address);

                return response()->json($response);
            }
        }
    }

    public function bepozIntegration($request, $unique_order_id, $restaurant_settings, $bepoz_data, $address)
    {
        /* Bepoz integration */

        if (!empty($restaurant_settings->url) && !empty($restaurant_settings->secret)) {
            $user = User::where('id', $request['user']['data']['id'])->first();

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $restaurant_settings->url . '/api/accounts/get?number=' . str_replace('+', '', $user->phone),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'secret: ' . $restaurant_settings->secret,
                ],
            ]);

            $response1 = curl_exec($curl);
            if (curl_errno($curl)) {
                Log::error("account get Curl error: " . curl_error($curl));
                // Handle the error and proceed to the next function
            } else {
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($http_code == 200) {
                    Log::info("account get Request successful: " . json_encode($response1));
                } else {
                    Log::error("account get Request failed with status code " . $http_code . ": " . json_encode($response1));
                    // Handle the error and proceed to the next function
                }
            }

            curl_close($curl);
            $res = json_decode($response1, true);
            if (!empty($res['message']) && $res['message'] == 'Success' && !empty($res['data']['AccountID'])) {
                $this->bepozTransaction($unique_order_id, $request, $user, $restaurant_settings, $res, $bepoz_data, $address);
            } else {
                $first_name = null;
                $last_name = null;

                if (!empty($user->first_name)) {
                    $first_name = $user->first_name;
                }

                if (!empty($user->last_name)) {
                    $last_name = $user->last_name;
                } elseif (!empty($user->name)) {
                    $last_name = $user->name;
                }

                //|| !empty($user->last_name)

                $post_data = [
                    'AccNumber' => str_replace('+61', '0', $user->phone),
                    'CardNumber' => str_replace('+61', '0', $user->phone),
                    'FirstName' => $first_name,
                    'LastName' => $last_name,
                    'GroupID' => $restaurant_settings->account_group,
                    'Mobile' => str_replace('+61', '0', $user->phone),
                    'Email1st' => $user->email,
                    'Comment' => $address,
                ];
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => $restaurant_settings->url . '/api/accounts/createUpdate',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($post_data),
                    CURLOPT_HTTPHEADER => [
                        'secret: ' . $restaurant_settings->secret,
                        'Content-Type: application/json',
                    ],
                ]);
                $create_custom_response = curl_exec($curl);
                if (curl_errno($curl)) {
                    Log::error("accounts/createUpdate Curl error: " . curl_error($curl));
                    // Handle the error and proceed to the next function
                } else {
                    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    if ($http_code == 200) {
                        Log::info("accounts/createUpdate  Request successful: " . json_encode($create_custom_response));
                    } else {
                        Log::error("accounts/createUpdate Request failed with status code " . $http_code . ": " . json_encode($create_custom_response));
                        // Handle the error and proceed to the next function
                    }
                }
                curl_close($curl);
                $create_custom_response = json_decode($create_custom_response, true);
                if (!empty($create_custom_response['message']) && $create_custom_response['message'] == 'Success') {
                    $this->bepozTransaction($unique_order_id, $request, $user, $restaurant_settings, $create_custom_response, $bepoz_data, $address);
                }
            }
        }

        /** Bepoz integration end */
    }

    public function bepozTransaction($unique_order_id, $request, $user, $restaurant_settings, $res, $bepoz_data, $address)
    {

        $post_data1 = [];

        if (empty($bepoz_data)) {
            return;
        }

        $tablename = 'string';
        if (!empty($user->name)) {
            $tablename = $user->name;
        } elseif (!empty($user->first_name) || !empty($user->last_name)) {
            $tablename = $user->first_name . ' ' . $user->last_name;
        }

        if ($request->delivery_type == 1) {
            $delivery_type = ($restaurant_settings->delivery_order_type) ? $restaurant_settings->delivery_order_type : null;
        } else {
            $delivery_type = ($restaurant_settings->self_pickup_order_type) ? $restaurant_settings->self_pickup_order_type : null;
        }

        $i = 0;
        foreach ($bepoz_data as $dt) {
            $post_data1[] = [
                //"Training" =>  true,
                'OrderID' => $unique_order_id,
                'OrderType' => $delivery_type,
                'TillID' => $restaurant_settings->till_id,
                'TillAlias' => 'string',
                'DrawerNum' => 0,
                'ShiftID' => 0,
                'OperatorID' => $restaurant_settings->operator_id,
                'OperatorNumber' => 'string',
                //"OrderComment" => "This is test comment for address",
                'ProductID' => $dt['bepoz_product_id'],
                'ProdNumber' => 'string',
                'ProdExportCode1' => 'string',
                'Size' => $dt['pos_size'],
                'SizeName' => 'string',
                'Barcode' => 'string',
                'ProductOption' => 0,
                'QtySold' => $dt['qty'],
                'Gross' => ($dt['rate'] * $dt['qty']),
                'Nett' => ($dt['rate'] * $dt['qty']),
                'PaymentName' => ($restaurant_settings->online_payment) ? $restaurant_settings->online_payment : 'Ozeats',
                'PaymentAmount' => ($dt['rate'] * $dt['qty']),
                'Rounding' => 0,
                'AccountID' => 0,
                'AccountNumber' => str_replace('+61', '0', $user->phone),
                'PaymentRefID' => 0,
                'TableGroup' => $restaurant_settings->order_table_group,
                'TableNumber' => str_replace('+61', '0', $user->phone),
                'TableName' => trim($tablename, ' '),
                'TableGuests' => 0,
                'TaxRatePlusOne' => 0,
                'TaxAmount' => 0,
                'ServiceCharge' => 0,
                'DiscNum_1' => 0,
                'TableID' => 0,
                'ProductSortType' => 0,
                'HandlingFee' => 0,
                'PointsAmount' => 0,
            ];
            if ($i == 0) {
                $post_data1[$i]['OrderComment'] = $tablename . ', ' . str_replace('+61', '0', $user->phone) . ',' . $address;
            } else {
                $post_data1[$i]['OrderComment'] = null;
            }
            $i++;
        }

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $restaurant_settings->url . '/api/transactions/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($post_data1),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'secret: ' . $restaurant_settings->secret,
            ],
        ]);

        $response = curl_exec($curl);
        Log::error("TransactionCreate create response error: " . json_encode($post_data1));
        if (curl_errno($curl)) {
            Log::error("TransactionCreate Curl error: " . curl_error($curl));
            // Handle the error and proceed to the next function
        } else {
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code == 200) {
                Log::info("TransactionCreate Request successful: " . json_encode($response));
            } else {
                Log::error("TransactionCreate Request failed with status code " . $http_code . ": " . json_encode($response));
                // Handle the error and proceed to the next function
            }
        }
        curl_close($curl);
    }

    public function getOrders(Request $request): JsonResponse
    {
        $user = auth()->user();
        if ($user) {
            $orders = Order::where('user_id', $user->id)->with('orderitems', 'orderitems.order_item_addons', 'restaurant', 'rating')->orderBy('id', 'DESC')->paginate(10);

            foreach ($orders as $order) {
                $ratable = false;
                if ($order->orderstatus_id == 5 && !$order->rating) {
                    $ratable = true;
                }
                $order->is_ratable = $ratable;
                $order->makeHidden(['reviews']);
            }

            return response()->json($orders);
        }

        return response()->json(['success' => false], 401);
    }

    public function getOrderItems(Request $request): JsonResponse
    {
        $user = auth()->user();
        if ($user) {
            $items = Orderitem::where('order_id', $request->order_id)->get();

            return response()->json($items);
        }

        return response()->json(['success' => false], 401);
    }

    public function cancelOrder(Request $request, TranslationHelper $translationHelper): JsonResponse
    {
        $keys = ['orderRefundWalletComment', 'orderPartialRefundWalletComment'];

        $translationData = $translationHelper->getDefaultLanguageValuesForKeys($keys);

        $order = Order::where('id', $request->order_id)->first();

        $user = auth()->user();

        //check if user is cancelling their own order...
        if ($order->user_id == $user->id && ($order->orderstatus_id == 1 || $order->orderstatus_id == 10)) {
            //if payment method is not COD, and order status is 1 (Order placed) then refund to wallet
            $refund = false;

            //if COD, then check if wallet is present
            if ($order->payment_mode == 'COD') {
                if ($order->wallet_amount != null) {
                    //refund wallet amount
                    $user->deposit($order->wallet_amount * 100, ['description' => $translationData->orderPartialRefundWalletComment . $order->unique_order_id]);
                    $refund = true;
                }
            } else {
                //if online payment, refund the total to wallet
                $user->deposit(($order->total) * 100, ['description' => $translationData->orderRefundWalletComment . $order->unique_order_id]);
                $refund = true;
            }

            //cancel order
            $order->orderstatus_id = 6; //6 means canceled..
            $order->save();

            //throw notification to user
            if (config('setting.enablePushNotificationOrders') == 'true') {
                $notify = new PushNotify();
                $notify->sendPushNotification('6', $order->user_id);
            }

            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties(['type' => 'Order_Canceled'])->log('Order canceled');

            $response = [
                'success' => true,
                'refund' => $refund,
            ];

            return response()->json($response);
        } else {
            $response = [
                'success' => false,
                'refund' => false,
            ];

            return response()->json($response);
        }
    }
}
