<?php

namespace App\Http\Controllers;

use App\Addon;
use App\AddonCategory;
use App\Area;
use App\Booking;
use App\Helpers\TranslationHelper;
use App\Item;
use App\ItemCategory;
use App\Order;
use App\PaymentGateway;
use App\PromoSlider;
use App\PushNotify;
use App\Restaurant;
use App\PushToken;
use App\Alert;
use App\RestaurantCustomerModel;
use App\RestaurantEarning;
use App\RestaurantPayout;
use App\RestaurantSettings;
use App\ShiftInformation;
use App\Slide;
use App\Sms;
use App\StorePayoutDetail;
use App\TableInformation;
use App\TableType;
use App\User;
use Artisan;
use Auth;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Wallet;
use Carbon\Carbon;
use Exception;
use Hashids;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Image;
use Mail;
use Modules\ThermalPrinter\Entities\PrinterSetting;
use Modules\ThermalPrinter\Entities\ThermalPrinter;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\StripeClient;

class RestaurantOwnerController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;

        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $newOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->whereIn('orderstatus_id', ['1', '10'])
            ->orderBy('id', 'DESC')
            ->with('restaurant')
            ->get();

        // dd($newOrders);

        $newOrdersIds = $newOrders->pluck('id')->toArray();

        $preparingOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->whereIn('orderstatus_id', ['2', '3', '11'])
            ->where('delivery_type', '<>', 2)
            ->orderBy('orderstatus_id', 'ASC')
            ->with('restaurant')
            ->get();

        $selfpickupOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->whereIn('orderstatus_id', ['2', '7'])
            ->where('delivery_type', 2)
            ->orderBy('orderstatus_id', 'DESC')
            ->with('restaurant')
            ->get();

        $ongoingOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->whereIn('orderstatus_id', ['4'])
            ->orderBy('orderstatus_id', 'DESC')
            ->with('restaurant')
            ->get();

        $ordersCount = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('orderstatus_id', '5')->count();

        $allCompletedOrders = Order::whereIn('restaurant_id', $restaurantIds)
            ->with('orderitems')
            ->where('orderstatus_id', '5')
            ->get();

        $orderItemsCount = 0;
        foreach ($allCompletedOrders as $cO) {
            foreach ($cO->orderitems as $orderItem) {
                $orderItemsCount += $orderItem->quantity;
            }
        }

        $totalEarning = 0;
        settype($var, 'float');

        foreach ($allCompletedOrders as $completedOrder) {
            $totalEarning += $completedOrder->total - ($completedOrder->delivery_charge + $completedOrder->tip_amount);
        }

        $zenMode = \Session::get('zenMode');

        if (Module::find('ThermalPrinter') && Module::find('ThermalPrinter')->isEnabled()) {
            $printerSetting = PrinterSetting::where('user_id', Auth::user()->id)->first();
            if ($printerSetting) {
                $data = json_decode($printerSetting->data);

                if ($data->automatic_printing == 'OFF') {
                    $autoPrinting = false;
                } else {
                    $autoPrinting = true;
                }
            } else {
                $autoPrinting = false;
            }
        } else {
            $autoPrinting = false;
        }
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        // dd($is_active);
        $arrayData = [
            'restaurantsCount' => count($user->restaurants),
            'ordersCount' => $ordersCount,
            'orderItemsCount' => $orderItemsCount,
            'totalEarning' => number_format((float) $totalEarning, 2, '.', ''),
            'newOrders' => $newOrders,
            'newOrdersIds' => $newOrdersIds,
            'preparingOrders' => $preparingOrders,
            'ongoingOrders' => $ongoingOrders,
            'selfpickupOrders' => $selfpickupOrders,
            'autoPrinting' => $autoPrinting,
            'is_active' => $is_active,
            'reservation' => $reservation,
            'restaurant' => $user->restaurants
        ];

        if ($zenMode == 'true') {
            return view('restaurantowner.dashboardv2', $arrayData);
        }

        return view('restaurantowner.dashboard', $arrayData);
    }

    public function getNewOrders(Request $request): JsonResponse
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;

        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $listedOrderIds = $request->listed_order_ids;
        if ($listedOrderIds) {
            $newOrders = Order::whereIn('restaurant_id', $restaurantIds)
                ->whereNotIn('id', $listedOrderIds)
                ->where('orderstatus_id', '1')
                ->orderBy('id', 'DESC')
                ->with('restaurant')
                ->get();
        } else {
            $newOrders = Order::whereIn('restaurant_id', $restaurantIds)
                ->where('orderstatus_id', '1')
                ->orderBy('id', 'DESC')
                ->with('restaurant')
                ->get();
        }

        return response()->json($newOrders);
    }

    public function acceptOrder($id)
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $order = Order::where('id', $id)->whereIn('restaurant_id', $restaurantIds)->first();

        if ($order->orderstatus_id == '1') {
            $order->orderstatus_id = 2;
            $order->save();

            if (config('setting.enablePushNotificationOrders') == 'true') {
                //to user
                $notify = new PushNotify();
                $notify->sendPushNotification('2', $order->user_id, $order->unique_order_id);
            }

            //send notification and sms to delivery only when order type is Delivery...
            if ($order->delivery_type == '1') {
                sendPushNotificationToDelivery($order->restaurant->id, $order);
                sendSmsToDelivery($order->restaurant->id);
            }

            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties(['type' => 'Order_Accepted_Store'])->log('Order accepted');

            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => true]);
            } else {
                return redirect()->back()->with(['success' => __('storeDashboard.orderAcceptedNotification')]);
            }
        } else {
            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => false], 406);
            } else {
                return redirect()->back()->with(['message' => __('storeDashboard.orderSomethingWentWrongNotification')]);
            }
        }
    }

    public function markOrderReady($id): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $order = Order::where('id', $id)->whereIn('restaurant_id', $restaurantIds)->first();

        if ($order->orderstatus_id == '2') {
            $order->orderstatus_id = 7;
            $order->save();

            if (config('setting.enablePushNotificationOrders') == 'true') {
                //to user
                $notify = new PushNotify();
                $notify->sendPushNotification('7', $order->user_id, $order->unique_order_id);
            }

            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties(['type' => 'Order_Ready_Store'])->log('Order prepared');

            return redirect()->back()->with(['success' => 'Order Marked as Ready']);
        } else {
            return redirect()->back()->with(['message' => 'Something went wrong.']);
        }
    }

    public function markSelfPickupOrderAsCompleted($id): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $order = Order::where('id', $id)->whereIn('restaurant_id', $restaurantIds)->first();

        if ($order->orderstatus_id == '7') {
            $order->orderstatus_id = 5;
            $order->save();

            //if selfpickup add amount to restaurant earnings if not COD then add order total
            if ($order->payment_mode == 'STRIPE' || $order->payment_mode == 'PAYPAL' || $order->payment_mode == 'PAYSTACK' || $order->payment_mode == 'RAZORPAY' || $order->payment_mode == 'PAYMONGO' || $order->payment_mode == 'MERCADOPAGO' || $order->payment_mode == 'PAYTM' || $order->payment_mode == 'FLUTTERWAVE' || $order->payment_mode == 'KHALTI' || $order->payment_mode == 'WALLET') {
                $restaurant_earning = RestaurantEarning::where('restaurant_id', $order->restaurant->id)
                    ->where('is_requested', 0)
                    ->first();
                if ($restaurant_earning) {
                    $restaurant_earning->amount += $order->total;
                    $restaurant_earning->zone_id = $order->restaurant->zone_id ? $order->restaurant->zone_id : null;
                    $restaurant_earning->save();
                } else {
                    $restaurant_earning = new RestaurantEarning();
                    $restaurant_earning->restaurant_id = $order->restaurant->id;
                    $restaurant_earning->amount = $order->total;
                    $restaurant_earning->zone_id = $order->restaurant->zone_id ? $order->restaurant->zone_id : null;
                    $restaurant_earning->save();
                }
            }
            //if COD, then take the $total minus $payable amount
            if ($order->payment_mode == 'COD') {
                $restaurant_earning = RestaurantEarning::where('restaurant_id', $order->restaurant->id)
                    ->where('is_requested', 0)
                    ->first();
                if ($restaurant_earning) {
                    $restaurant_earning->amount += $order->total - $order->payable;
                    $restaurant_earning->zone_id = $order->restaurant->zone_id ? $order->restaurant->zone_id : null;
                    $restaurant_earning->save();
                } else {
                    $restaurant_earning = new RestaurantEarning();
                    $restaurant_earning->restaurant_id = $order->restaurant->id;
                    $restaurant_earning->amount = $order->total - $order->payable;
                    $restaurant_earning->zone_id = $order->restaurant->zone_id ? $order->restaurant->zone_id : null;
                    $restaurant_earning->save();
                }
            }

            if (config('setting.enablePushNotificationOrders') == 'true') {
                //to user
                $notify = new PushNotify();
                $notify->sendPushNotification('5', $order->user_id, $order->unique_order_id);
            }

            if (config('setting.sendOrderInvoiceOverEmail') == 'true') {
                Mail::send('emails.invoice', ['order' => $order], function ($email) use ($order) {
                    $email->subject(config('setting.orderInvoiceEmailSubject') . '#' . $order->unique_order_id);
                    $email->from(config('setting.sendEmailFromEmailAddress'), config('setting.sendEmailFromEmailName'));
                    $email->to($order->user->email);
                });
            }

            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties(['type' => 'Order_Completed_Store'])->log('Order completed');

            return redirect()->back()->with(['success' => 'Order Completed']);
        } else {
            return redirect()->back()->with(['message' => 'Something went wrong.']);
        }
    }

    public function restaurants(): View
    {
        $user = Auth::user();
        $restaurants = $user->restaurants;
        $is_active = $restaurants[0]->is_active;
        $reservation = RestaurantSettings::find($restaurants[0]->id);
        return view('restaurantowner.restaurants', [
            'restaurants' => $restaurants,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function getEditRestaurant($id)
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $restaurant = Restaurant::where('id', $id)->whereIn('id', $restaurantIds)->first();

        $adminPaymentGateways = PaymentGateway::where('is_active', '1')->get();

        $payoutData = StorePayoutDetail::where('restaurant_id', $id)->first();
        if ($payoutData) {
            $payoutData = json_decode($payoutData->data);
        } else {
            $payoutData = null;
        }

        if ($restaurant) {
            $restaurants = $user->restaurants;
            $is_active = $restaurants[0]->is_active;
            $reservation = RestaurantSettings::find($restaurants[0]->id);
            return view('restaurantowner.editRestaurant', [
                'restaurant' => $restaurant,
                'schedule_data' => json_decode($restaurant->schedule_data),
                'adminPaymentGateways' => $adminPaymentGateways,
                'payoutData' => $payoutData,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        } else {
            return redirect()->route('restaurantowner.restaurants')->with(['message' => 'Access Denied']);
        }
    }

    public function disableRestaurant($id): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $restaurant = Restaurant::where('id', $id)->whereIn('id', $restaurantIds)->first();

        if ($restaurant) {
            $restaurant->is_schedulable = false;
            $restaurant->toggleActive();
            $restaurant->save();

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('restaurant.restaurants');
        }
    }

    public function saveNewRestaurant(Request $request): RedirectResponse
    {
        $restaurant = new Restaurant();

        $restaurant->name = $request->name;
        $restaurant->description = $request->description;

        $image = $request->file('image');
        $rand_name = time() . Str::random(10);
        $filename = $rand_name . '.jpg';
        Image::make($image)
            ->resize(160, 117)
            ->save(base_path('assets/img/restaurants/' . $filename), config('setting.uploadImageQuality '), 'jpg');
        $restaurant->image = '/assets/img/restaurants/' . $filename;

        $restaurant->delivery_time = $request->delivery_time;
        $restaurant->price_range = $request->price_range;

        if ($request->is_pureveg == 'true') {
            $restaurant->is_pureveg = true;
        } else {
            $restaurant->is_pureveg = false;
        }

        $restaurant->slug = Str::slug($request->name) . '-' . Str::random(15);
        $restaurant->certificate = $request->certificate;

        $restaurant->address = $request->address;
        $restaurant->pincode = $request->pincode;
        $restaurant->landmark = $request->landmark;
        $restaurant->latitude = $request->latitude;
        $restaurant->longitude = $request->longitude;

        $restaurant->restaurant_charges = $request->restaurant_charges;

        $restaurant->sku = time() . Str::random(10);

        $restaurant->is_active = 0;

        $restaurant->min_order_price = $request->min_order_price;

        if ($request->has('delivery_type')) {
            $restaurant->delivery_type = $request->delivery_type;
        }

        try {
            $restaurant->save();
            $user = Auth::user();
            $user->restaurants()->attach($restaurant);

            return redirect()->back()->with(['success' => 'Restaurant Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function updateRestaurant(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $restaurant = Restaurant::where('id', $request->id)->whereIn('id', $restaurantIds)->first();

        if ($restaurant) {
            $restaurant->name = $request->name;
            $restaurant->description = $request->description;

            if ($request->image == null) {
                $restaurant->image = $request->old_image;
            } else {
                $image = $request->file('image');
                $rand_name = time() . Str::random(10);
                $filename = $rand_name . '.jpg';
                Image::make($image)
                    ->resize(160, 117)
                    ->save(base_path('assets/img/restaurants/' . $filename), config('setting.uploadImageQuality '), 'jpg');
                $restaurant->image = '/assets/img/restaurants/' . $filename;
            }

            $restaurant->delivery_time = $request->delivery_time;
            $restaurant->price_range = $request->price_range;

            if ($request->is_pureveg == 'true') {
                $restaurant->is_pureveg = true;
            } else {
                $restaurant->is_pureveg = false;
            }

            $restaurant->certificate = $request->certificate;

            $restaurant->address = $request->address;
            $restaurant->pincode = $request->pincode;
            $restaurant->landmark = $request->landmark;
            $restaurant->latitude = $request->latitude;
            $restaurant->longitude = $request->longitude;
            $restaurant->stripe_public_key = $request->stripe_public_key;
            $restaurant->stripe_secret_key = $request->stripe_secret_key;

            $restaurant->restaurant_charges = $request->restaurant_charges;

            $restaurant->min_order_price = $request->min_order_price;

            if ($request->has('delivery_type')) {
                $restaurant->delivery_type = $request->delivery_type;
            }

            if ($request->is_schedulable == 'true') {
                $restaurant->is_schedulable = true;
            } else {
                $restaurant->is_schedulable = false;
            }

            if ($request->accept_scheduled_orders == 'true') {
                $restaurant->accept_scheduled_orders = true;
            } else {
                $restaurant->accept_scheduled_orders = false;
            }

            if ($request->has('schedule_slot_buffer')) {
                if ($request->schedule_slot_buffer == null) {
                    $restaurant->schedule_slot_buffer = 30; //defaults to 30 mins
                } else {
                    $restaurant->schedule_slot_buffer = $request->schedule_slot_buffer;
                }
            } else {
                $restaurant->schedule_slot_buffer = $restaurant->schedule_slot_buffer ? $restaurant->schedule_slot_buffer : 0;
            }

            try {
                if ($request->store_payment_gateways == null) {
                    $restaurant->payment_gateways()->sync($request->store_payment_gateways);
                }

                if (isset($request->store_payment_gateways)) {
                    $restaurant->payment_gateways()->sync($request->store_payment_gateways);
                }

                $restaurant->save();

                return redirect()->back()->with(['success' => 'Restaurant Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th]);
            }
        }
    }

    public function itemcategories(): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);

        $itemCategories = ItemCategory::orderBy('id', 'DESC')
            ->where('user_id', Auth::user()->id)
            ->get();
        $itemCategories->loadCount('items');
        $count = count($itemCategories);

        return view('restaurantowner.itemcategories', [
            'itemCategories' => $itemCategories,
            'count' => $count,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function createItemCategory(Request $request): RedirectResponse
    {
        $itemCategory = new ItemCategory();

        $itemCategory->name = $request->name;
        $itemCategory->user_id = Auth::user()->id;

        try {
            $itemCategory->save();

            return redirect()->back()->with(['success' => 'Category Created']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function disableCategory($id): RedirectResponse
    {
        $itemCategory = ItemCategory::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        if ($itemCategory) {
            $itemCategory->toggleEnable()->save();

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('restaurant.itemcategories');
        }
    }

    public function updateItemCategory(Request $request): RedirectResponse
    {
        $itemCategory = ItemCategory::where('id', $request->id)->where('user_id', Auth::user()->id)->firstOrFail();
        $itemCategory->name = $request->name;
        $itemCategory->save();

        return redirect()->back()->with(['success' => 'Operation Successful']);
    }

    public function items(): View
    {
        $user = Auth::user();
        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $items = Item::whereIn('restaurant_id', $restaurantIds)
            ->orderBy('id', 'DESC')
            ->with('item_category', 'restaurant')
            ->paginate(20);

        $count = $items->total();

        $restaurants = $user->restaurants;

        $itemCategories = ItemCategory::where('is_enabled', '1')
            ->where('user_id', Auth::user()->id)
            ->get();
        $addonCategories = AddonCategory::where('user_id', Auth::user()->id)->get();

        return view('restaurantowner.items', [
            'items' => $items,
            'count' => $count,
            'restaurants' => $restaurants,
            'itemCategories' => $itemCategories,
            'addonCategories' => $addonCategories,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function searchItems(Request $request): View
    {
        $user = Auth::user();

        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $query = $request['query'];

        $items = Item::whereIn('restaurant_id', $restaurantIds)
            ->where('name', 'LIKE', '%' . $query . '%')
            ->with('item_category', 'restaurant')
            ->paginate(20);

        $count = $items->total();

        $restaurants = $user->restaurants;

        $itemCategories = ItemCategory::where('is_enabled', '1')
            ->where('user_id', Auth::user()->id)
            ->get();

        $addonCategories = AddonCategory::where('user_id', Auth::user()->id)->get();

        return view('restaurantowner.items', [
            'items' => $items,
            'count' => $count,
            'restaurants' => $restaurants,
            'query' => $query,
            'itemCategories' => $itemCategories,
            'addonCategories' => $addonCategories,
        ]);
    }

    public function saveNewItem(Request $request): RedirectResponse
    {
        // dd($request->all());

        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        if (!in_array($request->restaurant_id, $restaurantIds)) {
            abort(404);
        }

        $item = new Item();

        $item->name = $request->name;
        $item->price = $request->price;
        $item->old_price = $request->old_price == null ? 0 : $request->old_price;
        $item->restaurant_id = $request->restaurant_id;
        $item->item_category_id = $request->item_category_id;
        $item->bepoz_pid = $request->bepoz_pid;
        $item->bepoz_psize = $request->bepoz_psize;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $rand_name = time() . Str::random(10);
            $filename = $rand_name . '.jpg';
            Image::make($image)
                ->resize(486, 355)
                ->save(base_path('assets/img/items/' . $filename), config('setting.uploadImageQuality '), 'jpg');

            $item->image = '/assets/img/items/' . $filename;
        }

        if ($request->is_recommended == 'true') {
            $item->is_recommended = true;
        } else {
            $item->is_recommended = false;
        }

        if ($request->is_popular == 'true') {
            $item->is_popular = true;
        } else {
            $item->is_popular = false;
        }

        if ($request->is_new == 'true') {
            $item->is_new = true;
        } else {
            $item->is_new = false;
        }

        if ($request->is_veg == 'veg') {
            $item->is_veg = true;
        } elseif ($request->is_veg == 'nonveg') {
            $item->is_veg = false;
        } else {
            $item->is_veg = null;
        }

        $item->desc = $request->desc;
        try {
            $item->save();
            if (isset($request->addon_category_item)) {
                $item->addon_categories()->sync($request->addon_category_item);
            }

            return redirect()->back()->with(['success' => 'Item Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function getEditItem($id)
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $item = Item::where('id', $id)
            ->whereIn('restaurant_id', $restaurantIds)
            ->first();

        $addonCategories = AddonCategory::where('user_id', Auth::user()->id)->get();

        if ($item) {
            $restaurants = $user->restaurants;
            $is_active = $restaurants[0]->is_active;
            $reservation = RestaurantSettings::find($restaurants[0]->id);
            $itemCategories = ItemCategory::where('user_id', Auth::user()->id)
                ->get();

            return view('restaurantowner.editItem', [
                'item' => $item,
                'restaurants' => $restaurants,
                'itemCategories' => $itemCategories,
                'addonCategories' => $addonCategories,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        } else {
            return redirect()->route('restaurant.items')->with(['message' => 'Access Denied']);
        }
    }

    public function disableItem($id)
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $item = Item::where('id', $id)
            ->whereIn('restaurant_id', $restaurantIds)
            ->first();
        if ($item) {
            $item->toggleActive()->save();
            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('restaurant.items');
        }
    }

    public function updateItem(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $item = Item::where('id', $request->id)
            ->whereIn('restaurant_id', $restaurantIds)
            ->first();

        if ($item) {
            $item->name = $request->name;
            $item->restaurant_id = $request->restaurant_id;
            $item->item_category_id = $request->item_category_id;
            $item->bepoz_pid = $request->bepoz_pid;
            $item->bepoz_psize = $request->bepoz_psize;

            if ($request->image == null) {
                $item->image = $request->old_image;
            } else {
                $image = $request->file('image');
                $rand_name = time() . Str::random(10);
                $filename = $rand_name . '.jpg';
                Image::make($image)
                    ->resize(486, 355)
                    ->save(base_path('assets/img/items/' . $filename), config('setting.uploadImageQuality '), 'jpg');
                $item->image = '/assets/img/items/' . $filename;
            }

            $item->price = $request->price;
            $item->old_price = $request->old_price == null ? 0 : $request->old_price;

            if ($request->is_recommended == 'true') {
                $item->is_recommended = true;
            } else {
                $item->is_recommended = false;
            }

            if ($request->is_popular == 'true') {
                $item->is_popular = true;
            } else {
                $item->is_popular = false;
            }

            if ($request->is_new == 'true') {
                $item->is_new = true;
            } else {
                $item->is_new = false;
            }

            if ($request->is_veg == 'veg') {
                $item->is_veg = true;
            } elseif ($request->is_veg == 'nonveg') {
                $item->is_veg = false;
            } else {
                $item->is_veg = null;
            }

            $item->desc = $request->desc;
            try {
                $item->save();
                if (isset($request->addon_category_item)) {
                    $item->addon_categories()->sync($request->addon_category_item);
                }
                if ($request->addon_category_item == null) {
                    $item->addon_categories()->sync($request->addon_category_item);
                }

                if ($request->remove_all_addons == '1') {
                    $item->addon_categories()->sync($request->addon_category_item);
                }

                return redirect()->back()->with(['success' => 'Item Saved']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th]);
            }
        }
    }

    public function removeItemImage($id): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $item = Item::where('id', $id)->whereIn('restaurant_id', $restaurantIds)->firstOrFail();

        $item->image = null;
        $item->save();

        return redirect()->back()->with(['success' => 'Item image removed']);
    }

    public function addonCategories(): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        $addonCategories = AddonCategory::where('user_id', Auth::user()->id)
            ->orderBy('id', 'DESC')
            ->paginate(20);
        $addonCategories->loadCount('addons');

        $count = $addonCategories->total();

        return view('restaurantowner.addonCategories', [
            'addonCategories' => $addonCategories,
            'count' => $count,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function searchAddonCategories(Request $request): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        $query = $request['query'];

        $addonCategories = AddonCategory::where('user_id', Auth::user()->id)
            ->where('name', 'LIKE', '%' . $query . '%')
            ->paginate(20);
        $addonCategories->loadCount('addons');

        $count = $addonCategories->total();

        return view('restaurantowner.addonCategories', [
            'addonCategories' => $addonCategories,
            'count' => $count,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function saveNewAddonCategory(Request $request): RedirectResponse
    {

        $addonCategory = new AddonCategory();

        $addonCategory->name = $request->name;
        $addonCategory->type = $request->type;
        $addonCategory->description = $request->description;
        $addonCategory->user_id = Auth::user()->id;
        $addonCategory->addon_limit = $request->addon_limit ? $request->addon_limit : 0;

        try {
            $addonCategory->save();
            if ($request->has('addon_names')) {
                foreach ($request->addon_names as $key => $addon_name) {
                    $addon = new Addon();
                    $addon->name = $addon_name;
                    $addon->price = $request->addon_prices[$key];
                    $addon->addon_category_id = $addonCategory->id;
                    $addon->user_id = Auth::user()->id;
                    $addon->save();
                }
            }

            return redirect()->route('restaurant.editAddonCategory', $addonCategory->id)->with(['success' => 'Addon Category Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function newAddonCategory(): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        return view('restaurantowner.newAddonCategory', [
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function getEditAddonCategory($id)
    {
        $addonCategory = AddonCategory::where('id', $id)->with('addons')->first();
        if ($addonCategory) {
            if ($addonCategory->user_id == Auth::user()->id) {
                $user = Auth::user();

                $restaurant = $user->restaurants;
                $is_active = $restaurant[0]->is_active;
                $reservation = RestaurantSettings::find($restaurant[0]->id);
                return view('restaurantowner.editAddonCategory', [
                    'addonCategory' => $addonCategory,
                    'addons' => $addonCategory->addons,
                    'is_active' => $is_active,
                    'reservation' => $reservation,
                ]);
            } else {
                return redirect()
                    ->route('restaurant.addonCategories')
                    ->with(['message' => 'Access Denied']);
            }
        } else {
            return redirect()
                ->route('restaurant.addonCategories')
                ->with(['message' => 'Access Denied']);
        }
    }

    public function updateAddonCategory(Request $request): RedirectResponse
    {
        $addonCategory = AddonCategory::where('id', $request->id)->first();

        if ($addonCategory) {
            $addonCategory->name = $request->name;
            $addonCategory->type = $request->type;
            $addonCategory->description = $request->description;
            $addonCategory->addon_limit = $request->addon_limit ? $request->addon_limit : 0;

            try {
                $addonCategory->save();
                $addons_old = $request->input('addon_old');
                if ($request->has('addon_old')) {
                    foreach ($addons_old as $ad) {
                        $addon_old_update = Addon::find($ad['id']);
                        $addon_old_update->name = $ad['name'];
                        $addon_old_update->price = $ad['price'];
                        $addon_old_update->user_id = Auth::user()->id;
                        $addon_old_update->save();
                    }
                }

                if ($request->addon_names) {
                    foreach ($request->addon_names as $key => $addon_name) {
                        $addon = new Addon();
                        $addon->name = $addon_name;
                        $addon->price = $request->addon_prices[$key];
                        $addon->addon_category_id = $addonCategory->id;
                        $addon->user_id = Auth::user()->id;
                        $addon->save();
                    }
                }

                return redirect()->back()->with(['success' => 'Addon Category Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th]);
            }
        }
    }

    public function addons(): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        $addons = Addon::where('user_id', Auth::user()->id)->with('addon_category')->paginate();

        $count = $addons->total();

        $addonCategories = AddonCategory::where('user_id', Auth::user()->id)->get();

        return view('restaurantowner.addons', [
            'addons' => $addons,
            'count' => $count,
            'addonCategories' => $addonCategories,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function searchAddons(Request $request): View
    {
        $query = $request['query'];

        $addons = Addon::where('user_id', Auth::user()->id)
            ->where('name', 'LIKE', '%' . $query . '%')
            ->with('addon_category')
            ->paginate(20);

        $count = $addons->total();

        $addonCategories = AddonCategory::where('user_id', Auth::user()->id)->get();

        return view('restaurantowner.addons', [
            'addons' => $addons,
            'count' => $count,
            'addonCategories' => $addonCategories,
        ]);
    }

    public function saveNewAddon(Request $request): RedirectResponse
    {
        $addon = new Addon();

        $addon->name = $request->name;
        $addon->price = $request->price;
        $addon->user_id = Auth::user()->id;
        $addon->addon_category_id = $request->addon_category_id;

        try {
            $addon->save();

            return redirect()->back()->with(['success' => 'Addon Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => 'Something went wrong. Please check your form and try again.']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    public function getEditAddon($id)
    {
        $addon = Addon::where('id', $id)
            ->where('user_id', Auth::user()->id)
            ->first();

        $addonCategories = AddonCategory::where('user_id', Auth::user()->id)->get();
        if ($addon) {
            $user = Auth::user();

            $restaurant = $user->restaurants;
            $is_active = $restaurant[0]->is_active;
            $reservation = RestaurantSettings::find($restaurant[0]->id);
            return view('restaurantowner.editAddon', [
                'addon' => $addon,
                'addonCategories' => $addonCategories,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        } else {
            return redirect()->route('restaurant.addons')->with(['message' => 'Access Denied']);
        }
    }

    public function updateAddon(Request $request): RedirectResponse
    {
        $addon = Addon::where('id', $request->id)->first();

        if ($addon) {
            if ($addon->user_id == Auth::user()->id) {
                $addon->name = $request->name;
                $addon->price = $request->price;
                $addon->addon_category_id = $request->addon_category_id;

                try {
                    $addon->save();

                    return redirect()->back()->with(['success' => 'Addon Updated']);
                } catch (\Illuminate\Database\QueryException $qe) {
                    return redirect()->back()->with(['message' => 'Something went wrong. Please check your form and try again.']);
                } catch (Exception $e) {
                    return redirect()->back()->with(['message' => $e->getMessage()]);
                } catch (\Throwable $th) {
                    return redirect()->back()->with(['message' => $th]);
                }
            } else {
                return redirect()->route('restaurant.addons')->with(['message' => 'Access Denied']);
            }
        } else {
            return redirect()->route('restaurant.addons')->with(['message' => 'Access Denied']);
        }
    }

    public function disableAddon($id): RedirectResponse
    {
        $addon = Addon::where('id', $id)->firstOrFail();
        if ($addon) {
            $addon->toggleActive()->save();

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->back()->with(['message' => 'Something Went Wrong']);
        }
    }

    public function deleteAddon($id): RedirectResponse
    {
        $addon = Addon::find($id);
        if ($addon->user_id == Auth::user()->id) {
            $addon->delete();

            return redirect()->back()->with(['success' => 'Addon Deleted']);
        } else {
            return redirect()->back()->with(['message' => 'Click on Update first, then try deleting again.']);
        }
    }

    public function orders(): View
    {
        $user = Auth::user();
        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $orders = Order::orderBy('id', 'DESC')
            ->whereIn('orderstatus_id', ['1', '2', '3', '4', '5', '6', '7', '10', '11'])
            ->whereIn('restaurant_id', $restaurantIds)
            ->with('accept_delivery.user', 'restaurant')
            ->paginate('20');

        $count = $orders->total();
        // dd($orders);
        return view('restaurantowner.orders', [
            'orders' => $orders,
            'count' => $count,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }
    public function notifications(): View
    {
        $user = Auth::user();
        $restaurants = $user->restaurants;
        $is_active = $restaurants[0]->is_active;
        $reservation = RestaurantSettings::find($restaurants[0]->id);
        $restaurantIds = $user->restaurants->pluck('id')->toArray();
        $restaurantUserCount = DB::table('restaurant_user')
            ->whereIn('restaurant_id', $restaurantIds) // Assuming user_id is the foreign key for users
            ->count();
        $usersCount = User::count();
        // Count the number of PushTokens with a user_id that exists in the restaurant_user table
        $subscriberCount = PushToken::whereExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('restaurant_user')
                ->whereColumn('restaurant_user.user_id', 'push_tokens.user_id');
        })->count();

        // Count the number of PushTokens with a user_id that does not exist in the restaurant_user table
        $appUsers = PushToken::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('restaurant_user')
                ->whereColumn('restaurant_user.user_id', 'push_tokens.user_id');
        })->count();

        $countJunkData = Alert::whereDate('created_at', '<', Carbon::now()->subDays(7))->count();

        return view('admin.notifications', [
            'subscriberCount' => $subscriberCount,
            'usersCount' => $restaurantUserCount,
            'appUsers' => $appUsers,
            'countJunkData' => $countJunkData,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }
    public function getUsersToSendNotification(Request $request): JsonResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $search = $request->search;

        $query = User::whereIn('id', function ($query) use ($restaurantIds) {
            $query->select('user_id')
                ->from('restaurant_user')
                ->whereIn('restaurant_id', $restaurantIds);
        });

        if ($search != '') {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $users = $query->orderBy('name', 'asc')
            ->select('id', 'name', 'email')
            ->limit(5)
            ->get();

        $response = [];
        foreach ($users as $user) {
            $response[] = [
                'id' => $user->id,
                'text' => $user->name . ' (' . $user->email . ')',
            ];
        }

        return response()->json($response);
    }

    public function sliders(): View
    {
        $user = Auth::user();
        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        if ($user->hasRole('Store Owner')) {
            $userRestaurants = $user->restaurants;
            $userRestaurantsIds = $userRestaurants->pluck('id')->toArray();

            // Get PromoSliders associated with the restaurant IDs
            $allRestaurants = PromoSlider::whereHas('slides', function ($query) use ($userRestaurantsIds) {
                $query->whereIn('restaurant_id', $userRestaurantsIds);
            })
                ->orderBy('id', 'DESC')
                ->with([
                    'slides' => function ($query) {
                        $query->select('id', 'promo_slider_id', 'is_active'); // Select the desired fields from the slides table
                    }
                ])
                ->get();

            // dd($userRestaurantsIds);
            return view('restaurantowner.restaurentOwnerSliders', [
                'user' => $user,
                'userRestaurants' => $userRestaurants,
                'allRestaurants' => $allRestaurants,
                'userRestaurantsIds' => $userRestaurantsIds,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        }
    }
    public function restaurantCreateSlider(Request $request): RedirectResponse
    {
        // $sliderCount = PromoSlider::where('is_active', 1)->count();

        // if ($sliderCount >= 2) {
        //     return redirect()->back()->with(['message' => 'Only two sliders can be created. Disbale or delete some Sliders to create more.']);
        // }

        $slider = new PromoSlider();
        $slider->name = $request->name;
        $slider->location_id = '0';
        $slider->position_id = $request->position_id;
        $slider->size = $request->size;
        $slider->view = $request->view;
        $PromoSlider = $slider->save();

        $url = url('/');
        $url = substr($url, 0, strrpos($url, '/')); //this will give url without " / "

        $slide = new Slide();
        $slide->promo_slider_id = $slider->id;
        $slide->name = $request->name;
        $slide->url = $request->url;

        $image = $request->file('image');
        $rand_name = time() . Str::random(10);
        $filename = $rand_name . '.' . $image->getClientOriginalExtension();

        Image::make($image)
            ->resize(384, 384)
            ->save(base_path('assets/img/slider/' . $filename));
        $slide->image = '/assets/img/slider/' . $filename;
        // dd($request->userRestaurantsIds);
        $slide->model = $request->model;
        $slide->restaurant_id = $request->userRestaurantsIds;
        $slide->item_id = $request->item_id;
        $slide->url = $request->customUrl;

        if ($request->customUrl != null) {
            if ($request->is_locationset == 'true') {
                $slide->is_locationset = true;
            } else {
                $slide->is_locationset = false;
            }

            $slide->latitude = $request->latitude;
            $slide->longitude = $request->longitude;
            $slide->radius = $request->radius;
        }

        $slide->save();

        return redirect()->back()->with(['success' => 'New Slide Created']);
        // return redirect()->back()->with(['success' => 'New Slider Created']);
    }
    public function updateStoreOwnerSlider(Request $request): RedirectResponse
    {
        $user = User::where('id', $request->id)->first();
        $sliderids = $request->sliderid;

        // Check if $request->user_restaurants is not null and is an array
        if (!is_null($request->user_restaurants) && is_array($request->user_restaurants)) {
            $userRestaurants = collect($request->user_restaurants); // Convert array to collection
            $userRestaurantsIds = $userRestaurants->pluck('id')->toArray();

            // Update the slides associated with the user's restaurants
            foreach ($sliderids as $sliderId) {
                $isActive = in_array($sliderId, $request->user_restaurants) ? 1 : 0;

                Slide::where('promo_slider_id', $sliderId)
                    ->update(['is_active' => $isActive]);
            }
        } else {
            // Handle the case where $request->user_restaurants is null or not an array
            // For example, log an error or provide a default behavior
            foreach ($sliderids as $sliderId) {
                $isActive = 0;

                Slide::where('promo_slider_id', $sliderId)
                    ->update(['is_active' => $isActive]);
            }
        }

        return redirect()->back()->with(['success' => 'Slider Updated: ']);
    }


    public function sendNotifiaction(Request $request): RedirectResponse
    {
        $secretKey = config('setting.firebaseSecret');
        $pushNotification = config('setting.enablePushNotification');
        $notification = $request->except(['_token']);

        $alertData = $request->except(['_token']);
        $alertData = json_encode($alertData);
        $alertData = json_decode($alertData);
        $alertData = [
            'title' => $alertData->data->title,
            'message' => $alertData->data->message,
            'badge' => $alertData->data->badge,
            'icon' => $alertData->data->icon,
            'click_action' => $alertData->data->click_action,
            'unique_order_id' => null,
            'custom_notification' => true,
            'custom_image' => $alertData->data->image,
        ];

        /* Save to Alerts table */
        $subscribers = User::all();

        $alertsInsertArray = [];
        foreach ($subscribers as $subscriber) {
            $alert = new Alert();
            $alert->data = json_encode($alertData);
            $alert->user_id = $subscriber->id;
            $alert->is_read = 0;
            $alert->created_at = Carbon::now();
            $alert->updated_at = Carbon::now();
            $alertsInsertArray[] = $alert->attributesToArray();
        }
        $alertsInsertCollection = collect($alertsInsertArray);
        $alertChunks = $alertsInsertCollection->chunk(1000);
        foreach ($alertChunks as $chunk) {
            Alert::insert($chunk->toArray());
        }

        // dd(count($alertsInsertArray));

        /*  END Save to Alerts Table */

        // $notification = json_encode($notification);

        // $notification = substr($notification, 0, -1);

        //get all push tokens excluding delivery guys and store owners...
        $toExclude = User::role(['Delivery Guy', 'Store Owner'])->pluck('id');
        $pushTokens = PushToken::where('is_active', '1')
            ->whereNotIn('user_id', $toExclude)
            ->get(['token'])
            ->pluck('token');

        if (count($pushTokens)) {
            $i = 0;
            $len = count($pushTokens);
            $last = $len - 1;

            $chunks = $pushTokens->chunk(900)->toArray();
            foreach ($chunks as $chunk) {
                $i = 0;
                $len = count($chunk);
                $last = $len - 1;

                // $tokens = '';

                foreach ($chunk as $key => $value) {
                    if ($len == 1) {
                        $tokens = $value;
                    } elseif ($i == 0) {
                        $tokens = '["' . $value . '",';
                    } elseif ($i == $last) {
                        $tokens .= '"' . $value . '"]';
                    } else {
                        $tokens .= '"' . $value . '",';
                    }
                    $i++;
                }
                // $main_picture = $notification['data']['image'];
                // $notifications['notification'] = [
                //     'title' => $data['data']['title'],
                //     'body' => $data['data']['message']
                //     // Add other properties if needed
                // ];
                // $mainPicture = [
                //     // 'main_picture' => [
                //         'main_picture' => $notification['data']['image']
                //     // ]/
                // ];
                // dd($notification['data']['title']);
                $notificationData = [
                    'registration_ids' => json_decode($tokens, true), // Use registration_ids instead of to
                    'notification' => [
                        'title' => $notification['data']['title'],
                        'body' => $notification['data']['message'],
                        'click_action' => $notification['data']['click_action'],
                        'unique_order_id' => null,
                        'custom_notification' => true,
                        'image' => substr(asset('/'), 0, strrpos(asset('/'), '/')) . $notification['data']['image']
                    ]
                ];

                $notificationJson = json_encode($notificationData, JSON_UNESCAPED_SLASHES);

                // dd($notificationJson);

                if ($pushNotification == 'true') {
                    $response = Curl::to('https://fcm.googleapis.com/fcm/send')
                        ->withHeader('Content-Type: application/json')
                        ->withHeader("Authorization: Bearer $secretKey")
                        ->withData($notificationJson)
                        ->post();
                    // dd($response);
                    return redirect()->back()->with(['success' => 'Notifications & Alerts Sent']);
                } else {
                    return redirect()->back()->with(['message' => 'Enable Push Notification']);
                }

            }
        }

        return redirect()->back()->with(['success' => 'Notifications & Alerts Sent']);
    }

    public function sendNotificationToSelectedUsers(Request $request): RedirectResponse
    {
        $secretKey = config('setting.firebaseSecret');
        $pushNotification = config('setting.enablePushNotification');
        // $secretKey = 'eHINrnwWSEiPA95zFLkRWm:APA91bHG1vS43mIudtFOFGrLYXfT9l2dKBuO5qn_I7rBxxd-TBZF1RtBVy9nGDgsJ1miClTGTJmVPnTsGib679qOzN8b1Z9bSXV6BdzEkp2WVyTAz34qrOSLOamQP8S2t0aR12DH8H4k';

        // $secretKey = 'key=3ceKqYvPKAYh_0x3dI8yMi7Kytp4hSs7RZZ_Cx_b9eE';
        // dd($secretKey);
        $notification = $request->except(['_token']);
        // dd($data);
        $alertData = $request->except(['_token']);
        $alertData = json_encode($alertData);
        $alertData = json_decode($alertData);
        $alertData = [
            'title' => $alertData->data->title,
            'message' => $alertData->data->message,
            'badge' => $alertData->data->badge,
            'icon' => $alertData->data->icon,
            'click_action' => $alertData->data->click_action,
            'unique_order_id' => null,
            'custom_notification' => true,
            'image' => substr(asset('/'), 0, strrpos(asset('/'), '/')) . $notification['data']['image']
        ];
        /* Save to Alerts table */
        $subscribers = User::whereIn('id', $request->users)->get();
        $alertsInsertArray = [];
        foreach ($subscribers as $subscriber) {
            $alert = new Alert();
            $alert->data = json_encode($alertData);
            $alert->user_id = $subscriber->id;
            $alert->is_read = 0;
            $alert->created_at = Carbon::now();
            $alert->updated_at = Carbon::now();
            $alertsInsertArray[] = $alert->attributesToArray();
        }
        $alertsInsertCollection = collect($alertsInsertArray);
        $alertChunks = $alertsInsertCollection->chunk(1000);
        foreach ($alertChunks as $chunk) {
            Alert::insert($chunk->toArray());
        }
        /*  END Save to Alerts Table */
        // $notifications = [];

        // // Assign properties to the array
        // $notifications['notification'] = [
        //     'title' => $data['data']['title'],
        //     'body' => $data['data']['message']
        //     // Add other properties if needed
        // ];
        // $notifications['click_action'] = $data['data']['click_action'];
        // $notifications['badge'] = $data['data']['badge'];
        // $notifications['icon'] = $data['data']['icon'];
        // $notifications['image'] = $data['data']['image'];
        // $notification = json_encode($notification);
        // dd($notification);
        // $notification = $data;
        // $notification=substr($notification, 0, -1);
        // $notification = json_encode($notification);

        // dd($notification);
        // $data = substr($notification, 0, -1);
        $pushTokens = PushToken::where('is_active', '1')
            ->whereIn('user_id', $request->users)
            ->get(['token'])
            ->pluck('token')
            ->toArray();
        // dd($pushTokens);
        if (count($pushTokens)) {
            $i = 0;
            $len = count($pushTokens);
            $last = $len - 1;
            // $tokens = '';

            foreach ($pushTokens as $key => $value) {
                if ($len == 1) {
                    $tokens = $value;
                } elseif ($i == 0) {
                    $tokens = '["' . $value;
                } elseif ($i == $last) {
                    $tokens .= '"' . $value . '"]';
                } else {
                    $tokens .= '"' . $value . '",';
                }
                $i++;
            }
            $notificationData = [
                // 'to' => "eHINrnwWSEiPA95zFLkRWm:APA91bHG1vS43mIudtFOFGrLYXfT9l2dKBuO5qn_I7rBxxd-TBZF1RtBVy9nGDgsJ1miClTGTJmVPnTsGib679qOzN8b1Z9bSXV6BdzEkp2WVyTAz34qrOSLOamQP8S2t0aR12DH8H4k",
                // $tokens,
                'to' => $tokens,
                'notification' => [
                    'title' => $notification['data']['title'],
                    'body' => $notification['data']['message'],
                    // 'badge' => $notification['data']['badge'],
                    'icon' => $notification['data']['icon'],
                    'click_action' => $notification['data']['click_action'],
                    'unique_order_id' => null,
                    'custom_notification' => true,
                    'image' => substr(asset('/'), 0, strrpos(asset('/'), '/')) . $notification['data']['image']
                ]
            ];

            // $fullData = $notification . $tokens;
            $fullData = json_encode($notificationData);
            // dd($fullData);
            // $secretKey = 'AAAABkn2H-c:APA91bHO4ywoNMivE0SIWVzrXo8W-7E5D8X7LT08cdgk2pDeWZCiEGD8jqlONua09R4Nycn5I_Op6jVr8UuRP_kc0E1pJ8B3tCfy-6S4iidT3OTT8K3zZomDOkRVKtahAyJuH9JwefA7';
            \Log::info("image ===  " . $fullData);
            if ($pushNotification == 'true') {
                $response = Curl::to('https://fcm.googleapis.com/fcm/send')
                    ->withHeader('Content-Type: application/json')
                    ->withHeader("Authorization: Bearer $secretKey")
                    ->withData($fullData)
                    ->post();
                // dd($response);

                $response = json_decode($response);
                return redirect()->back()->with(['success' => 'Notifications & Alerts Sent']);
            } else {
                return redirect()->back()->with(['message' => 'Enable Push Notification']);
            }


            // return redirect()->back()->with(['success' => 'Success: ' . $response->success . ' & Failed: ' . $response->failure]);
        }

        return redirect()->back()->with(['success' => 'Notifications & Alerts Sent']);
    }

    public function sendNotificationToNonRegisteredAppUsers(Request $request): RedirectResponse
    {
        $secretKey = config('setting.firebaseSecret');
        $pushNotification = config('setting.enablePushNotification');
        $notification = $request->except(['_token']);

        // $data = json_encode($notification);

        // $data = substr($data, 0, -1);

        $pushTokens = PushToken::where('user_id', null)->get(['token'])->pluck('token');

        if (count($pushTokens)) {
            $i = 0;
            $len = count($pushTokens);
            $last = $len - 1;

            $chunks = $pushTokens->chunk(900)->toArray();
            foreach ($chunks as $chunk) {
                $i = 0;
                $len = count($chunk);
                $last = $len - 1;

                // $tokens = '{';

                foreach ($chunk as $key => $value) {
                    if ($len == 1) {
                        $tokens = $value;
                    } elseif ($i == 0) {
                        $tokens = '["' . $value . '",';
                    } elseif ($i == $last) {
                        $tokens .= '"' . $value . '"]';
                    } else {
                        $tokens .= '"' . $value . '",';
                    }
                    $i++;
                }

                $notificationData = [
                    'registration_ids' => json_decode($tokens, true), // Use registration_ids instead of to
                    'notification' => [
                        'title' => $notification['data']['title'],
                        'body' => $notification['data']['message'],
                        'click_action' => $notification['data']['click_action'],
                        'unique_order_id' => null,
                        'custom_notification' => true,
                        'image' => substr(asset('/'), 0, strrpos(asset('/'), '/')) . $notification['data']['image']
                    ]
                ];

                $notificationJson = json_encode($notificationData, JSON_UNESCAPED_SLASHES);

                // dd($notificationJson);

                if ($pushNotification == 'true') {
                    $response = Curl::to('https://fcm.googleapis.com/fcm/send')
                        ->withHeader('Content-Type: application/json')
                        ->withHeader("Authorization: Bearer $secretKey")
                        ->withData($notificationJson)
                        ->post();
                    // dd($response);
                    return redirect()->back()->with(['success' => 'Notifications set to Non-Registered App Users']);

                } else {
                    return redirect()->back()->with(['message' => 'Enable Push Notification']);
                }
            }
        }

        return redirect()->back()->with(['success' => 'Notifications set to Non-Registered App Users']);
    }

    public function uploadNotificationImage(Request $request): JsonResponse
    {
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $filename = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            Image::make($request->file)->resize(1600, 1100)->save(base_path('/assets/img/various/' . $filename));

            return response()->json(['success' => $filename]);
        }
    }

    public function getUserNotifications(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $notifications = Alert::where('user_id', $user->id)
                ->orderBy('id', 'DESC')
                ->whereDate('created_at', '>', Carbon::now()->subDays(7))
                ->get()
                ->take(20);

            return response()->json($notifications);
        }

        return response()->json(['success' => false], 401);
    }

    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $notifications = Alert::where('user_id', $user->id)->get();
            foreach ($notifications as $notification) {
                $notification->is_read = true;
                $notification->save();
            }
            $notifications = Alert::where('user_id', $user->id)
                ->orderBy('id', 'DESC')
                ->whereDate('created_at', '>', Carbon::now()->subDays(7))
                ->get()
                ->take(20);

            return response()->json($notifications);
        }

        return response()->json(['success' => false], 401);
    }

    public function markOneNotificationRead(Request $request): JsonResponse
    {
        $user = auth()->user();
        $notification = Alert::where('id', $request->notification_id)->first();

        if ($user && $notification) {
            $notification->is_read = true;
            $notification->save();

            $notifications = Alert::where('user_id', $user->id)
                ->orderBy('id', 'DESC')
                ->whereDate('created_at', '>', Carbon::now()->subDays(7))
                ->get()
                ->take(20);

            return response()->json($notifications);
        }

        return response()->json(['success' => false], 401);
    }
    public function postSearchOrders(Request $request): View
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $query = $request['query'];

        $orders = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('unique_order_id', 'LIKE', '%' . $query . '%')
            ->with('accept_delivery.user', 'restaurant')
            ->paginate(20);

        $count = $orders->total();

        return view('restaurantowner.orders', [
            'orders' => $orders,
            'count' => $count,
        ]);
    }

    public function viewOrder($order_id)
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();
        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        $order = Order::whereIn('restaurant_id', $restaurantIds)
            ->where('unique_order_id', $order_id)
            ->with('orderitems.order_item_addons')
            ->first();

        $notConfirmedOrderStatusIds = ['8', '9']; //awaiting payment, payment failed and scheduled order

        if ($order && !in_array($order->orderstatus_id, $notConfirmedOrderStatusIds)) {
            return view('restaurantowner.viewOrder', [
                'order' => $order,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        } else {
            return redirect()->route('restaurant.orders')->with(['message' => 'Access Denied']);
        }
    }

    public function earnings($restaurant_id = null)
    {
        $userdata = Auth::user();
        $restaurantdata = $userdata->restaurants;
        $is_active = $restaurantdata[0]->is_active;
        $reservation = RestaurantSettings::find($restaurantdata[0]->id);
        if ($restaurant_id) {
            $user = Auth::user();
            $restaurant = $user->restaurants;
            $restaurantIds = $user->restaurants->pluck('id')->toArray();

            $restaurant = Restaurant::where('id', $restaurant_id)->first();
            // check if restaurant exists
            if ($restaurant) {
                //check if restaurant belongs to the auth user
                // $contains = Arr::has($restaurantIds, $restaurant->id);
                $contains = in_array($restaurant->id, $restaurantIds);
                if ($contains) {
                    //true
                    $allCompletedOrders = Order::where('restaurant_id', $restaurant->id)
                        ->where('orderstatus_id', '5')
                        ->get();

                    $totalEarning = 0;
                    settype($var, 'float');

                    foreach ($allCompletedOrders as $completedOrder) {
                        // $totalEarning += $completedOrder->total - $completedOrder->delivery_charge;
                        $totalEarning += $completedOrder->total - ($completedOrder->delivery_charge + $completedOrder->tip_amount);
                    }

                    // Build an array of the dates we want to show, oldest first
                    $dates = collect();
                    foreach (range(-30, 0) as $i) {
                        $date = Carbon::now()->addDays($i)->format('Y-m-d');
                        $dates->put($date, 0);
                    }

                    // Get the post counts
                    $posts = Order::where('restaurant_id', $restaurant->id)
                        ->where('orderstatus_id', '5')
                        ->where('created_at', '>=', $dates->keys()->first())
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get([
                            DB::raw('DATE( created_at ) as date'),
                            DB::raw('SUM( total ) as "total"'),
                        ])
                        ->pluck('total', 'date');

                    // Merge the two collections; any results in `$posts` will overwrite the zero-value in `$dates`
                    $dates = $dates->merge($posts);

                    // dd($dates);
                    $monthlyDate = '[';
                    $monthlyEarning = '[';
                    foreach ($dates as $date => $value) {
                        $monthlyDate .= "'" . $date . "' ,";
                        $monthlyEarning .= "'" . $value . "' ,";
                    }

                    $monthlyDate = rtrim($monthlyDate, ' ,');
                    $monthlyDate = $monthlyDate . ']';

                    $monthlyEarning = rtrim($monthlyEarning, ' ,');
                    $monthlyEarning = $monthlyEarning . ']';
                    /*=====  End of Monthly Post Analytics  ======*/

                    $balance = RestaurantEarning::where('restaurant_id', $restaurant->id)
                        ->where('is_requested', 0)
                        ->first();

                    if (!$balance) {
                        $balanceBeforeCommission = 0;
                        $balanceAfterCommission = 0;
                    } else {
                        $balanceBeforeCommission = $balance->amount;
                        $balanceAfterCommission = ($balance->amount - ($restaurant->commission_rate / 100) * $balance->amount);
                        $balanceAfterCommission = number_format((float) $balanceAfterCommission, 2, '.', '');
                    }

                    $payoutRequests = RestaurantPayout::where('restaurant_id', $restaurant_id)->orderBy('id', 'DESC')->get();

                    return view('restaurantowner.earnings', [
                        'restaurant' => $restaurant,
                        'totalEarning' => $totalEarning,
                        'monthlyDate' => $monthlyDate,
                        'monthlyEarning' => $monthlyEarning,
                        'balanceBeforeCommission' => $balanceBeforeCommission,
                        'balanceAfterCommission' => $balanceAfterCommission,
                        'payoutRequests' => $payoutRequests,
                        'is_active' => $is_active,
                        'reservation' => $reservation,
                    ]);
                } else {
                    return redirect()->route('restaurant.earnings')->with(['message' => 'Access Denied']);
                }
            } else {
                return redirect()->route('restaurant.earnings')->with(['message' => 'Access Denied']);
            }
        } else {
            $user = Auth::user();
            $restaurants = $user->restaurants;

            return view('restaurantowner.earnings', [
                'restaurants' => $restaurants,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        }
    }

    public function sendPayoutRequest(Request $request): RedirectResponse
    {
        $restaurant = Restaurant::where('id', $request->restaurant_id)->first();
        $earning = RestaurantEarning::where('restaurant_id', $request->restaurant_id)
            ->where('is_requested', 0)
            ->first();

        $balanceBeforeCommission = $earning->amount;
        $balanceAfterCommission = ($earning->amount - ($restaurant->commission_rate / 100) * $earning->amount);
        $balanceAfterCommission = number_format((float) $balanceAfterCommission, 2, '.', '');

        if ($earning) {
            $payoutRequest = new RestaurantPayout;
            $payoutRequest->restaurant_id = $request->restaurant_id;
            $payoutRequest->restaurant_earning_id = $earning->id;
            $payoutRequest->amount = $balanceAfterCommission;
            $payoutRequest->status = 'PENDING';
            $payoutRequest->zone_id = $restaurant->zone_id ? $restaurant->zone_id : null;

            try {
                $payoutRequest->save();
                $earning->is_requested = 1;
                $earning->restaurant_payout_id = $payoutRequest->id;
                $earning->save();
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => 'Something went wrong. Please check your form and try again.']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th]);
            }

            return redirect()->back()->with(['success' => 'Payout Request Sent']);
        } else {
            return redirect()->route('restaurant.earnings')->with(['message' => 'Access Denied']);
        }
    }

    public function cancelOrder($id, TranslationHelper $translationHelper)
    {
        $keys = ['orderRefundWalletComment', 'orderPartialRefundWalletComment'];
        $translationData = $translationHelper->getDefaultLanguageValuesForKeys($keys);

        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $order = Order::where('id', $id)->whereIn('restaurant_id', $restaurantIds)->first();

        $customer = User::where('id', $order->user_id)->first();
        $storeOwner = Auth::user();

        if ($order && $user) {
            if ($order->orderstatus_id == '1') {
                //change order status to 6 (Canceled)
                $order->orderstatus_id = 6;
                $order->save();

                //if COD, then check if wallet is present
                if ($order->payment_mode == 'COD') {
                    if ($order->wallet_amount != null) {
                        //refund wallet amount
                        $customer->deposit($order->wallet_amount * 100, ['description' => $translationData->orderPartialRefundWalletComment . $order->unique_order_id]);
                    }
                    activity()
                        ->performedOn($order)
                        ->causedBy($storeOwner)
                        ->withProperties(['type' => 'Order_Canceled_Store'])->log('Order canceled');
                } else {
                    //if online payment, refund the total to wallet
                    $customer->deposit(($order->total) * 100, ['description' => $translationData->orderRefundWalletComment . $order->unique_order_id]);
                    activity()
                        ->performedOn($order)
                        ->causedBy($storeOwner)
                        ->withProperties(['type' => 'Order_Canceled_Store'])->log('Order canceled with Full Refund');
                }

                //show notification to user
                if (config('setting.enablePushNotificationOrders') == 'true') {
                    //to user
                    $notify = new PushNotify();
                    $notify->sendPushNotification('6', $order->user_id, $order->unique_order_id);
                }

                if (\Illuminate\Support\Facades\Request::ajax()) {
                    return response()->json(['success' => true]);
                } else {
                    return redirect()->back()->with(['success' => __('storeDashboard.orderCanceledNotification')]);
                }
            }
        } else {
            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => false], 406);
            } else {
                return redirect()->back()->with(['message' => __('storeDashboard.orderSomethingWentWrongNotification')]);
            }
        }
    }

    public function updateRestaurantScheduleData(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();
        if (!in_array($request->restaurant_id, $restaurantIds)) {
            abort(404);
        }

        $data = $request->except(['_token', 'restaurant_id']);

        $i = 0;
        $str = '{';
        foreach ($data as $day => $times) {
            $str .= '"' . $day . '":[';
            if ($times) {
                foreach ($times as $key => $time) {
                    if ($key % 2 == 0) {
                        $t1 = $time;
                        $str .= '{"open" :' . '"' . $time . '"';
                    } else {
                        $t2 = $time;
                        $str .= '"close" :' . '"' . $time . '"}';
                    }

                    //check if last, if last then dont add comma,
                    if (count($times) != $key + 1) {
                        $str .= ',';
                    }
                }
                // dd($t1);
                if (Carbon::parse($t1) >= Carbon::parse($t2)) {
                    return redirect()->back()->with(['message' => 'Opening and Closing time is incorrect']);
                }
            } else {
                $str .= '}]';
            }

            if ($i != count($data) - 1) {
                $str .= '],';
            } else {
                $str .= ']';
            }
            $i++;
        }
        $str .= '}';

        // Fetches The Restaurant
        $restaurant = Restaurant::where('id', $request->restaurant_id)->first();
        // Enters The Data
        $restaurant->schedule_data = $str;
        // Saves the Data to Database
        $restaurant->save();

        return redirect()->back()->with(['success' => 'Scheduling data saved successfully']);
    }
    public function restaurantusers(): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;

        $restaurantIds = $user->restaurants->pluck('id')->toArray();
        $query = User::whereIn('id', function ($query) use ($restaurantIds) {
            $query->select('user_id')
                ->from('restaurant_user')
                ->whereIn('restaurant_id', $restaurantIds);
        });
        $roles = Role::all()->except(1)->except(2)->except(3);
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        return view('admin.restauranUsers', [
            'roles' => $roles,
            'restaurantIds' => $restaurantIds,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }
    public function restaurantcustomers(): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        return view('admin.restaurantmanageCustomers', [
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }
    public function restaurantmanageDeliveryGuys(): View
    {
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        return view('admin.restaurantmanageDeliveryGuys', [
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }
    public function restaurantstaffs(): View
    {
        $user = Auth::user();
        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        return view('admin.restaurantmanageStaffs', [
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }
    public function restaurantmanageRestaurantOwners(): View
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $users = User::role('Store Owner')
            ->orderBy('id', 'DESC')
            ->with('roles')
            ->whereIn('id', function ($query) use ($restaurantIds) {
                $query->select('user_id')
                    ->from('restaurant_user')
                    ->whereIn('restaurant_id', $restaurantIds);
            })
            ->paginate(20);

        $count = $users->total();
        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        return view('admin.restaurantmanageRestaurantOwners', [
            'users' => $users,
            'count' => $count,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function saveNewUser(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'role' => 'required|string', // Add more specific validation rules for 'role' if necessary
        ]);

        try {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                // 'password' => \Hash::make($request->password),
            ]);

            if ($request->has('role')) {
                $user->assignRole($request->role);
            }

            if ($user->hasRole('Delivery Guy')) {
                $deliveryGuyDetails = new DeliveryGuyDetail();
                $deliveryGuyDetails->name = $request->delivery_name;
                $deliveryGuyDetails->age = $request->delivery_age;
                if ($request->hasFile('delivery_photo')) {
                    $photo = $request->file('delivery_photo');
                    $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                    Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                    $deliveryGuyDetails->photo = $filename;
                }
                $deliveryGuyDetails->description = $request->delivery_description;
                $deliveryGuyDetails->vehicle_number = $request->delivery_vehicle_number;
                if ($request->delivery_commission_rate != null) {
                    $deliveryGuyDetails->commission_rate = $request->delivery_commission_rate;
                }
                if ($request->tip_commission_rate != null) {
                    $deliveryGuyDetails->tip_commission_rate = $request->tip_commission_rate;
                }
                if ($request->cash_limit != null) {
                    $deliveryGuyDetails->cash_limit = $request->cash_limit;
                } else {
                    $deliveryGuyDetails->cash_limit = 0;
                }

                $deliveryGuyDetails->save();
                $user->delivery_guy_detail_id = $deliveryGuyDetails->id;
                $user->save();
            }

            if ($request->has('restaurantIds')) {
                $user->restaurants()->sync($request->restaurantIds);
            }

            return redirect()->back()->with(['success' => 'User Created']);
        } catch (\Illuminate\Validation\ValidationException $validationException) {
            return redirect()->back()->withErrors($validationException->errors())->withInput();
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }


    public function checkOrderStatusNewOrder(Request $request): JsonResponse
    {
        $order = Order::where('unique_order_id', $request->order_id)->firstOrFail();

        if ($order->orderstatus_id != 1) {
            $data = [
                'reloadPage' => true,
            ];
        } else {
            $data = [
                'reloadPage' => false,
            ];
        }

        return response()->json($data);
    }

    public function checkOrderStatusSelfPickupOrder(Request $request): JsonResponse
    {
        $order = Order::where('unique_order_id', $request->order_id)->firstOrFail();
        if ($request->processSelfPickup) {
            if ($order->orderstatus_id == 5) {
                $data = [
                    'reloadPage' => true,
                ];
            } else {
                $data = [
                    'reloadPage' => false,
                ];
            }
        } else {
            if ($order->orderstatus_id == 2) {
                $data = [
                    'reloadPage' => false,
                ];
            } else {
                $data = [
                    'reloadPage' => true,
                ];
            }
        }

        return response()->json($data);
    }

    private function printInvoice($order_id, $printerSetting = null)
    {
        if (Module::find('ThermalPrinter') && Module::find('ThermalPrinter')->isEnabled()) {
            try {
                $print = new ThermalPrinter();
                $print->printInvoice($order_id);
            } catch (Exception $e) {
                \Session::flash('message', 'Printing Failed. Connection could not be established.');
            }
        }
    }

    public function updateStorePayoutDetails(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();
        if (!in_array($request->restaurant_id, $restaurantIds)) {
            abort(404);
        }

        $storePayoutDetail = StorePayoutDetail::where('restaurant_id', $request->restaurant_id)->first();
        if ($storePayoutDetail) {
            $storePayoutDetail->data = json_encode($request->except(['restaurant_id', '_token']));
        } else {
            $storePayoutDetail = new StorePayoutDetail();
            $storePayoutDetail->restaurant_id = $request->restaurant_id;
            $storePayoutDetail->data = json_encode($request->except(['restaurant_id', '_token']));
        }
        try {
            $storePayoutDetail->save();

            return redirect()->back()->with(['success' => 'Payout Data Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th]);
        }
    }

    /**
     * @return mixed
     */
    public function sortMenusAndItems($restaurant_id)
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $restaurant = Restaurant::where('id', $restaurant_id)->whereIn('id', $restaurantIds)->firstOrFail();

        $items = Item::where('restaurant_id', $restaurant_id)
            ->join('item_categories', function ($join) {
                $join->on('items.item_category_id', '=', 'item_categories.id');
            })
            ->orderBy('item_categories.order_column', 'asc')
            ->with('addon_categories')
            ->ordered()
            ->get(['items.*', 'item_categories.name as category_name']);

        $itemsArr = [];
        foreach ($items as $item) {
            $itemsArr[$item['category_name']][] = $item;
        }

        // dd($itemsArr);
        $itemCategories = ItemCategory::whereHas('items', function ($query) use ($restaurant_id) {
            return $query->where('restaurant_id', $restaurant_id);
        })->ordered()->get();

        $count = 0;

        return view('restaurantowner.sortMenusAndItemsForStore', [
            'restaurant' => $restaurant,
            'items' => $itemsArr,
            'itemCategories' => $itemCategories,
            'count' => $count,
        ]);
    }

    public function updateItemPositionForStore(Request $request): JsonResponse
    {
        Item::setNewOrder($request->newOrder);
        Artisan::call('cache:clear');

        return response()->json(['success' => true]);
    }

    public function updateMenuCategoriesPositionForStore(Request $request): JsonResponse
    {
        ItemCategory::setNewOrder($request->newOrder);
        Artisan::call('cache:clear');

        return response()->json(['success' => true]);
    }

    public function ratings($restaurant_id = null): View
    {
        $user = Auth::user();
        $restaurantdata = $user->restaurants;
        $is_active = $restaurantdata[0]->is_active;
        $reservation = RestaurantSettings::find($restaurantdata[0]->id);
        if ($restaurant_id) {
            $restaurant = $user->restaurants;
            $restaurantIds = $user->restaurants->pluck('id')->toArray();

            $restaurant = Restaurant::whereIn('id', $restaurantIds)
                ->where('id', $restaurant_id)
                ->with([
                    'ratings' => function ($query) {
                        $query->orderBy('id', 'DESC');
                    }
                ])->firstOrFail();
            $averageRating = number_format((float) $restaurant->ratings->avg('rating_store'), 1, '.', '');

            return view('restaurantowner.ratings', [
                'restaurant' => $restaurant,
                'reviews' => $restaurant->ratings,
                'averageRating' => $averageRating,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        } else {
            $restaurants = $user->restaurants;

            return view('restaurantowner.ratings', [
                'restaurants' => $restaurants,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        }
    }

    public function confirmScheduledOrder($id)
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $order = Order::where('id', $id)->whereIn('restaurant_id', $restaurantIds)->first();

        if ($order->orderstatus_id == '10') {
            $order->orderstatus_id = 11;
            $order->save();

            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties(['type' => 'Confirm_Scheduled_Order_Store'])->log('Scheduled order confirmed');

            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => true]);
            } else {
                return redirect()->back()->with(['success' => __('orderScheduleLang.scheduledOrderConfirmedNotification')]);
            }
        } else {
            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => false], 406);
            } else {
                return redirect()->back()->with(['message' => __('storeDashboard.orderSomethingWentWrongNotification')]);
            }
        }
    }

    /* Custom Functions */

    public function getSettingsRestaurant($id): View
    {
        $restaurant_settings = RestaurantSettings::where('restaurant_id', $id)->first();
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);

        return view('restaurantowner.settingsRestaurant', [
            'restaurant_id' => $id,
            'restaurant_settings' => $restaurant_settings,
            'holidays' => (!empty($restaurant_settings->holidays)) ? unserialize($restaurant_settings->holidays) : null,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }
    public function getSettingsRestaurantbooking($id, Request $request): JsonResponse
    {
        $restaurant_settings = RestaurantSettings::where('restaurant_id', $id)->first();

        if (!$restaurant_settings) {
            return response()->json(['error' => 'Settings not found'], 404); // Example error handling
        }

        return response()->json([
            'restaurant_settings' => $restaurant_settings,
            'holidays' => (!empty($restaurant_settings->holidays)) ? unserialize($restaurant_settings->holidays) : null,
        ]);
    }
    public function getRestaurantbooking($id)
    {
        $restaurant = Restaurant::where('id', $id)->first();

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404); // Example error handling
        }
        if (!$restaurant->stripe_public_key) {
            return redirect()->back()->with(['message' => 'Please Enable Deposit For this Restaurant']);
        }

        return response()->json([
            'restaurant' => $restaurant,
        ]);
    }
    public function getrestaurantbepozcconnection(Request $request)
    {
        $restaurant_settings = RestaurantSettings::where('restaurant_id', $request->id)->first();

        $response = [
            'success' => true,
            'Restaurant_bepoz' => $restaurant_settings,
        ];
        return response()->json($response);
    }

    public function saveRestaurantSettings(Request $request): RedirectResponse
    {
        //$restaurant_settings = RestaurantSettings::where('restaurant_id', $id)->first();
        // $restaurant_settings = new RestaurantSettings();

        //if($request->action == 'save'){
        $holidays = $request->input('holidays');
        $new_holidays = [];
        foreach ($holidays as $holiday) {
            $enable_holiday_deposit = false;
            if (isset($holiday['enable_holiday_deposit']) && $holiday['enable_holiday_deposit'] == true) {
                $enable_holiday_deposit = true;
            }

            $holiday_deposit_covers = null;
            if (!empty($holiday['holiday_deposit_covers'])) {
                $holiday_deposit_covers = $holiday['holiday_deposit_covers'];
            }

            $holiday_deposit_amount = null;
            if (!empty($holiday['holiday_deposit_amount'])) {
                $holiday_deposit_amount = $holiday['holiday_deposit_amount'];
            }

            if (!empty($holiday['occasion']) && !empty($holiday['date'])) {
                $new_holidays[] = ['occasion' => $holiday['occasion'], 'date' => $holiday['date'], 'holiday_deposit_covers' => $holiday_deposit_covers, 'holiday_deposit_amount' => $holiday_deposit_amount, 'enable_holiday_deposit' => $enable_holiday_deposit];
            }
        }

        $settings = [
            'url' => $request->bepoz_url,
            'secret' => $request->bepoz_secret,
            'till_id' => $request->bepoz_till_id,
            'operator_id' => $request->bepoz_operator_id,
            'offline_payment' => $request->bepoz_offiline_pay,
            'online_payment' => $request->bepoz_online_payment,
            'delivery_plu' => $request->bepoz_delivery_plu,
            'discount_plu' => $request->bepoz_discount_plu,
            'surcharge_plu' => $request->bepoz_surcharge_plu,
            'tip_plu' => $request->bepoz_tip_plu,
            'recipient_email' => $request->bepoz_recipient_email,
            'restaurant_id' => $request->restaurant_id,
            'pos_type' => $request->pos_type,
            'booking_plu' => $request->bepoz_booking_plu,
            'table_group' => $request->bepoz_table_group,
            'order_table_group' => $request->bepoz_order_table_group,
            'self_pickup_order_type' => $request->bepoz_self_pickup_order_type,
            'delivery_order_type' => $request->bepoz_delivery_order_type,
            'account_group' => $request->bepoz_account_group,
            'holidays' => (!empty($new_holidays)) ? serialize($new_holidays) : null,
            'enable_deposit' => (isset($request->enable_deposit) && $request->enable_deposit == true) ? true : false,
            'deposit_covers' => $request->deposit_covers,
            'deposit_amount_per_cover' => $request->deposit_amount_per_cover,
            'booking_custom_date_fieldidx' => $request->bepoz_booking_custom_date_fieldidx,
            'booking_pax_fieldidx' => $request->bepoz_booking_pax_fieldidx,
            'booking_name_fieldidx' => $request->bepoz_booking_name_fieldidx,
            'booking_comment_fieldidx' => $request->bepoz_booking_comment_fieldidx,
            'booking_option' => $request->bepoz_booking_option,
            'booking_number_fieldidx' => $request->bepoz_booking_number_fieldidx

        ];

        $restaurant_settings = RestaurantSettings::updateOrCreate(
            ['restaurant_id' => $request->restaurant_id],
            $settings
        );
        //}

        /*if($request->action == 'check_connection'){
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $request->bepoz_url."/api/systemcheck",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "secret: ".$request->bepoz_secret
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);
            if(!empty($res['message']) && $res['message']  == 'Success')
            return redirect()->back()->with(['success' => 'Connection established successfully.']);
            else
            return redirect()->back()->with(['message' => 'There is something wrong with connection.']);
        }*/

        // $restaurant_settings->save();
        return redirect()->back()->with(['success' => 'Restaurant Settings Saved']);
    }

    public function checkBepozConnection(Request $request): RedirectResponse
    {
        $curl = curl_init();
        echo "test";
        curl_setopt_array($curl, [
            CURLOPT_URL => $request->bepoz_url . '/api/systemcheck',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'secret: ' . $request->bepoz_secret,
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        $res = json_decode($response, true);
        if (!empty($res['message']) && $res['message'] == 'Success') {
            return redirect()->back()->with(['success' => 'Connection established successfully.']);
        } else {
            return redirect()->back()->with(['message' => 'There is something wrong with connection.']);
        }
    }

    public function getTableShiftRestaurant($id): View
    {
        $shif_settings = ShiftInformation::where('restaurant_id', $id)->first();
        $table_info = TableInformation::where('restaurant_id', $id)->get();
        $areas = Area::where('restaurant_id', $id)->where('is_enabled', 1)->get();
        $table_type = TableType::where('restaurant_id', $id)->where('is_enabled', 1)->get();
        $user = Auth::user();

        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);

        return view('restaurantowner.tableShiftRestaurant', [
            'restaurant_id' => $id,
            'shif_settings' => $shif_settings,
            'table_info' => $table_info,
            'areas' => $areas,
            'table_types' => $table_type,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function saveRestaurantTableShift(Request $request): RedirectResponse
    {
        $settings = [
            'breakfastStartTime' => $request->breakfast_startTime,
            'breakfastEndTime' => $request->breakfast_endTime,
            'breakfastDuration' => $request->breakfast_duration,
            'lunchStartTime' => $request->lunch_start_time,
            'lunchEndTime' => $request->lunch_end_time,
            'lunchDuration' => $request->lunch_duration,
            'dinnerStartTime' => $request->dinner_start_time,
            'dinnerEndTime' => $request->dinner_end_time,
            'dinnerDuration' => $request->dinner_duration,
            'maxNoOfCover' => $request->max_no_of_cover,
            'emailFrom' => $request->email_from,
            'teamName' => $request->team_name,
            'email_options' => $request->email_options,
            'max_cover_breakfast' => $request->max_cover_breakfast,
            'max_cover_lunch' => $request->max_cover_lunch,
            'max_cover_dinner' => $request->max_cover_dinner,
            'breakfast_warning_covers' => $request->breakfast_warning_covers,
            'lunch_warning_covers' => $request->lunch_warning_covers,
            'dinner_warning_covers' => $request->dinner_warning_covers,
        ];

        $shiftinforations = ShiftInformation::updateOrCreate(
            ['restaurant_id' => $request->restaurant_id],
            $settings
        );

        if (!empty($request->restaurant_area)) {
            foreach ($request->restaurant_area as $area) {
                if (!empty($area['name'])) {
                    Area::updateOrCreate(
                        ['restaurant_id' => $request->restaurant_id, 'id' => $area['id']],
                        ['area_name' => $area['name']]
                    );
                }

                if (empty($area['name']) && !empty($area['id'])) {
                    //Area::where('id', $area['id'])->delete();
                    $my_area = Area::where('id', $area['id'])->first();
                    $my_area->is_enabled = 0;
                    $my_area->save();
                }
            }
        }

        if (!empty($request->table_types)) {
            foreach ($request->table_types as $table_type) {
                if (!empty($table_type['name'])) {
                    TableType::updateOrCreate(
                        ['restaurant_id' => $request->restaurant_id, 'id' => $table_type['id']],
                        ['table_type_name' => $table_type['name']]
                    );
                }

                if (empty($table_type['name']) && !empty($table_type['id'])) {
                    //Area::where('id', $area['id'])->delete();
                    $table_type_obj = TableType::where('id', $table_type['id'])->first();
                    $table_type_obj->is_enabled = 0;
                    $table_type_obj->save();
                }
            }
        }

        if (!empty($request->table_info)) {
            TableInformation::where('restaurant_id', $request->restaurant_id)->delete();
            foreach ($request->table_info as $val) {
                if (!empty($val['table_number']) && !empty($val['no_of_seats'])) {
                    $res_settings = [
                        'table_number' => $val['table_number'],
                        'total_seats' => $val['no_of_seats'],
                    ];
                    if (isset($val['area_id'])) {
                        $get_enabled_area = Area::where('is_enabled', 1)->where('id', $val['area_id'])->first();
                        if (!empty($get_enabled_area)) {
                            $area_id_after_check = $val['area_id'];
                        } else {
                            $area_id_after_check = Area::where('area_name', 'Default')->first()->id;
                        }
                        $res_settings['area_id'] = $area_id_after_check;
                    }
                    if (isset($val['table_type_id'])) {
                        $get_enabled_table_type = TableType::where('is_enabled', 1)->where('id', $val['table_type_id'])->first();
                        if (!empty($get_enabled_table_type)) {
                            $table_type_id_after_check = $val['table_type_id'];
                        } else {
                            $table_type_id_after_check = TableType::where('table_type_name', 'Default')->first()->id;
                        }
                        $res_settings['table_type_id'] = $table_type_id_after_check;
                    }
                    TableInformation::updateOrCreate(
                        ['restaurant_id' => $request->restaurant_id, 'id' => $val['table_info_id']],
                        $res_settings
                    );
                }
            }
        }

        return redirect()->back()->with(['success' => 'Restaurant Settings Saved']);
    }

    public function bookings(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);

        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $bk_date = $booking_datetime = $request->input('bookingdate');
        $booking_status = $request->input('booking_status');
        $cust_query = $request->input('query');
        $restaurant = $request->input('resturant');
        $meal_type = $request->input('meal_type');
        $time_slot = $request->input('time_slot');
        $search_criteria = '';

        if (empty($booking_datetime)) {
            $booking_datetime = date('Y-m-d');
        }

        $booking_staus_search = 'all';
        if (!empty($booking_status) && $booking_status != 'all') {
            $booking_staus_search = $booking_staus[] = $_GET['booking_status'];
        } elseif (!empty($booking_status) && $booking_status == 'all') {
            $booking_staus = ['open', 'reserved', 'completed', 'cancelled'];
        } else {
            //$booking_staus = ['open', 'reserved', 'completed', 'cancelled'];
            $booking_staus = ['open'];
            $booking_staus_search = 'open';
        }
        // dd($booking_datetime);
        $query = Booking::query()->whereIn('restaurant_id', $restaurantIds)->whereDate('booking_datetime', '=', $booking_datetime)->whereIn('booking_status', $booking_staus);
        // $query = Booking::query()->whereIn('restaurant_id', $restaurantIds)->whereIn('booking_status', $booking_staus);

        if (!empty($cust_query)) {
            $query = $query->where('booking_status', 'LIKE', '%' . $cust_query . '%');
        }

        if (!empty($restaurant) && $restaurant != 'all') {
            $query = $query->where('restaurant_id', $restaurant);
        }

        if (!empty($meal_type) && $meal_type != 'all') {
            $search_criteria = '_' . $meal_type;
            $query = $query->where('booking_shift', 'LIKE', '%' . $meal_type . '%');
        }

        if (!empty($time_slot) && $time_slot != 'all') {
            $search_criteria = '_' . $time_slot;
            $query = $query->whereTime('booking_datetime', '=', date('H:i:s', strtotime($time_slot)));
        }

        $query = $query->orderBy('booking_datetime', 'ASC')->orderBy('booking_name', 'ASC')->with(['user', 'resTables']);
        $bookings_for_pax = $query->get();
        $bookings = $query->paginate(20);
        // dd($query);
        $in_complete_bookings = Booking::whereIn('restaurant_id', $restaurantIds)->whereDate('booking_datetime', '=', $booking_datetime)
            ->whereNotIn('booking_status', ['completed'])->paginate(20);
        // dd($in_complete_bookings);
        $count = $bookings->total();
        $restaurants = $user->restaurants;

        $no_of_pax = 0;
        if ($bookings_for_pax) {
            foreach ($bookings_for_pax as $book) {
                $no_of_pax = $no_of_pax + $book->no_of_seats;
            }
        }

        /*if($request->action == "bookingPrint" && (!empty($restaurant) && $restaurant != 'all')){
            $restaurant_obj = Restaurant::find($restaurant);
            $pdf = \App::make('dompdf.wrapper');
            $html = view('restaurantowner.bookingPrintList', array(
                'bookings' => $bookings_for_pax,
                'bookingdate' => $booking_datetime,
                'restaurant' => $restaurant_obj,
                'time_slot' => $time_slot,
                'shift' => $meal_type,
                'booking_staus_search' => $booking_staus_search,
            ))->render();
            $pdf->loadHTML($html);
            return $pdf->download('Reservation Report_'.$booking_datetime.$search_criteria.".pdf");
            /*return view('restaurantowner.bookingPrintList', array(
                'bookings' => $bookings_for_pax,
                'bookingdate' => $booking_datetime,
                'restaurant' => $restaurant_obj,
                'time_slot' => $time_slot,
            )); */
        /*} */

        $pagination_ar = [];

        if (!empty($restaurant)) {
            $pagination_ar['resturant'] = $restaurant;
        }

        if (!empty($meal_type)) {
            $pagination_ar['meal_type'] = $meal_type;
        }

        if (!empty($booking_status)) {
            $pagination_ar['booking_status'] = $booking_status;
        }

        if (!empty($bk_date)) {
            $pagination_ar['bookingdate'] = $bk_date;
        }

        if (!empty($time_slot)) {
            $pagination_ar['time_slot'] = $time_slot;
        }
        $restaurant_settings = RestaurantSettings::whereIn('restaurant_id', $restaurantIds)->get();
        // dd($in_complete_bookings->total());
        return view('restaurantowner.bookings', [
            'bookings' => $bookings,
            'bookingdate' => $booking_datetime,
            'booking_status' => $booking_staus_search,
            'count' => $count,
            'in_complete_booking' => $in_complete_bookings->total(),
            'restaurants' => $restaurants,
            'restaurant_id' => $restaurant,
            'meal_type' => $meal_type,
            'time_slot' => $time_slot,
            'no_of_pax' => $no_of_pax,
            'pagination' => $pagination_ar,
            'restaurant_settings' => $restaurant_settings,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function bookingPrint(Request $request)
    {
        $user = Auth::user();

        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $bk_date = $booking_datetime = $request->input('bookingdate');
        $booking_status = $request->input('booking_status');
        $cust_query = $request->input('query');
        $restaurant = $request->input('resturant');
        $meal_type = $request->input('meal_type');
        $time_slot = $request->input('time_slot');
        $search_criteria = '';

        if (empty($booking_datetime)) {
            $booking_datetime = date('Y-m-d');
        }

        $booking_staus_search = 'all';
        if (!empty($booking_status) && $booking_status != 'all') {
            $booking_staus_search = $booking_staus[] = $_GET['booking_status'];
        } elseif (!empty($booking_status) && $booking_status == 'all') {
            $booking_staus = ['open', 'reserved', 'completed', 'cancelled'];
        } else {
            //$booking_staus = ['open', 'reserved', 'completed', 'cancelled'];
            $booking_staus = ['open'];
            $booking_staus_search = 'open';
        }

        $query = Booking::query()->whereIn('restaurant_id', $restaurantIds)->whereDate('booking_datetime', '=', $booking_datetime)->whereIn('booking_status', $booking_staus);

        if (!empty($cust_query)) {
            $query = $query->where('booking_name', 'LIKE', '%' . $cust_query . '%');
        }

        if (!empty($restaurant) && $restaurant != 'all') {
            $query = $query->where('restaurant_id', $restaurant);
        }

        if (!empty($meal_type) && $meal_type != 'all') {
            $search_criteria = '_' . $meal_type;
            $query = $query->where('booking_shift', 'LIKE', '%' . $meal_type . '%');
        }

        if (!empty($time_slot) && $time_slot != 'all') {
            $search_criteria = '_' . $time_slot;
            $query = $query->whereTime('booking_datetime', '=', date('H:i:s', strtotime($time_slot)));
        }

        $query = $query->orderBy('booking_datetime', 'ASC')->orderBy('booking_name', 'ASC')->with(['user', 'resTables']);
        $bookings_for_pax = $query->get();
        $bookings = $query->paginate(20);

        $in_complete_bookings = Booking::whereIn('restaurant_id', $restaurantIds)->whereDate('booking_datetime', '=', $booking_datetime)
            ->whereNotIn('booking_status', ['completed'])->paginate(20);

        $count = $bookings->total();
        $restaurants = $user->restaurants;

        $no_of_pax = 0;
        if ($bookings_for_pax) {
            foreach ($bookings_for_pax as $book) {
                $no_of_pax = $no_of_pax + $book->no_of_seats;
            }
        }

        $restaurant_obj = Restaurant::find($restaurant);
        if (!empty($restaurant_obj)) {
            $pdf = \App::make('dompdf.wrapper');
            $html = view('restaurantowner.bookingPrintList', [
                'bookings' => $bookings_for_pax,
                'bookingdate' => $booking_datetime,
                'restaurant' => $restaurant_obj,
                'time_slot' => $time_slot,
                'shift' => $meal_type,
                'booking_staus_search' => $booking_staus_search,
            ])->render();
            $pdf->loadHTML($html);

            return $pdf->download('Reservation Report_' . $booking_datetime . $search_criteria . '.pdf');
        } else {
            return redirect()->back()->with(['message' => 'Please select any restaurant first.']);
        }
        /*return view('restaurantowner.bookingPrintList', array(
            'bookings' => $bookings_for_pax,
            'bookingdate' => $booking_datetime,
            'restaurant' => $restaurant_obj,
            'time_slot' => $time_slot,
        )); */
    }

    public function saveNewBooking(Request $request): RedirectResponse
    {
        $shif_settings = ShiftInformation::where('restaurant_id', $request->restaurant_id)->first();

        $user = User::where('email', $request->email_address)->first();
        if (!$user) {
            $user = new User();
            $user->password = bcrypt('ozeatspass@123');
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->name = $request->first_name . ' ' . $request->last_name;
            $user->phone = $request->mobile_number;
        }

        $user->email = $request->email_address;
        $user->dob = $request->dob;

        $user->save();
        if ($user) {
            $restaurantuser = User::findOrFail($user->id);
            // Sync restaurants for the user
            $restaurantuser->restaurants()->sync([$request->restaurant_id]);
            // Assign role to the user (if not already assigned)
            $user->assignRole('Customer');
            // Retrieve the role_id for 'Customer'
            $role_id = DB::table('roles')->where('name', 'Customer')->value('id');
            // Create record in restaurant_customer_model
            RestaurantCustomerModel::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'restaurant_id' => $request->restaurant_id
                ],
                [
                    'role_id' => $role_id
                ]
            );
        }

        if ($request->booking_type == 'onetime') {
            $booking = new Booking();

            $lastBooking = Booking::orderBy('id', 'desc')->first();

            if ($lastBooking) {
                $lastBookingId = $lastBooking->id;
                $newId = $lastBookingId + 1;
                $uniqueId = Hashids::connection('alternative')->encode($newId);
            } else {
                //first order
                $newId = 1;
            }
            $uniqueId = Hashids::connection('alternative')->encode($newId);
            $unique_booking_id = '989' . strtoupper($uniqueId);
            $booking->unique_booking_id = $unique_booking_id;

            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;

            $booking->no_of_seats = $request->no_of_seats;
            $booking->booking_name = $request->first_name . ' ' . $request->last_name;
            $booking->booking_firstname = $request->first_name;
            $booking->booking_lastname = $request->last_name;
            $booking->booking_mobile = $request->mobile_number;
            $booking->booking_email = $request->email_address;
            $booking->booking_datetime = date('Y-m-d H:i:s', strtotime($booking_date_time));
            $booking->comments = $request->comment;
            $booking->restaurant_id = $request->restaurant_id;
            $booking->user_id = $user->id;
            $booking->booking_type = $request->booking_type;
            $booking->area_id = $request->booking_table_location;

            $restaurant = Restaurant::find($request->restaurant_id);
            $booking_timing = strtotime(date('h:i A', strtotime($booking_date_time)));

            $shift_duration = '';
            $shift_start_time = '';
            $shift_end_time = '';
            if ($booking_timing >= strtotime($shif_settings->breakfastStartTime) && $booking_timing <= strtotime($shif_settings->breakfastEndTime)) {
                $booking->booking_shift = 'Breakfast';
                $shift_start_time = $shif_settings->breakfastStartTime;
                $shift_end_time = $shif_settings->breakfastEndTime;
                $shift_duration = $shif_settings->breakfastDuration;
            } elseif ($booking_timing >= strtotime($shif_settings->lunchStartTime) && $booking_timing <= strtotime($shif_settings->lunchEndTime)) {
                $booking->booking_shift = 'Lunch';
                $shift_start_time = $shif_settings->lunchStartTime;
                $shift_end_time = $shif_settings->lunchEndTime;
                $shift_duration = $shif_settings->lunchDuration;
            } elseif ($booking_timing >= strtotime($shif_settings->dinnerStartTime) && $booking_timing <= strtotime($shif_settings->dinnerEndTime)) {
                $booking->booking_shift = 'Dinner';
                $shift_start_time = $shif_settings->dinnerStartTime;
                $shift_end_time = $shif_settings->dinnerEndTime;
                $shift_duration = $shif_settings->dinnerDuration;
            } else {
                return redirect()->back()->with(['message' => 'Booking service not available at this time.']);
            }

            try {
                $pre_bookings_counts = Booking::where('booking_status', 'open')->whereBetween('booking_datetime', [date('Y/m/d H:i', strtotime($booking_date_time)), date('Y/m/d H:i', strtotime('+' . ($shift_duration - 1) . ' minutes', strtotime($booking_date_time)))])->get()->sum('no_of_seats');

                $next_total_seats = $request->no_of_seats + $pre_bookings_counts;

                $max_cover = 0;
                if ($booking->booking_shift == 'Breakfast') {
                    $max_cover = $shif_settings->max_cover_breakfast;
                }

                if ($booking->booking_shift == 'Lunch') {
                    $max_cover = $shif_settings->max_cover_lunch;
                }

                if ($booking->booking_shift == 'Dinner') {
                    $max_cover = $shif_settings->max_cover_dinner;
                }

                /*if($max_cover < $next_total_seats){
                    return redirect()->back()->with(['message' => "Booking Failed,  Max no of cover exceed for this duration"]);
                }*/

                /* $booking_timing = strtotime(date('H:i', strtotime($booking_date_time)));
                $booking_slot_start_time = '';
                $booking_slot_end = '';
                while(strtotime($shift_start_time) <= strtotime($shift_end_time)){
                    $start = date('H:i',strtotime($shift_start_time));
                    $end = date('H:i',strtotime('+'.$shift_duration.' minutes',strtotime($shift_start_time)));
                    $shift_start_time = date('H:i',strtotime('+'.$shift_duration.' minutes',strtotime($shift_start_time)));
                    if(strtotime($shift_start_time) <= strtotime($shift_end_time)){
                        if(strtotime($start) <= $booking_timing && strtotime($end) >= $booking_timing){
                            $booking_slot_start_time = $start;
                            $booking_slot_end = $end;
                        }
                    }
                }

                $booking_slot_start_time  =date('Y/m/d', strtotime($booking_date_time))." ".$booking_slot_start_time;
                $booking_slot_end  =date('Y/m/d', strtotime($booking_date_time))." ".$booking_slot_end;

                $pre_bookings_counts = Booking::where('booking_status', 'open')->whereBetween('booking_datetime', [date('Y/m/d H:i', strtotime($booking_slot_start_time)), date('Y/m/d H:i', strtotime($booking_slot_end))])->get()->sum('no_of_seats');

                $next_total_seats = $request->no_of_seats+$pre_bookings_counts;
                if($shif_settings->maxNoOfCover < $next_total_seats){
                    return redirect()->back()->with(['message' => "Booking Failed, ".($shif_settings->maxNoOfCover-$pre_bookings_counts)." seats are available int this time slot"]);
                }*/

                $booking->save();

                /* stripe payment integration */

                // Stripe::setApiKey('sk_test_hBgXCQyKglABQDgTCqzdVsC500U2srF8dw');

                // $stripe = new StripeClient(
                //     'sk_test_hBgXCQyKglABQDgTCqzdVsC500U2srF8dw'
                // );
                // dd($request->payment_method_types);
                $restaurant_settings = RestaurantSettings::where('restaurant_id', $request->restaurant_id)->first();
                $online_payment = $restaurant_settings->online_payment;
                $restaurant_name = Restaurant::where('id', $request->restaurant_id)->first();
                $amount = $request->stripeToken ? $request->no_of_seats * $restaurant_settings->deposit_amount_per_cover : 0;


                $wallet = Wallet::firstOrCreate(
                    [
                        'holder_type' => User::class,
                        'holder_id' => $user->id,
                        'slug' => $restaurant->slug, // Assuming you have a slug for the restaurant
                    ],
                    [
                        'name' => 'default Wallet',
                        'balance' => 0,
                        'decimal_places' => 2,
                    ]
                );
                // dd($restaurant_name->stripe_secret_key);
                if ($request->stripeToken != null) {


                    //   echo "<pre>"; print_r($responsesss); exit;
                    // dd($amount);
                    // \Stripe\Stripe::setApiKey(config('setting.stripePublicKey'));

                    \Stripe\Stripe::setApiKey($restaurant_name->stripe_secret_key);
                    $charge = \Stripe\Charge::create([
                        'amount' => $amount * 100,
                        'currency' => 'AUD',
                        'source' => $request->stripeToken,
                        'description' => 'Sommelier Payment',
                    ]);
                    Log::info("beposz accounts res :: " . $charge);
                    $wallet->balance += $amount * 100;
                    $wallet->save();
                    $transaction = new Transaction();
                    $transaction->payable_type = User::class;
                    $transaction->payable_id = $user->id;
                    $transaction->wallet_id = $wallet->id;
                    $transaction->type = 'deposit';
                    $transaction->amount = $amount * 100;
                    $transaction->confirmed = 1;
                    $transaction->meta = [
                        'description' => 'deposit for booking'
                    ];
                    $transaction->uuid = (string) Str::uuid();
                    $transaction->save();
                    // $output = [
                    //     'clientSecret' => $paymentIntent->client_secret,
                    // ];
                    // dd($output);
                    // dd($charge);
                    // $charge = \Stripe\Charge::create([
                    //     'amount' => $amount*100, // Amount in cents
                    //     'currency' => 'usd',
                    //     'source' => $request->stripeToken, // Token from Stripe.js
                    //     'description' => 'Laravel Payment',
                    // ]);

                }
                /* \Stripe\Charge::create ([
                         "amount" => 100 * 100,
                         "currency" => "usd",
                         "source" => $request->stripeToken,
                         "description" => "Test payment from sommilar."
                 ]);*/

                /* end stripe payment integration */

                $tables_info = TableInformation::with([
                    'bookings' => function ($q) {
                        $q->whereIn('booking_status', ['reserved']);
                    }
                ])->where('restaurant_id', $request->restaurant_id)->where('area_id', $booking->area_id)->orderBy('total_seats', 'asc')->get();

                $tables_info_count = TableInformation::with([
                    'bookings' => function ($q) {
                        $q->whereIn('booking_status', ['reserved']);
                    }
                ])->where('restaurant_id', $request->restaurant_id)->where('area_id', $booking->area_id)->sum('total_seats');

                $table_id = null;
                $table_number = '';
                if ($tables_info_count >= $request->no_of_seats) {
                    foreach ($tables_info as $table) {
                        if ($request->no_of_seats <= $table->total_seats) {
                            $table_id = $table->id;
                            $table_number = $table->table_number;
                            break;
                        }
                    }

                    /* if(empty($table_id)){

                     }*/
                }

                if (!empty($table_id)) {
                    $booking->booking_status = 'reserved';
                    $booking->resTables()->attach($table_id);
                }

                $bepoz_data[] = [
                    'date_time' => date('d-M-Y h:i A', strtotime($booking_date_time)),
                    'booking_comment' => $request->comment,
                    'total_guests' => $request->no_of_seats,
                    'unique_booking_id' => $unique_booking_id,
                ];
                $this->bepozIntegration($request, $user->id, $request->restaurant_id, $bepoz_data, $amount);


            } catch (\Stripe\Exception\CardException $e) {
                // Handle card errors
                return back()->withErrors(['card_error' => $e->getMessage()]);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th]);
            }

            try {
                $data['name'] = $request->first_name;
                $data['booking_date'] = date('d M, Y', strtotime($booking_date_time));
                $data['booking_time'] = date('h:i A', strtotime($booking_date_time));
                $data['email_name'] = $shif_settings->teamName;
                $data['email_from'] = $shif_settings->emailFrom;
                $data['customer_email'] = $request->email_address;
                $data['restaurant_name'] = $restaurant->name;
                $data['restaurant_add'] = $restaurant->address;
                $data['admin_name'] = $shif_settings->teamName;
                $data['booking_id'] = $unique_booking_id;
                $data['no_of_pax'] = $request->no_of_seats;
                $data['comment'] = $request->comment;

                Mail::send('emails.bookingConfirmClient', ['mailData' => $data], function ($message) use ($data) {
                    $message->subject('Booking Request Accepted');
                    $message->from($data['email_from'], $data['email_name']);
                    $message->to($data['customer_email']);
                });

                if (($shif_settings->email_options == 1 || $shif_settings->email_options == 3) && !empty($shif_settings->teamName) && !empty($shif_settings->emailFrom)) {
                    $data['customer_name'] = $request->first_name . ' ' . $request->last_name;
                    $data['admin_name'] = $shif_settings->teamName;
                    $data['customer_mobile'] = $request->mobile_number;
                    $data['client_name'] = $user->name;
                    $data['client_email'] = $shif_settings->emailFrom;
                    $data['no_of_pax'] = $request->no_of_seats;
                    $data['comment'] = $request->comment;

                    Mail::send('emails.bookingRecieveAdmin', ['mailData' => $data], function ($message) use ($data) {
                        $message->subject('Booking From Web');
                        $message->from($data['email_from'], $data['email_name']);
                        $message->to($data['client_email']);
                    });
                }
                // restore once set ip addrss in sendgrid whitelist // by brian ***
            } catch (\Throwable $th) {
                Log::error("savenewbooking mailing error:: " . $th->getMessage());
            }

            return redirect()->back()->with(['success' => 'Booking Saved']);
        }

        if ($request->booking_type == 'recurring') {
            $booking_frequncy = $request->booking_frequency;
            $booking_date = $request->booking_date;
            $day_on_date = date('D', strtotime($booking_date));

            $start = $month = strtotime($booking_date);
            $end = strtotime('+1 year', $month);
            $counter = 1;
            $parent_booking_id = 0;
            if ($booking_frequncy == 'weekly') {
                while ($month < $end) {
                    //echo date('d F Y', $month)."<br/>";
                    $new_booking_id = $this->createRecurringBookings($request, date('d-m-Y', $month), $user, $shif_settings, $parent_booking_id);
                    $month = strtotime('+1 week', $month);
                    if ($counter == 1) {
                        $parent_booking_id = $new_booking_id;
                    }

                    $counter++;
                }
            }

            if ($booking_frequncy == 'monthly') {
                while ($month < $end) {
                    //echo date('d F Y', $month)."<br/>";
                    $new_booking_id = $this->createRecurringBookings($request, date('d-m-Y', $month), $user, $shif_settings, $parent_booking_id);
                    $month = strtotime('+1 month', $month);
                    if ($counter == 1) {
                        $parent_booking_id = $new_booking_id;
                    }

                    $counter++;
                }
            }

            return redirect()->back()->with(['success' => 'Booking Saved']);
        }
    }

    public function createRecurringBookings($request, $booking_date, $user, $shif_settings, $parent_booking_id)
    {
        $booking = new Booking();

        $lastBooking = Booking::orderBy('id', 'desc')->first();

        if ($lastBooking) {
            $lastBookingId = $lastBooking->id;
            $newId = $lastBookingId + 1;
            $uniqueId = Hashids::connection('alternative')->encode($newId);
        } else {
            //first order
            $newId = 1;
        }
        $uniqueId = Hashids::connection('alternative')->encode($newId);
        $unique_booking_id = '989' . strtoupper($uniqueId);
        $booking->unique_booking_id = $unique_booking_id;

        $booking_date_time = $booking_date . ' ' . $request->booking_time;

        $booking->no_of_seats = $request->no_of_seats;
        $booking->booking_name = $request->first_name . ' ' . $request->last_name;
        $booking->booking_firstname = $request->first_name;
        $booking->booking_lastname = $request->last_name;
        $booking->booking_mobile = $request->mobile_number;
        $booking->booking_email = $request->email_address;
        $booking->booking_datetime = date('Y-m-d H:i:s', strtotime($booking_date_time));
        $booking->comments = $request->comment;
        $booking->restaurant_id = $request->restaurant_id;
        $booking->user_id = $user->id;
        $booking->booking_type = $request->booking_type;
        $booking->parent_booking_id = $parent_booking_id;
        $booking->area_id = $request->booking_table_location;

        $booking_timing = strtotime(date('h:i A', strtotime($booking_date_time)));

        $shift_duration = '';
        $shift_start_time = '';
        $shift_end_time = '';
        if ($booking_timing >= strtotime($shif_settings->breakfastStartTime) && $booking_timing <= strtotime($shif_settings->breakfastEndTime)) {
            $booking->booking_shift = 'Breakfast';
            $shift_start_time = $shif_settings->breakfastStartTime;
            $shift_end_time = $shif_settings->breakfastEndTime;
            $shift_duration = $shif_settings->breakfastDuration;
        } elseif ($booking_timing >= strtotime($shif_settings->lunchStartTime) && $booking_timing <= strtotime($shif_settings->lunchEndTime)) {
            $booking->booking_shift = 'Lunch';
            $shift_start_time = $shif_settings->lunchStartTime;
            $shift_end_time = $shif_settings->lunchEndTime;
            $shift_duration = $shif_settings->lunchDuration;
        } elseif ($booking_timing >= strtotime($shif_settings->dinnerStartTime) && $booking_timing <= strtotime($shif_settings->dinnerEndTime)) {
            $booking->booking_shift = 'Dinner';
            $shift_start_time = $shif_settings->dinnerStartTime;
            $shift_end_time = $shif_settings->dinnerEndTime;
            $shift_duration = $shif_settings->dinnerDuration;
        } else {
            return;
        }

        $booking->save();

        $tables_info = TableInformation::with([
            'bookings' => function ($q) {
                $q->whereIn('booking_status', ['reserved']);
            }
        ])->where('restaurant_id', $request->restaurant_id)->where('area_id', $booking->area_id)->orderBy('total_seats', 'asc')->get();

        $tables_info_count = TableInformation::with([
            'bookings' => function ($q) {
                $q->whereIn('booking_status', ['reserved']);
            }
        ])->where('restaurant_id', $request->restaurant_id)->where('area_id', $booking->area_id)->sum('total_seats');

        $table_id = null;
        if ($tables_info_count >= $booking->no_of_seats) {
            foreach ($tables_info as $table) {
                if ($booking->no_of_seats <= $table->total_seats) {
                    $table_id = $table->id;
                    break;
                }
            }

            /* if(empty($table_id)){

             }*/
        }

        if (!empty($table_id)) {
            $booking->booking_status = 'reserved';
            $booking->resTables()->attach($table_id);
        }

        return $booking->id;
    }

    public function getEditBooking($id)
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();


        $booking = Booking::where('id', $id)
            ->whereIn('restaurant_id', $restaurantIds)
            ->with('user')
            ->first();

        if ($booking) {
            $tables_info = TableInformation::with([
                'bookings' => function ($q) {
                    $q->whereIn('booking_status', ['reserved']);
                    //$q->whereNotIn('id', [$booking->id]);
                }
            ])->where('restaurant_id', $booking->restaurant_id)->where('area_id', $booking->area_id)->orderBy('total_seats', 'asc')->get();

            $restaurants = $user->restaurants;
            $is_active = $restaurants[0]->is_active;
            $reservation = RestaurantSettings::find($restaurants[0]->id);
            return view('restaurantowner.editBooking', [
                'booking' => $booking,
                'restaurants' => $restaurants,
                'tables_info' => $tables_info,
                'selected_booking' => $booking,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        } else {
            return redirect()->route('restaurant.bookings')->with(['message' => 'Access Denied']);
        }
    }

    public function updateBooking(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $shif_settings = ShiftInformation::where('restaurant_id', $request->restaurant_id)->first();

        $booking = Booking::where('id', $request->id)
            ->whereIn('restaurant_id', $restaurantIds)
            ->with('user')
            ->first();

        if ($booking) {
            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;

            $booking->no_of_seats = $request->no_of_seats;
            $booking->booking_name = $request->first_name . ' ' . $request->last_name;
            $booking->booking_firstname = $request->first_name;
            $booking->booking_lastname = $request->last_name;
            $booking->booking_mobile = $request->mobile_number;
            $booking->booking_email = $request->email_address;
            $booking->booking_datetime = date('Y-m-d H:i:s', strtotime($booking_date_time));
            $booking->comments = $request->comment;
            $booking->restaurant_id = $request->restaurant_id;

            $user = User::where('email', $request->email_address)->first();
            if (!$user) {
                $user = new User();
                $user->password = bcrypt('ozeatspass@123');
            }

            //$user->phone = $request->mobile_number;
            $user->email = $request->email_address;
            //$user->first_name = $request->first_name;
            //$user->last_name = $request->last_name;
            //$user->name = $request->first_name." ".$request->last_name;
            $user->dob = $request->dob;
            $user->save();
            if ($user) {
                $user->assignRole('Customer');
            }
            $booking->user_id = $user->id;
            /*$booking->user->phone = $request->mobile_number;
            $booking->user->email = $request->email_address;
            $booking->user->first_name = $request->first_name;
            $booking->user->last_name = $request->last_name;
            $booking->user->dob = $request->dob;
            $booking->user->name = $request->first_name." ".$request->last_name;
            $booking->user->save();*/

            $restaurant = Restaurant::find($request->restaurant_id);
            $booking_timing = strtotime(date('h:i A', strtotime($booking_date_time)));

            $shift_duration = '';
            $shift_start_time = '';
            $shift_end_time = '';
            if ($booking_timing >= strtotime($shif_settings->breakfastStartTime) && $booking_timing <= strtotime($shif_settings->breakfastEndTime)) {
                $booking->booking_shift = 'Breakfast';
                $shift_start_time = $shif_settings->breakfastStartTime;
                $shift_end_time = $shif_settings->breakfastEndTime;
                $shift_duration = $shif_settings->breakfastDuration;
            } elseif ($booking_timing >= strtotime($shif_settings->lunchStartTime) && $booking_timing <= strtotime($shif_settings->lunchEndTime)) {
                $booking->booking_shift = 'Lunch';
                $shift_start_time = $shif_settings->lunchStartTime;
                $shift_end_time = $shif_settings->lunchEndTime;
                $shift_duration = $shif_settings->lunchDuration;
            } elseif ($booking_timing >= strtotime($shif_settings->dinnerStartTime) && $booking_timing <= strtotime($shif_settings->dinnerEndTime)) {
                $booking->booking_shift = 'Dinner';
                $shift_start_time = $shif_settings->dinnerStartTime;
                $shift_end_time = $shif_settings->dinnerEndTime;
                $shift_duration = $shif_settings->dinnerDuration;
            } else {
                return redirect()->back()->with(['message' => 'Booking service not available at this time.']);
            }

            try {
                $pre_bookings_counts = Booking::whereNotIn('id', [$request->id])->where('booking_status', 'open')->whereBetween('booking_datetime', [date('Y/m/d H:i', strtotime($booking_date_time)), date('Y/m/d H:i', strtotime('+' . ($shift_duration - 1) . ' minutes', strtotime($booking_date_time)))])->get()->sum('no_of_seats');

                $next_total_seats = $request->no_of_seats + $pre_bookings_counts;

                $max_cover = 0;
                if ($booking->booking_shift == 'Breakfast') {
                    $max_cover = $shif_settings->max_cover_breakfast;
                }

                if ($booking->booking_shift == 'Lunch') {
                    $max_cover = $shif_settings->max_cover_lunch;
                }

                if ($booking->booking_shift == 'Dinner') {
                    $max_cover = $shif_settings->max_cover_dinner;
                }

                /*if($max_cover < $next_total_seats){
                    return redirect()->back()->with(['message' => "Booking Failed,  Max no of cover exceed for this duration"]);
                }*/

                if (!empty($request->selected_table)) {
                    if (!empty($booking->resTables)) {
                        foreach ($booking->resTables as $ttt) {
                            $booking->resTables()->detach($ttt->pivot->table_information_id);
                        }
                    }

                    $booking_seats = $booking->no_of_seats;
                    $table_info = TableInformation::whereIn('id', $request->selected_table)->get('total_seats');
                    $total_seats = 0;
                    if ($table_info->isNotEmpty()) {
                        foreach ($table_info as $table) {
                            $total_seats = $total_seats + $table->total_seats;
                        }
                    }

                    if ($booking_seats <= $total_seats) {
                        $booking->booking_status = 'reserved';
                        $booking->resTables()->attach($request->selected_table);
                    } else {
                        return redirect()->back()->with(['message' => "Oop's not sufficient seats available."]);
                    }
                } else {
                    if (!empty($booking->resTables)) {
                        $booking->booking_status = 'open';
                        foreach ($booking->resTables as $ttt) {
                            $booking->resTables()->detach($ttt->pivot->table_information_id);
                        }
                    }
                    //return redirect()->back()->with(['message' => "Please choose booking or table."]);
                }

                /*$booking_timing = strtotime(date('H:i', strtotime($request->timing)));


                $booking_slot_start_time = '';
                $booking_slot_end = '';
                while(strtotime($shift_start_time) <= strtotime($shift_end_time)){
                    $start = date('H:i',strtotime($shift_start_time));
                    $end = date('H:i',strtotime('+'.$shift_duration.' minutes',strtotime($shift_start_time)));
                    $shift_start_time = date('H:i',strtotime('+'.$shift_duration.' minutes',strtotime($shift_start_time)));
                    if(strtotime($shift_start_time) <= strtotime($shift_end_time)){
                        if(strtotime($start) <= $booking_timing && strtotime($end) >= $booking_timing){
                            $booking_slot_start_time = $start;
                            $booking_slot_end = $end;
                        }
                    }
                }

                $booking_slot_start_time  =date('Y/m/d', strtotime($request->timing))." ".$booking_slot_start_time;
                $booking_slot_end  =date('Y/m/d', strtotime($request->timing))." ".$booking_slot_end;

                $pre_bookings_counts = Booking::whereNotIn('id', [$request->id])->where('booking_status', 'open')->whereBetween('booking_datetime', [date('Y/m/d H:i', strtotime($booking_slot_start_time)), date('Y/m/d H:i', strtotime($booking_slot_end))])->get()->sum('no_of_seats');

                $next_total_seats = $request->no_of_seats+$pre_bookings_counts;
                if($shif_settings->maxNoOfCover < $next_total_seats){
                    return redirect()->back()->with(['message' => "Booking Failed, ".($shif_settings->maxNoOfCover-$pre_bookings_counts)." seats are available int this time slot"]);
                }*/

                $booking->save();

                $data['name'] = $request->first_name;
                $data['booking_date'] = date('d M, Y', strtotime($booking_date_time));
                $data['booking_time'] = date('h:i A', strtotime($booking_date_time));
                $data['email_name'] = $shif_settings->teamName;
                $data['email_from'] = $shif_settings->emailFrom;
                $data['customer_email'] = $request->email_address;
                $data['restaurant_name'] = $restaurant->name;
                $data['restaurant_add'] = $restaurant->address;
                $data['admin_name'] = $shif_settings->teamName;
                $data['booking_id'] = $booking->unique_booking_id;
                $data['no_of_pax'] = $request->no_of_seats;

                Mail::send('emails.bookingUpdateClient', ['mailData' => $data], function ($message) use ($data) {
                    $message->subject('Booking Request Accepted');
                    $message->from($data['email_from'], $data['email_name']);
                    $message->to($data['customer_email']);
                });

                /*if(($shif_settings->email_options == 1 || $shif_settings->email_options == 3) && !empty($shif_settings->teamName) && !empty($shif_settings->emailFrom)){


                    $data['customer_name'] = $request->first_name." ".$request->last_name;
                    $data['admin_name'] = $shif_settings->teamName;
                    $data['customer_mobile'] = $request->mobile_number;
                    $data['client_name'] =$user->name;
                    $data['client_email'] = $shif_settings->emailFrom;

                    Mail::send('emails.bookingRecieveAdmin', ['mailData' => $data], function ($message) use ($data) {
                        $message->subject("Booking From Web");
                        $message->from($data['email_from'], $data['email_name']);
                        $message->to($data['client_email']);
                    });
                }*/

                return redirect()->back()->with(['success' => 'Booking Saved']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th]);
            }
        }
    }

    public function searchBooking(Request $request): View
    {
        $user = Auth::user();

        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $query = $request['query'];

        /* $bookings = Booking::whereIn('restaurant_id', $restaurantIds)
             ->where('first_name', 'LIKE', '%' . $query . '%')
             ->with('user')
             ->paginate(20);
         $bookings = Booking::with(['user' => function($query) use ($search){
                 $query->where('first_name', 'LIKE', '%' . $search . '%');
            }])->whereIn('restaurant_id', $restaurantIds)->paginate(20);

            $orders = Order::whereIn('restaurant_id', $restaurantIds)
             ->where('unique_order_id', 'LIKE', '%' . $query . '%')
             ->with('accept_delivery.user', 'restaurant')
             ->paginate(20);*/

        $bookings = Booking::whereIn('restaurant_id', $restaurantIds)
            ->where('booking_name', 'LIKE', '%' . $query . '%')
            ->with('user')
            ->paginate(20);
        $count = $bookings->total();

        $restaurants = Restaurant::get();

        return view('restaurantowner.bookings', [
            'bookings' => $bookings,
            'count' => $count,
            'restaurants' => $restaurants,
            'query' => $query,
        ]);
    }

    public function doneAllBooking(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $booking_datetime = $request->input('booking_date');
        $booking_status = $request->input('booking_status');

        if (empty($booking_datetime)) {
            return redirect()->back()->with(['message' => 'Please select booking data or status']);
        }

        $booking_staus_search = '';
        if (!empty($booking_status) && $booking_status != 'all') {
            $booking_staus_search = $booking_staus[] = $booking_status;
        } else {
            $booking_staus = ['open', 'reserved', 'completed', 'cancelled'];
        }

        $bookings = Booking::whereIn('restaurant_id', $restaurantIds)->whereDate('booking_datetime', '=', $booking_datetime)
            ->whereIn('booking_status', $booking_staus)
            ->with(['user', 'resTables'])->paginate(20);

        if ($bookings->isNotEmpty()) {
            foreach ($bookings as $booking) {
                $booking->booking_status = 'completed';
                $booking->save();
            }
        }

        return redirect()->back()->with(['success' => 'Operation Successful']);
    }

    public function assignTable(Request $request): View
    {
        $user = Auth::user();
        $restaurant = $user->restaurants;
        $is_active = $restaurant[0]->is_active;
        $reservation = RestaurantSettings::find($restaurant[0]->id);
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $restaurant_id = $request->input('resturant');
        $booking_date = $request->input('booking_date');
        $meal_type = $request->input('meal_type');

        $query = Booking::query()->whereIn('restaurant_id', $restaurantIds)->where('booking_status', 'open');
        if (!empty($booking_date)) {
            $query = $query->whereDate('booking_datetime', date('Y-m-d', strtotime($booking_date)));
        } else {
            $query = $query->whereDate('booking_datetime', date('Y-m-d'));
        }

        if (!empty($meal_type) && $meal_type != 'all') {
            $query = $query->where('booking_shift', 'LIKE', '%' . $meal_type . '%');
        }

        //if(!empty($restaurant_id))
        $query = $query->where('restaurant_id', $restaurant_id);

        $bookings = $query->get();
        // $bookings = Booking::whereIn('restaurant_id', $restaurantIds)
        // ->whereDate('booking_datetime', date('Y-m-d', strtotime($request->booking_date)))
        //->where('booking_shift', 'LIKE', '%' . $request->meal_type . '%')
        //->where('booking_status', 'open')
        //->where('restaurant_id', $request->resturant)
        //->get();

        $tables_info = TableInformation::with([
            'bookings' => function ($q) {
                $q->whereIn('booking_status', ['reserved']);
            }
        ])->where('restaurant_id', $restaurant_id)->get();

        // $bookings = Booking::whereIn('restaurant_id', $restaurantIds)
        // ->orderBy('id', 'DESC')->paginate(20);

        $count = 0;
        $restaurants = $user->restaurants;

        return view('restaurantowner.assignTable', [
            'bookings' => $bookings,
            'count' => $count,
            'restaurants' => $restaurants,
            'tables_info' => $tables_info,
            'restaurant_id' => $restaurant_id,
            'meal_type' => $meal_type,
            'search_date' => (!empty($booking_date)) ? $booking_date : date('Y-m-d'),
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }

    public function getAvailableTables(Request $request): JsonResponse
    {
        $booking_id = $request->input('booking_id');
        $restaurant_id = $request->input('restaurant_id');
        if (!empty($booking_id) && !empty($restaurant_id)) {
            $booking = Booking::find($booking_id);

            $tables_info = TableInformation::with([
                'bookings' => function ($q) {
                    $q->whereIn('booking_status', ['reserved']);
                }
            ])->where('restaurant_id', $restaurant_id)->where('area_id', $booking->area_id)->orderBy('total_seats', 'asc')->get();

            /* $tables_info_count = TableInformation::with(array('bookings' => function($q){
                 $q->whereIn('booking_status', ['reserved']);
             }))->where('restaurant_id', $restaurant_id)->where('area_id', $booking->area_id )->sum('total_seats');




             $table_id = null;
             if($tables_info_count >= $booking->no_of_seats){
                 foreach($tables_info as $table){
                     if($booking->no_of_seats <= $table->total_seats)
                         $table_id = $table->id;
                 }

                 if(empty($table_id)){

                 }
             }*/

            $html = view('restaurantowner.getAvailableTables', [
                'tables_info' => $tables_info,
                'restaurant_id' => $restaurant_id,
                'selected_booking' => $booking,
            ])->render();

            return response()->json(['success' => true, 'html' => $html, 'no_of_persons' => $booking->no_of_seats], 200);
        }
    }

    public function getTableAreas($id): JsonResponse
    {
        $areas = Area::where('restaurant_id', $id)->where('is_enabled', 1)->get();
        if ($areas) {
            $html = view('restaurantowner.tableAreasLocations', [
                'areas' => $areas,
            ])->render();

            return response()->json(['success' => true, 'html' => $html], 200);
        } else {
            return response()->json(['success' => false, 'html' => ''], 200);
        }
    }
    public function getRestaurantVenue($id): JsonResponse
    {
        $user = User::where('id', $id)->first();
        $restaurants = $user->restaurants;
        if ($restaurants) {
            $html = view('restaurantowner.restaurantname', [
                'restaurants' => $restaurants,
            ])->render();

            return response()->json(['success' => true, 'html' => $html], 200);
        } else {
            return response()->json(['success' => false, 'html' => ''], 200);
        }
    }
    public function restaurantBookingSearch(Request $request): View
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        // $query = $request['query'];

        $bookings = Booking::whereIn('restaurant_id', $restaurantIds)
            ->whereDate('booking_datetime', date('Y-m-d', strtotime($request->booking_date)))
            ->where('booking_shift', 'LIKE', '%' . $request->meal_type . '%')
            ->where('booking_status', 'open')
            ->where('restaurant_id', $request->resturant)
            ->get();

        /* $tables_info = TableInformation::whereIn('restaurant_id', $restaurantIds)
             //->whereDate('booking_datetime', date('Y-m-d', strtotime($request->booking_date)))
            // ->where('booking_shift', 'LIKE', '%' . $request->meal_type . '%')
             ->where('restaurant_id', $request->resturant)
             ->get();*/

        $tables_info = TableInformation::with([
            'bookings' => function ($q) {
                $q->whereIn('booking_status', ['reserved']);
            }
        ])->where('restaurant_id', $request->resturant)->get();

        //$count = $bookings->total();
        //dd($tables_info);

        $restaurants = $user->restaurants;

        return view('restaurantowner.assignTable', [
            'bookings' => $bookings,
            //'count' => $count,
            'restaurants' => $restaurants,
            'tables_info' => $tables_info,
            'search_date' => date('Y-m-d', strtotime($request->booking_date)),
        ]);
    }

    public function assignTableToBooking(Request $request): RedirectResponse
    {
        if (!empty($request->selected_table) && !empty($request->booking_id)) {
            $booking = Booking::find($request->booking_id);
            $booking_seats = $booking->no_of_seats;
            $table_info = TableInformation::whereIn('id', $request->selected_table)->get('total_seats');
            $total_seats = 0;
            if ($table_info->isNotEmpty()) {
                foreach ($table_info as $table) {
                    $total_seats = $total_seats + $table->total_seats;
                }
            }

            if ($booking_seats <= $total_seats) {
                $booking->booking_status = 'reserved';
                $booking->save();
                $booking->resTables()->attach($request->selected_table);

                if ($booking->booking_type == 'recurring') {
                    $child_bookings = Booking::where('parent_booking_id', $request->booking_id)->get();
                    if ($child_bookings->isNotEmpty()) {
                        foreach ($child_bookings as $child_booking) {
                            $child_booking->booking_status = 'reserved';
                            $child_booking->save();
                            $child_booking->resTables()->attach($request->selected_table);
                        }
                    }
                }
            } else {
                return redirect()->route('restaurant.assignTable')->with(['message' => "Oop's not sufficient seats available."]);
            }
        } else {
            return redirect()->route('restaurant.assignTable')->with(['message' => 'Please choose booking or table.']);
        }

        return redirect()->route('restaurant.assignTable')->with(['success' => 'Table assigned to selected booking.']);
    }

    public function cancelBooking($id): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $booking = Booking::where('id', $id)
            ->whereIn('restaurant_id', $restaurantIds)
            ->first();
        if ($booking) {
            if ($booking->booking_status == 'completed') {
                return redirect()->route('restaurant.bookings')->with(['success' => 'Booking already completed.']);
            }

            $booking->booking_status = 'cancelled';
            $booking->save();
            $this->cancelBookingOnBepoz($booking);

            return redirect()->back()->with(['success' => 'Booking cancelled successfully.']);
        }

        return redirect()->back()->with(['message' => 'Booking not found!']);
    }

    public function cancelAllBooking($id): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $booking = Booking::where('id', $id)
            ->whereIn('restaurant_id', $restaurantIds)
            ->first();
        if ($booking) {
            if ($booking->booking_status == 'completed') {
                return redirect()->route('restaurant.bookings')->with(['success' => 'Booking already completed.']);
            }

            $booking->booking_status = 'cancelled';
            $booking->save();
            $this->cancelBookingOnBepoz($booking);

            if ($booking->parent_booking_id == 0) {
                $child_bookings = Booking::where('parent_booking_id', $id)->get();
                if ($child_bookings->isNotEmpty()) {
                    foreach ($child_bookings as $child_booking) {
                        $child_booking->booking_status = 'cancelled';
                        $child_booking->save();
                    }
                }
            }

            if ($booking->parent_booking_id >= 0) {
                $child_bookings = Booking::where('parent_booking_id', $booking->parent_booking_id)->get();
                if ($child_bookings->isNotEmpty()) {
                    foreach ($child_bookings as $child_booking) {
                        $child_booking->booking_status = 'cancelled';
                        $child_booking->save();
                    }
                }
            }

            return redirect()->back()->with(['success' => 'Booking cancelled successfully.']);
        }

        return redirect()->back()->with(['message' => 'Booking not found!']);
    }

    public function disableBooking($id): RedirectResponse
    {
        $user = Auth::user();
        $restaurantIds = $user->restaurants->pluck('id')->toArray();

        $booking = Booking::where('id', $id)
            ->whereIn('restaurant_id', $restaurantIds)
            ->first();
        if ($booking) {
            if (Booking::where('booking_status', 'open')) {
                $booking->booking_status = 'completed';
            } else {
                $booking->booking_status = 'open';
            }
            $booking->save();

            return redirect()->back()->with(['success' => 'Booking Closed Successfully']);
        } else {
            return redirect()->back();
        }
    }

    /* api's */

    public function maitredeAddBooking(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'no_of_seats' => ['required', 'numeric', 'max:255'],
                'timing' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'area_id' => ['required', 'numeric'],
                'mobile' => ['required', 'min:10'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'dob' => ['string', 'nullable'],
                'comment' => ['string', 'nullable'],
                'restaurant_id' => ['required', 'numeric', 'max:255'],
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                $user = new User();
                $user->password = bcrypt('ozeatspass@123');
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->name = $request->first_name . ' ' . $request->last_name;
            }

            $user->phone = $request->mobile;
            $user->email = $request->email;
            $user->dob = $request->dob;
            $user->save();
            if ($user) {
                $user->assignRole('Customer');
                if (isset($restaurant_id)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);

                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $restaurant_id)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
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
            }

            $booking = new Booking();

            $lastBooking = Booking::orderBy('id', 'desc')->first();

            if ($lastBooking) {
                $lastBookingId = $lastBooking->id;
                $newId = $lastBookingId + 1;
                $uniqueId = Hashids::connection('alternative')->encode($newId);
            } else {
                //first order
                $newId = 1;
            }
            $uniqueId = Hashids::connection('alternative')->encode($newId);
            $unique_booking_id = '989' . strtoupper($uniqueId);
            $booking->unique_booking_id = $unique_booking_id;

            $booking->no_of_seats = $request->no_of_seats;
            $booking->booking_name = $request->first_name . ' ' . $request->last_name;
            $booking->booking_firstname = $request->first_name;
            $booking->booking_lastname = $request->last_name;
            $booking->booking_datetime = date('Y-m-d H:i:s', strtotime($request->timing));
            $booking->comments = $request->comment;
            $booking->restaurant_id = $request->restaurant_id;
            $booking->user_id = $user->id;
            $booking->area_id = $request->area_id;

            $shif_settings = ShiftInformation::where('restaurant_id', $request->restaurant_id)->first();
            $restaurant = Restaurant::find($request->restaurant_id);
            $booking_timing = strtotime(date('h:i A', strtotime($request->timing)));

            $shift_duration = '';
            $shift_start_time = '';
            $shift_end_time = '';
            if ($booking_timing >= strtotime($shif_settings->breakfastStartTime) && $booking_timing <= strtotime($shif_settings->breakfastEndTime)) {
                $booking->booking_shift = 'Breakfast';
                $shift_start_time = $shif_settings->breakfastStartTime;
                $shift_end_time = $shif_settings->breakfastEndTime;
                $shift_duration = $shif_settings->breakfastDuration;
            } elseif ($booking_timing >= strtotime($shif_settings->lunchStartTime) && $booking_timing <= strtotime($shif_settings->lunchEndTime)) {
                $booking->booking_shift = 'Lunch';
                $shift_start_time = $shif_settings->lunchStartTime;
                $shift_end_time = $shif_settings->lunchEndTime;
                $shift_duration = $shif_settings->lunchDuration;
            } elseif ($booking_timing >= strtotime($shif_settings->dinnerStartTime) && $booking_timing <= strtotime($shif_settings->dinnerEndTime)) {
                $booking->booking_shift = 'Dinner';
                $shift_start_time = $shif_settings->dinnerStartTime;
                $shift_end_time = $shif_settings->dinnerEndTime;
                $shift_duration = $shif_settings->dinnerDuration;
            } else {
                $response = ['success' => false, 'data' => 'Booking service not available at this time.'];

                return response()->json($response, 201);
            }

            $booking_timing = strtotime(date('H:i', strtotime($request->timing)));

            $booking_slot_start_time = '';
            $booking_slot_end = '';
            while (strtotime($shift_start_time) <= strtotime($shift_end_time)) {
                $start = date('H:i', strtotime($shift_start_time));
                $end = date('H:i', strtotime('+' . $shift_duration . ' minutes', strtotime($shift_start_time)));
                $shift_start_time = date('H:i', strtotime('+' . $shift_duration . ' minutes', strtotime($shift_start_time)));
                if (strtotime($shift_start_time) <= strtotime($shift_end_time)) {
                    if (strtotime($start) <= $booking_timing && strtotime($end) >= $booking_timing) {
                        $booking_slot_start_time = $start;
                        $booking_slot_end = $end;
                    }
                }
            }

            $booking_slot_start_time = date('Y/m/d', strtotime($request->timing)) . ' ' . $booking_slot_start_time;
            $booking_slot_end = date('Y/m/d', strtotime($request->timing)) . ' ' . $booking_slot_end;

            $pre_bookings_counts = Booking::where('booking_status', 'open')->whereBetween('booking_datetime', [date('Y/m/d H:i', strtotime($booking_slot_start_time)), date('Y/m/d H:i', strtotime($booking_slot_end))])->get()->sum('no_of_seats');

            $next_total_seats = $request->no_of_seats + $pre_bookings_counts;
            if ($shif_settings->maxNoOfCover < $next_total_seats) {
                $response = ['success' => false, 'data' => 'Booking Failed, ' . ($shif_settings->maxNoOfCover - $pre_bookings_counts) . ' seats are available int this time slot'];

                return response()->json($response, 201);
            }

            $booking->save();

            $data['name'] = $request->first_name;
            $data['booking_date'] = date('d M, Y', strtotime($request->timing));
            $data['booking_time'] = date('h:i A', strtotime($request->timing));
            $data['email_name'] = $shif_settings->teamName;
            $data['email_from'] = $shif_settings->emailFrom;
            $data['customer_email'] = $request->email;
            $data['restaurant_name'] = $restaurant->name;
            $data['restaurant_add'] = $restaurant->address;
            $data['booking_id'] = $unique_booking_id;
            $data['no_of_pax'] = $request->no_of_seats;
            $data['comment'] = $request->comment;
            $data['admin_name'] = $shif_settings->teamName;

            Mail::send('emails.bookingConfirmClient', ['mailData' => $data], function ($message) use ($data) {
                $message->subject('Booking Request Accepted');
                $message->from($data['email_from'], $data['email_name']);
                $message->to($data['customer_email']);
            });

            if (($shif_settings->email_options == 2 || $shif_settings->email_options == 3) && !empty($shif_settings->teamName) && !empty($shif_settings->emailFrom)) {
                $data['customer_name'] = $request->first_name . ' ' . $request->last_name;
                $data['customer_mobile'] = $request->mobile;
                $data['client_name'] = $user->name;
                $data['client_email'] = $shif_settings->emailFrom;
                $data['no_of_pax'] = $request->no_of_seats;
                $data['comment'] = $request->comment;

                Mail::send('emails.bookingRecieveAdmin', ['mailData' => $data], function ($message) use ($data) {
                    $message->subject('Booking From App');
                    $message->from($data['email_from'], $data['email_name']);
                    $message->to($data['client_email']);
                });
            }

            return response()->json(['success' => true, 'data' => 'Booking saved successfully.'], 201);
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function maitredeUpdateBooking(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'no_of_seats' => ['required', 'numeric', 'max:255'],
                'timing' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'mobile' => ['required', 'min:10'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'dob' => ['string', 'nullable'],
                'comment' => ['string', 'nullable'],
                'restaurant_id' => ['required', 'numeric', 'max:255'],
                'booking_id' => ['required', 'numeric', 'max:255'],
                'user_id' => ['required', 'numeric', 'max:255'],
            ]);

            $user = User::find($request->user_id);
            $booking = Booking::where('id', $request->booking_id)
                ->where('restaurant_id', $request->restaurant_id)
                ->with('user')
                ->first();

            if ($booking) {
                $booking->no_of_seats = $request->no_of_seats;
                $booking->booking_name = $request->first_name . ' ' . $request->last_name;
                $booking->booking_firstname = $request->first_name;
                $booking->booking_lastname = $request->last_name;
                $booking->booking_datetime = date('Y-m-d H:i:s', strtotime($request->timing));
                $booking->comments = $request->comment;
                $booking->restaurant_id = $request->restaurant_id;

                $user = User::where('email', $request->email)->first();
                if (!$user) {
                    $user = new User();
                    $user->password = bcrypt('ozeatspass@123');
                }

                $user->phone = $request->mobile;
                $user->email = $request->email;
                //$user->first_name = $request->first_name;
                //$user->last_name = $request->last_name;
                //$user->name = $request->first_name." ".$request->last_name;
                $user->dob = $request->dob;
                $user->save();
                if ($user) {
                    $user->assignRole('Customer');
                }
                $booking->user_id = $user->id;

                /*$booking->user->phone = $request->mobile;
                $booking->user->email = $request->email;
                $booking->user->first_name = $request->first_name;
                $booking->user->last_name = $request->last_name;
                $booking->user->dob = $request->dob;
                $booking->user->name = $request->first_name." ".$request->last_name;
                $booking->user->save();*/

                $shif_settings = ShiftInformation::where('restaurant_id', $request->restaurant_id)->first();
                $restaurant = Restaurant::find($request->restaurant_id);
                $booking_timing = strtotime(date('h:i A', strtotime($request->timing)));

                $shift_duration = '';
                $shift_start_time = '';
                $shift_end_time = '';
                if ($booking_timing >= strtotime($shif_settings->breakfastStartTime) && $booking_timing <= strtotime($shif_settings->breakfastEndTime)) {
                    $booking->booking_shift = 'Breakfast';
                    $shift_start_time = $shif_settings->breakfastStartTime;
                    $shift_end_time = $shif_settings->breakfastEndTime;
                    $shift_duration = $shif_settings->breakfastDuration;
                } elseif ($booking_timing >= strtotime($shif_settings->lunchStartTime) && $booking_timing <= strtotime($shif_settings->lunchEndTime)) {
                    $booking->booking_shift = 'Lunch';
                    $shift_start_time = $shif_settings->lunchStartTime;
                    $shift_end_time = $shif_settings->lunchEndTime;
                    $shift_duration = $shif_settings->lunchDuration;
                } elseif ($booking_timing >= strtotime($shif_settings->dinnerStartTime) && $booking_timing <= strtotime($shif_settings->dinnerEndTime)) {
                    $booking->booking_shift = 'Dinner';
                    $shift_start_time = $shif_settings->dinnerStartTime;
                    $shift_end_time = $shif_settings->dinnerEndTime;
                    $shift_duration = $shif_settings->dinnerDuration;
                } else {
                    return response()->json(['success' => false, 'data' => 'Booking service not available at this time.'], 201);
                }

                $booking_timing = strtotime(date('H:i', strtotime($request->timing)));

                $booking_slot_start_time = '';
                $booking_slot_end = '';
                while (strtotime($shift_start_time) <= strtotime($shift_end_time)) {
                    $start = date('H:i', strtotime($shift_start_time));
                    $end = date('H:i', strtotime('+' . $shift_duration . ' minutes', strtotime($shift_start_time)));
                    $shift_start_time = date('H:i', strtotime('+' . $shift_duration . ' minutes', strtotime($shift_start_time)));
                    if (strtotime($shift_start_time) <= strtotime($shift_end_time)) {
                        if (strtotime($start) <= $booking_timing && strtotime($end) >= $booking_timing) {
                            $booking_slot_start_time = $start;
                            $booking_slot_end = $end;
                        }
                    }
                }

                $booking_slot_start_time = date('Y/m/d', strtotime($request->timing)) . ' ' . $booking_slot_start_time;
                $booking_slot_end = date('Y/m/d', strtotime($request->timing)) . ' ' . $booking_slot_end;

                $pre_bookings_counts = Booking::whereNotIn('id', [$request->id])->where('booking_status', 'open')->whereBetween('booking_datetime', [date('Y/m/d H:i', strtotime($booking_slot_start_time)), date('Y/m/d H:i', strtotime($booking_slot_end))])->get()->sum('no_of_seats');

                $next_total_seats = $request->no_of_seats + $pre_bookings_counts;
                if ($shif_settings->maxNoOfCover < $next_total_seats) {
                    return response()->json(['success' => false, 'data' => 'Booking Failed, ' . ($shif_settings->maxNoOfCover - $pre_bookings_counts) . ' seats are available int this time slot'], 201);
                }

                $booking->save();

                $data['name'] = $request->first_name;
                $data['booking_date'] = date('d M, Y', strtotime($request->timing));
                $data['booking_time'] = date('h:i A', strtotime($request->timing));
                $data['email_name'] = $shif_settings->teamName;
                $data['email_from'] = $shif_settings->emailFrom;
                $data['customer_email'] = $request->email;
                $data['restaurant_name'] = $restaurant->name;
                $data['restaurant_add'] = $restaurant->address;
                $data['admin_name'] = $shif_settings->teamName;
                $data['booking_id'] = $booking->unique_booking_id;
                $data['no_of_pax'] = $request->no_of_seats;

                Mail::send('emails.bookingUpdateClient', ['mailData' => $data], function ($message) use ($data) {
                    $message->subject('Booking Request Accepted');
                    $message->from($data['email_from'], $data['email_name']);
                    $message->to($data['customer_email']);
                });

                /*if(($shif_settings->email_options == 2 || $shif_settings->email_options == 3) && !empty($shif_settings->teamName) && !empty($shif_settings->emailFrom)){


                    $data['customer_name'] = $request->first_name." ".$request->last_name;
                    $data['admin_name'] = $shif_settings->teamName;
                    $data['customer_mobile'] = $request->mobile;
                    $data['client_name'] =$user->name;
                    $data['client_email'] = $shif_settings->emailFrom;;

                    Mail::send('emails.bookingRecieveAdmin', ['mailData' => $data], function ($message) use ($data) {
                        $message->subject("Booking From App");
                        $message->from($data['email_from'], $data['email_name']);
                        $message->to($data['client_email']);
                    });
                }*/

                return response()->json(['success' => false, 'data' => 'Booking update successfully.'], 201);
            }
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function maitredeCancelBooking($id): JsonResponse
    {
        try {
            $booking = Booking::find($id);
            if ($booking) {
                if ($booking->booking_status == 'completed') {
                    return response()->json(['success' => true, 'data' => 'Booking already completed.'], 201);
                }

                $booking->booking_status = 'cancelled';
                $booking->save();

                return response()->json(['success' => true, 'data' => 'Booking cancelled successfully.'], 201);
            } else {
                return response()->json(['success' => false, 'data' => "Opp's no booking found."], 201);
            }
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function maitredeDisableBooking($id): JsonResponse
    {
        try {
            $booking = Booking::find($id);
            if ($booking) {
                if (Booking::where('booking_status', 'open')) {
                    $booking->booking_status = 'completed';
                } else {
                    $booking->booking_status = 'open';
                }
                $booking->save();

                return response()->json(['success' => true, 'data' => 'Booking disabled successfully.'], 201);
            } else {
                return response()->json(['success' => false, 'data' => "Opp's no booking found."], 201);
            }
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function maitredeGetBooking($id): JsonResponse
    {
        try {
            $booking = Booking::where('id', $id)->with(['user', 'resTables'])->first();
            if ($booking) {
                return response()->json(['success' => true, 'data' => $booking], 201);
            } else {
                return response()->json(['success' => false, 'data' => "Opp's no booking found."], 201);
            }
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function maitredeChangeBookingStatus($id): JsonResponse
    {
        try {
            $booking = Booking::find($id);
            if ($booking) {
                if (Booking::where('booking_status', 'open')) {
                    $booking->booking_status = 'completed';
                } else {
                    $booking->booking_status = 'open';
                }
                $booking->save();

                return response()->json(['success' => true, 'data' => 'Booking completed successfully.'], 201);
            } else {
                return response()->json(['success' => false, 'data' => "Opp's no booking found."], 201);
            }
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function maitredeGetRestaurantTables($id): JsonResponse
    {
        try {
            $restaurant = Restaurant::where('id', $id)->first();
            if ($restaurant) {
                $table_info = TableInformation::where('restaurant_id', $id)->get();
                if ($table_info) {
                    return response()->json(['success' => true, 'data' => $table_info], 201);
                } else {
                    return response()->json(['success' => false, 'data' => "Opp's no tables Found."], 201);
                }
            } else {
                return response()->json(['success' => false, 'data' => "Opp's no restaurant found."], 201);
            }
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function maitredeGetRestaurantBooking($id): JsonResponse
    {
        try {
            $restaurant = Restaurant::where('id', $id)->first();
            if ($restaurant) {
                $bookings = Booking::where('restaurant_id', $id)->orderBy('id', 'DESC')->with(['user', 'resTables'])->get();
                if ($bookings) {
                    return response()->json(['success' => true, 'data' => $bookings], 201);
                } else {
                    return response()->json(['success' => false, 'data' => "Opp's no booking Found."], 201);
                }
            } else {
                return response()->json(['success' => false, 'data' => "Opp's no restaurant found."], 201);
            }
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function bookingFromAnotherSites($slug, Request $request): JsonResponse
    {
        try {
            if (empty($request->no_of_seats)) {
                return response()->json(['success' => false, 'message' => "Opp's no of seats is invalidedddddddd.", 'data' => []], 201);
            }

            if (empty($request->booking_date)) {
                return response()->json(['success' => false, 'message' => "Opp's booking date is invalid.", 'data' => []], 201);
            }

            if (empty($request->booking_time)) {
                return response()->json(['success' => false, 'message' => "Opp's booking time is invalid.", 'data' => []], 201);
            }

            if (empty($request->email_address)) {
                return response()->json(['success' => false, 'message' => "Opp's email address is invalid.", 'data' => []], 201);
            }

            if (empty($request->mobile_number)) {
                return response()->json(['success' => false, 'message' => "Opp's mobile number is invalid.", 'data' => []], 201);
            }

            if (empty($request->first_name)) {
                return response()->json(['success' => false, 'message' => "Opp's first name is invalid.", 'data' => []], 201);
            }

            if (empty($request->last_name)) {
                return response()->json(['success' => false, 'message' => "Opp's last name is invalid.", 'data' => []], 201);
            }

            $restaurant_id = $slug;
            $shif_settings = ShiftInformation::where('restaurant_id', $restaurant_id)->first();

            $restaurant = Restaurant::find($restaurant_id);

            if ($request->no_of_seats >= $shif_settings->maxNoOfCover) {
                return response()->json(['success' => false, 'message' => "$restaurant->name does not allow web booking for the number of guests you are trying to book. Please call $shif_settings->teamName for making the reservation", 'data' => []], 201);
            }

            $user = User::where('email', $request->email_address)->first();
            if (!$user) {
                $user = new User();
                $user->password = bcrypt('ozeatspass@123');
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->name = $request->first_name . ' ' . $request->last_name;
                $user->phone = $request->mobile_number;
            }

            $user->email = $request->email_address;
            $user->dob = $request->dob;

            $user->save();
            if ($user) {
                $restaurantuser = User::findOrFail($user->id);
                // Sync restaurants for the user
                $restaurantuser->restaurants()->sync([$restaurant_id]);
                // Assign role to the user (if not already assigned)
                $user->assignRole('Customer');
                // Retrieve the role_id for 'Customer'
                $role_id = DB::table('roles')->where('name', 'Customer')->value('id');
                // Create record in restaurant_customer_model
                RestaurantCustomerModel::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'restaurant_id' => $restaurant_id
                    ],
                    [
                        'role_id' => $role_id
                    ]
                );
            }
            if ($user) {
                $user->assignRole('Customer');
                if (isset($restaurant->id)) {
                    try {
                        $restaurantuser = User::findOrFail($user->id);

                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $restaurant->id)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $restaurant->id,
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
            }
            $booking = new Booking();

            $lastBooking = Booking::orderBy('id', 'desc')->first();

            if ($lastBooking) {
                $lastBookingId = $lastBooking->id;
                $newId = $lastBookingId + 1;
                $uniqueId = Hashids::connection('alternative')->encode($newId);
            } else {
                //first order
                $newId = 1;
            }
            $uniqueId = Hashids::connection('alternative')->encode($newId);
            $unique_booking_id = '989' . strtoupper($uniqueId);
            $booking->unique_booking_id = $unique_booking_id;

            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;

            $booking->no_of_seats = $request->no_of_seats;
            $booking->booking_name = $request->first_name . ' ' . $request->last_name;
            $booking->booking_firstname = $request->first_name;
            $booking->booking_lastname = $request->last_name;
            $booking->booking_mobile = $request->mobile_number;
            $booking->booking_email = $request->email_address;
            $booking->booking_datetime = date('Y-m-d H:i:s', strtotime($booking_date_time));
            $booking->comments = $request->comment;
            $booking->restaurant_id = $restaurant_id;
            $booking->user_id = $user->id;

            $booking_timing = strtotime(date('h:i A', strtotime($booking_date_time)));

            $shift_duration = '';
            $shift_start_time = '';
            $shift_end_time = '';
            if ($booking_timing >= strtotime($shif_settings->breakfastStartTime) && $booking_timing <= strtotime($shif_settings->breakfastEndTime)) {
                $booking->booking_shift = 'Breakfast';
                $shift_start_time = $shif_settings->breakfastStartTime;
                $shift_end_time = $shif_settings->breakfastEndTime;
                $shift_duration = $shif_settings->breakfastDuration;
            } elseif ($booking_timing >= strtotime($shif_settings->lunchStartTime) && $booking_timing <= strtotime($shif_settings->lunchEndTime)) {
                $booking->booking_shift = 'Lunch';
                $shift_start_time = $shif_settings->lunchStartTime;
                $shift_end_time = $shif_settings->lunchEndTime;
                $shift_duration = $shif_settings->lunchDuration;
            } elseif ($booking_timing >= strtotime($shif_settings->dinnerStartTime) && $booking_timing <= strtotime($shif_settings->dinnerEndTime)) {
                $booking->booking_shift = 'Dinner';
                $shift_start_time = $shif_settings->dinnerStartTime;
                $shift_end_time = $shif_settings->dinnerEndTime;
                $shift_duration = $shif_settings->dinnerDuration;
            } else {
                return response()->json(['success' => false, 'message' => 'Booking service not available at this time.', 'data' => []], 201);
            }

            $pre_bookings_counts = Booking::where('booking_status', 'open')->whereBetween('booking_datetime', [date('Y/m/d H:i', strtotime($booking_date_time)), date('Y/m/d H:i', strtotime('+' . ($shift_duration - 1) . ' minutes', strtotime($booking_date_time)))])->get()->sum('no_of_seats');

            $next_total_seats = $request->no_of_seats + $pre_bookings_counts;

            $max_cover = 0;
            if ($booking->booking_shift == 'Breakfast') {
                $max_cover = $shif_settings->max_cover_breakfast;
            }

            if ($booking->booking_shift == 'Lunch') {
                $max_cover = $shif_settings->max_cover_lunch;
            }

            if ($booking->booking_shift == 'Dinner') {
                $max_cover = $shif_settings->max_cover_dinner;
            }

            if ($max_cover < $next_total_seats) {
                return response()->json(['success' => false, 'message' => 'Booking Failed,  Max no of cover exceed for this duration', 'data' => []], 201);
            }

            /* $booking_timing = strtotime(date('H:i', strtotime($request->timing)));


             $booking_slot_start_time = '';
             $booking_slot_end = '';
             while(strtotime($shift_start_time) <= strtotime($shift_end_time)){
                 $start = date('H:i',strtotime($shift_start_time));
                 $end = date('H:i',strtotime('+'.$shift_duration.' minutes',strtotime($shift_start_time)));
                 $shift_start_time = date('H:i',strtotime('+'.$shift_duration.' minutes',strtotime($shift_start_time)));
                 if(strtotime($shift_start_time) <= strtotime($shift_end_time)){
                     if(strtotime($start) <= $booking_timing && strtotime($end) >= $booking_timing){
                         $booking_slot_start_time = $start;
                         $booking_slot_end = $end;
                     }
                 }
             }

             $booking_slot_start_time  =date('Y/m/d', strtotime($request->timing))." ".$booking_slot_start_time;
             $booking_slot_end  =date('Y/m/d', strtotime($request->timing))." ".$booking_slot_end;

             $pre_bookings_counts = Booking::where('booking_status', 'open')->whereBetween('booking_datetime', [date('Y/m/d H:i', strtotime($booking_slot_start_time)), date('Y/m/d H:i', strtotime($booking_slot_end))])->get()->sum('no_of_seats');

             $next_total_seats = $request->no_of_seats+$pre_bookings_counts;
             if($shif_settings->maxNoOfCover < $next_total_seats){
                 return response()->json(['success' => false, 'message' => "Booking Failed, ".($shif_settings->maxNoOfCover-$pre_bookings_counts)." seats are available int this time slot", 'data' => array()], 201);
             }*/

            $booking->save();
            Log::info('Stripe Payment Request: ', $request->all());
            $restaurant_settings = RestaurantSettings::where('restaurant_id', $restaurant_id)->first();
            $online_payment = $restaurant_settings->online_payment;
            $restaurant_name = Restaurant::where('id', $restaurant_id)->first();
            $amount = $request->stripeToken ? $request->no_of_seats * $restaurant_settings->deposit_amount_per_cover : 0;


            $wallet = Wallet::firstOrCreate(
                [
                    'holder_type' => User::class,
                    'holder_id' => $user->id,
                    'slug' => $restaurant->slug, // Assuming you have a slug for the restaurant
                ],
                [
                    'name' => 'default Wallet',
                    'balance' => 0,
                    'decimal_places' => 2,
                ]
            );
            // dd($restaurant_name->stripe_secret_key);
            if ($request->stripeToken != null) {

                Log::info("beposz accounts stripeToken :: " . $request->stripeToken);
                Log::info("beposz accounts stripe_secret_key :: " . $restaurant_name->stripe_secret_key);
                //   echo "<pre>"; print_r($responsesss); exit;
                // dd($amount);
                // \Stripe\Stripe::setApiKey(config('setting.stripePublicKey'));
                \Stripe\Stripe::setApiKey($restaurant_name->stripe_secret_key);
                if ($request->app) {
                    $paymentIntent = \Stripe\PaymentIntent::create([
                        'amount' => $amount * 100,
                        'payment_method' => $request->stripeToken,
                        'payment_method_types' => ['card'],
                        'currency' => 'AUD',
                        // 'return_url' => route('stripeRedirectCapture'),
                    ]);
                    Log::info("beposz accounts paymentIntent :: " . $paymentIntent);
                } else {
                    $charge = \Stripe\Charge::create([
                        'amount' => $amount * 100,
                        'currency' => 'AUD',
                        'source' => $request->stripeToken,
                        'description' => 'Sommelier Payment',
                    ]);

                    Log::info("beposz accounts res :: " . $charge);
                }
                $wallet->balance += $amount * 100;
                $wallet->save();
                $transaction = new Transaction();
                $transaction->payable_type = User::class;
                $transaction->payable_id = $user->id;
                $transaction->wallet_id = $wallet->id;
                $transaction->type = 'deposit';
                $transaction->amount = $amount * 100;
                $transaction->confirmed = 1;
                $transaction->meta = [
                    'description' => 'deposit for booking'
                ];
                $transaction->uuid = (string) Str::uuid();
                $transaction->save();

                // $output = [
                //     'clientSecret' => $paymentIntent->client_secret,
                // ];
                // dd($output);
                // dd($charge);
                // $charge = \Stripe\Charge::create([
                //     'amount' => $amount*100, // Amount in cents
                //     'currency' => 'usd',
                //     'source' => $request->stripeToken, // Token from Stripe.js
                //     'description' => 'Laravel Payment',
                // ]);

            }
            $data['name'] = $request->first_name;
            $data['booking_date'] = date('d M, Y', strtotime($booking_date_time));
            $data['booking_time'] = date('h:i A', strtotime($booking_date_time));
            $data['email_name'] = $shif_settings->teamName;
            $data['email_from'] = $shif_settings->emailFrom;
            $data['customer_email'] = $request->email_address;
            $data['restaurant_name'] = $restaurant->name;
            $data['restaurant_add'] = $restaurant->address;
            $data['admin_name'] = $shif_settings->teamName;
            $data['booking_id'] = $unique_booking_id;
            $data['no_of_pax'] = $request->no_of_seats;
            $data['comment'] = $request->comment;
            Mail::send('emails.bookingConfirmClient', ['mailData' => $data], function ($message) use ($data) {
                $message->subject('Booking Request Accepted');
                $message->from($data['email_from'], $data['email_name']);
                $message->to($data['customer_email']);
            });

            if (($shif_settings->email_options == 1 || $shif_settings->email_options == 3) && !empty($shif_settings->teamName) && !empty($shif_settings->emailFrom)) {
                $data['customer_name'] = $request->first_name . ' ' . $request->last_name;
                $data['admin_name'] = $shif_settings->teamName;
                $data['customer_mobile'] = $request->mobile_number;
                $data['client_name'] = $user->name;
                $data['client_email'] = $shif_settings->emailFrom;
                $data['no_of_pax'] = $request->no_of_seats;
                $data['comment'] = $request->comment;
                Mail::send('emails.bookingRecieveAdmin', ['mailData' => $data], function ($message) use ($data) {
                    $message->subject('Booking From Web');
                    $message->from($data['email_from'], $data['email_name']);
                    $message->to($data['client_email']);
                });

            }

            $bepoz_data[] = [
                'date_time' => date('d-M-Y h:i A', strtotime($booking_date_time)),
                'booking_comment' => $request->comment,
                'total_guests' => $request->no_of_seats,
                'unique_booking_id' => $unique_booking_id,
            ];

            $this->bepozIntegration($request, $user->id, $restaurant_id, $bepoz_data, $amount);

            return response()->json(['success' => true, 'message' => 'Booking Saved', 'data' => []], 201);
        } catch (\Throwable $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }

    public function bepozIntegration($request, $user_id, $restaurant_id, $bepoz_data, $amount)
    {
        /* Bepoz integration */

        $restaurant_settings = RestaurantSettings::where('restaurant_id', $restaurant_id)->first();
        if (!empty($restaurant_settings->url) && !empty($restaurant_settings->secret)) {
            Log::info("check bepozIntegration :: here");
            $user = User::where('id', $user_id)->first();

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $restaurant_settings->url . '/api/accounts/get?number=' . str_replace('+', '', $user->phone),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'secret: ' . $restaurant_settings->secret,
                ],
            ]);

            $response1 = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response1, true);

            Log::info("beposz accounts res account get :: " . json_encode($res));
            // dd($res);
            if (!empty($res['message']) && $res['message'] == 'Success' && !empty($res['data']['AccountID'])) {

                $this->bepozTransaction($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
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

                $post_data = [
                    'AccNumber' => str_replace('+61', '0', $user->phone),
                    'CardNumber' => str_replace('+61', '0', $user->phone),
                    'FirstName' => $first_name,
                    'LastName' => $last_name,
                    'Mobile' => str_replace('+61', '0', $user->phone),
                    'Email1st' => $user->email,
                    'GroupID' => $restaurant_settings->account_group,
                    'Title' => '',
                    'Status' => 0,
                ];
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => $restaurant_settings->url . '/api/accounts/createUpdate',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 10,
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
                curl_close($curl);
                $create_custom_response = json_decode($create_custom_response, true);
                Log::info("beposz accounts res account update :: " . json_encode($create_custom_response));
                if (!empty($create_custom_response['message']) && $create_custom_response['message'] == 'Success') {

                    $this->bepozTransaction($request, $user, $restaurant_settings, $create_custom_response, $bepoz_data, $amount);
                }
            }
        }

        /** Bepoz integration end */
    }

    public function bepozTransaction($request, $user, $restaurant_settings, $res, $bepoz_data, $amount)
    {
        $post_data1 = [];
        if (empty($bepoz_data)) {
            return;
        }
        $booking_date_time = $request->booking_date . ' ' . $request->booking_time;
        if ($restaurant_settings->booking_option === 'Table') {
            $online_payment = $restaurant_settings->online_payment;
            foreach ($bepoz_data as $dt) {
                $post_data1[] = [
                    //'DateTimeTrans' => '',
                    'Training' => false,
                    'OrderID' => 0,
                    'OrderType' => 528,
                    'TillID' => $restaurant_settings->till_id,
                    'OperatorID' => $restaurant_settings->operator_id,
                    'OrderComment' => $dt['booking_comment'],
                    'ProductID' => $restaurant_settings->booking_plu, //2744
                    'Size' => 1,
                    'QtySold' => 1,
                    'Gross' => 0,
                    'Nett' => 0,
                    "PaymentName" => $online_payment,
                    "PaymentAmount" => $amount,
                    'AccountNumber' => str_replace('+61', '0', $user->phone),
                    'TableGroup' => $restaurant_settings->table_group, //4
                    'TableNumber' => $dt['unique_booking_id'],
                    'TableName' => $user->first_name . ' ' . date('d M y H:i', strtotime($dt['date_time'])),
                    'TableGuests' => $dt['total_guests'],
                ];
            }

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $restaurant_settings->url . '/api/transactions/create',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
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
            curl_close($curl);
        } else if ($restaurant_settings->booking_option === 'Account') {

            $online_payment = $restaurant_settings->online_payment;

            $this->BookikingdateField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
            $this->BookikingpaxField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
            $this->BookikingcommentField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
            $this->BookikingnameField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
            $this->BookikingnumberField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
            $this->TransactionCreate($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
        }
    }
    public function BookikingdateField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount)
    {
        try {
            // Initialize as a single associative array
            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;
            $post_data1 = [
                'FieldType' => 1,
                'FieldIdx' => $restaurant_settings->booking_custom_date_fieldidx,
                'Data' => date('Y-m-d H:i:s', strtotime($booking_date_time)),
            ];

            $curl = curl_init();
            $accountId = $res['data']['accountId'];
            $url = $restaurant_settings->url . '/api/accounts/customfield/set?accountId=' . $accountId;

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode($post_data1),
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'secret: ' . $restaurant_settings->secret,
                ],
            ]);

            // Log the JSON payload for debugging
            Log::info('BookikingdateField Request Payload: ' . json_encode($post_data1));

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                Log::error("BookikingdateField Curl error : " . curl_error($curl));
                // Handle the error and proceed to the next function
            } else {
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($http_code == 200) {
                    Log::info("BookikingdateField Request successful: " . $response);
                } else {
                    Log::error("BookikingdateField Request failed with status code " . $http_code . ": " . $response);
                    // Handle the error and proceed to the next function
                }
            }

            curl_close($curl);
        } catch (Exception $e) {
            Log::error("BookikingdateField Exception occurred: " . $e->getMessage());
            // Handle the exception and proceed to the next function
        }

        // Continue to the next function
        // $this->nextFunction($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
    }
    public function BookikingpaxField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount)
    {
        try {
            $online_payment = $restaurant_settings->online_payment;
            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;
            $pax = $request->no_of_seats;
            $post_data1 = [
                'FieldType' => 3,
                'FieldIdx' => $restaurant_settings->booking_pax_fieldidx,
                'Data' => $pax,
            ];
            Log::info('BookikingpaxField Request Payload: ' . json_encode($post_data1));
            $curl = curl_init();
            $accountId = $res['data']['accountId']; // Assuming $res has an account_id property
            $url = $restaurant_settings->url . '/api/accounts/customfield/set?accountId=' . $accountId;
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode($post_data1),
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'secret: ' . $restaurant_settings->secret,
                ],
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                Log::error("BookikingpaxField Curl error: " . curl_error($curl));
                // Handle the error and proceed to the next function
            } else {
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($http_code == 200) {
                    Log::info("BookikingpaxField Request successful: " . json_encode($response));
                } else {
                    Log::error("BookikingpaxField Request failed with status code " . $http_code . ": " . json_encode($response));
                    // Handle the error and proceed to the next function
                }
            }

            curl_close($curl);
        } catch (Exception $e) {
            Log::error("BookikingpaxField Exception occurred: " . $e->getMessage());
            // Handle the exception and proceed to the next function
        }

        // Continue to the next function
        // $this->nextFunction($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
    }
    public function BookikingcommentField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount)
    {
        try {
            $online_payment = $restaurant_settings->online_payment;
            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;
            $post_data1 = [
                'FieldType' => 3,
                'FieldIdx' => $restaurant_settings->booking_comment_fieldidx,
                'Data' => $request->comment,
            ];
            Log::info("BookikingcommentField payload: " . json_encode($post_data1));
            $curl = curl_init();
            $accountId = $res['data']['accountId']; // Assuming $res has an account_id property
            $url = $restaurant_settings->url . '/api/accounts/customfield/set?accountId=' . $accountId;
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode($post_data1),
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'secret: ' . $restaurant_settings->secret,

                ],
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                Log::error("BookikingcommentField Curl error: " . curl_error($curl));
                // Handle the error and proceed to the next function
            } else {
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($http_code == 200) {
                    Log::info("BookikingcommentField Request successful: " . json_encode($response));
                } else {
                    Log::error("BookikingcommentField Request failed with status code " . $http_code . ": " . json_encode($response));
                    // Handle the error and proceed to the next function
                }
            }

            curl_close($curl);
        } catch (Exception $e) {
            Log::error("BookikingcommentField Exception occurred: " . $e->getMessage());
            // Handle the exception and proceed to the next function
        }

        // Continue to the next function
        // $this->nextFunction($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
    }
    public function BookikingnameField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount)
    {
        try {
            $online_payment = $restaurant_settings->online_payment;
            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;
            $post_data1 = [
                'FieldType' => 3,
                'FieldIdx' => $restaurant_settings->booking_name_fieldidx,
                'Data' => $user->first_name . ' ' . date('Y-m-d H:i:s', strtotime($booking_date_time)),
            ];
            Log::info('BookikingnameField Request Payload: ' . json_encode($post_data1));
            $curl = curl_init();
            $accountId = $res['data']['accountId']; // Assuming $res has an account_id property
            $url = $restaurant_settings->url . '/api/accounts/customfield/set?accountId=' . $accountId;
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode($post_data1),
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'secret: ' . $restaurant_settings->secret,
                ],
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                Log::error("BookikingnameField Curl error: " . curl_error($curl));
                // Handle the error and proceed to the next function
            } else {
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($http_code == 200) {
                    Log::info("BookikingnameField Request successful: " . json_encode($response));
                } else {
                    Log::error("BookikingnameField Request failed with status code " . $http_code . ": " . json_encode($response));
                    // Handle the error and proceed to the next function
                }
            }

            curl_close($curl);
        } catch (Exception $e) {
            Log::error("BookikingnameField Exception occurred: " . $e->getMessage());
            // Handle the exception and proceed to the next function
        }

        // Continue to the next function
        // $this->nextFunction($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
    }
    public function BookikingnumberField($request, $user, $restaurant_settings, $res, $bepoz_data, $amount)
    {
        try {
            $online_payment = $restaurant_settings->online_payment;
            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;
            $last_booking = end($bepoz_data);
            $post_data1 = [
                'FieldType' => 3,
                'FieldIdx' => $restaurant_settings->booking_number_fieldidx,
                'Data' => $last_booking['unique_booking_id'],
            ];
            Log::info('BookikingnumberField Request Payload: ' . json_encode($post_data1));
            $curl = curl_init();
            $accountId = $res['data']['accountId']; // Assuming $res has an account_id property
            $url = $restaurant_settings->url . '/api/accounts/customfield/set?accountId=' . $accountId;
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode($post_data1),
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'secret: ' . $restaurant_settings->secret,
                ],
            ]);

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                Log::error("BookikingnumberField Curl error: " . curl_error($curl));
                // Handle the error and proceed to the next function
            } else {
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($http_code == 200) {
                    Log::info("BookikingnumberField Request successful: " . json_encode($response));
                } else {
                    Log::error("BookikingnumberField Request failed with status code " . $http_code . ": " . json_encode($response));
                    // Handle the error and proceed to the next function
                }
            }

            curl_close($curl);
        } catch (Exception $e) {
            Log::error("BookikingnumberField Exception occurred: " . $e->getMessage());
            // Handle the exception and proceed to the next function
        }

        // Continue to the next function
        // $this->nextFunction($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
    }
    public function TransactionCreate($request, $user, $restaurant_settings, $res, $bepoz_data, $amount)
    {
        try {
            $online_payment = $restaurant_settings->online_payment;
            $booking_date_time = $request->booking_date . ' ' . $request->booking_time;
            $last_booking = end($bepoz_data);

            // Prepare the data structure as an array of objects
            $post_data = [
                [
                    'TillID' => $restaurant_settings->till_id,
                    'OperatorID' => $restaurant_settings->operator_id,
                    'ProductID' => $restaurant_settings->booking_plu,
                    'Size' => 1,
                    "Gross" => 0,
                    "Nett" => 0,
                    'QtySold' => $request->no_of_seats,
                    'DateTimeTrans' => date('Y-m-d H:i:s'),
                    'PaymentName' => $restaurant_settings->online_payment,
                    'PaymentAmount' => $amount,
                    'AccountID' => $res['data']['accountId']
                ]
            ];

            Log::info('TransactionCreate Request Payload: ' . json_encode($post_data));

            $curl = curl_init();
            $accountId = $res['data']['accountId']; // Assuming $res has an account_id property
            $url = $restaurant_settings->url . '/api/transactions/create';
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_POSTFIELDS => json_encode($post_data),
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'secret: ' . $restaurant_settings->secret,
                ],
            ]);

            $response = curl_exec($curl);

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
        } catch (Exception $e) {
            Log::error("TransactionCreate Exception occurred: " . $e->getMessage());
            // Handle the exception and proceed to the next function
        }

        // Continue to the next function
        // $this->nextFunction($request, $user, $restaurant_settings, $res, $bepoz_data, $amount);
    }

    public function cancelBookingOnBepoz($booking)
    {
        $table_number = $booking->unique_booking_id;
        $restaurant_id = $booking->restaurant_id;
        $restaurant_settings = RestaurantSettings::where('restaurant_id', $restaurant_id)->first();
        if (!empty($restaurant_settings->url) && !empty($restaurant_settings->secret)) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $restaurant_settings->url . '/api/tables/get?tableNumber=' . $table_number . '&tableGroup=' . $restaurant_settings->table_group,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'secret: ' . $restaurant_settings->secret,
                ],
            ]);

            $response1 = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response1, true);
            if (!empty($res) && ($res['responseCode'] == 0 && $res['message'] == 'Success')) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $restaurant_settings->url . '/api/tables/tryclose?id=' . $res['data']['TableID'],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => [
                        'secret: ' . $restaurant_settings->secret,
                    ],
                ]);
                $try_close_res = curl_exec($curl);
                curl_close($curl);
                // $try_close_res = json_decode($try_close_res, true);
            }
        }
    }

    public function getShiftTiming($id, Request $request): JsonResponse
    {
        $shif_settings = ShiftInformation::where('restaurant_id', $id)->first();
        $restaurant_settings = RestaurantSettings::where('restaurant_id', $id)->first();
        $holidays = (!empty($restaurant_settings->holidays)) ? unserialize($restaurant_settings->holidays) : null;
        if (!empty($holidays)) {
            foreach ($holidays as $holiday) {
                if (strtotime($holiday['date']) == strtotime($request->input('booking_date')) && (isset($holiday['enable_holiday_deposit']) && $holiday['enable_holiday_deposit'] != 1)) {
                    $html = view('restaurantowner.bookingHolidayMessage', [
                        'booking_date' => $request->input('booking_date'),
                    ])->render();

                    return response()->json(['success' => true, 'html' => $html], 200);
                }
            }
        }

        if ($shif_settings) {
            $booking_date = $request->input('booking_date');
            $booking_time = [];
            $breakfast_start = $shif_settings->breakfastStartTime;
            $breakfast_end = $shif_settings->breakfastEndTime;
            $breakfast_duration = $shif_settings->breakfastDuration;

            $breakfast_max_cover = $shif_settings->max_cover_breakfast;
            $lunch_max_cover = $shif_settings->max_cover_lunch;
            $dinner_max_cover = $shif_settings->max_cover_dinner;
            $breakfast_warning_covers = $shif_settings->breakfast_warning_covers;
            $lunch_warning_covers = $shif_settings->lunch_warning_covers;
            $dinner_warning_covers = $shif_settings->dinner_warning_covers;

            $bk_info = [];
            /* if(strtotime($booking_date) == strtotime(date('Y-m-d')) && strtotime(date('h:i A', strtotime($breakfast_start))) >= strtotime(date('h:i A'))){
                 $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', strtotime($breakfast_start)))->get()->sum('no_of_seats');

                 if($pre_bookings_counts){
                     $booked_percentage = $pre_bookings_counts/$breakfast_max_cover*100;
                 }

                 if($pre_bookings_counts >= $breakfast_max_cover)
                     $bk_info['class'] ="booking_time_not_available";
                 else if($pre_bookings_counts && $booked_percentage >= $breakfast_warning_covers)
                     $bk_info['class'] ="booking_time_warning";
                 else
                     $bk_info['class'] ="booking_time_available";

                     $bk_info['time'] = date('h:i A', strtotime($breakfast_start));
                     $booking_time['Breakfast'][] = $bk_info;
             }elseif(strtotime($booking_date) > strtotime(date('Y-m-d'))){
                 //$booking_time['Breakfast'][] = date('h:i A', strtotime($breakfast_start));
                 $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', strtotime($breakfast_start)))->get()->sum('no_of_seats');

                 if($pre_bookings_counts){
                     $booked_percentage = $pre_bookings_counts/$breakfast_max_cover*100;
                 }

                 if($pre_bookings_counts >= $breakfast_max_cover)
                     $bk_info['class'] ="booking_time_not_available";
                 else if($pre_bookings_counts && $booked_percentage >= $breakfast_warning_covers)
                     $bk_info['class'] ="booking_time_warning";
                 else
                     $bk_info['class'] ="booking_time_available";

                     $bk_info['time'] = date('h:i A', strtotime($breakfast_start));
                     $booking_time['Breakfast'][] = $bk_info;
             }*/

            $new_time = strtotime($breakfast_start);

            while ($new_time < strtotime($breakfast_end)) {
                $bk_info = [];

                if (strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($breakfast_end) && $new_time >= strtotime(date('h:i A'))) {
                    //$booking_time['Breakfast'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $breakfast_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $breakfast_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Breakfast'][] = $bk_info;
                } elseif (strtotime($booking_date) > strtotime(date('Y-m-d'))) {
                    //$booking_time['Breakfast'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $breakfast_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $breakfast_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Breakfast'][] = $bk_info;
                }
                $new_time = strtotime('+' . $breakfast_duration . ' minutes', $new_time);
            }

            $lunch_start = $shif_settings->lunchStartTime;
            $lunch_end = $shif_settings->lunchEndTime;
            $lunch_duration = $shif_settings->lunchDuration;

            $bk_info = [];
            /*if(strtotime($booking_date) == strtotime(date('Y-m-d')) && strtotime(date('h:i A', strtotime($lunch_start))) >= strtotime(date('h:i A'))){
                //$booking_time['Lunch'][] = date('h:i A', strtotime($lunch_start));
                $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', strtotime($lunch_start)))->get()->sum('no_of_seats');
                if($pre_bookings_counts){
                    $booked_percentage = $pre_bookings_counts/$lunch_max_cover*100;
                }

                if($pre_bookings_counts >= $lunch_max_cover)
                    $bk_info['class'] ="booking_time_not_available";
                else if($pre_bookings_counts && $booked_percentage >= $lunch_warning_covers)
                    $bk_info['class'] ="booking_time_warning";
                else
                    $bk_info['class'] ="booking_time_available";

                    $bk_info['time'] = date('h:i A', strtotime($lunch_start));
                    $booking_time['Lunch'][] = $bk_info;
            }elseif(strtotime($booking_date) > strtotime(date('Y-m-d'))){
                //$booking_time['Lunch'][] = date('h:i A', strtotime($lunch_start));
                $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', strtotime($lunch_start)))->get()->sum('no_of_seats');
                if($pre_bookings_counts){
                    $booked_percentage = $pre_bookings_counts/$lunch_max_cover*100;
                }

                if($pre_bookings_counts >= $lunch_max_cover)
                    $bk_info['class'] ="booking_time_not_available";
                else if($pre_bookings_counts && $booked_percentage >= $lunch_warning_covers)
                    $bk_info['class'] ="booking_time_warning";
                else
                    $bk_info['class'] ="booking_time_available";

                    $bk_info['time'] = date('h:i A', strtotime($lunch_start));
                    $booking_time['Lunch'][] = $bk_info;
            }*/

            $new_time = strtotime($lunch_start);
            while ($new_time < strtotime($lunch_end)) {
                $bk_info = [];

                if (strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($lunch_end) && $new_time >= strtotime(date('h:i A'))) {
                    //$booking_time['Lunch'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $lunch_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $lunch_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Lunch'][] = $bk_info;
                } elseif (strtotime($booking_date) > strtotime(date('Y-m-d'))) {
                    //$booking_time['Lunch'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $lunch_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $lunch_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Lunch'][] = $bk_info;
                }
                $new_time = strtotime('+' . $lunch_duration . ' minutes', $new_time);
            }

            $dinner_start = $shif_settings->dinnerStartTime;
            $dinner_end = $shif_settings->dinnerEndTime;
            $dinner_duration = $shif_settings->dinnerDuration;

            $bk_info = [];
            /*if(strtotime($booking_date) == strtotime(date('Y-m-d')) && strtotime(date('h:i A', strtotime($dinner_start))) >= strtotime(date('h:i A'))){
                //$booking_time['Dinner'][] = date('h:i A', strtotime($dinner_start));
                $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', strtotime($dinner_start)))->get()->sum('no_of_seats');
                if($pre_bookings_counts){
                    $booked_percentage = $pre_bookings_counts/$lunch_madinner_max_coverx_cover*100;
                }

                if($pre_bookings_counts >= $dinner_max_cover)
                    $bk_info['class'] ="booking_time_not_available";
                else if($pre_bookings_counts && $booked_percentage >= $dinner_warning_covers)
                    $bk_info['class'] ="booking_time_warning";
                else
                    $bk_info['class'] ="booking_time_available";

                    $bk_info['time'] = date('h:i A', strtotime($dinner_start));
                    $booking_time['Dinner'][] = $bk_info;
            }elseif(strtotime($booking_date) > strtotime(date('Y-m-d'))){
                //$booking_time['Dinner'][] = date('h:i A', strtotime($dinner_start));
                $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', strtotime($dinner_start)))->get()->sum('no_of_seats');
                if($pre_bookings_counts){
                    $booked_percentage = $pre_bookings_counts/$dinner_max_cover*100;
                }

                if($pre_bookings_counts >= $dinner_max_cover)
                    $bk_info['class'] ="booking_time_not_available";
                else if($pre_bookings_counts && $booked_percentage >= $dinner_warning_covers)
                    $bk_info['class'] ="booking_time_warning";
                else
                    $bk_info['class'] ="booking_time_available";

                    $bk_info['time'] = date('h:i A', strtotime($dinner_start));
                    $booking_time['Dinner'][] = $bk_info;
            }*/

            $new_time = strtotime($dinner_start);
            while ($new_time < strtotime($dinner_end)) {
                $bk_info = [];

                if (strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($dinner_end) && $new_time >= strtotime(date('h:i A'))) {
                    //$booking_time['Dinner'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $dinner_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $dinner_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Dinner'][] = $bk_info;
                } elseif (strtotime($booking_date) > strtotime(date('Y-m-d'))) {
                    //$booking_time['Dinner'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $dinner_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $dinner_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Dinner'][] = $bk_info;
                }
                $new_time = strtotime('+' . $dinner_duration . ' minutes', $new_time);
            }

            $html = view('restaurantowner.bookingTimeButtons', [
                'time_buttons' => $booking_time,
                'current_booking_time' => $request->input('time'),
            ])->render();

            return response()->json(['success' => true, 'html' => $html], 200);
        }
    }

    public function getShiftTimingForFilter($id): JsonResponse
    {
        $shif_settings = ShiftInformation::where('restaurant_id', $id)->first();

        if ($shif_settings) {
            $booking_time = [];
            $breakfast_start = $shif_settings->breakfastStartTime;
            $breakfast_end = $shif_settings->breakfastEndTime;
            $breakfast_duration = $shif_settings->breakfastDuration;

            $booking_time[] = date('h:i A', strtotime($breakfast_start));

            $new_time = strtotime($breakfast_start);
            while ($new_time < strtotime($breakfast_end)) {
                $new_time = strtotime('+' . $breakfast_duration . ' minutes', $new_time);
                if ($new_time <= strtotime($breakfast_end)) {
                    $booking_time[] = date('h:i A', $new_time);
                }
            }

            $lunch_start = $shif_settings->lunchStartTime;
            $lunch_end = $shif_settings->lunchEndTime;
            $lunch_duration = $shif_settings->lunchDuration;

            $booking_time[] = date('h:i A', strtotime($lunch_start));

            $new_time = strtotime($lunch_start);
            while ($new_time < strtotime($lunch_end)) {
                $new_time = strtotime('+' . $lunch_duration . ' minutes', $new_time);
                if ($new_time <= strtotime($lunch_end)) {
                    $booking_time[] = date('h:i A', $new_time);
                }
            }

            $dinner_start = $shif_settings->dinnerStartTime;
            $dinner_end = $shif_settings->dinnerEndTime;
            $dinner_duration = $shif_settings->dinnerDuration;

            $booking_time[] = date('h:i A', strtotime($dinner_start));

            $new_time = strtotime($dinner_start);
            while ($new_time < strtotime($dinner_end)) {
                $new_time = strtotime('+' . $dinner_duration . ' minutes', $new_time);
                if ($new_time <= strtotime($dinner_end)) {
                    $booking_time[] = date('h:i A', $new_time);
                }
            }

            $html = view('restaurantowner.bookingTimeForFilter', [
                'time_buttons' => $booking_time,
            ])->render();

            return response()->json(['success' => true, 'html' => $html], 200);
        } else {
            return response()->json(['success' => false, 'html' => ''], 200);
        }
    }

    public function getShiftTimingExt($id, $booking_date, Request $request): JsonResponse
    {
        $shif_settings = ShiftInformation::where('restaurant_id', $id)->first();
        $restaurant_settings = RestaurantSettings::where('restaurant_id', $id)->first();
        $holidays = (!empty($restaurant_settings->holidays)) ? unserialize($restaurant_settings->holidays) : null;
        if (!empty($holidays)) {
            foreach ($holidays as $holiday) {
                if (strtotime($holiday['date']) == strtotime($request->input('booking_date'))) {
                    $html = view('restaurantowner.bookingHolidayMessage', [
                        'booking_date' => $request->input('booking_date'),
                    ])->render();

                    return response()->json(['success' => true, 'html' => $html], 200);
                }
            }
        }

        if ($shif_settings) {
            $booking_time = [];
            $breakfast_start = $shif_settings->breakfastStartTime;
            $breakfast_end = $shif_settings->breakfastEndTime;
            $breakfast_duration = $shif_settings->breakfastDuration;

            $breakfast_max_cover = $shif_settings->max_cover_breakfast;
            $lunch_max_cover = $shif_settings->max_cover_lunch;
            $dinner_max_cover = $shif_settings->max_cover_dinner;
            $breakfast_warning_covers = $shif_settings->breakfast_warning_covers;
            $lunch_warning_covers = $shif_settings->lunch_warning_covers;
            $dinner_warning_covers = $shif_settings->dinner_warning_covers;

            /*if(strtotime($booking_date) == strtotime(date('Y-m-d')) && strtotime(date('h:i A', strtotime($breakfast_start))) >= strtotime(date('h:i A')))
                $booking_time['Breakfast'][] = date('h:i A', strtotime($breakfast_start));
            elseif(strtotime($booking_date) > strtotime(date('Y-m-d')))
                $booking_time['Breakfast'][] = date('h:i A', strtotime($breakfast_start));*/

            $new_time = strtotime($breakfast_start);
            while ($new_time < strtotime($breakfast_end)) {
                /*if(strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($breakfast_end) && $new_time >= strtotime(date('h:i A')))
                    $booking_time['Breakfast'][]= date('h:i A', $new_time);
                elseif(strtotime($booking_date) > strtotime(date('Y-m-d')))
                     $booking_time['Breakfast'][]= date('h:i A', $new_time);*/
                $bk_info = [];

                if (strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($breakfast_end) && $new_time >= strtotime(date('h:i A'))) {
                    //$booking_time['Breakfast'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $breakfast_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $breakfast_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Breakfast'][] = $bk_info;
                } elseif (strtotime($booking_date) > strtotime(date('Y-m-d'))) {
                    //$booking_time['Breakfast'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $breakfast_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $breakfast_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Breakfast'][] = $bk_info;
                }

                $new_time = strtotime('+' . $breakfast_duration . ' minutes', $new_time);
            }

            $lunch_start = $shif_settings->lunchStartTime;
            $lunch_end = $shif_settings->lunchEndTime;
            $lunch_duration = $shif_settings->lunchDuration;

            /*if(strtotime($booking_date) == strtotime(date('Y-m-d')) && strtotime(date('h:i A', strtotime($lunch_start))) >= strtotime(date('h:i A')))
                $booking_time['Lunch'][] = date('h:i A', strtotime($lunch_start));
            elseif(strtotime($booking_date) > strtotime(date('Y-m-d')))
                $booking_time['Lunch'][] = date('h:i A', strtotime($lunch_start));*/

            $new_time = strtotime($lunch_start);
            while ($new_time < strtotime($lunch_end)) {
                /*if(strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($lunch_end) && $new_time >= strtotime(date('h:i A')))
                    $booking_time['Lunch'][]= date('h:i A', $new_time);
                elseif(strtotime($booking_date) > strtotime(date('Y-m-d')))
                    $booking_time['Lunch'][]= date('h:i A', $new_time);*/

                $bk_info = [];

                if (strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($lunch_end) && $new_time >= strtotime(date('h:i A'))) {
                    //$booking_time['Lunch'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $lunch_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $lunch_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Lunch'][] = $bk_info;
                } elseif (strtotime($booking_date) > strtotime(date('Y-m-d'))) {
                    //$booking_time['Lunch'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $lunch_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $lunch_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Lunch'][] = $bk_info;
                }

                $new_time = strtotime('+' . $lunch_duration . ' minutes', $new_time);
            }

            $dinner_start = $shif_settings->dinnerStartTime;
            $dinner_end = $shif_settings->dinnerEndTime;
            $dinner_duration = $shif_settings->dinnerDuration;

            /*if(strtotime($booking_date) == strtotime(date('Y-m-d')) && strtotime(date('h:i A', strtotime($dinner_start))) >= strtotime(date('h:i A')))
                $booking_time['Dinner'][] = date('h:i A', strtotime($dinner_start));
            elseif(strtotime($booking_date) > strtotime(date('Y-m-d')))
                $booking_time['Dinner'][] = date('h:i A', strtotime($dinner_start));*/

            $new_time = strtotime($dinner_start);
            while ($new_time < strtotime($dinner_end)) {
                /*if(strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($dinner_end) && $new_time >= strtotime(date('h:i A')))
                    $booking_time['Dinner'][]= date('h:i A', $new_time);
                elseif(strtotime($booking_date) > strtotime(date('Y-m-d')))
                    $booking_time['Dinner'][]= date('h:i A', $new_time);*/

                $bk_info = [];

                if (strtotime($booking_date) == strtotime(date('Y-m-d')) && $new_time <= strtotime($dinner_end) && $new_time >= strtotime(date('h:i A'))) {
                    //$booking_time['Dinner'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $dinner_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $dinner_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Dinner'][] = $bk_info;
                } elseif (strtotime($booking_date) > strtotime(date('Y-m-d'))) {
                    //$booking_time['Dinner'][]= date('h:i A', $new_time);
                    $pre_bookings_counts = Booking::where('booking_status', 'open')->whereDate('booking_datetime', '=', date('Y-m-d', strtotime($booking_date)))->whereTime('booking_datetime', '=', date('H:i:s', $new_time))->get()->sum('no_of_seats');

                    if ($pre_bookings_counts >= $dinner_max_cover) {
                        $bk_info['class'] = 'booking_time_not_available';
                    } elseif ($pre_bookings_counts && $pre_bookings_counts >= $dinner_warning_covers) {
                        $bk_info['class'] = 'booking_time_warning';
                    } else {
                        $bk_info['class'] = 'booking_time_available';
                    }

                    $bk_info['time'] = date('h:i A', $new_time);
                    $booking_time['Dinner'][] = $bk_info;
                }

                $new_time = strtotime('+' . $dinner_duration . ' minutes', $new_time);
            }

            $html = view('restaurantowner.bookingTimeButtonsExt', [
                'time_buttons' => $booking_time,
            ])->render();

            return response()->json(['success' => true, 'html' => $html], 200);
        }
    }

    public function checkMaxPax(Request $request): JsonResponse
    {
        try {
            if (empty($request->no_of_pax)) {
                return response()->json(['success' => false, 'message' => "Opp's no of pax is invalid.", 'data' => []], 201);
            }

            if (empty($request->restaurant_id)) {
                return response()->json(['success' => false, 'message' => "Opp's restaurant id is invalid.", 'data' => []], 201);
            }

            $restaurant = Restaurant::find($request->restaurant_id);
            $shif_settings = ShiftInformation::where('restaurant_id', $request->restaurant_id)->first();

            if ($request->no_of_pax >= $shif_settings->maxNoOfCover) {
                return response()->json(['success' => false, 'message' => "$restaurant->name does not allow web booking for the number of guests you are trying to book. Please call $shif_settings->teamName for making the reservation", 'data' => []], 201);
            }

            return response()->json(['success' => true, 'message' => 'ok', 'data' => []], 201);
        } catch (\Throwable $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];

            return response()->json($response, 201);
        }
    }
    public function restaurantVenue($id): JsonResponse
    {
        // Fetch the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Get all restaurant IDs associated with this user
        $restaurantIds = RestaurantCustomerModel::where('user_id', $id)
            ->pluck('restaurant_id');

        // Retrieve all restaurants associated with these IDs
        $restaurants = Restaurant::whereIn('id', $restaurantIds)->get();

        return response()->json(['success' => true, 'data' => $restaurants], 200);
    }

}
