<?php
/*
 * @ https://CodesOnSale.xyz -- Get More Premium Apps & Scripts
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

namespace App\Http\Controllers;

use Illuminate\View\View;

class InstallController extends \Illuminate\Routing\Controller
{
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\RedirectIfInstalled::class);
    }

    public function start(): View
    {
        return view('install.start');
    }

    public function preInstallation(\App\Install\Requirement $requirement): View
    {
        return view('install.pre_installation', ['requirement' => $requirement]);
    }

    public function getConfiguration(\App\Install\Requirement $requirement)
    {
        if (! $requirement->satisfied()) {
            return redirect('install/pre-installation');
        }

        return view('install.configuration', ['requirement' => $requirement]);
    }

    public function postConfiguration(\App\Http\Requests\InstallRequest $request, \App\Install\Database $database, \App\Install\AdminAccount $admin, \App\Install\Store $store, \App\Install\App $app, \Illuminate\Contracts\Cache\Factory $cache)
    {
        try {
            try {
                $database->setup($request->db);
                $this->processData();
                $admin->setup($request->admin);
                $store->setup($request->store, $cache);
                $app->setup();

                return redirect('install/complete');
            } catch (\PDOException $pe) {
                return back()->withInput()->with('error', $pe->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with(['message' => $e->getMessage()]);
        }
    }

    public function complete()
    {
        if (config('app.installed')) {
            return redirect()->route('admin.dashboard');
        }
        \DotenvEditor::setKey('APP_INSTALLED', 'true')->save();

        return view('install.complete');
    }

    private function processData()
    {
        $data = file_get_contents(storage_path('data/data.json'));
        $data = json_decode($data);
        $dbSet = [];
        foreach ($data as $s) {
            $dbSet[] = ['key' => $s->key, 'value' => $s->value];
        }
        \Illuminate\Support\Facades\DB::table('settings')->insert($dbSet);
    }
}
