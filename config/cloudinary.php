<?php

return [
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'url' => env('CLOUDINARY_URL'),
    'secure' => filter_var(env('CLOUDINARY_SECURE', true), FILTER_VALIDATE_BOOLEAN),
    'allow_test_uploads' => filter_var(env('CLOUDINARY_ALLOW_TEST_UPLOADS', false), FILTER_VALIDATE_BOOLEAN),
];
