<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width" />
        <style type="text/css">
            @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
                body[yahoo] .buttonwrapper { background-color: transparent !important; }
                body[yahoo] .button { padding: 0 !important; }
                body[yahoo] .button a { background-color: #de4b39; padding: 15px 25px !important; }
            }

            @media only screen and (min-device-width: 601px) {
                .content { width: 600px !important; }
                .col387 { width: 387px !important; }
            }
        </style>
    </head>
    <body bgcolor="#252d2f" style="margin: 0; padding: 0;" yahoo="fix">
        <!--[if (gte mso 9)|(IE)]>
        <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td>
        <![endif]-->
        <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 600px;" class="content">
            <tr>
                <td align="center" style="padding: 20px 20px 20px 20px; color: #ffffff; font-family: Arial, sans-serif; font-size: 36px; font-weight: bold;">
                    {{-- <img src="{{substr(url("/"), 0, strrpos(url("/"), '/'))}}/assets/img/logos/logo.png') }}" alt="{{ config('settings.storeName') }}" width="80" height="80" style="display:block;" /> --}}
                </td>
            </tr>
            <tr>
                <td bgcolor="#ffffff" style="padding: 40px 20px 0 20px; color: #555555; font-family: Arial, sans-serif; font-size: 20px; line-height: 30px;">
                	<p>Hi, {{ $mailData["name"] }}</p>
                    <p>Your booking request has been Updated. Your booking reference is {{ "#".$mailData["booking_id"] }}</p>
                    <p style="margin:0;">Booking Date : {{ $mailData["booking_date"] }}</p>
                    <p style="margin:0;">Booking Time : {{ $mailData["booking_time"] }}</p>
                    <p style="margin:0;">No of pax : {{ $mailData["no_of_pax"] }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 40px 20px 0 20px; color: #555555; font-family: Arial, sans-serif; font-size: 20px; line-height: 30px;">
                    <img style="height:150px;width:150px;" src="{{ $message->embedData(base64_decode(\DNS2D::getBarcodePNG($mailData['booking_id'], 'QRCODE')), 'barcode.png') }}" />
                </td>
            </tr>
            <tr>
                <td style="padding: 40px 20px 0 20px; color: #555555; font-family: Arial, sans-serif; font-size: 20px; line-height: 30px;">Please bring this email along so we can provide you with a great experience.</td>
            </tr>
            <tr>
                <td style="padding: 40px 20px 0 20px; color: #555555; font-family: Arial, sans-serif; font-size: 20px; line-height: 30px;">Kind Regards</td>
            </tr>
            <tr>
                <td style="padding: 0px 20px 0 20px; color: #555555; font-family: Arial, sans-serif; font-size: 20px;">The {{ $mailData['admin_name'] }}</td>
            </tr>
            
        </table>
        <!--[if (gte mso 9)|(IE)]>
                </td>
            </tr>
        </table>
        <![endif]-->
    </body>
</html>