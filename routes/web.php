<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicTrackingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes (Jalur Navigasi Web)
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. PELACAKAN PUBLIK (TANPA LOGIN)
// =========================================================================
Route::get('/', [PublicTrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [PublicTrackingController::class, 'search'])->name('tracking.search');
Route::get('/track/{tracking_number}', [PublicTrackingController::class, 'show'])->name('tracking.show');

// =========================================================================
// 2. AUTENTIKASI WEB (LOGIN & LOGOUT)
// =========================================================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// 3. DASHBOARD INTERNAL (WAJIB LOGIN)
// =========================================================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});