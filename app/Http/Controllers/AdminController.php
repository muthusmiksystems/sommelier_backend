<?php

namespace App\Http\Controllers;

use App\AcceptDelivery;
use App\Addon;
use App\AddonCategory;
use App\Address;
use App\Booking;
use App\DeliveryGuyDetail;
use App\EagleView;
use App\FoodomaaNews;
use App\Helpers\TranslationHelper;
use App\Item;
use App\ItemCategory;
use App\Order;
use App\Orderstatus;
use App\Page;
use App\PaymentGateway;
use App\PopularGeoPlace;
use App\PromoSlider;
use App\PushNotify;
use App\Rating;
use App\Restaurant;
use App\RestaurantCategory;
use App\RestaurantPayout;
use App\RestaurantSettings;
use App\Setting;
use App\ShiftInformation;
use App\Slide;
use App\Sms;
use App\SmsGateway;
use App\SocketPush;
use App\State;
use App\StorePayoutDetail;
use App\TableInformation;
use App\TodoNote;
use App\Translation;
use App\User;
use App\VehicleType;
use App\Zone;
use Artisan;
use Auth;
use Bavix\Wallet\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Image;
use Nwidart\Modules\Facades\Module;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Log;
class AdminController extends Controller
{
    /**
     * @return mixed
     */
    public function dashboard()
    {
        $orders = Order::orderBy('id', 'DESC')->with('orderstatus', 'restaurant')->take(6)->get();

        $users = User::orderBy('id', 'DESC')->with('roles')->take(6)->get();

        $todaysDate = Carbon::now()->format('Y-m-d');

        $orderStatusesName = '[';

        $orderStatuses = Orderstatus::get(['name'])
            ->pluck('name')
            ->toArray();
        foreach ($orderStatuses as $key => $value) {
            $orderStatusesName .= "'" . $value . "', ";
        }
        $orderStatusesName = rtrim($orderStatusesName, ' ,');
        $orderStatusesName = $orderStatusesName . ']';

        $ifAnyOrders = Order::count();
        if ($ifAnyOrders == 0) {
            $ifAnyOrders = false;
        } else {
            $ifAnyOrders = true;
        }

        $orderStatusOrders = Order::select('orderstatus_id', DB::raw('count(*) as total'))
            ->groupBy('orderstatus_id')
            ->pluck('total', 'orderstatus_id')->all();

        $orderStatusesData = '[';
        foreach ($orderStatusOrders as $key => $value) {
            if ($key == 1) {
                $orderStatusesData .= '{value:' . $value . ", name:'Order Placed'}, ";
            }
            if ($key == 2) {
                $orderStatusesData .= '{value:' . $value . ", name:'Preparing Order'}, ";
            }
            if ($key == 3) {
                $orderStatusesData .= '{value:' . $value . ", name:'Delivery Guy Assigned'}, ";
            }
            if ($key == 4) {
                $orderStatusesData .= '{value:' . $value . ", name:'Order Picked Up'}, ";
            }
            if ($key == 5) {
                $orderStatusesData .= '{value:' . $value . ", name:'Delivered'}, ";
            }
            if ($key == 6) {
                $orderStatusesData .= '{value:' . $value . ", name:'Canceled'}, ";
            }
            if ($key == 7) {
                $orderStatusesData .= '{value:' . $value . ", name:'Ready For Pick Up'}, ";
            }
            if ($key == 8) {
                $orderStatusesData .= '{value:' . $value . ", name:'Awaiting Payment'}, ";
            }
            if ($key == 9) {
                $orderStatusesData .= '{value:' . $value . ", name:'Payment Failed'}, ";
            }
        }
        $orderStatusesData = rtrim($orderStatusesData, ',');
        $orderStatusesData .= ']';

        $reviews = Rating::orderBy('id', 'DESC')->with('user', 'order.accept_delivery.user')->take(5)->get();

        if (config('setting.adminDailyTargetRevenue') != null) {
            $todayRevenue = Order::where('orderstatus_id', '5')->whereBetween('created_at', [
                Carbon::now()->startOfDay(),
                Carbon::now(),
            ])->select(DB::raw('SUM(total) AS revenue'))->first();
            $todayRevenue = $todayRevenue->revenue ? $todayRevenue->revenue : 0;
        } else {
            $todayRevenue = null;
        }

        $todayOrders = Order::where('orderstatus_id', 5)->select('id', 'orderstatus_id', 'created_at')->whereBetween('created_at', [
            Carbon::now()->startOfDay(),
            Carbon::now(),
        ])->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('H');
        });

        $yesterdayOrders = Order::where('orderstatus_id', 5)->select('id', 'orderstatus_id', 'created_at')->whereBetween('created_at', [
            Carbon::now()->subDays(1)->startOfDay(),
            Carbon::now()->subDays(1),
        ])->get()->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('H');
        });

        $yesterdayOrdersOnly = [];
        foreach ($yesterdayOrders as $key => $value) {
            $yesterdayOrdersOnly[(int) $key] = count($value);
        }

        $todayOrderOnly = [];
        $todayOrderFullArr = [];

        foreach ($todayOrders as $key => $value) {
            $todayOrderOnly[(int) $key] = count($value);
        }

        for ($i = 1; $i <= 24; $i++) {
            if (!empty($todayOrderOnly[$i])) {
                $todayOrderFullArr[$i] = $todayOrderOnly[$i];
            } else {
                $todayOrderFullArr[$i] = 0;
            }
        }

        $todayOrderCount = array_sum($todayOrderOnly);
        $yesterdayOrderCount = array_sum($yesterdayOrdersOnly);

        $todoNotes = TodoNote::where('user_id', Auth::user()->id)->orderBy('id', 'DESC')->get();

        $walletTransactions = Transaction::orderBy('id', 'DESC')->where('amount', '>', 0)->take(6)->get();

        $latestNews = FoodomaaNews::latest()->first();
        if ($latestNews) {
            if ($latestNews->is_read) {
                $latestNews = null;
            }
        } else {
            $latestNews = null;
        }

        return view('admin.dashboard', [
            'orders' => $orders,
            'users' => $users,
            'todaysDate' => $todaysDate,
            'orderStatusesName' => $orderStatusesName,
            'orderStatusesData' => $orderStatusesData,
            'ifAnyOrders' => $ifAnyOrders,
            'reviews' => $reviews,
            'todayRevenue' => $todayRevenue,
            'todayOrderCount' => $todayOrderCount,
            'yesterdayOrderCount' => $yesterdayOrderCount,
            'todayOrderFullArr' => array_values($todayOrderFullArr),
            'todoNotes' => $todoNotes,
            'walletTransactions' => $walletTransactions,
            'latestNews' => $latestNews,
        ]);
    }

    public function manager(): View
    {
        return view('admin.manager');
    }

    public function users(): View
    {
        $roles = Role::all()->except(1);
        return view('admin.users', [
            'roles' => $roles,
        ]);
    }

    public function customers(): View
    {
        return view('admin.manageCustomers');
    }

    public function staffs(): View
    {
        return view('admin.manageStaffs');
    }

    public function saveNewUser(Request $request): RedirectResponse
    {
        try {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => \Hash::make($request->password),
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

            return redirect()->back()->with(['success' => 'User Created']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function getEditUser($id): View
    {
        $user = User::where('id', $id)->with('orders', 'addresses')->first();
        $roles = Role::all()->except(1);
        $ratings = Rating::where('delivery_id', $user->id)->get();
        $zones = Zone::get(['id', 'name']);
        $vehicle_types = VehicleType::get();
        $states = State::get();
        $userRestaurants = $user->restaurants;
        $userRestaurantsIds = $user->restaurants->pluck('id')->toArray();
        // dd($userRestaurants);
        $allRestaurants = Restaurant::get();
        return view('admin.editUser', [
            'orders' => $user->orders,
            'user' => $user,
            'roles' => $roles,
            'rating' => deliveryAvgRating($ratings),
            'zones' => $zones,
            'vehicle_types' => $vehicle_types,
            'states' => $states,
            'userRestaurants' => $userRestaurants,
            'allRestaurants' => $allRestaurants,
            'userRestaurantsIds' => $userRestaurantsIds,
        ]);
    }
    public function getEditUserstore($id): View
    {
        $user = User::where('id', $id)->with('orders', 'addresses')->first();
        $roles = Role::all()->except(1);
        $ratings = Rating::where('delivery_id', $user->id)->get();
        $zones = Zone::get(['id', 'name']);
        $vehicle_types = VehicleType::get();
        $states = State::get();
        $userRestaurants = $user->restaurants;
        $userRestaurantsIds = $user->restaurants->pluck('id')->toArray();
        // dd($userRestaurants);
        $is_active = $userRestaurants[0]->is_active;
        $reservation = RestaurantSettings::find($userRestaurants[0]->id);
        $allRestaurants = Restaurant::get();
        return view('admin.editUser', [
            'orders' => $user->orders,
            'user' => $user,
            'roles' => $roles,
            'rating' => deliveryAvgRating($ratings),
            'zones' => $zones,
            'vehicle_types' => $vehicle_types,
            'states' => $states,
            'userRestaurants' => $userRestaurants,
            'allRestaurants' => $allRestaurants,
            'userRestaurantsIds' => $userRestaurantsIds,
            'is_active' => $is_active,
            'reservation' => $reservation,
        ]);
    }
    public function updateUser(Request $request): RedirectResponse
    {
        // dd($request->all());
        $user = User::where('id', $request->id)->with('delivery_collections', 'delivery_collections.delivery_collection_logs')->first();
        $res = User::where('id', $request->id)->first();
        $res->restaurants()->sync($request->user_restaurants);
        $res->save();
        // dd($request);
        $user->restaurants()->sync($request->restaurant_id);
        try {
            $setDeliveryNickName = false;

            if ($user->hasRole('Customer') && $request->roles == 'Delivery Guy') {
                $setDeliveryNickName = true;
            }

            $user->name = $request->first_name . ' ' . $request->last_name;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;

            $user->dob = date('Y-m-d', strtotime($request->dob));
            $user->licence_no = $request->licence_no;
            $user->state_id = $request->state;

            if ($request->hasFile('licence_photo')) {
                $photo = $request->file('licence_photo');
                $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                $user->licence_photo = $filename;
            }

            if ($request->has('password') && $request->password != null) {
                $user->password = \Hash::make($request->password);
            }

            if ($request->roles != null) {
                $user->syncRoles($request->roles);
            }

            if ($user->hasRole('Delivery Guy')) {
                $user->stripe_account_id = $request->stripe_account_id;
            }

            if ($setDeliveryNickName) {
                $request->delivery_name = $request->name;
            }

            if ($request->zone_id != null) {
                $user->zone_id = $request->zone_id;
            }

            $user->save();

            if ($user->hasRole('Delivery Guy')) {
                if ($user->delivery_guy_detail == null) {
                    $deliveryGuyDetails = new DeliveryGuyDetail();
                    $deliveryGuyDetails->name = $request->delivery_name;
                    //$deliveryGuyDetails->age = $request->delivery_age;
                    if ($request->hasFile('delivery_photo')) {
                        $photo = $request->file('delivery_photo');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $deliveryGuyDetails->photo = $filename;
                    }
                    $deliveryGuyDetails->description = $request->delivery_description;
                    //$deliveryGuyDetails->vehicle_number = $request->delivery_vehicle_number;

                    $deliveryGuyDetails->vehicle_type = $request->vehicle_type;
                    $deliveryGuyDetails->registration_no = $request->registration_no;
                    $deliveryGuyDetails->abn_no = $request->abn_no;
                    $deliveryGuyDetails->bank_name = $request->bank_name;
                    $deliveryGuyDetails->bsb = $request->bsb;
                    $deliveryGuyDetails->account_number = $request->account_number;
                    $deliveryGuyDetails->account_name = $request->account_name;

                    if ($request->hasFile('vehicle_registration')) {
                        $photo = $request->file('vehicle_registration');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $deliveryGuyDetails->vehicle_registration = $filename;
                    }

                    if ($request->hasFile('vehicle_insurance_policy')) {
                        $photo = $request->file('vehicle_insurance_policy');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $deliveryGuyDetails->vehicle_insurance_policy = $filename;
                    }

                    if ($request->hasFile('certificate')) {
                        $photo = $request->file('certificate');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $deliveryGuyDetails->certificate = $filename;
                    }

                    if ($request->hasFile('police_clearence_certificate')) {
                        $photo = $request->file('police_clearence_certificate');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $deliveryGuyDetails->police_clearence_certificate = $filename;
                    }

                    if ($request->delivery_commission_rate != null) {
                        $deliveryGuyDetails->commission_rate = $request->delivery_commission_rate;
                    }

                    if ($request->tip_commission_rate != null) {
                        $deliveryGuyDetails->tip_commission_rate = $request->tip_commission_rate;
                    }

                    if ($request->is_notifiable == 'true') {
                        $deliveryGuyDetails->is_notifiable = true;
                    } else {
                        $deliveryGuyDetails->is_notifiable = false;
                    }

                    if ($request->max_accept_delivery_limit != null) {
                        $deliveryGuyDetails->max_accept_delivery_limit = $request->max_accept_delivery_limit;
                    }

                    if ($request->cash_limit != null) {
                        $deliveryGuyDetails->cash_limit = $request->cash_limit;
                    } else {
                        $deliveryGuyDetails->cash_limit = 0;
                    }

                    $deliveryGuyDetails->save();
                    $user->delivery_guy_detail_id = $deliveryGuyDetails->id;

                    $user->save();
                } else {
                    $user->delivery_guy_detail->name = $request->delivery_name;
                    //$user->delivery_guy_detail->age = $request->delivery_age;
                    if ($request->hasFile('delivery_photo')) {
                        $photo = $request->file('delivery_photo');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $user->delivery_guy_detail->photo = $filename;
                    }
                    $user->delivery_guy_detail->description = $request->delivery_description;
                    //$user->delivery_guy_detail->vehicle_number = $request->delivery_vehicle_number;

                    $user->delivery_guy_detail->vehicle_type = $request->vehicle_type;
                    $user->delivery_guy_detail->registration_no = $request->registration_no;
                    $user->delivery_guy_detail->abn_no = $request->abn_no;
                    $user->delivery_guy_detail->bank_name = $request->bank_name;
                    $user->delivery_guy_detail->bsb = $request->bsb;
                    $user->delivery_guy_detail->account_number = $request->account_number;
                    $user->delivery_guy_detail->account_name = $request->account_name;

                    if ($request->hasFile('vehicle_registration')) {
                        $photo = $request->file('vehicle_registration');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $user->delivery_guy_detail->vehicle_registration = $filename;
                    }

                    if ($request->hasFile('vehicle_insurance_policy')) {
                        $photo = $request->file('vehicle_insurance_policy');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $user->delivery_guy_detail->vehicle_insurance_policy = $filename;
                    }

                    if ($request->hasFile('certificate')) {
                        $photo = $request->file('certificate');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $user->delivery_guy_detail->certificate = $filename;
                    }

                    if ($request->hasFile('police_clearence_certificate')) {
                        $photo = $request->file('police_clearence_certificate');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $user->delivery_guy_detail->police_clearence_certificate = $filename;
                    }

                    if ($request->delivery_commission_rate != null) {
                        $user->delivery_guy_detail->commission_rate = $request->delivery_commission_rate;
                    }
                    if ($request->tip_commission_rate != null) {
                        $user->delivery_guy_detail->tip_commission_rate = $request->tip_commission_rate;
                    }
                    if ($request->is_notifiable == 'true') {
                        $user->delivery_guy_detail->is_notifiable = true;
                    } else {
                        $user->delivery_guy_detail->is_notifiable = false;
                    }

                    if ($request->max_accept_delivery_limit != null) {
                        $user->delivery_guy_detail->max_accept_delivery_limit = $request->max_accept_delivery_limit;
                    }

                    if ($request->cash_limit != null) {
                        $user->delivery_guy_detail->cash_limit = $request->cash_limit;
                    } else {
                        $user->delivery_guy_detail->cash_limit = 0;
                    }

                    $user->delivery_guy_detail->save();
                }

                //for delivery guy, save zone id it's delivery collection and collection logs if zone present.
                if ($request->zone_id != null) {
                    if (!empty($user->delivery_collections)) {
                        foreach ($user->delivery_collections as $deliveryCollection) {
                            $deliveryCollection->zone_id = $request->zone_id;
                            $deliveryCollection->save();
                            if (!empty($deliveryCollection->delivery_collection_logs)) {
                                foreach ($deliveryCollection->delivery_collection_logs as $deliveryCollectionLog) {
                                    $deliveryCollectionLog->zone_id = $request->zone_id;
                                    $deliveryCollectionLog->save();
                                }
                            }
                        }
                    }
                }
            }

            return redirect(route('admin.get.editUser', $user->id) . $request->window_redirect_hash)->with(['success' => 'User Updated']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function banUser($id): RedirectResponse
    {
        $user = User::where('id', $id)->firstOrFail();
        $user->toggleActive()->save();

        return redirect()->back()->with(['success' => 'Operation Successful']);
    }

    public function manageDeliveryGuys(): View
    {
        return view('admin.manageDeliveryGuys');
    }

    public function getManageDeliveryGuysRestaurants($id): View
    {
        $user = User::where('id', $id)->first();
        if ($user->hasRole('Delivery Guy')) {
            $userRestaurants = $user->restaurants;
            $userRestaurantsIds = $user->restaurants->pluck('id')->toArray();

            $allRestaurants = Restaurant::get();

            return view('admin.manageDeliveryGuysRestaurants', [
                'user' => $user,
                'userRestaurants' => $userRestaurants,
                'allRestaurants' => $allRestaurants,
                'userRestaurantsIds' => $userRestaurantsIds,
            ]);
        }
    }
    public function getManageDeliveryGuysRestaurantstore($id): View
    {
        $user = User::where('id', $id)->first();
        if ($user->hasRole('Delivery Guy')) {
            $userRestaurants = $user->restaurants;
            $userRestaurantsIds = $user->restaurants->pluck('id')->toArray();
            $is_active = $userRestaurants[0]->is_active;
            $reservation = RestaurantSettings::find($userRestaurants[0]->id);
            $allRestaurants = Restaurant::get();

            return view('admin.manageDeliveryGuysRestaurants', [
                'user' => $user,
                'userRestaurants' => $userRestaurants,
                'allRestaurants' => $allRestaurants,
                'userRestaurantsIds' => $userRestaurantsIds,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        }
    }
    public function updateDeliveryGuysRestaurants(Request $request): RedirectResponse
    {
        $user = User::where('id', $request->id)->first();
        $user->restaurants()->sync($request->user_restaurants);
        $user->save();

        return redirect()->back()->with(['success' => 'Delivery Guy Updated']);
    }

    public function manageRestaurantOwners(): View
    {
        $users = User::role('Store Owner')->orderBy('id', 'DESC')->with('roles')->paginate(20);
        $count = $users->total();

        return view('admin.manageRestaurantOwners', [
            'users' => $users,
            'count' => $count,
        ]);
    }

    public function getManageRestaurantOwnersRestaurants($id): View
    {
        $user = User::where('id', $id)->first();
        if ($user->hasRole('Store Owner')) {
            $userRestaurants = $user->restaurants;
            $userRestaurantsIds = $user->restaurants->pluck('id')->toArray();
            $allRestaurants = Restaurant::get();

            return view('admin.manageRestaurantOwnersRestaurants', [
                'user' => $user,
                'userRestaurants' => $userRestaurants,
                'allRestaurants' => $allRestaurants,
                'userRestaurantsIds' => $userRestaurantsIds,
            ]);
        }
    }
    public function getManageRestaurantOwnersRestaurantstore($id): View
    {
        $user = User::where('id', $id)->first();
        if ($user->hasRole('Store Owner')) {
            $userRestaurants = $user->restaurants;
            $userRestaurantsIds = $user->restaurants->pluck('id')->toArray();
            $allRestaurants = Restaurant::get();
            $is_active = $userRestaurants[0]->is_active;
            $reservation = RestaurantSettings::find($userRestaurants[0]->id);
            return view('admin.manageRestaurantOwnersRestaurants', [
                'user' => $user,
                'userRestaurants' => $userRestaurants,
                'allRestaurants' => $allRestaurants,
                'userRestaurantsIds' => $userRestaurantsIds,
                'is_active' => $is_active,
                'reservation' => $reservation,
            ]);
        }
    }
    public function updateManageRestaurantOwnersRestaurants(Request $request): RedirectResponse
    {
        $user = User::where('id', $request->id)->first();
        $user->restaurants()->sync($request->user_restaurants);
        $user->save();

        return redirect()->back()->with(['success' => 'Store Owner Updated']);
    }

    public function orders(): View
    {
        return view('admin.orders');
    }

    public function viewOrder($order_id)
    {
        if (config('setting.iHaveFoodomaaDeliveryApp') == 'true') {
            $eagleView = new EagleView();
            $eagleViewData = $eagleView->getViewOrderSemiEagleViewData();
            if ($eagleViewData == null) {
                print_r('You have enabled <b>I Have Delivery App</b> in Admin Settings that requires delivery google services file to be correctly set on your server. <br><br><b>delivery-google-services.json</b> file is either missing or incorrect. <br><br> <b><u>Possible Solutions:</u> </b>
                <ul><li>Make sure the delivery-google-services.json is present on your server</li> <li>Or disable <b>I have Delivery App</b> from Admin Settings</li>');
                exit();
            }
        } else {
            $eagleViewData = null;
        }

        $order = Order::where('unique_order_id', $order_id)->with('orderitems.order_item_addons', 'rating', 'razorpay_data')->first();
        // dd($order);
        $zone_id = session('selectedZone');
        if ($zone_id) {
            $users = User::role('Delivery Guy')->with('delivery_guy_detail')->where('zone_id', $zone_id)->get();
        } else {
            $users = User::role('Delivery Guy')->with('delivery_guy_detail')->get();
        }

        if ($order) {
            $activities = Activity::where('subject_id', $order->id)->with('causer', 'causer.roles')->orderBy('id', 'DESC')->get();

            return view('admin.viewOrder', [
                'order' => $order,
                'users' => $users,
                'activities' => $activities,
                'eagleViewData' => $eagleViewData,
            ]);
        } else {
            return redirect()->route('admin.orders');
        }
    }

    public function getOrderDeliveryGuyInfo($order_id): JsonResponse
    {
        $order = Order::where('unique_order_id', $order_id)->with('accept_delivery')->first();
        if ($order && $order->accept_delivery && $order->accept_delivery->user->delivery_guy_detail->delivery_lat != null) {
            $response = [
                'success' => true,
                'lat' => $order->accept_delivery->user->delivery_guy_detail->delivery_lat,
                'lng' => $order->accept_delivery->user->delivery_guy_detail->delivery_long,
            ];

            return response()->json($response);
        }
        $response = ['success' => false];

        return response()->json($response);
    }

    public function printThermalBill($order_id)
    {
        $order = Order::where('unique_order_id', $order_id)->with('orderitems.order_item_addons')->first();
        $users = User::role('Delivery Guy')->get();
        if ($order) {
            return view('admin.printOrder', [
                'order' => $order,
                'users' => $users,
            ]);
        } else {
            return redirect()->route('admin.orders');
        }
    }

    public function sliders(): View
    {
        $sliders = PromoSlider::orderBy('id', 'DESC')->with('slides')->get();
        $count = count($sliders);

        return view('admin.sliders', [
            'sliders' => $sliders,
            'count' => $count,
        ]);
    }

    public function getEditSlider($id)
    {
        $restaurants = Restaurant::with('items')->get();
        $slider = PromoSlider::where('id', $id)->with('slides')->firstOrFail();
        $slides = $slider->slides;
        foreach ($slides as $slide) {
            if ($slide->model == null) {
                $link = 'Not Linked';
            }
            if ($slide->model == 1) {
                $slideRestaurant = $slide->restaurant;
                if ($slideRestaurant) {
                    $link = 'Linked to: ' . $slideRestaurant->name;
                } else {
                    $link = 'Not Linked';
                }
            }

            if ($slide->model == 2) {
                $slideItem = $slide->item;
                if ($slideItem) {
                    $link = 'Linked to item: ' . $slideItem->name . ' from Store: ' . $slideItem->restaurant->name;
                } else {
                    $link = 'Not Linked';
                }
            }

            if ($slide->model == 3) {
                if ($slide->url != null) {
                    $link = 'Linked to: ' . $slide->url;
                } else {
                    $link = 'Not Linked';
                }
            }

            $slide->link = $link;
        }
        if ($slider) {
            return view('admin.editSlider', [
                'restaurants' => $restaurants,
                'slider' => $slider,
                'slides' => $slides,
            ]);
        } else {
            return redirect()->route('admin.sliders');
        }
    }

    public function updateSlider(Request $request): RedirectResponse
    {
        $slider = PromoSlider::where('id', $request->id)->first();
        $slider->name = $request->name;
        $slider->position_id = $request->position_id;
        $slider->size = $request->size;
        $slider->view = $request->view;
        $slider->save();

        return redirect()->back()->with(['success' => 'Slider Updated']);
    }

    public function createSlider(Request $request): RedirectResponse
    {
        $sliderCount = PromoSlider::where('is_active', 1)->count();

        if ($sliderCount >= 2) {
            return redirect()->back()->with(['message' => 'Only two sliders can be created. Disbale or delete some Sliders to create more.']);
        }

        $slider = new PromoSlider();
        $slider->name = $request->name;
        $slider->location_id = '0';
        $slider->position_id = $request->position_id;
        $slider->size = $request->size;
        $slider->view = $request->view;
        $slider->save();

        return redirect()->back()->with(['success' => 'New Slider Created']);
    }

    public function disableSlider($id): RedirectResponse
    {
        $slider = PromoSlider::where('id', $id)->first();
        if ($slider) {
            $slider->toggleActive()->save();

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('admin.sliders');
        }
    }

    public function deleteSlider($id): RedirectResponse
    {
        $slider = PromoSlider::where('id', $id)->first();
        if ($slider) {
            $slides = $slider->slides;
            foreach ($slides as $slide) {
                $slide->delete();
            }
            $slider->delete();

            return redirect()->route('admin.sliders')->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('admin.sliders');
        }
    }

    public function saveSlide(Request $request): RedirectResponse
    {
        $url = url('/');
        $url = substr($url, 0, strrpos($url, '/')); //this will give url without " / "

        $slide = new Slide();
        $slide->promo_slider_id = $request->promo_slider_id;
        $slide->name = $request->name;
        $slide->url = $request->url;

        $image = $request->file('image');
        $rand_name = time() . Str::random(10);
        $filename = $rand_name . '.' . $image->getClientOriginalExtension();

        Image::make($image)
            ->resize(384, 384)
            ->save(base_path('assets/img/slider/' . $filename));
        $slide->image = '/assets/img/slider/' . $filename;

        $slide->model = $request->model;
        $slide->restaurant_id = $request->restaurant_id;
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
    }

    public function editSlide($id)
    {
        $slide = Slide::where('id', $id)->with('promo_slider')->first();

        if ($slide) {
            if ($slide->model == null) {
                $link = null;
            }
            if ($slide->model == 1) {
                $slideRestaurant = $slide->restaurant;
                if ($slideRestaurant) {
                    $link = '<b>Store - </b>' . $slideRestaurant->name;
                } else {
                    $link = null;
                }
            }

            if ($slide->model == 2) {
                $slideItem = $slide->item;
                if ($slideItem) {
                    $link = '<b>Item - </b>' . $slideItem->name . '<br><b> From Store - </b>' . $slideItem->restaurant->name;
                } else {
                    $link = null;
                }
            }

            if ($slide->model == 3) {
                if ($slide->url != null) {
                    $link = '<b>Custom URL - </b>' . $slide->url;
                } else {
                    $link = null;
                }
            }

            $restaurants = Restaurant::with('items')->get();

            return view('admin.editSlide', [
                'slide' => $slide,
                'restaurants' => $restaurants,
                'link' => $link,
            ]);
        } else {
            return redirect()->route('admin.sliders')->with(['message' => 'Slide Not Found']);
        }
    }

    public function updateSlide(Request $request): RedirectResponse
    {
        // dd($request->all());
        $slide = Slide::where('id', $request->id)->first();
        if ($slide) {
            $slide->name = $request->name;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $rand_name = time() . Str::random(10);
                $filename = $rand_name . '.' . $image->getClientOriginalExtension();
                Image::make($image)
                    ->resize(384, 384)
                    ->save(base_path('assets/img/slider/' . $filename));
                $slide->image = '/assets/img/slider/' . $filename;
            }

            if ($request->model != null) {
                $slide->model = $request->model;
                $slide->restaurant_id = $request->restaurant_id;
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
            }

            $slide->save();

            return redirect()->back()->with(['success' => 'Slide Updated']);
        } else {
            return redirect()->route('admin.sliders')->with(['message' => 'Slide Not Found']);
        }
    }

    public function updateSlidePosition(Request $request): JsonResponse
    {
        Slide::setNewOrder($request->newOrder);
        Artisan::call('cache:clear');

        return response()->json(['success' => true]);
    }

    public function deleteSlide($id): RedirectResponse
    {
        $slide = Slide::where('id', $id)->first();
        if ($slide) {
            $slide->delete();

            return redirect()->back()->with(['success' => 'Deleted']);
        } else {
            return redirect()->route('admin.sliders');
        }
    }

    public function disableSlide($id): RedirectResponse
    {
        $slide = Slide::where('id', $id)->first();
        if ($slide) {
            $slide->toggleActive()->save();

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('admin.sliders');
        }
    }

    public function restaurants(): View
    {
        $dapCheck = false;
        if (Module::find('DeliveryAreaPro') && Module::find('DeliveryAreaPro')->isEnabled()) {
            $dapCheck = true;
        }

        $pendingCount = Restaurant::orderBy('id', 'DESC')->where('is_accepted', '0')->count();
        $zones = Zone::get(['id', 'name']);

        return view('admin.restaurants', [
            'pendingCount' => $pendingCount,
            'zones' => $zones,
            'dapCheck' => $dapCheck,
        ]);
    }

    public function sortStores(): View
    {
        $restaurants = Restaurant::where('is_accepted', '1')->with('users.roles')->ordered()->get();
        $count = $restaurants->count();

        $dapCheck = false;
        if (Module::find('DeliveryAreaPro') && Module::find('DeliveryAreaPro')->isEnabled()) {
            $dapCheck = true;
        }

        return view('admin.sortStores', [
            'restaurants' => $restaurants,
            'count' => $count,
            'dapCheck' => $dapCheck,
        ]);
    }

    public function updateStorePosition(Request $request): JsonResponse
    {
        Restaurant::setNewOrder($request->newOrder);
        Artisan::call('cache:clear');

        return response()->json(['success' => true]);
    }

    public function sortMenusAndItems($restaurant_id)
    {
        $restaurant = Restaurant::where('id', $restaurant_id)->firstOrFail();

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

        return view('admin.sortMenusAndItemsForStore', [
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

    public function pendingAcceptance(): View
    {
        $dapCheck = false;
        if (Module::find('DeliveryAreaPro') && Module::find('DeliveryAreaPro')->isEnabled()) {
            $dapCheck = true;
        }

        $pendingCount = Restaurant::orderBy('id', 'DESC')->where('is_accepted', '0')->count();
        $zones = Zone::get(['id', 'name']);

        return view('admin.restaurants', [
            'dapCheck' => $dapCheck,
            'pendingCount' => $pendingCount,
            'zones' => $zones,
        ]);
    }

    public function acceptRestaurant($id): RedirectResponse
    {
        $restaurant = Restaurant::where('id', $id)->first();
        if ($restaurant) {
            $restaurant->toggleAcceptance()->save();

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('admin.restaurants');
        }
    }

    public function searchRestaurants(Request $request): View
    {
        $query = $request['query'];

        $restaurants = Restaurant::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('sku', 'LIKE', '%' . $query . '%')->with('users.roles')->paginate(20);

        $count = $restaurants->total();

        $dapCheck = false;
        if (Module::find('DeliveryAreaPro') && Module::find('DeliveryAreaPro')->isEnabled()) {
            $dapCheck = true;
        }
        $zones = Zone::get(['id', 'name']);

        return view('admin.restaurants', [
            'restaurants' => $restaurants,
            'query' => $query,
            'count' => $count,
            'dapCheck' => $dapCheck,
            'zones' => $zones,
        ]);
    }

    public function disableRestaurant($id)
    {
        $restaurant = Restaurant::where('id', $id)->first();
        if ($restaurant) {
            $restaurant->is_schedulable = false;
            $restaurant->toggleActive();
            $restaurant->save();
            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => true], 200);
            }

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('admin.restaurants');
        }
    }

    public function deleteRestaurant($id): RedirectResponse
    {
        $restaurant = Restaurant::where('id', $id)->first();
        if ($restaurant) {
            $items = $restaurant->items;
            foreach ($items as $item) {
                $item->delete();
            }
            $restaurant->delete();

            return redirect()->route('admin.restaurants');
        } else {
            return redirect()->route('admin.restaurants');
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

        $restaurant->rating = $request->rating;
        $restaurant->delivery_time = $request->delivery_time;
        $restaurant->price_range = $request->price_range;

        if ($request->is_pureveg == 'true') {
            $restaurant->is_pureveg = true;
        } else {
            $restaurant->is_pureveg = false;
        }

        if ($request->is_featured == 'true') {
            $restaurant->is_featured = true;
        } else {
            $restaurant->is_featured = false;
        }

        $restaurant->slug = Str::slug($request->name) . '-' . Str::random(15);
        $restaurant->certificate = $request->certificate;

        $restaurant->address = $request->address;
        $restaurant->pincode = $request->pincode;
        $restaurant->landmark = $request->landmark;
        $restaurant->latitude = $request->latitude;
        $restaurant->longitude = $request->longitude;

        $restaurant->restaurant_charges = $request->restaurant_charges;
        $restaurant->delivery_charges = $request->delivery_charges;
        $restaurant->commission_rate = $request->commission_rate;

        if ($request->has('delivery_type')) {
            $restaurant->delivery_type = $request->delivery_type;
        }

        if ($request->delivery_charge_type == 'FIXED') {
            $restaurant->delivery_charge_type = 'FIXED';
            $restaurant->delivery_charges = $request->delivery_charges;
        }
        if ($request->delivery_charge_type == 'DYNAMIC') {
            $restaurant->delivery_charge_type = 'DYNAMIC';
            $restaurant->base_delivery_charge = $request->base_delivery_charge;
            $restaurant->base_delivery_distance = $request->base_delivery_distance;
            $restaurant->extra_delivery_charge = $request->extra_delivery_charge;
            $restaurant->extra_delivery_distance = $request->extra_delivery_distance;
        }
        if ($request->delivery_radius != null) {
            $restaurant->delivery_radius = $request->delivery_radius;
        }

        $restaurant->sku = time() . Str::random(10);
        $restaurant->is_active = 0;
        $restaurant->is_accepted = 1;

        $restaurant->min_order_price = $request->min_order_price;

        if ($request->zone_id != null) {
            $restaurant->zone_id = $request->zone_id;
        }

        try {
            $restaurant->save();

            return redirect()->back()->with(['success' => 'Restaurant Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function getRestaurantItems($id): View
    {
        $items = Item::where('restaurant_id', $id)->orderBy('id', 'DESC')->with('item_category', 'restaurant')->paginate(20);
        $count = $items->total();

        $restaurants = Restaurant::all();
        $itemCategories = ItemCategory::where('is_enabled', '1')->get();
        $addonCategories = AddonCategory::all();

        return view('admin.items', [
            'items' => $items,
            'count' => $count,
            'restaurants' => $restaurants,
            'itemCategories' => $itemCategories,
            'addonCategories' => $addonCategories,
            'restaurant_id' => $id,
        ]);
    }

    public function getEditRestaurant($id): View
    {
        $restaurant = Restaurant::where('id', $id)->with('users.roles', 'delivery_areas')->ordered()->firstOrFail();

        $restaurantCategories = RestaurantCategory::where('is_active', '1')->get();

        $dapCheck = false;
        if (Module::find('DeliveryAreaPro') && Module::find('DeliveryAreaPro')->isEnabled()) {
            $dapCheck = true;
        }

        $adminPaymentGateways = PaymentGateway::where('is_active', '1')->get();

        $payoutData = StorePayoutDetail::where('restaurant_id', $id)->first();
        if ($payoutData) {
            $payoutData = json_decode($payoutData->data);
        } else {
            $payoutData = null;
        }

        $zones = Zone::get(['id', 'name']);

        return view('admin.editRestaurant', [
            'restaurant' => $restaurant,
            'restaurantCategories' => $restaurantCategories,
            'schedule_data' => json_decode($restaurant->schedule_data),
            'dapCheck' => $dapCheck,
            'adminPaymentGateways' => $adminPaymentGateways,
            'payoutData' => $payoutData,
            'rating' => storeAvgRating($restaurant->ratings),
            'zones' => $zones,
        ]);
    }

    public function updateRestaurant(Request $request): RedirectResponse
    {
        // dd($request->all());

        $restaurant = Restaurant::where('id', $request->id)->with([
            'items' => function ($query) {
                $query->select('id', 'restaurant_id', 'zone_id');
            },
            'orders' => function ($query) {
                $query->select('id', 'restaurant_id', 'zone_id');
            },
        ])->first();

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
            if ($request->background_image == null) {
                $restaurant->background_image = $request->background_image;
            } else {
                $background_image = $request->file('background_image');
                $rand_name = time() . Str::random(10);
                $filename = $rand_name . '.jpg';
                Image::make($background_image)
                    ->resize(250, 250)
                    ->save(base_path('assets/img/restaurants/' . $filename), config('setting.uploadImageQuality '), 'jpg');
                $restaurant->background_image = '/assets/img/restaurants/' . $filename;
            }

            $restaurant->rating = $request->rating;
            $restaurant->delivery_time = $request->delivery_time;
            $restaurant->price_range = $request->price_range;

            if ($request->is_pureveg == 'true') {
                $restaurant->is_pureveg = true;
            } else {
                $restaurant->is_pureveg = false;
            }

            if ($request->is_featured == 'true') {
                $restaurant->is_featured = true;
            } else {
                $restaurant->is_featured = false;
            }

            $restaurant->certificate = $request->certificate;

            $restaurant->address = $request->address;
            $restaurant->pincode = $request->pincode;
            $restaurant->landmark = $request->landmark;
            $restaurant->latitude = $request->latitude;
            $restaurant->longitude = $request->longitude;

            $restaurant->restaurant_charges = $request->restaurant_charges;
            $restaurant->delivery_charges = $request->delivery_charges;
            $restaurant->commission_rate = $request->commission_rate;

            $restaurant->stripe_account_id = $request->stripe_account_id;

            if ($request->has('delivery_type')) {
                $restaurant->delivery_type = $request->delivery_type;
            }

            if ($request->delivery_charge_type == 'FIXED') {
                $restaurant->delivery_charge_type = 'FIXED';
                $restaurant->delivery_charges = $request->delivery_charges;
            }
            if ($request->delivery_charge_type == 'DYNAMIC') {
                $restaurant->delivery_charge_type = 'DYNAMIC';
                $restaurant->base_delivery_charge = $request->base_delivery_charge;
                $restaurant->base_delivery_distance = $request->base_delivery_distance;
                $restaurant->extra_delivery_charge = $request->extra_delivery_charge;
                $restaurant->extra_delivery_distance = $request->extra_delivery_distance;
            }
            if ($request->delivery_radius != null) {
                $restaurant->delivery_radius = $request->delivery_radius;
            }

            $restaurant->min_order_price = $request->min_order_price;

            if ($request->is_schedulable == 'true') {
                $restaurant->is_schedulable = true;
            } else {
                $restaurant->is_schedulable = false;
            }

            if ($request->is_notifiable == 'true') {
                $restaurant->is_notifiable = true;
            } else {
                $restaurant->is_notifiable = false;
            }

            if ($request->auto_acceptable == 'true') {
                $restaurant->auto_acceptable = true;
            } else {
                $restaurant->auto_acceptable = false;
            }

            $restaurant->custom_message = $request->custom_message;

            $restaurant->custom_featured_name = $request->custom_featured_name;

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

            $restaurant->free_delivery_subtotal = $request->free_delivery_subtotal;

            $restaurant->custom_message_on_list = $request->custom_message_on_list;

            $restaurantZone = $restaurant->zone_id;
            if ($restaurantZone != $request->zone_id) {
                //zone id has changed, so update all related tables with the new zone ID
                $restaurantItemIds = [];
                //restaurant items
                foreach ($restaurant->items as $restaurantItem) {
                    array_push($restaurantItemIds, $restaurantItem->id);
                }
                $restaurantOrderIds = [];
                //restaurant orders
                foreach ($restaurant->orders as $restaurantOrder) {
                    array_push($restaurantOrderIds, $restaurantOrder->id);
                }

                $restaurantEarningsIds = [];
                //restaurant earnings
                foreach ($restaurant->restaurant_earnings as $restaurantEarning) {
                    array_push($restaurantEarningsIds, $restaurantEarning->id);
                }

                $restaurantPayoutsIds = [];
                //restaurant payouts
                foreach ($restaurant->restaurant_payouts as $restaurantPayout) {
                    array_push($restaurantPayoutsIds, $restaurantPayout->id);
                }

                DB::table('items')->whereIn('id', $restaurantItemIds)->update(['zone_id' => $request->zone_id]);
                DB::table('orders')->whereIn('id', $restaurantOrderIds)->update(['zone_id' => $request->zone_id]);
                DB::table('restaurant_earnings')->whereIn('id', $restaurantEarningsIds)->update(['zone_id' => $request->zone_id]);
                DB::table('restaurant_payouts')->whereIn('id', $restaurantPayoutsIds)->update(['zone_id' => $request->zone_id]);

                $restaurant->zone_id = $request->zone_id;
            }

            try {
                if (isset($request->restaurant_category_restaurant)) {
                    $restaurant->restaurant_categories()->sync($request->restaurant_category_restaurant);
                }

                if ($request->store_payment_gateways == null) {
                    $restaurant->payment_gateways()->sync($request->store_payment_gateways);
                }

                if (isset($request->store_payment_gateways)) {
                    $restaurant->payment_gateways()->sync($request->store_payment_gateways);
                }

                $restaurant->save();

                try {
                    $restaurant->slug = Str::slug($request->store_url);
                    $restaurant->save();
                } catch (\Illuminate\Database\QueryException $qe) {
                    $errorCode = $qe->errorInfo[1];
                    if ($errorCode == 1062) {
                        return redirect()->back()->with(['message' => 'URL should be unique, it should not match with other store URLs']);
                    }

                    return redirect()->back()->with(['message' => $qe->getMessage()]);
                }
                // dd('here');
                // return redirect()->back()->with(['success' => 'Store Updated']);
                return redirect(route('admin.get.editRestaurant', $restaurant->id) . $request->window_redirect_hash)->with(['success' => 'Store Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th->getMessage()]);
            }
        }
    }

    public function updateSlug(Request $request): RedirectResponse
    {
        $restaurant = Restaurant::where('id', $request->id)->firstOrFail();

        try {
            $restaurant->slug = Str::slug($request->store_url);
            $restaurant->save();

            return redirect()->back()->with(['success' => 'URL Updated']);
        } catch (\Illuminate\Database\QueryException $qe) {
            $errorCode = $qe->errorInfo[1];
            if ($errorCode == 1062) {
                return redirect()->back()->with(['message' => 'URL should be unique, it should not match with other store URLs']);
            }

            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function items(Request $request): View
    {
        $restaurants = Restaurant::all();
        $itemCategories = ItemCategory::where('is_enabled', '1')->get();
        $addonCategories = AddonCategory::all();

        if ($request->has('store_id')) {
            $store_id = $request->store_id;
        } else {
            $store_id = null;
        }

        return view('admin.items', [
            'restaurants' => $restaurants,
            'itemCategories' => $itemCategories,
            'addonCategories' => $addonCategories,
            'store_id' => $store_id ? $store_id : null,
        ]);
    }

    public function searchItems(Request $request): View
    {
        $query = $request['query'];

        if ($request->has('restaurant_id')) {
            $items = Item::where('restaurant_id', $request->restaurant_id)
                ->where('name', 'LIKE', '%' . $query . '%')
                ->with('item_category', 'restaurant')
                ->paginate(20);
        } else {
            $items = Item::where('name', 'LIKE', '%' . $query . '%')
                ->with('item_category', 'restaurant')
                ->paginate(20);
        }

        $count = $items->total();

        $restaurants = Restaurant::get();
        $itemCategories = ItemCategory::where('is_enabled', '1')->get();
        $addonCategories = AddonCategory::all();

        return view('admin.items', [
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

            $item->zone_id = $item->restaurant->zone_id ? $item->restaurant->zone_id : null;
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
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function getEditItem($id): View
    {
        $item = Item::where('id', $id)->first();
        $restaurants = Restaurant::get();
        $itemCategories = ItemCategory::get();
        $addonCategories = AddonCategory::all();

        return view('admin.editItem', [
            'item' => $item,
            'restaurants' => $restaurants,
            'itemCategories' => $itemCategories,
            'addonCategories' => $addonCategories,
        ]);
    }

    public function disableItem($id)
    {
        $item = Item::where('id', $id)->first();
        if ($item) {
            $item->toggleActive()->save();
            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => true, 'currentStatus' => $item->is_active]);
            }

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('admin.items');
        }
    }

    public function updateItem(Request $request): RedirectResponse
    {
        // dd($request->all());
        $item = Item::where('id', $request->id)->first();

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

            $item->zone_id = $item->restaurant->zone_id ? $item->restaurant->zone_id : null;

            try {
                $item->save();

                if ($request->addon_category_item == null) {
                    $item->addon_categories()->sync($request->addon_category_item);
                }

                if (isset($request->addon_category_item)) {
                    $item->addon_categories()->sync($request->addon_category_item);
                }

                return redirect()->back()->with(['success' => 'Item Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th->getMessage()]);
            }
        }
    }

    public function removeItemImage($id): RedirectResponse
    {
        $item = Item::where('id', $id)->firstOrFail();

        $item->image = null;
        $item->save();

        return redirect()->back()->with(['success' => 'Item image removed']);
    }

    public function addonCategories(): View
    {
        $addonCategories = AddonCategory::orderBy('id', 'DESC')->paginate(20);
        $addonCategories->loadCount('addons');

        $count = $addonCategories->total();

        return view('admin.addonCategories', [
            'addonCategories' => $addonCategories,
            'count' => $count,
        ]);
    }

    public function searchAddonCategories(Request $request): View
    {
        $query = $request['query'];

        $addonCategories = AddonCategory::where('name', 'LIKE', '%' . $query . '%')->paginate(20);
        $addonCategories->loadCount('addons');

        $count = $addonCategories->total();

        return view('admin.addonCategories', [
            'addonCategories' => $addonCategories,
            'count' => $count,
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
                    $addon->user_id = Auth::user()->id;
                    $addon->addon_category_id = $addonCategory->id;
                    $addon->save();
                }
            }

            return redirect()->route('admin.editAddonCategory', $addonCategory->id)->with(['success' => 'Addon Category Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function newAddonCategory(): View
    {
        return view('admin.newAddonCategory');
    }

    public function deleteAddon($id): RedirectResponse
    {
        $addon = Addon::find($id);
        $addon->delete();

        return redirect()->back()->with(['success' => 'Addon Deleted']);
    }

    public function getEditAddonCategory($id): View
    {
        $addonCategory = AddonCategory::where('id', $id)->with('addons')->first();

        return view('admin.editAddonCategory', [
            'addonCategory' => $addonCategory,
            'addons' => $addonCategory->addons,
        ]);
    }

    public function updateAddonCategory(Request $request): RedirectResponse
    {
        // dd($request->all());
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
                return redirect()->back()->with(['message' => $th->getMessage()]);
            }
        }
    }

    public function addons(): View
    {
        $addons = Addon::orderBy('id', 'DESC')->with('addon_category')->paginate(20);
        $count = $addons->total();

        $addonCategories = AddonCategory::all();

        return view('admin.addons', [
            'addons' => $addons,
            'count' => $count,
            'addonCategories' => $addonCategories,
        ]);
    }

    public function searchAddons(Request $request): View
    {
        $query = $request['query'];

        $addons = Addon::where('name', 'LIKE', '%' . $query . '%')->with('addon_category')->paginate(20);

        $count = $addons->total();

        $addonCategories = AddonCategory::all();

        return view('admin.addons', [
            'addons' => $addons,
            'count' => $count,
            'addonCategories' => $addonCategories,
        ]);
    }

    public function saveNewAddon(Request $request): RedirectResponse
    {
        // dd($request->all());
        $addon = new Addon();

        $addon->name = $request->name;
        $addon->price = $request->price;
        $addon->user_id = Auth::user()->id;
        $addon->addon_category_id = $request->addon_category_id;

        try {
            $addon->save();

            return redirect()->back()->with(['success' => 'Addon Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function getEditAddon($id): View
    {
        $addon = Addon::where('id', $id)->first();
        $addonCategories = AddonCategory::all();

        return view('admin.editAddon', [
            'addon' => $addon,
            'addonCategories' => $addonCategories,
        ]);
    }

    public function updateAddon(Request $request): RedirectResponse
    {
        $addon = Addon::where('id', $request->id)->first();

        if ($addon) {
            $addon->name = $request->name;
            $addon->price = $request->price;
            $addon->addon_category_id = $request->addon_category_id;

            try {
                $addon->save();

                return redirect()->back()->with(['success' => 'Addon Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th->getMessage()]);
            }
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

    public function addonsOfAddonCategory($id): View
    {
        $addons = Addon::orderBy('id', 'DESC')->where('addon_category_id', $id)->with('addon_category')->paginate(20);
        $count = $addons->total();
        $addonCategories = AddonCategory::all();

        return view('admin.addons', [
            'addons' => $addons,
            'count' => $count,
            'addonCategories' => $addonCategories,
        ]);
    }

    public function itemcategories(): View
    {
        $itemCategories = ItemCategory::orderBy('id', 'DESC')->with('user')->get();
        $itemCategories->loadCount('items');

        $count = count($itemCategories);

        return view('admin.itemcategories', [
            'itemCategories' => $itemCategories,
            'count' => $count,
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
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function disableCategory($id)
    {
        $itemCategory = ItemCategory::where('id', $id)->first();
        if ($itemCategory) {
            $itemCategory->toggleEnable()->save();
            if (\Illuminate\Support\Facades\Request::ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('admin.itemcategories');
        }
    }

    public function updateItemCategory(Request $request): RedirectResponse
    {
        $itemCategory = ItemCategory::where('id', $request->id)->firstOrFail();
        $itemCategory->name = $request->name;
        $itemCategory->save();

        return redirect()->back()->with(['success' => 'Operation Successful']);
    }

    public function pages(): View
    {
        $pages = Page::all();

        return view('admin.pages', [
            'pages' => $pages,
        ]);
    }

    public function saveNewPage(Request $request): RedirectResponse
    {
        $page = new Page();
        $page->name = $request->name;
        $page->slug = Str::slug($request->slug, '-');
        $page->body = $request->body;

        try {
            $page->save();

            return redirect()->back()->with(['success' => 'New Page Created']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function getEditPage($id)
    {
        $page = Page::where('id', $id)->first();

        if ($page) {
            return view('admin.editPage', [
                'page' => $page,
            ]);
        } else {
            return redirect()->route('admin.pages');
        }
    }

    public function updatePage(Request $request): RedirectResponse
    {
        $page = Page::where('id', $request->id)->first();

        if ($page) {
            $page->name = $request->name;
            $page->slug = Str::slug($request->slug, '-');
            $page->body = $request->body;
            try {
                $page->save();

                return redirect()->back()->with(['success' => 'Page Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th->getMessage()]);
            }
        } else {
            return redirect()->route('admin.pages');
        }
    }

    public function deletePage($id): RedirectResponse
    {
        $page = Page::where('id', $id)->first();
        if ($page) {
            $page->delete();

            return redirect()->back()->with(['success' => 'Deleted']);
        } else {
            return redirect()->route('admin.pages');
        }
    }

    public function restaurantpayouts(): View
    {
        $count = RestaurantPayout::count();

        $restaurantPayouts = RestaurantPayout::orderBy('id', 'DESC')->paginate(20);

        return view('admin.restaurantPayouts', [
            'restaurantPayouts' => $restaurantPayouts,
            'count' => $count,
        ]);
    }

    public function viewRestaurantPayout($id): View
    {
        $restaurantPayout = RestaurantPayout::where('id', $id)->first();

        if ($restaurantPayout) {
            $payoutData = StorePayoutDetail::where('restaurant_id', $restaurantPayout->restaurant->id)->first();
            if ($payoutData) {
                $payoutData = json_decode($payoutData->data);
            } else {
                $payoutData = null;
            }

            return view('admin.viewRestaurantPayout', [
                'restaurantPayout' => $restaurantPayout,
                'payoutData' => $payoutData,
            ]);
        }
    }

    public function updateRestaurantPayout(Request $request): RedirectResponse
    {
        $restaurantPayout = RestaurantPayout::where('id', $request->id)->first();

        if ($restaurantPayout) {
            $restaurantPayout->status = $request->status;
            $restaurantPayout->transaction_mode = $request->transaction_mode;
            $restaurantPayout->transaction_id = $request->transaction_id;
            $restaurantPayout->message = $request->message;
            try {
                $restaurantPayout->save();

                return redirect()->back()->with(['success' => 'Restaurant Payout Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th->getMessage()]);
            }
        }
    }

    public function fixUpdateIssues(): RedirectResponse
    {
        try {
            $duplicates = AcceptDelivery::whereIn('order_id', function ($query) {
                $query->select('order_id')->from('accept_deliveries')->groupBy('order_id')->havingRaw('count(*) > 1');
            })->get();

            foreach ($duplicates as $duplicate) {
                if ($duplicate->is_completed == 0 && ($duplicate->order->orderstatus_id == 5 || $duplicate->order->orderstatus_id == 6)) {
                    //just delete
                    $duplicate->delete(); //delete the duplicate entry in db
                }

                if ($duplicate->is_completed == 0 && $duplicate->order->orderstatus_id == 3) {
                    //delete and change orderstatus to 2
                    $duplicate->order->orderstatus_id = 2; //change order status to not delivery assigned
                    $duplicate->order->save(); //save the order
                    $duplicate->delete(); //delete the duplicate entry in db
                }
            }

            // ** MIGRATE ** //
            //first migrate the db if any new db are avaliable...
            Artisan::call('migrate', [
                '--force' => true,
            ]);
            Artisan::call('module:migrate', [
                '--force' => true,
            ]);
            // ** MIGRATE END ** //

            // ** SETTINGS ** //
            $data = file_get_contents(storage_path('data/data.json'));
            $data = json_decode($data);
            $dbSet = [];
            foreach ($data as $s) {
                //check if the setting key already exists, if exists, do nothing..
                $settingAlreadyExists = Setting::where('key', $s->key)->first();
                //else create an array of settings which doesnt exists...
                if (!$settingAlreadyExists) {
                    $dbSet[] = [
                        'key' => $s->key,
                        'value' => $s->value,
                    ];
                }
            }
            //insert new settings keys into settings table.
            DB::table('settings')->insert($dbSet);
            // ** SETTINGS END ** //

            // ** PAYMENTGATEWAYS ** //
            // check if paystack is installed
            $hasPayStack = PaymentGateway::where('name', 'PayStack')->first();
            if (!$hasPayStack) {
                //if not, then install new payment gateway "PayStack"
                $payStackPaymentGateway = new PaymentGateway();
                $payStackPaymentGateway->name = 'PayStack';
                $payStackPaymentGateway->description = 'PayStack Payment Gateway';
                $payStackPaymentGateway->is_active = 0;
                $payStackPaymentGateway->save();
            }
            // check if razorpay is installed
            $hasRazorPay = PaymentGateway::where('name', 'Razorpay')->first();
            if (!$hasRazorPay) {
                //if not, then install new payment gateway "Razorpay"
                $razorPayPaymentGateway = new PaymentGateway();
                $razorPayPaymentGateway->name = 'Razorpay';
                $razorPayPaymentGateway->description = 'Razorpay Payment Gateway';
                $razorPayPaymentGateway->is_active = 0;
                $razorPayPaymentGateway->save();
            }
            // ** END PAYMENTGATEWAYS ** //

            $hasPayMongo = PaymentGateway::where('name', 'PayMongo')->first();
            if (!$hasPayMongo) {
                //if not, then install new payment gateway "PayMongo"
                $payMongoPaymentGateway = new PaymentGateway();
                $payMongoPaymentGateway->name = 'PayMongo';
                $payMongoPaymentGateway->description = 'PayMongo Payment Gateway';
                $payMongoPaymentGateway->is_active = 0;
                $payMongoPaymentGateway->save();
            }

            $hasMercadoPago = PaymentGateway::where('name', 'MercadoPago')->first();
            if (!$hasMercadoPago) {
                //if not, then install new payment gateway "MercadoPago"
                $mercadoPagoPaymentGateway = new PaymentGateway();
                $mercadoPagoPaymentGateway->name = 'MercadoPago';
                $mercadoPagoPaymentGateway->description = 'MercadoPago Payment Gateway';
                $mercadoPagoPaymentGateway->is_active = 0;
                $mercadoPagoPaymentGateway->save();
            }

            $hasPaytm = PaymentGateway::where('name', 'Paytm')->first();
            if (!$hasPaytm) {
                //if not, then install new payment gateway "MercadoPago"
                $paytmPaymentGateway = new PaymentGateway();
                $paytmPaymentGateway->name = 'Paytm';
                $paytmPaymentGateway->description = 'Paytm Payment Gateway';
                $paytmPaymentGateway->is_active = 0;
                $paytmPaymentGateway->save();
            }

            $hasFlutterwave = PaymentGateway::where('name', 'Flutterwave')->first();
            if (!$hasFlutterwave) {
                $flutterwavePaymentGateway = new PaymentGateway();
                $flutterwavePaymentGateway->name = 'Flutterwave';
                $flutterwavePaymentGateway->description = 'Flutterwave Payment Gateway';
                $flutterwavePaymentGateway->is_active = 0;
                $flutterwavePaymentGateway->save();
            }

            $hasKhalti = PaymentGateway::where('name', 'Khalti')->first();
            if (!$hasKhalti) {
                $khaltiPaymentGateway = new PaymentGateway();
                $khaltiPaymentGateway->name = 'Khalti';
                $khaltiPaymentGateway->description = 'Khalti Payment Gateway';
                $khaltiPaymentGateway->is_active = 0;
                $khaltiPaymentGateway->save();
            }

            $hasMsg91 = SmsGateway::where('gateway_name', 'MSG91')->first();
            if (!$hasMsg91) {
                //if not, then install new sms gateway gateway "MSG91"
                $msg91Gateway = new SmsGateway();
                $msg91Gateway->gateway_name = 'MSG91';
                $msg91Gateway->save();
            }

            $hasTwilio = SmsGateway::where('gateway_name', 'TWILIO')->first();
            if (!$hasTwilio) {
                //if not, then install new sms gateway gateway "TWILIO"
                $twilioGateway = new SmsGateway();
                $twilioGateway->gateway_name = 'TWILIO';
                $twilioGateway->save();
            }

            // ** ORDERSTATUS ** //
            DB::table('orderstatuses')->truncate();
            DB::statement("INSERT INTO `orderstatuses` (`id`, `name`) VALUES (1, 'Order Placed'), (2, 'Preparing Order'), (3, 'Delivery Guy Assigned'), (4, 'Order Picked Up'), (5, 'Delivered'), (6, 'Canceled'), (7, 'Ready For Pick Up'), (8, 'Awaiting Payment'), (9, 'Payment Failed'), (10, 'Scheduled Order'), (11, 'Confirmed Order')");

            /* Save new keys for translations languages */
            $langData = file_get_contents(storage_path('language/english.json'));
            $a1 = json_decode($langData, true);

            $translations = Translation::all();

            foreach ($translations as $translation) {
                //get the existing data of a translated language
                $a2 = json_decode($translation->data, true);

                //get the difference between the master file and the existing translation, and get the non-existing key
                $diff = array_diff_key($a1, $a2);

                //merge the non existing keys with the existing translation
                $merged = array_merge($a2, $diff);

                //save the translation
                $translation->data = json_encode($merged);
                $translation->save();
            }

            /* Create Permissions */
            Schema::disableForeignKeyConstraints();
            DB::table('permissions')->truncate();
            Schema::enableForeignKeyConstraints();

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            Permission::create(['name' => 'dashboard_view', 'readable_name' => 'View Admin Dashboard']);

            Permission::create(['name' => 'stores_view', 'readable_name' => 'View Stores']);
            Permission::create(['name' => 'stores_edit', 'readable_name' => 'Edit Stores']);
            Permission::create(['name' => 'stores_sort', 'readable_name' => 'Sort Stores']);
            Permission::create(['name' => 'approve_stores', 'readable_name' => 'Approve Pending Stores']);
            Permission::create(['name' => 'stores_add', 'readable_name' => 'Add Store']);
            Permission::create(['name' => 'login_as_store_owner', 'readable_name' => 'Login as Store Owner']);

            Permission::create(['name' => 'addon_categories_view', 'readable_name' => 'View Addon Categories']);
            Permission::create(['name' => 'addon_categories_edit', 'readable_name' => 'Edit Addon Categories']);
            Permission::create(['name' => 'addon_categories_add', 'readable_name' => 'Add Addon Category']);

            Permission::create(['name' => 'addons_view', 'readable_name' => 'View Addons']);
            Permission::create(['name' => 'addons_edit', 'readable_name' => 'Edit Addons']);
            Permission::create(['name' => 'addons_add', 'readable_name' => 'Add Addon']);
            Permission::create(['name' => 'addons_actions', 'readable_name' => 'Addon Actions']);

            Permission::create(['name' => 'menu_categories_view', 'readable_name' => 'View Menu Categories']);
            Permission::create(['name' => 'menu_categories_edit', 'readable_name' => 'Edit Menu Categories']);
            Permission::create(['name' => 'menu_categories_add', 'readable_name' => 'Add Menu Category']);
            Permission::create(['name' => 'menu_categories_actions', 'readable_name' => 'Menu Category Actions']);

            Permission::create(['name' => 'items_view', 'readable_name' => 'View Items']);
            Permission::create(['name' => 'items_edit', 'readable_name' => 'Edit Items']);
            Permission::create(['name' => 'items_add', 'readable_name' => 'Add Item']);
            Permission::create(['name' => 'items_actions', 'readable_name' => 'Item Actions']);

            Permission::create(['name' => 'all_users_view', 'readable_name' => 'View All Users']);
            Permission::create(['name' => 'all_users_edit', 'readable_name' => 'Edit All Users']);
            Permission::create(['name' => 'all_users_wallet', 'readable_name' => 'Users Wallet Transactions']);

            Permission::create(['name' => 'delivery_guys_view', 'readable_name' => 'View Delivery Guy Users']);
            Permission::create(['name' => 'delivery_guys_manage_stores', 'readable_name' => 'Manage Delivery Guy Stores']);

            Permission::create(['name' => 'store_owners_view', 'readable_name' => 'View Store Owner Users']);
            Permission::create(['name' => 'store_owners_manage_stores', 'readable_name' => 'Manage Store Owner Stores']);

            Permission::create(['name' => 'order_view', 'readable_name' => 'View Orders']);
            Permission::create(['name' => 'order_actions', 'readable_name' => 'Order Actions']);

            Permission::create(['name' => 'promo_sliders_manage', 'readable_name' => 'Manage Promo Sliders']);
            Permission::create(['name' => 'store_category_sliders_manage', 'readable_name' => 'Manage Category Sliders']);
            Permission::create(['name' => 'coupons_manage', 'readable_name' => 'Manage Coupons']);
            Permission::create(['name' => 'pages_manage', 'readable_name' => 'Manage Pages']);
            Permission::create(['name' => 'popular_location_manage', 'readable_name' => 'Manage Popular Geo Locations']);
            Permission::create(['name' => 'send_notification_manage', 'readable_name' => 'Send Notifications']);
            Permission::create(['name' => 'store_payouts_manage', 'readable_name' => 'Manage Store Payouts']);
            Permission::create(['name' => 'translations_manage', 'readable_name' => 'Manage Translations']);
            Permission::create(['name' => 'delivery_collection_manage', 'readable_name' => 'Manage Delivery Collection']);
            Permission::create(['name' => 'delivery_collection_logs_view', 'readable_name' => 'View Delivery Collection Logs']);
            Permission::create(['name' => 'wallet_transactions_view', 'readable_name' => 'View Wallet Transactions']);
            Permission::create(['name' => 'reports_view', 'readable_name' => 'View Reports']);

            Permission::create(['name' => 'settings_manage', 'readable_name' => 'Manage Settings']);

            Permission::create(['name' => 'login_as_customer', 'readable_name' => 'Login as Customer']);

            $user = User::where('id', '1')->first();
            $user->givePermissionTo(Permission::all());
            /* END Create Permission and add all permissions to Admin */

            /*restaurant zone fixes */
            $restaurants = Restaurant::with('items', 'orders', 'restaurant_earnings', 'restaurant_payouts')->get();
            foreach ($restaurants as $restaurant) {
                $restaurantItemIds = [];
                //restaurant items
                foreach ($restaurant->items as $restaurantItem) {
                    array_push($restaurantItemIds, $restaurantItem->id);
                }
                $restaurantOrderIds = [];
                //restaurant orders
                foreach ($restaurant->orders as $restaurantOrder) {
                    array_push($restaurantOrderIds, $restaurantOrder->id);
                }

                $restaurantEarningsIds = [];
                //restaurant earnings
                foreach ($restaurant->restaurant_earnings as $restaurantEarning) {
                    array_push($restaurantEarningsIds, $restaurantEarning->id);
                }

                $restaurantPayoutsIds = [];
                //restaurant payouts
                foreach ($restaurant->restaurant_payouts as $restaurantPayout) {
                    array_push($restaurantPayoutsIds, $restaurantPayout->id);
                }

                DB::table('items')->whereIn('id', $restaurantItemIds)->update(['zone_id' => $restaurant->zone_id]);
                DB::table('orders')->whereIn('id', $restaurantOrderIds)->update(['zone_id' => $restaurant->zone_id]);
                DB::table('restaurant_earnings')->whereIn('id', $restaurantEarningsIds)->update(['zone_id' => $restaurant->zone_id]);
                DB::table('restaurant_payouts')->whereIn('id', $restaurantPayoutsIds)->update(['zone_id' => $restaurant->zone_id]);
            }
            /* END */

            /* END Save new keys for translations languages */
            /** CLEAR LARAVEL CACHES **/
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            /** END CLEAR LARAVEL CACHES **/

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function addMoneyToWallet(Request $request): RedirectResponse
    {
        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            try {
                $user->deposit($request->add_amount * 100, ['description' => $request->add_amount_description]);

                $alert = new PushNotify();
                $alert->sendWalletAlert($request->user_id, $request->add_amount, $request->add_amount_description, $type = 'deposit');

                return redirect()->back()->with(['success' => config('setting.walletName') . ' Updated']);
            } catch (\Illuminate\Database\QueryException $qe) {
                return redirect()->back()->with(['message' => $qe->getMessage()]);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            } catch (\Throwable $th) {
                return redirect()->back()->with(['message' => $th->getMessage()]);
            }
        }
    }

    public function substractMoneyFromWallet(Request $request): RedirectResponse
    {
        $user = User::where('id', $request->user_id)->first();

        if ($user) {
            if ($user->balanceFloat * 100 >= $request->substract_amount * 100) {
                try {
                    $user->withdraw($request->substract_amount * 100, ['description' => $request->substract_amount_description]);

                    $alert = new PushNotify();
                    $alert->sendWalletAlert($request->user_id, $request->substract_amount, $request->substract_amount_description, $type = 'withdraw');

                    return redirect()->back()->with(['success' => config('setting.walletName') . ' Updated']);
                } catch (\Illuminate\Database\QueryException $qe) {
                    return redirect()->back()->with(['message' => $qe->getMessage()]);
                } catch (Exception $e) {
                    return redirect()->back()->with(['message' => $e->getMessage()]);
                } catch (\Throwable $th) {
                    return redirect()->back()->with(['message' => $th->getMessage()]);
                }
            } else {
                return redirect()->back()->with(['message' => 'Substract amount is less that the user balance amount.']);
            }
        }
    }

    public function walletTransactions(): View
    {
        $count = $transactions = Transaction::count();

        $transactions = Transaction::orderBy('id', 'DESC')->paginate(20);

        return view('admin.viewAllWalletTransactions', [
            'transactions' => $transactions,
            'count' => $count,
        ]);
    }

    public function searchWalletTransactions(Request $request): View
    {
        $query = $request['query'];

        $transactions = Transaction::where('uuid', 'LIKE', '%' . $query . '%')
            ->paginate(20);

        $count = $transactions->total();

        return view('admin.viewAllWalletTransactions', [
            'transactions' => $transactions,
            'query' => $query,
            'count' => $count,
        ]);
    }

    public function cancelOrderFromAdmin(Request $request, TranslationHelper $translationHelper): RedirectResponse
    {
        $keys = ['orderRefundWalletComment', 'orderPartialRefundWalletComment'];
        $translationData = $translationHelper->getDefaultLanguageValuesForKeys($keys);

        $order = Order::where('id', $request->order_id)->first();

        $user = User::where('id', $order->user_id)->first();
        $admin = Auth::user();

        try {
            if ($order->orderstatus_id != 5 || $order->orderstatus_id != 6) {
                //5 = completed, 6 = canceled

                //check refund type

                switch ($request->refund_type) {
                    case 'NOREFUND':
                        if ($order->wallet_amount != null) {
                            $user->deposit($order->wallet_amount * 100, ['description' => $translationData->orderRefundWalletComment . $order->unique_order_id]);
                        }
                        activity()
                            ->performedOn($order)
                            ->causedBy($admin)
                            ->withProperties(['type' => 'Order_Canceled'])->log('Order canceled');
                        break;

                    case 'FULL':
                        $user->deposit($order->total * 100, ['description' => $translationData->orderRefundWalletComment . $order->unique_order_id]);
                        activity()
                            ->performedOn($order)
                            ->causedBy($admin)
                            ->withProperties(['type' => 'Order_Canceled'])->log('Order canceled with Full Refund');

                        break;

                    case 'HALF':
                        $user->deposit($order->total / 2 * 100, ['description' => $translationData->orderPartialRefundWalletComment . $order->unique_order_id]);
                        activity()
                            ->performedOn($order)
                            ->causedBy($admin)
                            ->withProperties(['type' => 'Order_Canceled'])->log('Order canceled with Half Refund');
                        break;
                }

                //cancel order
                $order->orderstatus_id = 6; //6 means canceled..
                $order->save();

                //throw notification to user
                if (config('setting.enablePushNotificationOrders') == 'true') {
                    $notify = new PushNotify();
                    $notify->sendPushNotification('6', $order->user_id, $order->unique_order_id);
                }

                return redirect()->back()->with(['success' => 'Operation Successful']);
            }
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function acceptOrderFromAdmin(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $order = Order::where('id', $request->id)->with('restaurant')->first();

        if ($order->orderstatus_id == '1' || $order->orderstatus_id == '11') {
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
                ->withProperties(['type' => 'Order_Accepted'])->log('Order accepted');

            return redirect()->back()->with(['success' => 'Order Accepted']);
        } else {
            return redirect()->back()->with(['message' => 'Something went wrong.']);
        }
    }

    public function assignDeliveryFromAdmin(Request $request): RedirectResponse
    {
        // dd($request->all());
        $user = Auth::user();

        $deliveryUser = User::where('id', $request->user_id)->first();
        if (!$deliveryUser) {
            abort(404, 'Delivery Guy not found');
        }

        DB::beginTransaction();
        try {
            $order = Order::where('id', $request->order_id)
                ->with('restaurant')
                ->lockForUpdate()
                ->first();

            $assignment = new AcceptDelivery;
            $assignment->order_id = $order->id;
            $assignment->user_id = $deliveryUser->id;
            $assignment->customer_id = $request->customer_id;
            $assignment->is_complete = 0;
            $assignment->created_at = Carbon::now();
            $assignment->updated_at = Carbon::now();
            $assignment->save();

            $order->orderstatus_id = 3;
            $order->save();

            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties(['type' => 'Order_Assigned'])->log('Order assigned to Delivery Guy');

            DB::commit();

            if (config('setting.enablePushNotificationOrders') == 'true') {
                $notify = new PushNotify();
                $notify->sendPushNotification('3', $order->user_id, $order->unique_order_id);
            }

            // Send SMS Notification to Delivery Guy
            if (config('setting.smsDeliveryNotify') == 'true') {
                $message = config('setting.defaultSmsDeliveryMsg');
                $otp = null;
                $smsnotify = new Sms();
                $smsnotify->processSmsAction('OD_NOTIFY', $deliveryUser->phone, $otp, $message);
            }

            // Send Push Notification to Delivery Guy
            if (config('setting.enablePushNotificationOrders') == 'true') {
                if (config('setting.hasSocketPush') != 'true') {
                    $notify = new PushNotify();
                    $notify->sendPushNotification('TO_DELIVERY', $deliveryUser->id, $order->unique_order_id);
                } else {
                    if (config('setting.iHaveFoodomaaDeliveryApp') == 'true') {
                        stopPlayingNotificationSoundDeliveryAppHelper($order);
                        $deliveryGuyIds = [$deliveryUser->id];
                        $notify = new SocketPush();
                        $notify->pushNewOrder($order->unique_order_id, $deliveryGuyIds);
                    }
                }
            }

            return redirect()->back()->with(['success' => 'Order Assigned']);
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return redirect()->back()->with(['message' => 'Delivery already accepted']);
            }
        }
    }

    public function reAssignDeliveryFromAdmin(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $deliveryUser = User::where('id', $request->user_id)->first();
        if (!$deliveryUser) {
            abort(404, 'Delivery Guy not found');
        }

        $order = Order::where('id', $request->order_id)->firstOrFail();

        switch ($order->orderstatus_id) {
            case '5':
                return redirect()->back()->with(['message' => 'Cannot assign delivery guy to a completed order.']);
            case '6':
                return redirect()->back()->with(['message' => 'Cannot assign delivery guy to a cancelled order.']);
        }

        $assignment = AcceptDelivery::where('order_id', $request->order_id)->first();
        $assignment->user_id = $deliveryUser->id;
        $assignment->is_complete = 0;
        $assignment->updated_at = Carbon::now();
        $assignment->save();

        // Send SMS Notification to Delivery Guy
        if (config('setting.smsDeliveryNotify') == 'true') {
            $message = config('setting.defaultSmsDeliveryMsg');
            $otp = null;
            $smsnotify = new Sms();
            $smsnotify->processSmsAction('OD_NOTIFY', $deliveryUser->phone, $otp, $message);
        }

        // Send Push Notification to Delivery Guy
        if (config('setting.enablePushNotificationOrders') == 'true') {
            if (config('setting.hasSocketPush') != 'true') {
                $notify = new PushNotify();
                $notify->sendPushNotification('TO_DELIVERY', $deliveryUser->id, $order->unique_order_id);
            } else {
                if (config('setting.iHaveFoodomaaDeliveryApp') == 'true') {
                    stopPlayingNotificationSoundDeliveryAppHelper($order);
                    $deliveryGuyIds = [$deliveryUser->id];
                    $notify = new SocketPush();
                    $notify->pushNewOrder($order->unique_order_id, $deliveryGuyIds);
                }
            }
        }

        activity()
            ->performedOn($order)
            ->causedBy($user)
            ->withProperties(['type' => 'Order_Reassigned'])->log('Order re-assigned to Delivery Guy');

        return redirect()->back()->with(['success' => 'Order reassigned successfully']);
    }

    public function popularGeoLocations(Request $request): View
    {
        $locations = PopularGeoPlace::orderBy('id', 'DESC')->paginate(20);
        $count = $locations->total();

        $primaryLocation = PopularGeoPlace::where('is_default', '1')->first();
        if (!$primaryLocation) {
            if ($count > 0) {
                $message = 'Create atleast one primary business location or set one as primary location (click the check mark button)';
                $request->session()->flash('message', $message);
            } else {
                $message = 'Create atleast one primary business location';
                $request->session()->flash('message', $message);
            }
        }

        return view('admin.popularGeoLocations', [
            'locations' => $locations,
            'count' => $count,
        ]);
    }

    public function saveNewPopularGeoLocation(Request $request): RedirectResponse
    {
        $existing = PopularGeoPlace::count();
        if ($existing == 0) {
            $setPrimary = true;
        } else {
            $setPrimary = false;
        }

        $location = new PopularGeoPlace();

        $location->name = $request->name;

        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;

        if ($request->is_active == 'true') {
            $location->is_active = true;
        } else {
            $location->is_active = false;
        }

        $location->is_default = $setPrimary;

        try {
            $location->save();

            return redirect()->back()->with(['success' => 'Location Saved']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function disablePopularGeoLocation($id): RedirectResponse
    {
        $location = PopularGeoPlace::where('id', $id)->first();

        if ($location) {
            if ($location->is_default) {
                return redirect()->back()->with(['message' => 'Primary location cannot be disabled.']);
            }

            $location->toggleActive()->save();

            return redirect()->back()->with(['success' => 'Location Disabled']);
        } else {
            return redirect()->route('admin.popularGeoLocations');
        }
    }

    public function deletePopularGeoLocation($id): RedirectResponse
    {
        $location = PopularGeoPlace::where('id', $id)->first();

        if ($location) {
            if ($location->is_default) {
                return redirect()->back()->with(['message' => 'Primary location cannot be deleted.']);
            }

            $location->delete();

            return redirect()->route('admin.popularGeoLocations')->with(['success' => 'Location Deleted']);
        } else {
            return redirect()->route('admin.popularGeoLocations');
        }
    }

    public function makeDefaultLocation($id): RedirectResponse
    {
        $location = PopularGeoPlace::where('id', $id)->firstOrFail();

        //remove default of other
        $currentDefaults = PopularGeoPlace::where('is_default', '1')->get();
        if (!empty($currentDefaults)) {
            foreach ($currentDefaults as $currentDefault) {
                $currentDefault->is_default = 0;
                $currentDefault->save();
            }
        }

        $location->is_active = 1;
        $location->is_default = 1;
        $location->save();

        return redirect()->back()->with(['success' => 'Primary location updated successfully.']);
    }

    public function translations(): View
    {
        $translations = Translation::orderBy('id', 'DESC')->get();
        $count = count($translations);

        return view('admin.translations', [
            'translations' => $translations,
            'count' => $count,
        ]);
    }

    public function newTranslation(): View
    {
        return view('admin.newTranslation');
    }

    public function saveNewTranslation(Request $request): RedirectResponse
    {
        // dd($request->all());
        // dd(json_encode($request->except(['language_name'])));

        $translation = new Translation();

        $translation->language_name = $request->language_name;
        $translation->data = json_encode($request->except(['language_name', '_token']));

        try {
            $translation->save();

            return redirect()->route('admin.translations')->with(['success' => 'Translation Created']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function editTranslation($id)
    {
        $translation = Translation::where('id', $id)->first();
        // dd(json_decode($translation->data));

        if ($translation) {
            return view('admin.editTranslation', [
                'translation_id' => $translation->id,
                'language_name' => $translation->language_name,
                'data' => json_decode($translation->data),
            ]);
        } else {
            return redirect()->route('admin.translations')->with(['message' => 'Translation Not Found']);
        }
    }

    public function updateTranslation(Request $request): RedirectResponse
    {
        $translation = Translation::where('id', $request->translation_id)->first();

        $translation->language_name = $request->language_name;
        $translation->data = json_encode($request->except(['translation_id', 'language_name', '_token']));

        try {
            $translation->save();

            return redirect()->back()->with(['success' => 'Translation Updated']);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => $qe->getMessage()]);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function disableTranslation($id): RedirectResponse
    {
        $translation = Translation::where('id', $id)->first();
        if ($translation) {
            $translation->toggleEnable()->save();

            return redirect()->back()->with(['success' => 'Operation Successful']);
        } else {
            return redirect()->route('admin.translations');
        }
    }

    public function deleteTranslation($id): RedirectResponse
    {
        $translation = Translation::where('id', $id)->first();
        if ($translation) {
            $translation->delete();

            return redirect()->route('admin.translations')->with(['success' => 'Translation Deleted']);
        } else {
            return redirect()->route('admin.translations');
        }
    }

    public function makeDefaultLanguage($id): RedirectResponse
    {
        $translation = Translation::where('id', $id)->firstOrFail();

        //remove default of other
        $currentDefaults = Translation::where('is_default', '1')->get();
        // dd($currentDefault);
        if (!empty($currentDefaults)) {
            foreach ($currentDefaults as $currentDefault) {
                $currentDefault->is_default = 0;
                $currentDefault->save();
            }
        }

        //make this default
        $translation->is_default = 1;
        $translation->is_active = 1;
        $translation->save();

        return redirect()->back()->with(['success' => 'Operation Successful']);
    }

    public function updateRestaurantScheduleData(Request $request): RedirectResponse
    {
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

    public function impersonate($id): RedirectResponse
    {
        $user = User::where('id', $id)->first();
        if ($user && $user->hasRole('Store Owner')) {
            Auth::user()->impersonate($user);

            return redirect()->route('get.login');
        } else {
            return redirect()->route('admin.dashboard')->with(['message' => 'User not found']);
        }
    }

    public function approvePaymentOfOrder($order_id)
    {
        $user = Auth::user();

        $order = Order::where('id', $order_id)->with('restaurant')->firstOrFail();

        if ($order->orderstatus_id == '8') {
            if ($order->restaurant->auto_acceptable) {
                $orderstatus_id = '2';
                if (Module::find('OrderSchedule') && Module::find('OrderSchedule')->isEnabled() && $order->schedule_date != null && $order->schedule_slot != null) {
                    $orderstatus_id = '10';
                }
            } else {
                $orderstatus_id = '1';
                if (Module::find('OrderSchedule') && Module::find('OrderSchedule')->isEnabled()) {
                    if ($order->schedule_date != null && $order->schedule_slot != null) {
                        $orderstatus_id = '10';
                    }
                }
            }

            $order->orderstatus_id = $orderstatus_id;
            $order->save();

            if ($order->restaurant->auto_acceptable) {
                if ($orderstatus_id == '2') {
                    //to user
                    $notify = new PushNotify();
                    $notify->sendPushNotification('2', $order->user_id, $order->unique_order_id);
                    //to delivery
                    sendSmsToDelivery($order->restaurant_id);
                    sendPushNotificationToDelivery($order->restaurant_id, $order);
                }

                sendPushNotificationToStoreOwner($order->restaurant_id, $order->unique_order_id);
            } else {
                sendSmsToStoreOwner($order->restaurant_id, $order->total);
                sendPushNotificationToStoreOwner($order->restaurant_id, $order->unique_order_id);
            }

            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties(['type' => 'Payment_Approved'])->log('Order payment approved');

            if ($order->orderstatus_id == '2') {
                activity()
                    ->performedOn($order)
                    ->causedBy(User::find(1))
                    ->withProperties(['type' => 'Order_Accepted_Auto'])->log('Order auto accepted');
            }

            return redirect()->back()->with(['success' => 'Payment Approved']);
        } else {
            return 'Error! Payment already approved.';
        }
    }

    public function updateStorePayoutDetails(Request $request): RedirectResponse
    {
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
            return redirect()->back()->with(['message' => $th->getMessage()]);
        }
    }

    public function confirmScheduledOrder($id): RedirectResponse
    {
        $user = Auth::user();

        $order = Order::where('id', $id)->firstOrFail();

        if ($order->orderstatus_id == '10') {
            $order->orderstatus_id = 11;
            $order->save();

            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties(['type' => 'Confirm_Scheduled_Order'])->log('Scheduled order confirmed');

            return redirect()->back()->with(['success' => 'Scheduled order confirmed.']);
        } else {
            return redirect()->back()->with(['message' => 'Something went wrong']);
        }
    }

    public function acceptNotice(): JsonResponse
    {
        $setting = Setting::where('key', 'moduleRedownloadNotice')->first();
        $setting->value = 'true';
        $setting->save();
        Artisan::call('cache:clear');

        $response = [
            'success' => true,
        ];

        return response()->json($response, 200);
    }

    public function deleteUserAddress(Request $request): RedirectResponse
    {
        $user = User::where('id', $request->user_id)->firstOrFail();

        $defaultAddressId = $user->default_address_id;
        if ($defaultAddressId != $request->address_id) {
            $address = Address::where('id', $request->address_id)->first();
            if ($address) {
                $address->delete();

                return redirect(route('admin.get.editUser', $request->user_id) . $request->window_redirect_hash)->with(['success' => 'Address deleted']);
            } else {
                return redirect(route('admin.get.editUser', $request->user_id) . $request->window_redirect_hash)->with(['message' => 'Address not found']);
            }
        } else {
            return redirect(route('admin.get.editUser', $request->user_id) . $request->window_redirect_hash)->with(['message' => 'Primary address cannot be deleted']);
        }
    }

    /* Custom Functions */

    public function getSettingsRestaurant($id): View
    {
        $restaurant_settings = RestaurantSettings::where('restaurant_id', $id)->first();
        $restaurant = Restaurant::where('id', $id)->first();


        return view('admin.settingsRestaurant', [
            'restaurant_id' => $id,
            'restaurant' => $restaurant,
            'restaurant_settings' => $restaurant_settings,
        ]);
    }

    public function saveRestaurantSettings(Request $request): RedirectResponse
    {
        //$restaurant_settings = RestaurantSettings::where('restaurant_id', $id)->first();
        // $restaurant_settings = new RestaurantSettings();

        //if($request->action == 'save'){
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
            'booking_custom_date_fieldidx' => $request->bepoz_booking_custom_date_fieldidx,
            'booking_pax_fieldidx' => $request->bepoz_booking_pax_fieldidx,
            'booking_name_fieldidx' => $request->bepoz_booking_name_fieldidx,
            'booking_comment_fieldidx' => $request->bepoz_booking_comment_fieldidx,
            'booking_option' => $request->bepoz_booking_option,
            'booking_number_fieldidx' => $request->bepoz_booking_number_fieldidx
        ];
        $restaurant = Restaurant::where('id', $request->restaurant_id)->first();
        if ($restaurant) {
            if (isset($request->sommelier_online_enb) && $request->sommelier_online_enb == 'yes') {
                $settings['sommelier_online'] = $request->sommelier_online_enb;
                $restaurant->is_active = 1;
            } else {
                $settings['sommelier_online'] = 'no';
                $restaurant->is_active = 0;
            }
            $restaurant->save();
        }

        if (isset($request->sommelier_online_enb) && $request->sommelier_online_enb == 'yes') {
            $settings['sommelier_online'] = $request->sommelier_online_enb;
        } else {
            $settings['sommelier_online'] = 'no';
        }

        if (isset($request->sommelier_reservations_enb) && $request->sommelier_reservations_enb == 'yes') {
            $settings['sommelier_reservations'] = $request->sommelier_reservations_enb;
        } else {
            $settings['sommelier_reservations'] = 'no';
        }

        if (isset($request->sommelier_functions_enb) && $request->sommelier_functions_enb == 'yes') {
            $settings['sommelier_functions'] = $request->sommelier_functions_enb;
        } else {
            $settings['sommelier_functions'] = 'no';
        }

        if (isset($request->somemmlier_loyalty_enb) && $request->somemmlier_loyalty_enb == 'yes') {
            $settings['somemmlier_loyalty'] = $request->somemmlier_loyalty_enb;
        } else {
            $settings['somemmlier_loyalty'] = 'no';
        }

        if (isset($request->sommelier_time_attendance_enb) && $request->sommelier_time_attendance_enb == 'yes') {
            $settings['sommelier_time_attendance'] = $request->sommelier_time_attendance_enb;
        } else {
            $settings['sommelier_time_attendance'] = 'no';
        }

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
        // Initialize cURL session for system check
        // $booking = Booking::latest('id')->first();
        $curl = curl_init();
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
                'bepoz_till_id: ' . $request->bepoz_till_id,
                'bepoz_operator_id: ' . $request->bepoz_operator_id,
                'bepoz_offline_pay: ' . $request->bepoz_offline_pay,
                'bepoz_online_payment: ' . $request->bepoz_online_payment,
                'bepoz_booking_option: ' . $request->bepoz_booking_option,
                'bepoz_booking_custom: ' . $request->bepoz_booking_custom,
                'bepoz_delivery_plu: ' . $request->bepoz_delivery_plu,
                'bepoz_discount_plu: ' . $request->bepoz_discount_plu,
                'bepoz_surcharge_plu: ' . $request->bepoz_surcharge_plu,
                'bepoz_tip_plu: ' . $request->bepoz_tip_plu,
                'bepoz_booking_plu: ' . $request->bepoz_booking_plu,
                'bepoz_table_group: ' . $request->bepoz_table_group,
                'bepoz_order_table_group: ' . $request->bepoz_order_table_group,
                'bepoz_self_pickup_order_type: ' . $request->bepoz_self_pickup_order_type,
                'bepoz_delivery_order_type: ' . $request->bepoz_delivery_order_type,
                'bepoz_account_group: ' . $request->bepoz_account_group,
                'bepoz_loyalty_account_group: ' . $request->bepoz_loyalty_account_group,
                'bepoz_booking_account_group: ' . $request->bepoz_booking_account_group,
            ],
        ]);

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            Log::info("beposz accounts res :: " . $error_msg);

            curl_close($curl);
            return redirect()->back()->with(['message' => 'Error during system check: ' . $error_msg]);
        }
        curl_close($curl);

        // $fields = [
        //     [
        //         "FieldType" => 1,
        //         "FieldIdx" => $request->bepoz_booking_custom_date_fieldidx,
        //         "Data" => $booking->booking_datetime
        //     ],
        //     [
        //         "FieldType" => 2,
        //         "FieldIdx" => $request->bepoz_booking_pax_fieldidx,
        //         "Data" => $booking->no_of_seats
        //     ],
        //     [
        //         "FieldType" => 3,
        //         "FieldIdx" => $request->bepoz_booking_name_fieldidx,
        //         "Data" => $booking->booking_name
        //     ],
        //     [
        //         "FieldType" => 3,
        //         "FieldIdx" => $request->bepoz_booking_comment_fieldidx,
        //         "Data" => $booking->comments
        //     ]
        // ];

        // $errors = [];
        // foreach ($fields as $field) {
        //     $jsonData = json_encode([$field]);

        //     // Initialize cURL session for custom field set
        //     $curl = curl_init();
        //     curl_setopt_array($curl, [
        //         CURLOPT_URL => $request->bepoz_url . '/api/accounts/customfield/set',
        //         CURLOPT_RETURNTRANSFER => true,
        //         CURLOPT_ENCODING => '',
        //         CURLOPT_MAXREDIRS => 10,
        //         CURLOPT_TIMEOUT => 0,
        //         CURLOPT_FOLLOWLOCATION => true,
        //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //         CURLOPT_CUSTOMREQUEST => 'PUT',
        //         CURLOPT_POSTFIELDS => $jsonData,
        //         CURLOPT_HTTPHEADER => [
        //             'Content-Type: application/json',
        //             'Content-Length: ' . strlen($jsonData)
        //         ],
        //     ]);

        //     $response = curl_exec($curl);
        //     if (curl_errno($curl)) {
        //         $errors[] = 'Error during custom field set for ' . json_encode($field) . ': ' . curl_error($curl);
        //     } else {
        //         $res = json_decode($response, true);
        //         if (empty($res['message']) || $res['message'] != 'Success') {
        //             $errors[] = 'Error during custom field set for ' . json_encode($field) . ': ' . $response;
        //         }
        //     }

        //     curl_close($curl);
        // }

        if (empty($errors)) {
            return redirect()->back()->with(['success' => 'Connection established successfully.']);
        } else {
            return redirect()->back()->with(['message' => 'There were some errors with the connection: ' . implode(', ', $errors)]);
        }
    }


    public function tableShiftRestaurant($id): View
    {
        $shif_settings = ShiftInformation::where('restaurant_id', $id)->first();
        $table_info = TableInformation::where('restaurant_id', $id)->get();

        return view('admin.tableShiftRestaurant', [
            'restaurant_id' => $id,
            'shif_settings' => $shif_settings,
            'table_info' => $table_info,
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
        ];

        $shiftinforations = ShiftInformation::updateOrCreate(
            ['restaurant_id' => $request->restaurant_id],
            $settings
        );

        if (!empty($request->table_info)) {
            TableInformation::where('restaurant_id', $request->restaurant_id)->delete();
            foreach ($request->table_info as $val) {
                if (!empty($val['table_number']) && !empty($val['no_of_seats'])) {
                    $res_settings = [
                        'table_number' => $val['table_number'],
                        'total_seats' => $val['no_of_seats'],
                    ];
                    TableInformation::updateOrCreate(
                        ['restaurant_id' => $request->restaurant_id, 'id' => $val['table_info_id']],
                        $res_settings
                    );
                }
            }
        }

        return redirect()->back()->with(['success' => 'Restaurant Settings Saved']);
    }
}
