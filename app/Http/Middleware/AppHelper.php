<?php
/*
 * @ https://CodesOnSale.xyz -- Get More Premium Apps & Scripts
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppHelper
{
    public function handle(Request $request, \Closure $next): Response
    {
        return $next($request);
    }

    private function dec($data)
    {
        $enc = 'AES-256-CBC';
        $sk = '1244874128985';
        $s_iv = 'cd999d87e995d999';
        $k = hash('sha256', $sk);
        $iv = substr(hash('sha256', $s_iv), 0, 16);
        $op = openssl_decrypt(base64_decode($data), $enc, $k, 0, $iv);

        return $op;
    }

    private function enc($data)
    {
        $enc = 'AES-256-CBC';
        $sk = '1244874128985';
        $s_iv = 'cd999d87e995d999';
        $k = hash('sha256', $sk);
        $iv = substr(hash('sha256', $s_iv), 0, 16);
        $op = openssl_encrypt($data, $enc, $k, 0, $iv);
        $op = base64_encode($op);

        return $op;
    }
}
