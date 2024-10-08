<?php

namespace App\Http\Controllers;

use App\User;
use Artisan;
use Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function __construct()
    {
        set_time_limit(0);
    }

    public function filesBackup(Request $request): RedirectResponse
    {
        try {
            Artisan::call('backup:run', [
                '--only-files' => true,
            ]);

            return redirect()->back()->with(['success' => 'Backup stored in /storage/app/foodomaa-backup directory']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => 'Something went wrong!!!']);
        }
    }

    public function dbBackup(Request $request): RedirectResponse
    {
        try {
            Artisan::call('backup:run', [
                '--only-db' => true,
            ]);

            return redirect()->back()->with(['success' => 'Backup stored in /storage/app/foodomaa-backup directory']);
        } catch (Exception $e) {
            return redirect()->back()->with(['message' => 'Something went wrong!!!']);
        }
    }

    public function filesBackuprun($password)
    {
        $admin = User::where('id', '1')->first();
        $hashedPassword = $admin->password;

        if (Hash::check($password, $hashedPassword)) {
            Artisan::call('backup:run', [
                '--only-files' => true,
            ]);
        } else {
            echo 'Access Denied.';
        }
    }

    public function dbBackuprun($password)
    {
        $admin = User::where('id', '1')->first();
        $hashedPassword = $admin->password;

        if (Hash::check($password, $hashedPassword)) {
            Artisan::call('backup:run', [
                '--only-db' => true,
            ]);
        } else {
            echo 'Access Denied.';
        }
    }
}
