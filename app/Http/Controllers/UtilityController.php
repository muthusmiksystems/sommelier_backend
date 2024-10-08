<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UtilityController extends Controller
{
    public function index(): View
    {
        return view('admin.utility.index');
    }

    public function toggleStoreStatus($status): RedirectResponse
    {
        switch ($status) {
            case 'enable':
                DB::statement("UPDATE restaurants SET is_active = '1' WHERE is_accepted = '1'");

                return redirect()->back()->with(['success' => 'All stores are enabled']);
                break;
            case 'disable':
                DB::statement("UPDATE restaurants SET is_active = '0' WHERE is_accepted = '1'");

                return redirect()->back()->with(['success' => 'All stores are disabled']);
                break;
            default:
                return redirect()->back()->with(['message' => 'Invalid Command']);
                break;
        }
    }
}
