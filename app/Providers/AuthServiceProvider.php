<?php

namespace App\Providers;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
    }

    protected $auth;

    // public function __construct()
    // {
    //     $firebaseCredentials = env('FIREBASE_CREDENTIALS');

    //     $factory = (new Factory)->withServiceAccount($firebaseCredentials);
       
    //     $this->auth = $factory->createAuth();
    // }        

    // public function sendOTP($phoneNumber)
    // {
    //     try {
    //         $verification = $this->auth->startPhoneNumberVerification($phoneNumber);
    //         return $verification->sessionId();
    //     } catch (\Exception $e) {
    //         // Handle error
    //         return null;
    //     }
    // }
}
