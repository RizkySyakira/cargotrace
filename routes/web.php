<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicTrackingController;

/*
|--------------------------------------------------------------------------
| Web Routes (Jalur Navigasi Web)
|--------------------------------------------------------------------------
| Rute pelacakan publik (Slide 04: Akses publik tanpa perlu autentikasi login).
*/

// Halaman utama / form pencarian resi
Route::get('/', [PublicTrackingController::class, 'index'])->name('tracking.index');

// Pemrosesan form pencarian nomor resi
Route::post('/track', [PublicTrackingController::class, 'search'])->name('tracking.search');

// Halaman detail kargo dan visualisasi peta Leaflet.js
Route::get('/track/{tracking_number}', [PublicTrackingController::class, 'show'])->name('tracking.show');