<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paystack Configuration
    |--------------------------------------------------------------------------
    */
    'public_key' => env('PAYSTACK_PUBLIC_KEY', 'pk_test_sample_key'),
    'secret_key' => env('PAYSTACK_SECRET_KEY', 'sk_test_sample_key'),
    'payment_url' => env('PAYSTACK_PAYMENT_URL', 'https://api.paystack.co'),
    'currency' => env('PAYSTACK_CURRENCY', 'GHS'),
    'merchant_email' => env('PAYSTACK_MERCHANT_EMAIL', 'info@church.org'),
];
