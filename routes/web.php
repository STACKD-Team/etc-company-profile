<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pilih_program');
});

Route::get('/dashboard', function () {
    return view('pilih_program');
});

Route::get('/pilih-program', function () {
    return view('pilih_program');
});

Route::get('/pilih_program', function () {
    return view('pilih_program');
});

Route::get('/dashboard-siswa', function () {
    return view('dashboard_siswa');
});

Route::get('/dashboard_siswa', function () {
    return view('dashboard_siswa');
});
