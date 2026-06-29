<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'is_production' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOLEAN),
    'sanitize' => filter_var(env('MIDTRANS_SANITIZE', true), FILTER_VALIDATE_BOOLEAN),
    '3ds' => filter_var(env('MIDTRANS_3DS', true), FILTER_VALIDATE_BOOLEAN),
];
