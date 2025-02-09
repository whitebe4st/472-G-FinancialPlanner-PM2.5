<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/transaction', 'html.transaction');
Route::view('/dashboard', 'html.dashboard');
Route::view('/bookmark', 'html.bookmark');
