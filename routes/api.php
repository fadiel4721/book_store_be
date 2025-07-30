<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BukuController;
use App\Http\Controllers\API\PenerbitController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

// Semua route berikut butuh autentikasi Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

       // Admin only: CRUD penuh
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('penerbit', PenerbitController::class);
        Route::apiResource('buku', BukuController::class);
    });

    // Penerbit only: kelola bukunya sendiri
    Route::middleware('role:penerbit')->group(function () {
        Route::get('buku-saya', [BukuController::class, 'index']);
        Route::post('buku-saya', [BukuController::class, 'store']);
        // put, delete sesuai kebutuhan
    });

    // Semua terautentikasi dapat melihat daftar
    Route::get('penerbit', [PenerbitController::class, 'index']);
    Route::get('buku', [BukuController::class, 'index']);

});
