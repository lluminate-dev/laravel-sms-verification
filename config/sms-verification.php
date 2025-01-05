<?php

return [

    'twilio' => [
        /*
        |--------------------------------------------------------------------------
        | Default Credentials
        |--------------------------------------------------------------------------
        |
        | Here you can set the default credentials that will be used to send the
        | SMS verification code. You can also set the default passcode that will
        | be used to bypass the verification process.
        |
        */
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'verify_service_sid' => env('TWILIO_VERIFY_SERVICE_SID'),

        /*
        |--------------------------------------------------------------------------
        | Bypass
        |--------------------------------------------------------------------------
        |
        | If set to true, the passcode will be used to bypass the verification
        | process. This is useful for testing purposes and local development.
        |
        */
        'bypass' => env('TWILIO_BYPASS', false),
        'passcode' => env('TWILIO_PASSCODE'),
    ],

];
