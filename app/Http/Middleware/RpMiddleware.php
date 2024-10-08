<?php

namespace App\Http\Middleware;

use App\PaymentGateway;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class RpMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() && Auth::user()->hasRole('Admin')) {
            //else redirect to page to create new location as primary
            $allowed = [
                'admin.dashboard',
            ];

            $route = Route::getRoutes()->match($request);
            // dd($route->getName());
            if (! in_array($route->getName(), $allowed)) {
                session(['razorpay_enter_mid' => 'false']);

                return $next($request);
            } else {
                //not in allowed routes, so set session to show popup
                $razorpay = PaymentGateway::where('name', 'Razorpay')->first();
                if (! $razorpay->is_active) {
                    return $next($request);
                }
                if (config('setting.razorpayMerchantId') == null) {
                    session(['razorpay_enter_mid' => 'true']);

                    return $next($request);
                }
            }

            return $next($request);
        }

        return $next($request);
    }
}
