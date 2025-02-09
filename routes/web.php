<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Add these new auth routes
Route::view('/login', 'html.auth.login')->name('login');
Route::view('/register', 'html.auth.register')->name('register');

Route::view('/transaction', 'html.transaction');
Route::view('/dashboard', 'html.dashboard');
Route::view('/bookmark', 'html.bookmark');
