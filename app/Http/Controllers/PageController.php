<?php

namespace App\Http\Controllers;

use App\Page;
use App\State;
use App\User;
use App\VehicleType;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function indexPage(): RedirectResponse
    {
        return redirect()->route('get.login');
    }

    public function loginPage()
    {
        if (Auth::user()) {
            if (Auth::user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->hasRole('Store Owner')) {
                return redirect()->route('restaurant.dashboard');
            } elseif (Auth::user()->hasPermissionTo('dashboard_view')) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('admin.manager');
            }
        }

        return view('auth.login');
    }

    public function storeRegistration()
    {
        if (Auth::user()) {
            if (Auth::user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->hasRole('Store Owner')) {
                return redirect()->route('restaurant.dashboard');
            }
        }

        return view('auth.storeRegistration');
    }

    public function deliveryRegistration()
    {
        if (Auth::user()) {
            if (Auth::user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->hasRole('Store Owner')) {
                return redirect()->route('restaurant.dashboard');
            }
        }

        return view('auth.deliveryRegistration');
    }

    public function getPages(): JsonResponse
    {
        $pages = Page::where('slug', '!=', 'store-app')->get();

        return response()->json($pages);
    }

    public function getSinglePage(Request $request): JsonResponse
    {
        $page = Page::where('slug', $request->slug)->first();

        if ($page) {
            return response()->json($page);
        } else {
            $page = null;

            return response()->json($page);
        }
    }

    public function forgotPassword()
    {
        if (config('setting.enPassResetEmail') == 'false') {
            abort(404);
        }
        if (Auth::user()) {
            if (Auth::user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->hasRole('Store Owner')) {
                return redirect()->route('restaurant.dashboard');
            }
        }

        return view('auth.forgotPassword');
    }

    public function forgotPasswordSendEmail(Request $request): RedirectResponse
    {
        if (config('setting.enPassResetEmail') == 'false') {
            abort(404);
        }

        $validator = $request->validate(
            [
                'captcha' => ['required', 'captcha'],
                'email' => ['required', 'string', 'email'],
            ],
            [
                'captcha.required' => 'Captcha is a required field.',
                'captcha.captcha' => 'Invalid Captcha',

                'email.required' => 'Email is a required field.',
                'email.string' => 'Email is invalid.',
                'email.email' => 'Email is invalid.',
            ]
        );

        $user = User::where('email', $request->email)->first();
        //if not user, send message, but dont redirect
        if (! $user) {
            return redirect()->back()->with(['resetPasswordMessage' => 'An email will be sent shortly to your email address if an account exists with us.']);
        }

        //generate password reset code...
        // try {
        $token = strtoupper(Str::random(6));
        $exists = DB::table('password_resets')->where('email', $user->email)->first();
        if (! $exists) {
            DB::table('password_resets')->insert([
                'email' => $user->email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);
        } else {
            DB::table('password_resets')->where('email', $user->email)->update(['token' => $token]);
        }

        $this->sendPasswordResetEmail($user->name, $user->email, $token);

        return redirect()->route('changePassword');
    }

    private function sendPasswordResetEmail($name, $email, $token)
    {
        $data = [
            'name' => $name,
            'email' => $email,
            'code' => $token,
        ];

        Mail::send('emails.passwordReset', ['mailData' => $data], function ($message) use ($data) {
            $message->subject(config('setting.passwordResetEmailSubject'));
            $message->from(config('setting.sendEmailFromEmailAddress'), config('setting.sendEmailFromEmailName'));
            $message->to($data['email']);
        });
    }

    public function changePassword()
    {
        if (config('setting.enPassResetEmail') == 'false') {
            abort(404);
        }
        if (Auth::user()) {
            if (Auth::user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->hasRole('Store Owner')) {
                return redirect()->route('restaurant.dashboard');
            }
        }

        return view('auth.changePassword');
    }

    public function changePasswordPost(Request $request): RedirectResponse
    {
        $validator = $request->validate(
            [
                'captcha' => ['required', 'captcha'],
                'code' => ['required', 'min:6', 'max:6'],
                'password' => ['required', 'string', 'min:6'],
            ],
            [
                'captcha.required' => 'Captcha is a required field.',
                'captcha.captcha' => 'Invalid Captcha',

                'code.required' => 'Reset Code is a required field.',
                'code.min' => 'Reset Code is invalid.',
                'code.max' => 'Reset Code is invalid.',

                'password.required' => 'Password is a required field.',
                'password.string' => 'Password is invalid.',
                'password.min' => 'Password must be atleast 6 characters long.',
            ]
        );

        $code = $request->code;

        $token = DB::table('password_resets')->where('token', $code)->first();

        if (! $token) {
            return redirect()->back()->with(['invalidFields' => 'Invalid reset code or code not found in the records.']);
        }

        $user = User::where('email', $token->email)->first();
        if (! $user) {
            return redirect()->back()->with(['invalidFields' => 'User not found.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        //delete token.
        DB::table('password_resets')->where('email', $token->email)->delete();

        return redirect()->route('get.login')->with(['success' => 'Password reset successful. You can now login with the new password.']);
    }

    /**
     * Register view for Delivery guy
     */
    public function registerDelivery()
    {
        if (Auth::user()) {
            if (Auth::user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            }
            if (Auth::user()->hasRole('Restaurant Owner')) {
                return redirect()->route('restaurant.dashboard');
            }
        }
        $vehicle_types = VehicleType::get();
        $states = State::get();

        return view('auth.register-delivery', ['vehicle_types' => $vehicle_types, 'states' => $states]);
    }

    public function externalOrdering($user_id): View
    {
        $user = User::find($user_id);
        if ($user) {
            $restaurants = $user->restaurants;

            return view('webform.restaurants', [
                'restaurants' => $restaurants,
            ]);
        }
    }
}
