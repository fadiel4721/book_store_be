<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PenerbitController;
use App\Http\Controllers\API\BukuController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Public: register & login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// 2. Protected: butuh token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // 2.a. Semua terautentikasi bisa baca data
    Route::get('/penerbit',       [PenerbitController::class, 'index']);
    Route::get('/penerbit/{id}',  [PenerbitController::class, 'show']);
    Route::get('/buku',           [BukuController::class, 'index']);
    Route::get('/buku/{id}',      [BukuController::class, 'show']);

    // 2.b. Admin only — CRUD penuh (kecuali index/show, karena sudah di‑a)
    Route::middleware('role:admin')->group(function () {
        Route::post('/penerbit',        [PenerbitController::class, 'store']);
        Route::put('/penerbit/{id}',   [PenerbitController::class, 'update']);
        Route::delete('/penerbit/{id}',   [PenerbitController::class, 'destroy']);

        Route::post('/buku',            [BukuController::class, 'store']);
        Route::put('/buku/{id}',       [BukuController::class, 'update']);
        Route::delete('/buku/{id}',       [BukuController::class, 'destroy']);
    });

    // 2.c. Penerbit only — kelola bukunya sendiri
    Route::middleware('role:penerbit')->prefix('buku-saya')->group(function () {
        Route::get('/',        [BukuController::class, 'indexByPenerbit']);
        Route::post('/',        [BukuController::class, 'store']);
        Route::get('/{id}',    [BukuController::class, 'showByPenerbit']);
        Route::put('/{id}',    [BukuController::class, 'update']);
        Route::delete('/{id}',    [BukuController::class, 'destroy']);
    });
});
