<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Postmark, resend, AWS and more. This file provides a sane default
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS / OTP delivery
    |--------------------------------------------------------------------------
    |
    | 'provider' selects which App\Services\Sms\SmsProviderInterface binding
    | AppServiceProvider resolves. Use 'log' locally (writes OTPs to the log
    | instead of sending real SMS), 'msg91' once you have MSG91 + DLT set up.
    |
    */

    'sms' => [
        'provider' => env('SMS_PROVIDER', 'log'),
    ],

    'msg91' => [
        'authkey' => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payments
    |--------------------------------------------------------------------------
    |
    | 'gateway' selects which App\Services\Payment\PaymentGatewayInterface
    | binding AppServiceProvider resolves. Use 'log' locally (fabricates
    | gateway ids, accepts every signature - never outside dev/testing),
    | 'razorpay' once real keys are in .env.
    |
    */

    'payment' => [
        'gateway' => env('PAYMENT_GATEWAY', 'log'),
        // Temporary, explicit toggle - when false, checkout places orders
        // directly (no Razorpay step) instead of waiting on payment.
        // Flip PAYMENT_ENABLED=true in .env to restore the normal flow;
        // nothing else needs to change.
        'enabled' => env('PAYMENT_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Franchise settlement payouts
    |--------------------------------------------------------------------------
    |
    | Separate from 'payment' above on purpose - you may want customer
    | payments live before franchise payouts are ready to send real money.
    | RazorpayX shares API keys with the payment gateway (see 'razorpay'
    | below); x_account_number is your own RazorpayX business account,
    | not any franchise's.
    |
    */

    'payout' => [
        'gateway' => env('PAYOUT_GATEWAY', 'log'),
    ],

    'razorpay' => [
        'key' => env('RAZORPAY_KEY_ID'),
        'secret' => env('RAZORPAY_KEY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
        'x_account_number' => env('RAZORPAYX_ACCOUNT_NUMBER'),
    ],

];
