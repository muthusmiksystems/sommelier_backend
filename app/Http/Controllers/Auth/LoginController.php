<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\SmsOtp;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    public function redirectTo()
    {
        // Admin Role
        if (Auth::user()->hasRole('Admin')) {
            return '/admin/dashboard';
        }
        // Store Owner Role
        elseif (Auth::user()->hasRole('Store Owner')) {
            return '/store-owner/dashboard';
        } elseif (Auth::user()->hasPermissionTo('dashboard_view')) {
            return '/admin/dashboard';
        } else {
            return '/admin/manager';
        }
    }

    protected function authenticated(Request $request, $user)
    {
        //Check user role, if it is not admin then logout
        // if (!$user->hasRole(['Admin', 'Store Owner'])) {
        //     $this->guard()->logout();
        //     $request->session()->invalidate();
        //     return redirect('/auth/login')->withErrors('You are unauthorized to login');
        // }

        if ($user->hasRole(['Customer', 'Delivery Guy'])) {
            $this->guard()->logout();
            $request->session()->invalidate();

            return redirect('/auth/login')->withErrors('You are unauthorized to login');
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @return mixed
     */
    public function logout(Request $request)
    {
        $locale = Session::get('locale');
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request, $locale) ?: redirect('/');
    }

    protected function loggedOut(Request $request, $locale)
    {
        Session::put('locale', $locale);

        return redirect()->route('get.login');
    }
    public function showOtpForm(Request $request)
    {
        return view('auth.otp', ['email' => $request->email]);
    }

    public function loginotp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = User::where('email', $email)->first();
        $otpTable = SmsOtp::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'The provided email is not registered.']);
        }
        $otp = $this->sendOTPEmail($email);

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

        return redirect()->route('get.verifyotp', ['email' => $email]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ]);

        $otpRecord = SmsOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if ($otpRecord && Carbon::now()->lt(Carbon::parse($otpRecord->expires_at))) {
            $user = User::where('email', $request->email)->first();
            Auth::login($user);
            SmsOtp::where('email', $request->email)->delete();

            // return redirect()->route('admin.dashboard');
            $user = Auth::user();
            if ($user) {

                $roles = $user->roles;
                // dd($roles);
                if (Auth::user()->hasRole('Admin')) {
                    return redirect()->route('admin.dashboard');
                } elseif (Auth::user()->hasRole('Store Owner')) {
                    return redirect()->route('restaurant.dashboard');
                } elseif (Auth::user()->hasRole('dashboard_view')) {
                    return redirect()->route('admin.dashboard');
                } else {
                    return redirect()->route('admin.manager');
                }
            }

            return view('auth.login');
        } else {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
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
}
