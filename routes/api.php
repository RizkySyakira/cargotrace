<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShipmentApiController;

/*
|--------------------------------------------------------------------------
| REST API Routes (CargoTrace v1) - Slide 13
|--------------------------------------------------------------------------
| Seluruh endpoint di bawah ini otomatis diawali dengan prefix: /api/...
*/

Route::prefix('v1')->group(function () {

    // =========================================================================
    // 1. ENDPOINT PUBLIK (TERBUKA - TANPA PERLU BEARER TOKEN)
    // =========================================================================
    
    // Login API untuk mendapatkan Bearer Token Sanctum
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

    // Pelacakan Kargo Terbuka (Public Tracking API)
    Route::get('/public/track/{tracking_number}', [ShipmentApiController::class, 'trackPublic'])->name('api.public.track');


    // =========================================================================
    // 2. ENDPOINT TERLINDUNGI (WAJIB MENYERTAKAN BEARER TOKEN SANCTUM)
    // =========================================================================
    Route::middleware('auth:sanctum')->group(function () {

        // Cek Profil & Pencabutan Token (Logout)
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

        // Daftar & Detail Kargo
        Route::get('/shipments', [ShipmentApiController::class, 'index'])->name('api.shipments.index');
        Route::get('/shipments/{tracking_number}', [ShipmentApiController::class, 'show'])->name('api.shipments.show');

        // Operasional Petugas: Catat Checkpoint & Lapor Gangguan/Badai
        Route::post('/shipments/{tracking_number}/checkpoints', [ShipmentApiController::class, 'storeCheckpoint'])->name('api.shipments.checkpoints');
        Route::post('/shipments/{tracking_number}/exceptions', [ShipmentApiController::class, 'storeException'])->name('api.shipments.exceptions');
    });

});