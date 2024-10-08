<!DOCTYPE html>
<html>
<head>
    <title>One-Time Password</title>
    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .otp-message {
            text-align: center;
            color: #333;
        }
        .otp-code {
            font-size: 32px;
            margin-top: 20px;
            margin-bottom: 40px;
            color: #007bff;
            text-align:center;
        }
        /* Add more custom styles as needed */
    </style>
</head>
<body>
    <div class="container">
        <h1 class="otp-message">Your One-Time Password (OTP)</h1>
        <p class="otp-code">{{ $mailData["otp"] }}</p>
        <p>This OTP is valid for a single use. Please do not share it with anyone.</p>
        <!-- Add more information or instructions as needed -->
    </div>
</body>
</html>
