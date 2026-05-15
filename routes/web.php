<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ETC Planet Registration Flow
|--------------------------------------------------------------------------
*/

// Redirect root ke halaman pendaftaran
Route::get('/', function () {
    return redirect()->route('pendaftaran');
});

// Step 2: Form Pendaftaran Online (Data Pribadi)
Route::get('/pendaftaran', function () {
    return view('pendaftaran');
})->name('pendaftaran');

// Step 3: Halaman Pembayaran
Route::get('/pembayaran', function () {
    return view('pembayaran');
})->name('pembayaran');

// Step 4: Konfirmasi Berhasil
Route::get('/konfirmasi', function () {
    return view('konfirmasi');
})->name('konfirmasi');