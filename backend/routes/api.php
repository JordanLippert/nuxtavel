<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login',           [AuthController::class, 'login']);
Route::post('/register',        [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/users/me', [AuthController::class, 'me']);
    Route::apiResource('users', UserController::class);
    // Rota extra para update com multipart/form-data (PUT não suporta file upload)
    Route::post('/users/{user}/update', [UserController::class, 'update']);
});
