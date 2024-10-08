<?php

namespace App\Http\Controllers;

use App\Setting;
use Artisan;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Nwidart\Modules\Facades\Module;
use Zipper;

class ModuleController extends Controller
{
    public function modules(): View
    {
        $checkZipExtension = extension_loaded('zip');

        $modules = Module::all();

        return view('admin.modules', [
            'checkZipExtension' => $checkZipExtension,
            'modules' => $modules,
        ]);
    }

    public function uploadModuleZipFile(Request $request): JsonResponse
    {
        //take the zip file and save it inside the tmp folder
        $file = $request->file('file');

        try {
            $destinationPath = base_path('tmp');

            $originalFile = $file->getClientOriginalName();
            //moving file to /tmp folder for installation
            $file->move($destinationPath, $originalFile);
            $response = [
                'success' => true,
            ];

            return response()->json($response);
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 401);
        }
    }

    public function installModule(Request $request, Factory $cache)
    {
        try {
            $moduleFile = base_path('tmp/UPLOAD-THIS-MODULE.zip');
            $checkIfExists = File::get($moduleFile);
            //if it is present then continue, else error message exception

            //take the zip and extract to base folder
            $zipper = new Zipper;
            $zipper = Zipper::make($moduleFile);

            //extract to the Modules directory of the application (base path/Modules)
            $zipper->extractTo(base_path('Modules'));

            Artisan::call('migrate', [
                '--force' => true,
            ]);

            Artisan::call('module:migrate', [
                '--force' => true,
            ]);

            Artisan::call('module:seed', [
                '--force' => true,
            ]);

            Artisan::call('cache:clear');

            // return redirect()->route('admin.modules')->with(['success' => 'Module Uploaded Successfully']);
            return response()->json(['success' => true, 'message' => 'Module Installation Done'], 200);
        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
            //redirect with file not found
            return redirect()->route('admin.modules')->with(['message' => 'Module File Not Found']);
        }
    }

    public function enableModule($name): RedirectResponse
    {
        $module = Module::find($name);

        if ($module) {
            try {
                /* Settings Update */
                //get all the boolean settings
                $settingKeys = $module->get('settingKeys');
                if ($settingKeys && count($settingKeys) > 0) {
                    foreach ($settingKeys as $key => $value) {
                        //search for the key in settings
                        $setting = Setting::where('key', $key)->first();
                        // if not found create the key and set it as the default value
                        if (! $setting) {
                            $newSetting = new Setting();
                            $newSetting->key = $key;
                            if (gettype($value) == 'boolean') {
                                $newSetting->value = $value ? 'true' : 'false';
                            } else {
                                $newSetting->value = $value;
                            }
                            $newSetting->save();
                        } else {
                            //if found set the key as default value
                            if (gettype($value) == 'boolean') {
                                $setting->value = $value ? 'true' : 'false';
                            } else {
                                $setting->value = $value;
                            }
                            $setting->save();
                        }
                    }
                }
                /* END Settings update => this helps in clearing out the true values */

                $module->enable();
                Artisan::call('migrate', [
                    '--force' => true,
                ]);

                Artisan::call('module:migrate', [
                    '--force' => true,
                ]);

                Artisan::call('module:seed', [
                    '--force' => true,
                ]);

                Artisan::call('cache:clear');

                return redirect()->back()->with(['success' => $name.' module enabled']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        } else {
            return redirect()->back()->with(['message', 'Something went wrong!!!']);
        }
    }

    public function disableModule($name): RedirectResponse
    {
        $module = Module::find($name);

        if ($module) {
            try {
                /* Settings Update */
                //get all the boolean settings
                $settingKeys = $module->get('settingKeys');
                if ($settingKeys && count($settingKeys) > 0) {
                    foreach ($settingKeys as $key => $value) {
                        //check if value is boolean and is true
                        if (gettype($value) == 'boolean' && $value) {
                            //search for the key in settings
                            $setting = Setting::where('key', $key)->first();
                            // if not found create the key and set it as false
                            if (! $setting) {
                                $newSetting = new Setting();
                                $newSetting->key = $key;
                                $newSetting->value = 'false';
                                $newSetting->save();
                            } else {
                                //if found set the key as false
                                $setting->value = 'false';
                                $setting->save();
                            }
                        }
                    }
                }
                /* END Settings update => this helps in clearing out the true values */

                $module->disable();
                Artisan::call('cache:clear');

                return redirect()->back()->with(['success' => $name.' module disabled']);
            } catch (Exception $e) {
                return redirect()->back()->with(['message' => $e->getMessage()]);
            }
        } else {
            return redirect()->back()->with(['message', 'Something went wrong!!!']);
        }
    }
}
