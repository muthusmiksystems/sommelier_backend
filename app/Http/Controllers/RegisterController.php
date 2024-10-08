<?php

namespace App\Http\Controllers;

use App\DeliveryGuyDetail;
use App\SmsOtp;
use App\User;
use Auth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function registerRestaurantDelivery(Request $request): RedirectResponse
    {
        // Validate incoming request data
        $validator = $request->validate([
            'captcha' => ['required'],
            'email' => ['required', 'string', 'email', 'max:180', 'unique:users'],
            'phone' => ['required', 'string', 'min:8', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'captcha.required' => 'Captcha is a required field.',
            'email.required' => 'Email is a required field.',
            'email.string' => 'Email is invalid.',
            'email.email' => 'Email is invalid.',
            'email.max' => 'Email must be within 190 characters.',
            'email.unique' => 'This email address is already registered.',
            'phone.required' => 'Phone is a required field.',
            'phone.string' => 'Phone is invalid.',
            'phone.min' => 'Phone must be at least 8 characters.',
            'phone.unique' => 'This phone number is already registered.',
            'password.required' => 'Password is a required field.',
            'password.string' => 'Password is invalid.',
            'password.min' => 'Password must be at least 6 characters long.',
        ]);
        
        try {
            // Store user input in session for later use
            $request->session()->put('user_data', $request->all());
            $otpTable = SmsOtp::where('email', $request->email)->first();
            // Generate OTP and save it to database
            $otp = $this->sendOTPEmail($request->email);

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

            // Redirect to OTP verification page with email data
            return redirect()->route('get.deliveryotp', ['email' => $request->email]);
        } catch (\Illuminate\Database\QueryException $qe) {
            return redirect()->back()->with(['message' => 'Something went wrong1. Please try again.']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => 'Something went wrong2. Please try again.' . $e]);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['message' => 'Something went wrong3. Please try again.']);
        }
        try {

            $user = User::create([
                'name' => $request->first_name. ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => \Hash::make($request->password),
            ]);

            if (isset($request->role)) {

                if ($request->role == 'DELIVERY') {
                    $user->assignRole('Delivery Guy');

                    $deliveryGuyDetails = new DeliveryGuyDetail();
                    $deliveryGuyDetails->name = $request->first_name;

                    $deliveryGuyDetails->save();
                    $user->delivery_guy_detail_id = $deliveryGuyDetails->id;
                    $user->save();

                    //return session message...
                    return redirect()->back()->with(['delivery_register_message' => 'Delivery User Registered', 'success' => 'Delivery User Registered']);
                }
                if ($request->role == 'RESOWN') {
                    $user->assignRole('Store Owner');
                    // login and redirect to dashbaord...
                    Auth::loginUsingId($user->id);
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
                    if ($request->hasFile('licence_photo')) {
                        $photo = $request->file('licence_photo');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $deliveryGuyDetails->photo = $filename;
                    }
                    if ($request->hasFile('vehicle_registration')) {
                        $photo = $request->file('vehicle_registration');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $deliveryGuyDetails->photo = $filename;
                    }
                    if ($request->hasFile('certificate')) {
                        $photo = $request->file('certificate');
                        $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                        Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                        $deliveryGuyDetails->photo = $filename;
                    }
                    if ($request->hasFile('police_clearence_certificate')) {
                        $photo = $request->file('police_clearence_certificate');
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
                }

            } else {
                $user->assignRole('Customer');

                return redirect()->back()->with(['success' => 'User Created']);
            }

            return redirect()->back()->with(['success' => 'User Created']);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to register user. Please try again.');
        }
    }

    private function sendOTPEmail($email)
    {
        $otp = $this->generateOTP();
        return $otp;
        $data['customer_email'] = $email;
        $data['otp'] = $otp;

        Mail::send('emails.loginotp', ['mailData' => $data], function ($message) use ($data) {
            $message->subject('OTP Login');
            $message->from(config('setting.sendEmailFromEmailAddress'), config('setting.sendEmailFromEmailName'));
            $message->to($data['customer_email']);
        });

        return $otp;
    }

    private function generateOTP()
    {
        return rand(100000, 999999);
    }
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $storedOTP = SmsOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();
        $user_data = $request->session()->get('user_data');
        if ($request->otp == $storedOTP->otp) {
            // OTP verification successful
            // Proceed with user creation

            try {

                $user = User::create([
                    'name' => $user_data['first_name'] . ' ' . $user_data['last_name'],
                    'email' => $user_data['email'],
                    'phone' => $user_data['phone'],
                    'password' => \Hash::make($user_data['password']),
                ]);

                if (isset($user_data['role'])) {

                    if ($user_data['role'] == 'DELIVERY') {
                        $user->assignRole('Delivery Guy');

                        $deliveryGuyDetails = new DeliveryGuyDetail();
                        $deliveryGuyDetails->name = $user_data['first_name'];

                        $deliveryGuyDetails->save();
                        $user->delivery_guy_detail_id = $deliveryGuyDetails->id;
                        $user->save();

                        //return session message...
                        return redirect()->back()->with(['delivery_register_message' => 'Delivery User Registered', 'success' => 'Delivery User Registered']);
                    }
                    if ($request->role == 'RESOWN') {
                        $user->assignRole('Store Owner');
                        // login and redirect to dashbaord...
                        Auth::loginUsingId($user->id);
                    }
                    if ($user->hasRole('Delivery Guy')) {
                        $deliveryGuyDetails = new DeliveryGuyDetail();
                        $deliveryGuyDetails->name = $user_data['delivery_name'];
                        $deliveryGuyDetails->age = $user_data['delivery_age'];
                        if ($user_data->hasFile('delivery_photo')) {
                            $photo = $user_data->file('delivery_photo');
                            $filename = time() . Str::random(10) . '.' . strtolower($photo->getClientOriginalExtension());
                            Image::make($photo)->resize(250, 250)->save(base_path('/assets/img/delivery/' . $filename));
                            $deliveryGuyDetails->photo = $filename;
                        }
                        $deliveryGuyDetails->description = $user_data['delivery_description'];
                        $deliveryGuyDetails->vehicle_number = $user_data['delivery_vehicle_number'];
                        if ($user_data['delivery_commission_rate'] != null) {
                            $deliveryGuyDetails->commission_rate = $user_data['delivery_commission_rate'];
                        }
                        if ($user_data['tip_commission_rate'] != null) {
                            $deliveryGuyDetails->tip_commission_rate = $user_data['tip_commission_rate'];
                        }
                        if ($user_data['cash_limit'] != null) {
                            $deliveryGuyDetails->cash_limit = $user_data['cash_limit'];
                        } else {
                            $deliveryGuyDetails->cash_limit = 0;
                        }

                        $deliveryGuyDetails->save();
                        $user->delivery_guy_detail_id = $deliveryGuyDetails->id;
                        $user->save();
                    }

                } else {
                    $user->assignRole('Customer');

                    return redirect()->back()->with(['success' => 'User Created']);
                }

                return redirect()->back()->with(['success' => 'User Created']);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to register user. Please try again.');
            }
        } else {
            // Invalid OTP
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
        }
    }
    public function showotp(Request $request)
    {
        return view('auth.deliveryotp', ['email' => $request->email]);
    }
}
