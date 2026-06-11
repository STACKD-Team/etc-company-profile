<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    'sanitize' => (bool) env('MIDTRANS_SANITIZE', true),
    '3ds' => (bool) env('MIDTRANS_3DS', true),
];
