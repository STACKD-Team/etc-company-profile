<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    require __DIR__ . '/web/public.php';
    require __DIR__ . '/web/registration.php';
});
