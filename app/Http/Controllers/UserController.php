<?php

namespace App\Http\Controllers;

use App\AcceptDelivery;
use App\Address;
use App\Mail\OtpMail;
use App\Order;
use App\Providers\AuthServiceProvider;
use App\Rating;
use App\Restaurant;
use App\RestaurantCustomerModel;
use App\RestaurantUser;
use App\ShiftInformation;
use App\SmsOtp;
use App\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Ixudra\Curl\Facades\Curl;
use JWTAuthException;
use Throwable;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Intervention\Image\ImageManagerStatic as Image;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Message;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * @return mixed
     */
    // protected $firebaseOTPService;

    // public function __construct(AuthServiceProvider $firebaseOTPService)
    // {
    //     $this->firebaseOTPService = $firebaseOTPService;
    // }
    private function getToken($email, $password)
    {
        // $token = null;
        $token = auth()->attempt(['email' => $email, 'password' => $password ? $password : '']);
        // $token = '12345678esdfghjkdrcfvgbhjn';
        try {
            if (!$token) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid..',
                    'token' => $token,
                ]);
            }
        } catch (UserNotDefinedException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }

        return $token;
    }
    private function getsocialToken($email)
    {
        $token = null;

        try {
            // Retrieve password securely (e.g., from user login form)
            $password = '12345';

            // Attempt authentication and token generation
            // $token = auth()->attempt(['email' => $email, 'password' => $password]);
            $token = '12345678esdfghjkdrcfvgbhjn';
            if (!$token) {
                // Handle authentication failure
                return response()->json([
                    'response' => 'error',
                    'message' => 'Invalid credentials or user does not exist.'
                ], 401); // Unauthorized status code
            }
        } catch (JWTException $e) {
            // Handle general JWT exceptions
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed.'
            ], 500); // Internal Server Error status code
        }

        return $token;
    }

    public function login(Request $request): JsonResponse
    {
        $store= $request->storeId || $request->storeSlug;
        if(!$store)
        {
            $response = ['success' => false, 'data' => 'Store Not Select'];
            return response()->json($response);
        }
        $user = User::where('email', $request->email)->get()->first();
        $otpTable = SmsOtp::where('email', $request->email)->first();
        if ($request->loginType != null) {
            if ($otpTable->otp != $request->otp) {
                $response = ['success' => false, 'data' => 'Otp_not_match'];
                return response()->json($response);
            }
            if ($user && $request->email === $user->email) { // The passwords match...
                SmsOtp::where('email', $request->email)->delete();
                $token = self::getsocialToken($request->email);
                $user->auth_token = $token;

                // Add address if address present
                if (isset($request->address['lat'])) {
                    $address = new Address();
                    $address->user_id = $user->id;
                    $address->latitude = $request->address['lat'];
                    $address->longitude = $request->address['lng'];
                    $address->address = $request->address['address'];
                    $address->house = $request->address['house'];
                    $address->tag = $request->address['tag'];
                    $address->save();
                    $user->default_address_id = $address->id;
                }

                $user->save();
                if ($user->default_address_id !== 0) {
                    $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                } else {
                    $default_address = null;
                }

                $running_order = null;
                if (isset($request->storeId)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);

                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $request->storeId)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $request->storeId,
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
                if (isset($request->storeSlug)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);
                        $restaurantData = Restaurant::where('slug',$request->storeSlug)->first();
                    
                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $restaurantData->id)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $restaurantData->id,
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
                $response = [
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'auth_token' => $token,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'dob' => $user->dob,
                        'default_address_id' => $user->default_address_id,
                        'default_address' => $default_address,
                        'wallet_balance' => $user->balanceFloat,
                        'avatar' => $user->avatar,
                        'tax_number' => $user->tax_number,
                        'is_fingerprint' => $user->is_fingerprint
                    ],
                    'running_order' => $running_order,
                ];

                return response()->json($response, 201);
            } else {
                $response = ['success' => false, 'data' => 'DONOTMATCH' . $user];

                return response()->json($response, 201);
            }
        }
        //check if it is coming from social login,
        if ($request->accessToken != null) {
            //check socialtoken validation
            $validation = $this->validateAccessToken($request->email, $request->provider, $request->accessToken);
            if ($validation) {
                if ($user) {
                    //user exists -> check if user has phone
                    if ($user->phone != null) {
                        // user has phone
                        //LOGIN USER
                        $token = auth()->login($user);
                        $user->auth_token = $token;

                        // Add address if address present
                        if (isset($request->address['lat'])) {
                            $address = new Address();
                            $address->user_id = $user->id;
                            $address->latitude = $request->address['lat'];
                            $address->longitude = $request->address['lng'];
                            $address->address = $request->address['address'];
                            $address->house = $request->address['house'];
                            $address->tag = $request->address['tag'];
                            $address->save();
                            $user->default_address_id = $address->id;
                        }

                        $user->save();
                        if ($user->default_address_id !== 0) {
                            $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                        } else {
                            $default_address = null;
                        }

                        $running_order = null;

                        $response = [
                            'success' => true,
                            'data' => [
                                'id' => $user->id,
                                'auth_token' => $token,
                                'name' => $user->first_name . ' ' . $user->last_name,
                                'first_name' => $user->first_name,
                                'last_name' => $user->last_name,
                                'email' => $user->email,
                                'phone' => $user->phone,
                                'dob' => $user->dob,
                                'default_address_id' => $user->default_address_id,
                                'default_address' => $default_address,
                                'wallet_balance' => $user->balanceFloat,
                                'avatar' => $user->avatar,
                                'tax_number' => $user->tax_number,
                                'is_fingerprint' => $user->is_fingerprint
                            ],
                            'running_order' => $running_order,
                        ];

                        return response()->json($response);
                    }
                    if ($request->phone != null) {
                        $checkPhone = User::where('phone', $request->phone)->first();
                        if ($checkPhone) {
                            $response = [
                                'email_phone_already_used' => true,
                            ];

                            return response()->json($response);
                        } else {
                            try {
                                $user->phone = $request->phone;
                                $user->save();
                                $token = JWTAuth::fromUser($user);
                                $user->auth_token = $token;

                                // Add address if address present
                                if (isset($request->address['lat'])) {
                                    $address = new Address();
                                    $address->user_id = $user->id;
                                    $address->latitude = $request->address['lat'];
                                    $address->longitude = $request->address['lng'];
                                    $address->address = $request->address['address'];
                                    $address->house = $request->address['house'];
                                    $address->tag = $request->address['tag'];
                                    $address->save();
                                    $user->default_address_id = $address->id;
                                }

                                $user->save();
                            } catch (Throwable $e) {
                                $response = ['success' => false, 'data' => 'Something went wrong. Please try again...'];

                                return response()->json($response, 201);
                            }

                            if ($user->default_address_id !== 0) {
                                $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                            } else {
                                $default_address = null;
                            }

                            $running_order = null;

                            $response = [
                                'success' => true,
                                'data' => [
                                    'id' => $user->id,
                                    'auth_token' => $token,
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'phone' => $user->phone,
                                    'default_address_id' => $user->default_address_id,
                                    'default_address' => $default_address,
                                    'wallet_balance' => $user->balanceFloat,
                                    'avatar' => $user->avatar,
                                    'tax_number' => $user->tax_number,
                                    'is_fingerprint' => $user->is_fingerprint
                                ],
                                'running_order' => $running_order,
                            ];

                            return response()->json($response);
                        }
                    } else {
                        $response = [
                            'enter_phone_after_social_login' => true,
                        ];

                        return response()->json($response);
                    }
                } else {
                    // there is no user with this email..

                    if ($request->phone != null) {
                        $checkPhone = User::where('phone', $request->phone)->first();
                        if ($checkPhone) {
                            $response = [
                                'email_phone_already_used' => true,
                            ];

                            return response()->json($response);
                        } else {
                            enSovCheck($request);

                            //reg user
                            $user = new User();
                            $user->name = $request->name;
                            $user->email = $request->email;
                            $user->phone = $request->phone;
                            $user->password = Hash::make(Str::random(8));
                            $user->user_ip = $request->ip();

                            try {
                                $user->save();
                                $user->assignRole('Customer');
                                $token = JWTAuth::fromUser($user);
                                $user->auth_token = $token;

                                // Add address if address present
                                if (isset($request->address['lat'])) {
                                    $address = new Address();
                                    $address->user_id = $user->id;
                                    $address->latitude = $request->address['lat'];
                                    $address->longitude = $request->address['lng'];
                                    $address->address = $request->address['address'];
                                    $address->house = $request->address['house'];
                                    $address->tag = $request->address['tag'];
                                    $address->save();
                                    $user->default_address_id = $address->id;
                                }

                                $user->save();
                            } catch (Throwable $e) {
                                $response = ['success' => false, 'data' => 'Something went wrong. Please try again...'];

                                return response()->json($response, 201);
                            }

                            if ($user->default_address_id !== 0) {
                                $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                            } else {
                                $default_address = null;
                            }

                            $running_order = null;

                            $response = [
                                'success' => true,
                                'data' => [
                                    'id' => $user->id,
                                    'auth_token' => $token,
                                    'name' => $user->name,
                                    'first_name' => $user->first_name,
                                    'last_name' => $user->last_name,
                                    'email' => $user->email,
                                    'phone' => $user->phone,
                                    'dob' => $user->dob,
                                    'default_address_id' => $user->default_address_id,
                                    'default_address' => $default_address,
                                    'wallet_balance' => $user->balanceFloat,
                                    'avatar' => $user->avatar,
                                    'tax_number' => $user->tax_number,
                                ],
                                'running_order' => $running_order,
                            ];

                            return response()->json($response);
                        }
                    } else {
                        // SHOW ENTER PHONE NUMBER
                        $response = [
                            'enter_phone_after_social_login' => true,
                        ];

                        return response()->json($response);
                    }

                    return response()->json($response);
                }
            } else {
                $response = false;

                return response()->json($response);
            }
        }
        // if user exists, check user
        if ($otpTable->otp != $request->otp) {
            $response = ['success' => false, 'data' => 'Otp_not_match'];
            return response()->json($response);
        }
        if ($request->otp != null) {
            if ($otpTable->otp == $request->otp && $user) { // The passwords match...
                SmsOtp::where('email', $request->email)->delete();
                // $token = self::getToken($request->email, $request->otp);
                // Auth::login($user);
                SmsOtp::where('email', $request->email)->delete();
                
                $token = JWTAuth::fromUser($user);
                $user->auth_token = $token;

                // Add address if address present
                if (isset($request->address['lat'])) {
                    $address = new Address();
                    $address->user_id = $user->id;
                    $address->latitude = $request->address['lat'];
                    $address->longitude = $request->address['lng'];
                    $address->address = $request->address['address'];
                    $address->house = $request->address['house'];
                    $address->tag = $request->address['tag'];
                    $address->save();
                    $user->default_address_id = $address->id;
                }

                $user->save();
                if ($user->default_address_id !== 0) {
                    $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                } else {
                    $default_address = null;
                }

                $running_order = null;
                if (isset($request->storeId)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);

                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $request->storeId)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $request->storeId,
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
                if (isset($request->storeSlug)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);
                        $restaurantData = Restaurant::where('slug',$request->storeSlug)->first();
                        
                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $restaurantData->id)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $restaurantData->id,
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
                $response = [
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'auth_token' => $token,
                        'name' => $user->name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'dob' => $user->dob,
                        'default_address_id' => $user->default_address_id,
                        'default_address' => $default_address,
                        'wallet_balance' => $user->balanceFloat,
                        'avatar' => $user->avatar,
                        'tax_number' => $user->tax_number,
                    ],
                    'running_order' => $running_order,
                ];

                return response()->json($response, 201);
            } else {
                $response = ['success' => false, 'data' => 'DONOTMATCH'];

                return response()->json($response, 201);
            }
        }
    }

    public function loginWithOtp(Request $request): JsonResponse
    {
        $otpTable = SmsOtp::where('phone', $request->phone)->first();
        if ($otpTable && $request->otp == $otpTable->otp) {
            //check if user exists...
            $user = User::where('phone', $request->phone)->first();

            if ($user) {
                //user exists, save the address and login user
                if (isset($request->address['lat'])) {
                    $address = new Address();
                    $address->user_id = $user->id;
                    $address->latitude = $request->address['lat'];
                    $address->longitude = $request->address['lng'];
                    $address->address = $request->address['address'];
                    $address->house = $request->address['house'];
                    $address->tag = $request->address['tag'];
                    $address->save();
                    $user->default_address_id = $address->id;
                }
                $token = JWTAuth::fromUser($user);
                $user->auth_token = $token;

                $user->save();

                if ($user->default_address_id !== 0) {
                    $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                } else {
                    $default_address = null;
                }
                $running_order = null;

                $response = [
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'auth_token' => $token,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'default_address_id' => $user->default_address_id,
                        'default_address' => $default_address,
                        'wallet_balance' => $user->balanceFloat,
                        'avatar' => $user->avatar,
                        'tax_number' => $user->tax_number,
                    ],
                    'running_order' => $running_order,
                ];

                return response()->json($response);
            } else {
                //new user...

                $randomPassword = Str::random(8);
                $payload = [
                    'password' => Hash::make($randomPassword),
                    'email' => $request->email,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'auth_token' => '',
                    'user_ip' => $request->ip(),
                ];

                // try {

                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'phone' => ['required'],
                ]);

                $user = new User($payload);
                if ($user->save()) {
                    $token = self::getToken($request->email, $randomPassword); // generate user token

                    if (!is_string($token)) {
                        return response()->json(['success' => false, 'data' => 'Token generation failed'], 201);
                    }

                    $user = User::where('email', $request->email)->get()->first();

                    $user->auth_token = $token; // update user token

                    // Add address if address present
                    if (isset($request->address['lat'])) {
                        $address = new Address();
                        $address->user_id = $user->id;
                        $address->latitude = $request->address['lat'];
                        $address->longitude = $request->address['lng'];
                        $address->address = $request->address['address'];
                        $address->house = $request->address['house'];
                        $address->tag = $request->address['tag'];
                        $address->save();
                        $user->default_address_id = $address->id;
                    }

                    $user->save();
                    $user->assignRole('Customer');

                    if ($user->default_address_id !== 0) {
                        $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                    } else {
                        $default_address = null;
                    }

                    $response = [
                        'success' => true,
                        'data' => [
                            'id' => $user->id,
                            'auth_token' => $token,
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'default_address_id' => $user->default_address_id,
                            'default_address' => $default_address,
                            'wallet_balance' => $user->balanceFloat,
                            'avatar' => $user->avatar,
                            'tax_number' => $user->tax_number,
                        ],
                        'running_order' => null,
                    ];

                    return response()->json($response);
                } else {
                    $response = ['success' => false, 'data' => 'Couldnt register user'];
                }
                // } catch (Throwable $e) {
                //     $response = ['success' => false, 'data' => 'Couldnt register user.'];
                //     return response()->json($response, 201);
                // }
            }
        } else {
            //otp not present... error
            $response = ['success' => false, 'data' => 'DONOTMATCH'];

            return response()->json($response, 201);
        }
    }

    public function register(Request $request): JsonResponse
    {
        enSovCheck($request);
        $user = User::where('email', $request->email)->first();
        // $restaurantuser = User::findOrFail($user->id);

        if($user){
            // Retrieve the role_id for 'Customer'
        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

        // Check if the record already exists
        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
            ->where('restaurant_id', $request->storeId)
            ->where('role_id', $role_id)
            ->first();

        // If the record does not exist, create it
        if (!$existingRecord) {
            RestaurantCustomerModel::create([
                'role_id' => $role_id,
                'user_id' => $user->id,
                'restaurant_id' => $request->storeId,
            ]);
        }
        }
        $store= $request->storeId || $request->storeSlug;
        if(!$store)
        {
            $response = ['success' => false, 'data' => 'Store Not Select'];
            return response()->json($response);
        }
        $otpTable = SmsOtp::where('email', $request->email)->first();
        if ($request->loginType != null) {

            $checkEmail = User::where('email', $request->email)->first();
            $checkPhone = User::where('phone', $request->phone)->first();
            if (!$request->phone) {
                $response = [
                    'enter_phone_after_social_login' => true,
                ];

                return response()->json($response);
            }
            if ($otpTable->otp != $request->otp) {
                $response = ['success' => false, 'data' => 'Otp_not_match'];
                return response()->json($response);
            }
            if ($checkPhone || $checkEmail) {
                $response = [
                    'email_phone_already_used' => true,
                ];

                return response()->json($response);
            }


            $payload = [
                // 'password' => Hash::make($request->password),
                'email' => $request->email,
                /*'name' => $request->name, */
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'dob' => $request->dob,
                'loginType' => $request->loginType,
                'auth_token' => '',
                'default_address' => $request->address,
                'user_ip' => $request->ip(),
            ];

            try {
                $request->validate([
                    'first_name' => ['required', 'string', 'max:255'],
                    // 'last_name' => [ 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    // 'password' => ['required', 'string', 'min:8'],
                    // 'phone' => ['required'],
                ]);

                $user = new User($payload);

                if ($user->save()) {
                    SmsOtp::where('email', $request->email)->delete();
                    $token = self::getsocialToken($request->email); // generate user token
                    if (!is_string($token)) {
                        return response()->json(['success' => false, 'data' => $token], 201);
                    }

                    $user = User::where('email', $request->email)->get()->first();

                    $user->auth_token = $token; // update user token
                    $user->dob = $request->dob;
                    // Add address if address present
                    if (isset($request->address['lat'])) {
                        $address = new Address();
                        $address->user_id = $user->id;
                        $address->latitude = $request->address['lat'];
                        $address->longitude = $request->address['lng'];
                        $address->address = $request->address['address'];
                        $address->house = $request->address['house'];
                        $address->tag = $request->address['tag'];
                        $address->save();
                        $user->default_address_id = $address->id;
                    }
                    if (isset($request->storeId)) {
                        try {
                            $user = User::where('email', $request->email)->first();
                            $restaurantuser = User::findOrFail($user->id);
    
                            // Retrieve the role_id for 'Customer'
                            $role_id = DB::table('roles')->where('name', 'Customer')->value('id');
    
                            // Check if the record already exists
                            $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                                ->where('restaurant_id', $request->storeId)
                                ->where('role_id', $role_id)
                                ->first();
    
                            // If the record does not exist, create it
                            if (!$existingRecord) {
                                RestaurantCustomerModel::create([
                                    'role_id' => $role_id,
                                    'user_id' => $user->id,
                                    'restaurant_id' => $request->storeId,
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
                    if (isset($request->storeSlug)) {
                        try {
                            $user = User::where('email', $request->email)->first();
                            $restaurantuser = User::findOrFail($user->id);
                            $restaurantData = Restaurant::where('slug',$request->storeSlug)->first();
                            
                            // Retrieve the role_id for 'Customer'
                            $role_id = DB::table('roles')->where('name', 'Customer')->value('id');
    
                            // Check if the record already exists
                            $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                                ->where('restaurant_id', $restaurantData->id)
                                ->where('role_id', $role_id)
                                ->first();
    
                            // If the record does not exist, create it
                            if (!$existingRecord) {
                                RestaurantCustomerModel::create([
                                    'role_id' => $role_id,
                                    'user_id' => $user->id,
                                    'restaurant_id' => $restaurantData->id,
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

                    $user->save();
                    $user->assignRole('Customer');

                    if ($user->default_address_id !== 0) {
                        $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                    } else {
                        $default_address = null;
                    }

                    $response = [
                        'success' => true,
                        'data' => [
                            'id' => $user->id,
                            'auth_token' => $token,
                            'name' => $request->first_name . ' ' . $request->last_name,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'phone' => $user->phone,
                            'dob' => $request->dob,
                            'default_address_id' => $user->default_address_id,
                            'default_address' => $default_address,
                            'wallet_balance' => $user->balanceFloat,
                            'avatar' => $user->avatar,
                            'tax_number' => $user->tax_number,
                            'is_fingerprint' => $user->is_fingerprint
                        ],
                        'running_order' => null,
                    ];
                } else {
                    $response = ['success' => false, 'data' => 'Couldnt register user'];
                }
            } catch (Throwable $e) {
                $response = ['success' => false, 'data' => 'Couldnt register user'];
                return response()->json($response, 201);
            }

            return response()->json($response, 201);
        }
        $checkEmail = User::where('email', $request->email)->first();
        $checkPhone = User::where('phone', $request->phone)->first();

        // if ($checkPhone || $checkEmail) {
        //     if (isset($request->storeId)) {
        //         $restaurantuser = User::where('email', $request->email)->first();
        //         // $restaurantuser->restaurants()->sync($request->storeId);
        //         if ($restaurantuser) {
        //             // Retrieve the restaurantIds where user_id matches $user->id
        //             $restaurantIds = $restaurantuser->restaurants()->where('user_id', $restaurantuser->id)->get();
        //             // return response()->json($restaurantIds);
        //             // Assuming $restaurantIds is the collection of restaurants

        //             foreach ($restaurantIds as $restaurant) {
        //                 // Accessing the pivot data for the current restaurant
        //                 $pivotData = $restaurant->pivot;

        //                 // Comparing the restaurant_id from pivot data with $request->storeId
        //                 if ($pivotData->restaurant_id === $request->storeId) {
        //                     $response = [
        //                         'email_phone_already_used' => true,
        //                     ];

        //                     return response()->json($response);
        //                 }
        //             }

        //             if ($restaurantIds) {
        //                 // $restaurantuser->restaurants()->sync($request->storeId);
        //                 // $restaurantuser->restaurants()->attach($request->storeId);

        //                 // Save the changes
        //                 // $restaurantuser->save();
        //                 $restaurantuser->save();
        //                 $response = [
        //                     'success' => true,
        //                     'data' => [
        //                         'id' => $restaurantuser->id,
        //                         'auth_token' => $restaurantuser->auth_token,
        //                         'name' => $restaurantuser->first_name . ' ' . $restaurantuser->last_name,
        //                         'first_name' => $restaurantuser->first_name,
        //                         'last_name' => $restaurantuser->last_name,
        //                         'email' => $restaurantuser->email,
        //                         'phone' => $restaurantuser->phone,
        //                         'dob' => $request->dob,
        //                         'default_address_id' => $restaurantuser->default_address_id,
        //                         'default_address' => $restaurantuser->default_address,
        //                         'wallet_balance' => $restaurantuser->balanceFloat,
        //                         'avatar' => $restaurantuser->avatar,
        //                         'tax_number' => $restaurantuser->tax_number,
        //                     ],
        //                     'running_order' => null,
        //                 ];
        //                 return response()->json($response);
        //             }

        //             // Return the response with the restaurant IDs
        //             // return response()->json($restaurantIds, 201);
        //         }
        //         // $restaurantuser->save();

        //     }
        //     // $response = [
        //     //     'email_phone_already_used' => true,
        //     // ];

        //     // return response()->json($response);
        // }

        $payload = [
            // 'password' => Hash::make($request->password),
            'email' => $request->email,
            /*'name' => $request->name, */
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'auth_token' => '',
            'address' => $request->address,
            'user_ip' => $request->ip(),
        ];
        try {
            
            $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                // 'last_name' => [ 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                // 'password' => ['required', 'string', 'min:8'],
                // 'phone' => ['required'],
            ]);
            

            
            $user = User::where('email', $request->email)->get()->first();
            if (!$user) {
                $user = new User($payload);
                $user->save();
            }
            
            if ($otpTable->otp != $request->otp) {
                $response = ['success' => false, 'data' => 'Otp_not_match'];
                return response()->json($response);
            }
            if ($otpTable->otp == $request->otp) {
                // $token = self::getToken($request->email, $request->otp); // generate user token
                $token = JWTAuth::fromUser($user);
                SmsOtp::where('email', $request->email)->delete();
                if (!is_string($token)) {
                    return response()->json(['success' => false, 'data' => 'Couldnt register user'], 201);
                }

                $user = User::where('email', $request->email)->get()->first();

                $user->auth_token = $token; // update user token
                $user->dob = $request->dob;
                // Add address if address present
                if (isset($request->address['lat'])) {
                    $address = new Address();
                    $address->user_id = $user->id;
                    $address->latitude = $request->address['lat'];
                    $address->longitude = $request->address['lng'];
                    $address->address = $request->address['address'];
                    $address->house = $request->address['house'];
                    $address->tag = $request->address['tag'];
                    $address->save();
                    $user->default_address_id = $address->id;
                }
                if (isset($request->storeId)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);

                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $request->storeId)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $request->storeId,
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
                if (isset($request->storeSlug)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);
                        $restaurantData = Restaurant::where('slug',$request->storeSlug)->first();
                        
                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $restaurantData->id)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $restaurantData->id,
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

                $user->save();
                $user->assignRole('Customer');

                if ($user->default_address_id !== 0) {
                    $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                } else {
                    $default_address = null;
                }
                if (isset($request->storeId)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);

                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $request->storeId)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $request->storeId,
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
                if (isset($request->storeSlug)) {
                    try {
                        $user = User::where('email', $request->email)->first();
                        $restaurantuser = User::findOrFail($user->id);
                        $restaurantData = Restaurant::where('slug',$request->storeSlug)->first();
                       
                        // Retrieve the role_id for 'Customer'
                        $role_id = DB::table('roles')->where('name', 'Customer')->value('id');

                        // Check if the record already exists
                        $existingRecord = RestaurantCustomerModel::where('user_id', $user->id)
                            ->where('restaurant_id', $restaurantData->id)
                            ->where('role_id', $role_id)
                            ->first();

                        // If the record does not exist, create it
                        if (!$existingRecord) {
                            RestaurantCustomerModel::create([
                                'role_id' => $role_id,
                                'user_id' => $user->id,
                                'restaurant_id' => $restaurantData->id,
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
                $response = [
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'auth_token' => $token,
                        'name' => $request->first_name . ' ' . $request->last_name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'dob' => $request->dob,
                        'default_address_id' => $user->default_address_id,
                        'default_address' => $default_address,
                        'wallet_balance' => $user->balanceFloat,
                        'avatar' => $user->avatar,
                        'tax_number' => $user->tax_number,
                        'is_fingerprint' => $user->is_fingerprint
                    ],
                    'running_order' => null,
                ];
            } else {
                $response = ['success' => false, 'data' => 'Couldnt register user'];
            }
        } 
        // catch (Throwable $e) {
        //     $response = ['success' => false, 'data' => 'Couldnt register user.', $e];

        //     return response()->json($response, 201);
        // }
        catch (Throwable $e) {
            \Log::error('Error in register function: ' . $e->getMessage());
            $response = ['success' => false, 'data' => 'Could not register user. ' . $e->getMessage()];
            return response()->json($response, 404);
        }        
        // \Log::info("response== " . $response);
        return response()->json($response, 201);
    }

    public function updateUserInfo(Request $request): JsonResponse
    {
        // $user = auth()->user();
        $user = User::where('id', $request->user_id)->get()->first();
        if ($user) {
            if ($user->default_address_id !== 0) {
                $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
            } else {
                $default_address = null;
            }

            $running_order = Order::where('user_id', $user->id)
                ->whereIn('orderstatus_id', ['1', '2', '3', '4', '7', '8'])
                ->where('unique_order_id', $request->unique_order_id)
                ->with('restaurant')
                ->first();

            $delivery_details = null;
            if ($running_order) {
                if ($running_order->orderstatus_id == 3 || $running_order->orderstatus_id == 4) {
                    //get assigned delivery guy and get the details to show to customers
                    $delivery_guy = AcceptDelivery::where('order_id', $running_order->id)->first();
                    if ($delivery_guy) {
                        $delivery_user = User::where('id', $delivery_guy->user_id)->first();
                        $delivery_details = $delivery_user->delivery_guy_detail;
                        if (!empty($delivery_details)) {
                            $delivery_details = $delivery_details->toArray();
                            $delivery_details['phone'] = $delivery_user->phone;
                        }

                        $ratings = Rating::where('delivery_id', $delivery_user->id)->select(['rating_delivery', 'review_delivery'])->get();
                        $averageRating = number_format((float) $ratings->avg('rating_delivery'), 1, '.', '');
                        $delivery_details['rating'] = $averageRating;
                    }
                }
            }

            $response = [
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'auth_token' => $user->auth_token,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'default_address_id' => $user->default_address_id,
                    'default_address' => $default_address,
                    'wallet_balance' => $user->balanceFloat,
                    'avatar' => $user->avatar,
                    'tax_number' => $user->tax_number,
                    'is_fingerprint' => $user->is_fingerprint
                ],
                'running_order' => $running_order,
                'delivery_details' => $delivery_details,
            ];

            return response()->json($response);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }
    public function updateUserProfile(Request $request): JsonResponse
    {
        // Find the user by ID
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Check if the provided phone number is already registered
        if ($request->phone && $request->phone !== $user->phone) {
            $existingUser = User::where('phone', $request->phone)->first();
            if ($existingUser) {
                return response()->json(['success' => false, 'message' => 'Phone number already registered']);
            }
        }

        // Update user's address
        $address = Address::where('user_id', $user->id)->first();
        if ($address) {
            $address->address = $request->address ? $request->address : $address->address;
            $address->save();
            // return response()->json(['success' => false, 'message' => 'adress success'], 404);
        } else {
            // If no address exists, create a new one
            $address = new Address();
            $address->user_id = $user->id;
            $address->address = $request->address;
            $address->save();
        }

        // Update user profile
        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name = $request->last_name ?? $user->last_name;
        $user->email = $request->email ?? $user->email;
        $user->phone = $request->phone ?? $user->phone;
        $user->dob = $request->dob ?? $user->dob;
        $user->default_address_id = $address->id;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'image_' . time() . '.' . $image->getClientOriginalExtension();
            // Debugging
            $imagePath = '/public/assets/img/profile/' . $imageName;

            if ($image->move(public_path('assets/img/profile/'), $imageName)) {
                // Image moved successfully
                $user->avatar = $imagePath;
            } else {
                // Image move failed
                $error = error_get_last();
                if ($error) {
                    // Log or return the error
                    return response()->json(['success' => false, 'message' => 'Failed to move image: ' . $error['message']]);
                }
            }
        }
        $user->save();

        $response = [
            'success' => true,
            'message' => 'User profile updated successfully',
            'data' => [
                'id' => $user->id,
                'auth_token' => $user->auth_token,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'dob' => $user->dob,
                'default_address_id' => $user->default_address_id,
                'default_address' => $address,
                'wallet_balance' => $user->balanceFloat,
                'avatar' => $user->avatar,
                'tax_number' => $user->tax_number,
            ],
        ];

        return response()->json($response);
    }
    public function checkRunningOrder(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $running_order = Order::where('user_id', $user->id)
                ->whereIn('orderstatus_id', ['1', '2', '3', '4', '7'])
                ->get();

            if (count($running_order) > 0) {
                $success = true;

                return response()->json($success);
            } else {
                $success = false;

                return response()->json($success);
            }
        }
    }

    public function validateAccessToken($email, $provider, $accessToken)
    {
        if ($provider == 'facebook') {
            // validate facebook access token
            $curl = Curl::to('https://graph.facebook.com/app/?access_token=' . $accessToken)->get();
            $curl = json_decode($curl);

            if (isset($curl->id)) {
                if ($curl->id == config('setting.facebookAppId')) {
                    return true;
                }

                return false;
            }

            return false;
        }
        if ($provider == 'google') {
            // validate google access token
            $curl = Curl::to('https://www.googleapis.com/oauth2/v3/tokeninfo?access_token=' . $accessToken)->get();
            $curl = json_decode($curl);

            if (isset($curl->email)) {
                echo 'email' . $curl->email;
                if ($curl->email == $email) {
                    return true;
                }

                return false;
            }

            return false;
        }
    }

    public function getWalletTransactions(Request $request): JsonResponse
    {
        $user = auth()->user();
        // $user = auth()->user();
        if ($user) {
            // $balance = sprintf('%.2f', $user->balanceFloat);
            $balance = $user->balanceFloat;
            $transactions = $user->transactions()->orderBy('id', 'DESC')->get();

            $response = [
                'success' => true,
                'balance' => $balance,
                'transactions' => $transactions,
            ];

            return response()->json($response);
        } else {
            $response = [
                'success' => false,
            ];

            return response()->json($response);
        }
    }

    public function changeAvatar(Request $request): JsonResponse
    {
        $user = auth()->user();
        $user->avatar = $request->avatar;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function checkBan(Request $request): JsonResponse
    {
        $user = auth()->user();
        if ($user->is_active) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    /**
     * @param $id
     */
    public function toggleFavorite(Request $request): JsonResponse
    {
        $user = auth()->user();
        $restaurant = Restaurant::find($request->id);
        $restaurant->toggleFavorite();
        $restaurant->makeHidden(['delivery_areas']);
        $restaurant->is_favorited = $restaurant->isFavorited();
        $restaurant->avgRating = storeAvgRating($restaurant->ratings);

        return response()->json($restaurant);
    }

    public function updateTaxNumber(Request $request): JsonResponse
    {
        $user = auth()->user();
        $user->tax_number = $request->tax_number;
        $user->save();

        return response()->json(['success' => true]);
    }

    /* Custom API's Developed */

    public function maitredeGetUser(Request $request): JsonResponse
    {
        try {
            $user = User::where('id', $request->id)->first();

            $response = [
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
            ];
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => 'Couldnt get user.'];

            return response()->json($response, 201);
        }

        return response()->json($response, 201);
    }
    public function getuserbyrestaurantdetails($id): JsonResponse
    {

        return response()->json(201);
    }
    // Function to send OTP via email using SendGrid and return OTP
    private function sendOTPEmail($email, $userEmail)
    {
        // Generate OTP


        if ($userEmail) {
            $restuarant_id = RestaurantCustomerModel::where('user_id', $userEmail->id)->first();
            // return $restuarant_id;
            if ($restuarant_id) {
                $shif_settings = ShiftInformation::where('restaurant_id', $restuarant_id->restaurant_id)->first();
                $data['email_name'] = $shif_settings->teamName;
                $data['email_from'] = $shif_settings->emailFrom;
            } else {
                $data['email_name'] = config('setting.sendEmailFromEmailName');
                $data['email_from'] = config('setting.sendEmailFromEmailAddress');
            }

        } else {
            $data['email_name'] = config('setting.sendEmailFromEmailName');
            $data['email_from'] = config('setting.sendEmailFromEmailAddress');
        }
        $otp = $this->generateOTP();

        $data['customer_email'] = $email;
        $data['otp'] = $otp;
        // return $data['email_from'] . $data['email_name'];
        // Mail::send('emails.loginotp', ['mailData' => $data], function ($message) use ($data) {
        //     $message->subject('OTP Login');
        //     $message->from($data['email_from'], $data['email_name']);
        //     $message->to($data['customer_email']);
        // });
        // SmsOtp::create([
        //     'email' => $email,
        //     'otp' => $otp,
        // ]);
        return $otp;
    }

    public function registerotp(Request $request)
    {
        if(!$request->storeId || $request->storeId == 'undefined')
        {
            $response = ['success' => false, 'data' => 'Store Not Select'];
            return response()->json($response);
        }
        if(!$request->email)
        {
            $response = ['success' => false, 'data' => 'Enter your email ID'];
            return response()->json($response);
        }
        // Check if the email exists in the database
        $userEmail = User::where('email', $request->email)->first();
        $userPhone = User::where('phone', $request->phone)->first();
        $otpTable = SmsOtp::where('email', $request->email)->first();
        if ($userEmail) {
            $existingRecord = RestaurantCustomerModel::where('restaurant_id', $request->storeId)
            ->where('user_id', $userEmail->id)
                            ->first();
            if ($existingRecord) {
                $response = [
                    'email_phone_already_used' => true,
                ];

                return response()->json($response);
            }
            $otp = $this->sendOTPEmail($request->email, $userEmail);
            $response = [
                'otp' => true,
            ];
            if ($otpTable) {
                //phone exists, just update the otp
                $otpTable->otp = $otp;
                $otpTable->save();
            } else {
                $otpTable = new SmsOtp();
                $otpTable->email = $request->email;
                // $otpTable->phone = $request->phone;
                $otpTable->otp = $otp;
                $otpTable->save();
            }
            return response()->json($response);
        } else if (!$userEmail) {
            $otp = $this->sendOTPEmail($request->email, $userEmail);
            $response = [
                'otp' => true,
            ];
            if ($otpTable) {
                //phone exists, just update the otp
                $otpTable->otp = $otp;
                $otpTable->save();
            } else {
                $otpTable = new SmsOtp();
                $otpTable->email = $request->email;
                // $otpTable->phone = $request->phone;
                $otpTable->otp = $otp;
                $otpTable->save();
            }
            return response()->json($response);
        }
        // else {
        //     if ($request->loginType == 'google' && !$request->phone && !$request->loginPage) {
        //         $response = [
        //             'success' => false,
        //             'enter_phone_after_social_login' => true,
        //         ];
        //         return response()->json($response);
        //     }

        //     if (!$userEmail && $request->loginPage) {
        //         $response = ['success' => false, 'data' => 'User not register'];
        //         return response()->json($response);
        //     }
        //     if ($request->loginType == 'google' && $request->phone) {
        //         $otp = $this->sendOTPEmail($request->email, $userEmail);
        //         $response = [
        //             'otp' => true,
        //         ];
        //         if (!$otpTable) {
        //             $otpTable = new SmsOtp();
        //         }
        //         $otpTable->email = $request->email;
        //         $otpTable->phone = $request->phone;
        //         $otpTable->otp = $otp;
        //         $otpTable->save();
        //         return response()->json($response);
        //     }
        //     if (!$userEmail && $request->login == true) {
        //         $response = [
        //             'email_not_register' => true,
        //         ];
        //         return response()->json($response);
        //     }
        //     $otp = $this->sendOTPEmail($request->email, $userEmail);
        //     $response = [
        //         'otp' => true,
        //     ];
        //     if (!$otpTable) {
        //         $otpTable = new SmsOtp();
        //     }
        //     $otpTable->email = $request->email;
        //     $otpTable->phone = $request->phone;
        //     $otpTable->otp = $otp;
        //     $otpTable->save();
        //     return response()->json($response);
        // }
    }

    public function loginotp(Request $request)
    {
        if(!$request->storeId || $request->storeId == 'undefined')
        {
            $response = ['success' => false, 'data' => 'Store Not Select'];
            return response()->json($response);
        }
        if(!$request->email)
        {
            $response = ['success' => false, 'data' => 'Enter your email ID'];
            return response()->json($response);
        }
        // Check if the email exists in the database
        $userEmail = User::where('email', $request->email)->first();
        $userPhone = User::where('phone', $request->phone)->first();
        $otpTable = SmsOtp::where('email', $request->email)->first();
        if ($userEmail) {
            $existingRecord = RestaurantCustomerModel::where('restaurant_id', $request->storeId)
            ->where('user_id', $userEmail->id)
                            ->first();
            if (!$existingRecord) {
                $response = ['success' => false, 'data' => 'User not register'];
                return response()->json($response);
            }
            $otp = $this->sendOTPEmail($request->email, $userEmail);
            $response = [
                'otp' => true,
            ];
            if ($otpTable) {
                //phone exists, just update the otp
                $otpTable->otp = $otp;
                $otpTable->save();
            } else {
                $otpTable = new SmsOtp();
                $otpTable->email = $request->email;
                // $otpTable->phone = $request->phone;
                $otpTable->otp = $otp;
                $otpTable->save();
            }
            return response()->json($response);
        }
        if (!$userEmail) {
            $response = ['success' => false, 'data' => 'User not register'];
                return response()->json($response);
        }
        // else {
        //     if ($request->loginType == 'google' && !$request->phone && !$request->loginPage) {
        //         $response = [
        //             'success' => false,
        //             'enter_phone_after_social_login' => true,
        //         ];
        //         return response()->json($response);
        //     }

        //     if (!$userEmail && $request->loginPage) {
        //         $response = ['success' => false, 'data' => 'User not register'];
        //         return response()->json($response);
        //     }
        //     if ($request->loginType == 'google' && $request->phone) {
        //         $otp = $this->sendOTPEmail($request->email, $userEmail);
        //         $response = [
        //             'otp' => true,
        //         ];
        //         if (!$otpTable) {
        //             $otpTable = new SmsOtp();
        //         }
        //         $otpTable->email = $request->email;
        //         $otpTable->phone = $request->phone;
        //         $otpTable->otp = $otp;
        //         $otpTable->save();
        //         return response()->json($response);
        //     }
        //     if (!$userEmail) {
        //         $response = [
        //             'email_not_register' => true,
        //         ];
        //         return response()->json($response);
        //     }
        //     $otp = $this->sendOTPEmail($request->email, $userEmail);
        //     $response = [
        //         'otp' => true,
        //     ];
        //     if (!$otpTable) {
        //         $otpTable = new SmsOtp();
        //     }
        //     $otpTable->email = $request->email;
        //     $otpTable->phone = $request->phone;
        //     $otpTable->otp = $otp;
        //     $otpTable->save();
        //     return response()->json($response);
        // }
    }

    // Function to generate OTP (placeholder code)
    private function generateOTP()
    {
        // Generate a random 6-digit OTP
        return rand(100000, 999999);
    }

    public function addfingerprintuser(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->is_fingerprint = $request->fingerprint;
            if ($user->save()) {
                if ($user->default_address_id !== 0) {
                    $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
                } else {
                    $default_address = null;
                }
                $running_order = null;
                $response = [
                    'success' => true,
                    'message' => 'Fingerprint added successfully',
                    'data' => [
                        'id' => $user->id,
                        'auth_token' => $user->token,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'default_address_id' => $user->default_address_id,
                        'default_address' => $default_address,
                        'wallet_balance' => $user->balanceFloat,
                        'avatar' => $user->avatar,
                        'tax_number' => $user->tax_number,
                        'is_fingerprint' => $user->is_fingerprint
                    ],
                    'running_order' => $running_order,
                ];
            }

            return response()->json($response);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 201);
        }
    }
    public function getbyuserid(Request $request): JsonResponse
    {
        try {
            $user = User::where('id', $request->id)->first();
            if ($user->default_address_id !== 0) {
                $default_address = Address::where('id', $user->default_address_id)->get(['address', 'house', 'latitude', 'longitude', 'tag'])->first();
            } else {
                $default_address = null;
            }
            $running_order = null;
            $response = [
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'auth_token' => $user->token,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'default_address_id' => $user->default_address_id,
                    'default_address' => $default_address,
                    'wallet_balance' => $user->balanceFloat,
                    'avatar' => $user->avatar,
                    'tax_number' => $user->tax_number,
                    'dob' => $user->dob,
                    'is_fingerprint' => $user->is_fingerprint
                ],
                'running_order' => $running_order,
            ];

            // return response()->json($response, 201);
        } catch (\Throwable $e) {
            $response = ['success' => false, 'data' => 'Couldnt get user.'];

            return response()->json($response, 201);
        }

        return response()->json($response, 201);
    }
    // Function to validate access token (placeholder code)

}
