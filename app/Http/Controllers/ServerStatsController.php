<?php

namespace App\Http\Controllers;

use App\Install\ServerPhpExtension;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ServerStatsController extends Controller
{
    /**
     * @param  ServerPhpExtension  $requirements
     */
    public function getServerStatsPage(ServerPhpExtension $phpExtensions): View
    {
        $phpVersion = PHP_VERSION;

        return view('admin.serverInfo', [
            'phpVersion' => $phpVersion,
            'phpExtensions' => $phpExtensions,
        ]);
    }

    public function getServerStatsData(): JsonResponse
    {
        try {
            $linfo = new \Linfo\Linfo;
            $parser = $linfo->getParser();

            $ram = $parser->getRam();

            $usedRam = floatval($ram['total']) - floatval($ram['free']);

            $data = [
                'cpu' => substr_replace($parser->getLoad(), '', -1),
                'totalRam' => floatval($ram['total']),
                'usedRam' => $usedRam,
                'freeRam' => floatval($ram['free']),
                'ramUsedPercentage' => number_format((float) ($usedRam / floatval($ram['total']) * 100), 2, '.', ''),
                'avgLoad' => $parser->getLoad(),
                'upTime' => $parser->getUpTime(),
            ];

            return response()->json($data);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }
}
