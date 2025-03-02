<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookmarkController;

Route::get('/', function () {
    return view('welcome');
});

// Add these new auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::view('/transaction', 'html.transaction');
Route::view('/bookmark', 'html.bookmark');

// Transaction routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transaction', function() {
        return view('html.transaction');
    });
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/categories', [TransactionController::class, 'getCategories']);
    Route::get('/api/transactions/chart-data/{timeFrame}', [DashboardController::class, 'getChartData']);
    Route::get('/api/transactions', [TransactionController::class, 'getTransactions']);
    Route::post('/bookmark-transaction', [TransactionController::class, 'bookmarkTransaction'])->name('bookmark.transaction');
    Route::get('/bookmark', [BookmarkController::class, 'index'])->name('bookmark.index');
    Route::post('/api/add-bookmarked-transactions', [TransactionController::class, 'addFromBookmarks'])->name('transactions.add-from-bookmarks');
    Route::get('/api/bookmarks', [BookmarkController::class, 'getBookmarks'])->name('bookmarks.get');

    Route::get('/transactions/{id}', [TransactionController::class, 'show']); //  ดึงข้อมูล Transaction เพื่อนำไปแก้ไข
    Route::put('/transactions/{id}', [TransactionController::class, 'update']); //  อัปเดต Transaction หลังจากแก้ไข
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
});



