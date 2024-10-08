<?php

namespace App\Http\Controllers;

use App\User;
use Artisan;
use Hash;

class SchedulerController extends Controller
{
    public function run($password)
    {
        $admin = User::where('id', '1')->first();
        $hashedPassword = $admin->password;

        if (Hash::check($password, $hashedPassword)) {
            Artisan::call('schedule:run');
        } else {
            echo 'Access Denied.';
        }
    }
}
