<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/program', function () {
    return view('program.program');
});

Route::get('/dashboard-admin', function () {
    return view('dashboardAdmin.dashboardA');
});
