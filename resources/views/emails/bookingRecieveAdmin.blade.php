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
                <td align="center" bgcolor="#ffffff" style="padding: 40px 20px 0 20px; color: #555555; font-family: Arial, sans-serif; font-size: 20px; line-height: 30px;">
                	<p>Hi, {{ $mailData["admin_name"] }}</p>
                    <p>You have received new Booking request in your restaurant. Booking details are given below:</p>
                    <p>Customer Name : {{ $mailData["customer_name"] }}</p>
                    <p>Mobile Number : {{ $mailData["customer_mobile"] }}</p>
                    <p>Booking Date : {{ $mailData["booking_date"] }}</p>
                    <p>Booking Time : {{ $mailData["booking_time"] }}</p>
                    <p>No of Pax : {{ $mailData["no_of_pax"] }}</p>
                    @if(!empty($mailData["comment"]))
                        <p>Comment : {{ $mailData["comment"] }}</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td align="center" bgcolor="#e9e9e9" style="padding: 12px 10px 12px 10px; color: #888888; font-family: Arial, sans-serif; font-size: 12px; line-height: 18px;">
                    <b>Sommelier </b> | ThincLab, 10 Pulteney Street , Adelaide SA 5000
                </td>
            </tr>
        </table>
        <!--[if (gte mso 9)|(IE)]>
                </td>
            </tr>
        </table>
        <![endif]-->
    </body>
</html>