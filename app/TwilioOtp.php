<?php

namespace App;

use Twilio\Rest\Client;

class TwilioOtp
{
    public function sendOtp($phone)
    {
        $sid = config('setting.twilioSid');
        $token = config('setting.twilioAccessToken');
        $verification_service = config('setting.twilioServiceId');

        $twilio = new Client($sid, $token);

        // phone number should be in E.164 format
        // twilio.com/docs/glossary/what-e164
        try {
            $verification = $twilio->verify->v2->services($verification_service)
                ->verifications
                ->create($request->phone, 'sms'); //sms verification

            return true;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    public function verifyOtp($otp, $phone)
    {
        $sid = config('setting.twilioSid');
        $token = config('setting.twilioAccessToken');
        $verification_service = config('setting.twilioServiceId');

        $twilio = new Client($sid, $token);

        try {
            $verification_check = $twilio->verify->v2->services($verification_service)
                ->verificationChecks
                ->create(
                    $request->otp, // code
                    ['to' => $request->phone] //phone number to verify
                );

            if ($verification_check->status === 'pending') {
                $success = true;

                return response()->json($success);
            } else {
                $success = false;

                return response()->json($success);
            }
        } catch (Exception $e) {
            $success = false;

            return response()->json($success);
        }
        $success = false;

        return response()->json($success);
    }
}
