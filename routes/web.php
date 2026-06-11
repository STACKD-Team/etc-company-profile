<?php

use App\Http\Controllers\Payment\MidtransNotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/payments/midtrans/notification', MidtransNotificationController::class)
    ->middleware('throttle:payment')
    ->name('payments.midtrans.notification');

require __DIR__.'/web/public.php';
require __DIR__.'/web/auth.php';
require __DIR__.'/web/registration.php';
require __DIR__.'/web/student.php';
// Sprint 2 Mia: Filament resources are the canonical admin CRUD under /admin.
// Legacy Blade admin CRUD route names are kept under /admin/legacy for old links/tests
// without taking over Filament's canonical /admin resource URLs.
require __DIR__.'/web/admin/legacy.php';
require __DIR__.'/web/admin/reports.php';
require __DIR__.'/web/instructor.php';
