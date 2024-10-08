<?php

namespace App\Http\Controllers;

use App\Item;
use App\Restaurant;
use App\RestaurantSettings;
use App\User;
use Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ixudra\Curl\Facades\Curl;
use Modules\DeliveryAreaPro\DeliveryArea;
use Modules\SuperCache\SuperCache;
use Nwidart\Modules\Facades\Module;

class RestaurantController extends Controller
{
    /**
     * @return mixed
     */
    public function getDeliveryRestaurants(Request $request)
    {
        // Cache::forget('stores-delivery-active');
        // Cache::forget('stores-delivery-inactive');
        // die();

        // get all active restauants doing delivery
        if (Cache::has('stores-delivery-active')) {
            $restaurants = Cache::get('stores-delivery-active');
        } else {
            $restaurants = Restaurant::where('is_accepted', '1')
                ->where('is_active', 1)
                ->whereIn('delivery_type', [1, 3])
                ->with('delivery_areas', 'ratings')
                ->ordered()
                ->get();
            $this->processSuperCache('stores-delivery-active', $restaurants);
        }

        //Create a new Laravel collection from the array data
        $nearMe = new Collection();

        foreach ($restaurants as $restaurant) {
            $distance = getDistance($request->latitude, $request->longitude, $restaurant->latitude, $restaurant->longitude);
            $restaurant->distance = $distance;
            $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant);
            if ($check) {
                $nearMe->push($restaurant);
            }
        }

        $nearMe = $nearMe->map(function ($restaurant) {
            $restaurant->avgRating = storeAvgRating($restaurant->ratings);

            return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'distance', 'custom_featured_name', 'custom_message_on_list']);
        });

        $nearMe = $nearMe->toArray();

        if (config('setting.randomizeStores') == 'true') {
            shuffle($nearMe);
            usort($nearMe, function ($left, $right) {
                return $right['is_featured'] - $left['is_featured'];
            });
        }

        if (config('setting.sortDeliveryStoresByDistance') == 'true') {
            $nearMe = collect($nearMe)->sortBy('distance')->toArray();
        }

        if (Cache::has('stores-delivery-inactive')) {
            $inactiveRestaurants = Cache::get('stores-delivery-inactive');
        } else {
            $inactiveRestaurants = Restaurant::where('is_accepted', '1')
                ->where('is_active', 0)
                ->whereIn('delivery_type', [1, 3])
                ->with('delivery_areas', 'ratings')
                ->ordered()
                ->get();
            $this->processSuperCache('stores-delivery-inactive', $inactiveRestaurants);
        }

        $nearMeInActive = new Collection();
        foreach ($inactiveRestaurants as $inactiveRestaurant) {
            $distance = getDistance($request->latitude, $request->longitude, $inactiveRestaurant->latitude, $inactiveRestaurant->longitude);
            $inactiveRestaurant->distance = $distance;
            $check = $this->checkOperation($request->latitude, $request->longitude, $inactiveRestaurant);
            if ($check) {
                $nearMeInActive->push($inactiveRestaurant);
            }
        }
        $nearMeInActive = $nearMeInActive->map(function ($restaurant) {
            $restaurant->avgRating = storeAvgRating($restaurant->ratings);

            return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'distance', 'custom_featured_name', 'custom_message_on_list']);
        });
        $nearMeInActive = $nearMeInActive->toArray();

        if (config('setting.sortDeliveryStoresByDistance') == 'true') {
            $nearMeInActive = collect($nearMeInActive)->sortBy('distance')->toArray();
        }

        $merged = array_merge($nearMe, $nearMeInActive);

        return response()->json($merged);
    }

    /**
     * @return mixed
     */
    public function getSelfPickupRestaurants(Request $request)
    {
        // sleep(500);
        // get all active restauants doing selfpickups
        if (Cache::has('stores-selfpickup-active')) {
            $restaurants = Cache::get('stores-selfpickup-active');
        } else {
            $restaurants = Restaurant::where('is_accepted', '1')
                ->where('is_active', 1)
                ->whereIn('delivery_type', [2, 3])
                ->with('delivery_areas', 'ratings')
                ->ordered()
                ->get();
            $this->processSuperCache('stores-selfpickup-active', $restaurants);
        }

        //Create a new Laravel collection from the array data
        $nearMe = new Collection();

        foreach ($restaurants as $restaurant) {
            $distance = getDistance($request->latitude, $request->longitude, $restaurant->latitude, $restaurant->longitude);
            // if ($distance <= $restaurant->delivery_radius) {
            //     $nearMe->push($restaurant);
            // }
            $restaurant->distance = $distance;
            $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant);
            if ($check) {
                $nearMe->push($restaurant);
            }
        }

        $nearMe = $nearMe->map(function ($restaurant) {
            $restaurant->avgRating = storeAvgRating($restaurant->ratings);

            return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'distance', 'custom_featured_name', 'custom_message_on_list']);
        });

        $nearMe = $nearMe->toArray();
        if (config('setting.randomizeStores') == 'true') {
            shuffle($nearMe);
            usort($nearMe, function ($left, $right) {
                return $right['is_featured'] - $left['is_featured'];
            });
        }

        if (config('setting.sortSelfpickupStoresByDistance') == 'true') {
            $nearMe = collect($nearMe)->sortBy('distance')->toArray();
        }

        if (Cache::has('stores-selfpickup-inactive')) {
            $inactiveRestaurants = Cache::get('stores-selfpickup-inactive');
        } else {
            $inactiveRestaurants = Restaurant::where('is_accepted', '1')
                ->where('is_active', 0)
                ->whereIn('delivery_type', [2, 3])
                ->with('delivery_areas')
                ->ordered()
                ->get();
            $this->processSuperCache('stores-selfpickup-inactive', $inactiveRestaurants);
        }

        $nearMeInActive = new Collection();
        foreach ($inactiveRestaurants as $inactiveRestaurant) {
            $distance = getDistance($request->latitude, $request->longitude, $inactiveRestaurant->latitude, $inactiveRestaurant->longitude);
            // if ($distance <= $inactiveRestaurant->delivery_radius) {
            //     $nearMeInActive->push($inactiveRestaurant);
            // }
            $inactiveRestaurant->distance = $distance;
            $check = $this->checkOperation($request->latitude, $request->longitude, $inactiveRestaurant);
            if ($check) {
                $nearMeInActive->push($inactiveRestaurant);
            }
        }
        $nearMeInActive = $nearMeInActive->map(function ($restaurant) {
            $restaurant->avgRating = storeAvgRating($restaurant->ratings);

            return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'distance', 'custom_featured_name', 'custom_message_on_list']);
        });
        $nearMeInActive = $nearMeInActive->toArray();

        if (config('setting.sortSelfpickupStoresByDistance') == 'true') {
            $nearMeInActive = collect($nearMeInActive)->sortBy('distance')->toArray();
        }

        $merged = array_merge($nearMe, $nearMeInActive);

        return response()->json($merged);
    }

    public function getRestaurantInfo($slug): JsonResponse
    {
        $restaurantInfo = Restaurant::where('slug', $slug)->first();
        $restaurantsettings = RestaurantSettings::where('id', $restaurantInfo->id)->first();
        $restaurantInfo->avgRating = storeAvgRating($restaurantInfo->ratings);
        if (!$restaurantInfo->is_accepted) {
            $restaurantInfo->is_active = 0;
        }
        $restaurantInfo->sommelier_reservations = $restaurantsettings->sommelier_reservations == "yes" ? 1 : 0;
        $restaurantInfo->makeHidden(['delivery_areas', 'ratings', 'commission_rate']);
        $restaurantInfo->is_favorited = false;

        return response()->json($restaurantInfo);
    }

    public function getRestaurantInfoWithFavourite($slug, Request $request): JsonResponse
    {
        $restaurantInfo = Restaurant::where('slug', $slug)->first();

        $restaurantInfo->avgRating = storeAvgRating($restaurantInfo->ratings);

        if (!$restaurantInfo->is_accepted) {
            $restaurantInfo->is_active = 0;
        }

        $restaurantInfo->makeHidden(['delivery_areas', 'ratings', 'commission_rate']);
        $restaurantInfo->is_favorited = $restaurantInfo->isFavorited();

        return response()->json($restaurantInfo);
    }

    public function getRestaurantInfoById($id): JsonResponse
    {
        try {
            $id = str_replace(['{', '}'], '', $id);
            $restaurant = Restaurant::where('id', $id)->first();

            if (!$restaurant) {
                // Restaurant with the given ID not found
                return response()->json(['error' => 'Restaurant not found' . $id], 404);
            }


            return response()->json($restaurant);
        } catch (QueryException $e) {
            // Log the error
            Log::error('QueryException: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Database error'], 500);
        }
    }

    public function getRestaurantItems($slug): JsonResponse
    {
        // Cache::forget('store-info-' . $slug);
        Cache::forever('items-cache', 'true');
        if (Cache::has('store-info-' . $slug)) {
            $restaurant = Cache::get('store-info-' . $slug);
        } else {
            $restaurant = Restaurant::where('slug', $slug)->first();
            $this->processSuperCache('store-info-' . $slug, $restaurant);
        }

        // Cache::forget('items-recommended-' . $restaurant->id);
        // Cache::forget('items-all-' . $restaurant->id);

        if (Cache::has('items-recommended-' . $restaurant->id) && Cache::has('items-all-' . $restaurant->id)) {
            $recommended = Cache::get('items-recommended-' . $restaurant->id);
            $array = Cache::get('items-all-' . $restaurant->id);
        } else {
            if (config('setting.showInActiveItemsToo') == 'true') {
                $recommended = Item::where('restaurant_id', $restaurant->id)->where('is_recommended', '1')
                    ->whereHas('item_category', function ($q) {
                        $q->where('is_enabled', '1');
                    })
                    ->with('addon_categories')
                    ->with([
                        'addon_categories.addons' => function ($query) {
                            $query->where('is_active', 1);
                        }
                    ])
                    ->ordered()
                    ->get();

                $items = Item::where('restaurant_id', $restaurant->id)
                    ->join('item_categories', function ($join) {
                        $join->on('items.item_category_id', '=', 'item_categories.id');
                        $join->where('is_enabled', '1');
                    })
                    ->orderBy('item_categories.order_column', 'asc')
                    ->with('addon_categories')
                    ->with([
                        'addon_categories.addons' => function ($query) {
                            $query->where('is_active', 1);
                        }
                    ])
                    ->ordered()
                    ->get(['items.*', 'item_categories.name as category_name']);
            } else {
                $recommended = Item::where('restaurant_id', $restaurant->id)->where('is_recommended', '1')
                    ->whereHas('item_category', function ($q) {
                        $q->where('is_enabled', '1');
                    })
                    ->where('is_active', '1')
                    ->with('addon_categories')
                    ->with([
                        'addon_categories.addons' => function ($query) {
                            $query->where('is_active', 1);
                        }
                    ])
                    ->ordered()
                    ->get();

                $items = Item::where('restaurant_id', $restaurant->id)
                    ->join('item_categories', function ($join) {
                        $join->on('items.item_category_id', '=', 'item_categories.id');
                        $join->where('is_enabled', '1');
                    })
                    ->orderBy('item_categories.order_column', 'asc')
                    ->where('is_active', '1')
                    ->with('addon_categories')
                    ->with([
                        'addon_categories.addons' => function ($query) {
                            $query->where('is_active', 1);
                        }
                    ])
                    ->ordered()
                    ->get(['items.*', 'item_categories.name as category_name']);
            }

            $items = json_decode($items, true);

            $array = [];
            foreach ($items as $item) {
                $array[$item['category_name']][] = $item;
            }

            $this->processSuperCache('items-recommended-' . $restaurant->id, $recommended);
            $this->processSuperCache('items-all-' . $restaurant->id, $array);
        }

        return response()->json([
            'recommended' => $recommended,
            'items' => $array,
        ]);
    }

    public function searchRestaurants(Request $request): JsonResponse
    {
        //get lat and lng and query from user...
        // get all active restauants doing delivery & selfpickup
        $restaurants = Restaurant::where('name', 'LIKE', "%$request->q%")
            ->where('is_accepted', '1')
            ->take(20)->get();

        //Create a new Laravel collection from the array data
        $nearMeRestaurants = new Collection();

        foreach ($restaurants as $restaurant) {
            $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant);
            if ($check) {
                $restaurant->avgRating = storeAvgRating($restaurant->ratings);
                $nearMeRestaurants->push($restaurant);
            }
        }

        $items = Item::where('is_active', '1')
            ->where('name', 'LIKE', "%$request->q%")
            ->with('restaurant')
            ->get();

        $nearMeItems = new Collection();
        foreach ($items as $item) {
            if ($item->restaurant->is_active && $item->restaurant->is_accepted) {
                $itemRestro = $item->restaurant;
                $check = $this->checkOperation($request->latitude, $request->longitude, $itemRestro);
                if ($check) {
                    $nearMeItems->push($item);
                }
            }
        }

        $response = [
            'restaurants' => $nearMeRestaurants,
            'items' => $nearMeItems->take(20),
        ];

        return response()->json($response);
    }

    public function getSingleItem(Request $request): JsonResponse
    {
        if (Cache::has('item-single-' . $request->id)) {
            $item = Cache::get('item-single-' . $request->id);
        } else {
            if (config('setting.showInActiveItemsToo') == 'true') {
                $item = Item::where('id', $request->id)
                    ->with('addon_categories')
                    ->with([
                        'addon_categories.addons' => function ($query) {
                            $query->where('is_active', 1);
                        }
                    ])
                    ->first();
            } else {
                $item = Item::where('id', $request->id)
                    ->where('is_active', '1')
                    ->with('addon_categories')
                    ->with([
                        'addon_categories.addons' => function ($query) {
                            $query->where('is_active', 1);
                        }
                    ])
                    ->first();
            }

            $this->processSuperCache('item-single-' . $request->id, $item);
        }

        if ($item) {
            return response()->json($item);
        }
    }

    /**
     * @return mixed
     */
    public function getFilteredRestaurants(Request $request)
    {
        $activeFilteredRestaurants = Restaurant::where('is_accepted', '1')
            ->where('is_active', 1)
            ->whereHas('restaurant_categories', function ($query) use ($request) {
                $query->whereIn('restaurant_category_id', $request->category_ids);
            })->get();

        $nearMe = new Collection();

        foreach ($activeFilteredRestaurants as $restaurant) {
            $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant);
            if ($check) {
                $nearMe->push($restaurant);
            }
        }
        $nearMe = $nearMe->map(function ($restaurant) {
            $restaurant->avgRating = storeAvgRating($restaurant->ratings);

            return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'custom_featured_name', 'custom_message_on_list']);
        });
        $nearMe = $nearMe->toArray();

        $inActiveFilteredRestaurants = Restaurant::where('is_accepted', '1')
            ->where('is_active', 0)
            ->whereHas('restaurant_categories', function ($query) use ($request) {
                $query->whereIn('restaurant_category_id', $request->category_ids);
            })->get();

        $nearMeInActive = new Collection();

        foreach ($inActiveFilteredRestaurants as $restaurant) {
            $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant);
            if ($check) {
                $nearMeInActive->push($restaurant);
            }
        }

        $nearMeInActive = $nearMeInActive->map(function ($restaurant) {
            $restaurant->avgRating = storeAvgRating($restaurant->ratings);

            return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'custom_featured_name', 'custom_message_on_list']);
        });
        $nearMeInActive = $nearMeInActive->toArray();

        $merged = array_merge($nearMe, $nearMeInActive);

        return response()->json($merged);
    }

    public function checkCartItemsAvailability(Request $request): JsonResponse
    {
        $items = $request->items;
        $idsArr = [];

        foreach ($items as $item) {
            array_push($idsArr, $item['id']);
        }
        $cartItems = Item::whereIn('id', $idsArr)->get();

        $inActiveItem = [];

        foreach ($cartItems as $cartItem) {
            if ($cartItem) {
                $item = [
                    'id' => $cartItem->id,
                    'price' => $cartItem->price,
                    'is_active' => $cartItem->is_active,
                ];
                array_push($inActiveItem, $item);
            }
        }

        return response()->json($inActiveItem);
    }

    /**
     * @return mixed
     */
    public function getFavoriteStores(Request $request)
    {
        $user = auth()->user();
        $restaurants = $user->favorite(Restaurant::class);

        $nearMe = new Collection();

        foreach ($restaurants as $restaurant) {
            $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant);
            if ($check) {
                $nearMe->push($restaurant);
            }
        }
        $nearMe = $nearMe->map(function ($restaurant) {
            $restaurant->avgRating = storeAvgRating($restaurant->ratings);

            return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'custom_featured_name', 'custom_message_on_list']);
        });
        $nearMe = $nearMe->toArray();

        return response()->json($nearMe);
    }

    /**
     * @return mixed
     */
    public function checkRestaurantOperationService(Request $request)
    {
        $check = false;

        $restaurant = Restaurant::where('id', $request->restaurant_id)->first();
        if ($restaurant) {
            $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant, true); //true is set for cart page check

            return $check;
        }

        return response()->json($check);
    }

    public function getRestaurantInfoAndOperationalStatus(Request $request): JsonResponse
    {
        $restaurant = Restaurant::where('id', $request->id)->first();

        if ($restaurant) {
            $restaurant->avgRating = storeAvgRating($restaurant->ratings);
            $restaurant->makeHidden(['delivery_areas']);
            $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant, true); //true is set for cart page check
            $is_operational = false;
            if ($check) {
                $is_operational = true;
            }
            $restaurant->is_operational = $is_operational;

            return response()->json($restaurant);
        } else {
            abort(400, 'Restaurant ID not passed or not found.');
        }
    }

    private function checkOperation($latitudeFrom, $longitudeFrom, $restaurant, $forCartPageCheck = null): bool
    {
        //check if distance matrix enabled
        if (config('setting.enGDMA') == 'true' && $forCartPageCheck) {
            $destination = trim($latitudeFrom, '"') . ',' . trim($longitudeFrom, '"');
            $origin = trim($restaurant->latitude, '"') . ',' . trim($restaurant->longitude, '"');
            $apiKey = trim(config('setting.googleApiKeyIP'), '"');

            $apiUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json?origins=' . $origin . '&destinations=' . $destination . '&key=' . trim($apiKey, '"') . '&mode=driving';

            $data = Curl::to($apiUrl)->get();
            $response = json_decode($data, true);
            \Log::info($response);

            if (@$response['rows'][0]['elements'][0]['status'] == 'OK') {
                $distance = round($response['rows'][0]['elements'][0]['distance']['value'] / 1000, 1);
            } else {
                $distance = 999999999;
            }
            \Log::info('Distance: ' . $distance);
        } else {
            //if Distance matrix not enabled
            $latFrom = deg2rad($latitudeFrom);
            $lonFrom = deg2rad($longitudeFrom);
            $latTo = deg2rad($restaurant->latitude);
            $lonTo = deg2rad($restaurant->longitude);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

            $distance = $angle * 6371; //distance in km
        }

        //if any delivery area assigned
        if (count($restaurant->delivery_areas) > 0) {
            //check if delivery pro module exists,
            if (Module::find('DeliveryAreaPro') && Module::find('DeliveryAreaPro')->isEnabled()) {
                $dap = new DeliveryArea();

                return $dap->checkArea($latitudeFrom, $longitudeFrom, $restaurant->delivery_areas);
            } else {
                //else use general distance
                if ($distance <= $restaurant->delivery_radius) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            //if no delivery areas, then use general distance
            if ($distance <= $restaurant->delivery_radius) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function processSuperCache($name, $data = null)
    {
        if (Module::find('SuperCache') && Module::find('SuperCache')->isEnabled()) {
            $superCache = new SuperCache();
            $superCache->cacheResponse($name, $data);
        }
    }

    /* Custom API's */

    /**
     * @return mixed
     */
    public function getDeliveryRestaurantsByOwnerId(Request $request)
    {
        //if ($request->has('storeownerid')) {
        try {
            $request->validate([
                'storeownerid' => ['required', 'numeric', 'max:255'],
            ]);

            $user = User::find($request->storeownerid);
            $restaurants = $user->restaurants;

            $restaurants = $restaurants->map(function ($restaurant) {
                $restaurant->avgRating = storeAvgRating($restaurant->ratings);

                return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'distance', 'custom_featured_name', 'custom_message_on_list']);
            });

            $nearMe = $restaurants->toArray();

            return response()->json($nearMe);
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => $e->getMessage()];

            return response()->json($response, 201);
        }
        //}else{
        //  return response()->json(array('error': "Something went wrong."));
        //}
        // Cache::forget('stores-delivery-active');
        // Cache::forget('stores-delivery-inactive');
        // die();

        // get all active restauants doing delivery
        /* if (Cache::has('stores-delivery-active')) {
             $restaurants = Cache::get('stores-delivery-active');
         } else {
             $restaurants = Restaurant::where('is_accepted', '1')
                 ->where('is_active', 1)
                 ->whereIn('delivery_type', [1, 3])
                 ->with('delivery_areas', 'ratings')
                 ->ordered()
                 ->get();
             $this->processSuperCache('stores-delivery-active', $restaurants);
         }

         //Create a new Laravel collection from the array data
         $nearMe = new Collection();

         foreach ($restaurants as $restaurant) {
             $distance = getDistance($request->latitude, $request->longitude, $restaurant->latitude, $restaurant->longitude);
             $restaurant->distance = $distance;
             $check = $this->checkOperation($request->latitude, $request->longitude, $restaurant);
             if ($check) {
                 $nearMe->push($restaurant);
             }
         }

         $nearMe = $nearMe->map(function ($restaurant) {
             $restaurant->avgRating = storeAvgRating($restaurant->ratings);
             return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'distance', 'custom_featured_name', 'custom_message_on_list']);
         });

         $nearMe = $nearMe->toArray();

         if (config('setting.randomizeStores') == 'true') {
             shuffle($nearMe);
             usort($nearMe, function ($left, $right) {
                 return $right['is_featured'] - $left['is_featured'];
             });
         }

         if (config('setting.sortDeliveryStoresByDistance') == 'true') {
             $nearMe = collect($nearMe)->sortBy('distance')->toArray();
         }

         if (Cache::has('stores-delivery-inactive')) {
             $inactiveRestaurants = Cache::get('stores-delivery-inactive');
         } else {
             $inactiveRestaurants = Restaurant::where('is_accepted', '1')
                 ->where('is_active', 0)
                 ->whereIn('delivery_type', [1, 3])
                 ->with('delivery_areas', 'ratings')
                 ->ordered()
                 ->get();
             $this->processSuperCache('stores-delivery-inactive', $inactiveRestaurants);
         }

         $nearMeInActive = new Collection();
         foreach ($inactiveRestaurants as $inactiveRestaurant) {
             $distance = getDistance($request->latitude, $request->longitude, $inactiveRestaurant->latitude, $inactiveRestaurant->longitude);
             $inactiveRestaurant->distance = $distance;
             $check = $this->checkOperation($request->latitude, $request->longitude, $inactiveRestaurant);
             if ($check) {
                 $nearMeInActive->push($inactiveRestaurant);
             }
         }
         $nearMeInActive = $nearMeInActive->map(function ($restaurant) {
             $restaurant->avgRating = storeAvgRating($restaurant->ratings);
             return $restaurant->only(['id', 'name', 'description', 'image', 'rating', 'avgRating', 'delivery_time', 'price_range', 'slug', 'is_featured', 'is_active', 'distance', 'custom_featured_name', 'custom_message_on_list']);
         });
         $nearMeInActive = $nearMeInActive->toArray();

         if (config('setting.sortDeliveryStoresByDistance') == 'true') {
             $nearMeInActive = collect($nearMeInActive)->sortBy('distance')->toArray();
         }

         $merged = array_merge($nearMe, $nearMeInActive);

         return response()->json($merged);*/
    }
}
