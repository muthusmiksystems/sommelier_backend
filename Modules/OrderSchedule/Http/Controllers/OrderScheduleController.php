<?php

namespace Modules\OrderSchedule\Http\Controllers;

use App\Setting;
use Artisan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class OrderScheduleController extends Controller
{
    public function settings(): View
    {
        return view('orderschedule::settings');
    }

    public function saveSettings(Request $request): RedirectResponse
    {
        // dd($request->all());

        $allSettings = $request->except(['enableOrderSchedulingOnCustomer', 'enFixedNumberOfDays']);

        foreach ($allSettings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting != null) {
                $setting->value = $value;
                $setting->save();
            }
        }

        $checkboxesSettings = ['enableOrderSchedulingOnCustomer', 'enFixedNumberOfDays'];

        foreach ($checkboxesSettings as $checkboxSetting) {
            $setting = Setting::where('key', $checkboxSetting)->first();
            if ($setting) {
                if ($request->$checkboxSetting == 'true') {
                    $setting->value = 'true';
                    $setting->save();
                } else {
                    $setting->value = 'false';
                    $setting->save();
                }
            } else {
                if ($checkboxSetting != null || $checkboxSetting != '') {
                    $setting = new Setting();
                    $setting->key = $checkboxSetting;
                    if ($request->$checkboxSetting == 'true') {
                        $setting->value = 'true';
                        $setting->save();
                    } else {
                        $setting->value = 'false';
                        $setting->save();
                    }
                }
            }
        }

        Artisan::call('cache:clear');

        return redirect()->back()->with(['success' => 'Settings Saved']);
    }
}
