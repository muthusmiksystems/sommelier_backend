<?php
/*
 * @ https://CodesOnSale.xyz -- Get More Premium Apps & Scripts
 * @ PHP 7.2
 * @ Decoder version: 1.0.4
 * @ Release: 01/09/2021
 */

function iio()
{
    $val = q_e_c_f_y_p().config('permission.table_names.role_has_permissions');

    return hash('sha256', $val);
}
function biHshvaablenwsh()
{
    $val = lkajsdlk();

    return hash('sha256', $val);
}
function woNnoroMsIohWaamodooFyaH()
{
    $msg = 'Read this function name in reverse :p LOL';

    return $msg;
}
function enSovCheck($request)
{
    if (config('appSettings.enSOV') == 'true') {
        if (! isset($request->otp) || $request->otp == null) {
            abort(500, 'SPAM Request or Something Went Wrong');
        }
        if (isset($request->otp) && $request->otp != null) {
            $otpTable = App\SmsOtp::where('phone', $request->phone)->first();
            if (! $otpTable) {
                abort(500, 'SPAM Request or Something Went Wrong');
            } else {
                if ($otpTable->otp != $request->otp) {
                    abort(500, 'SPAM Request or Something Went Wrong');
                }
            }
        }
    }
}
