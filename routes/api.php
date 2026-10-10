<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('room-type', RoomTypeController::class)->only(['index', 'show']);
Route::apiResource('room', RoomController::class)->only(['index', 'show']);
Route::apiResource('package', PackageController::class)->only(['index', 'show']);
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('package', PackageController::class)->except(['index', 'show']);
    Route::apiResource('room', RoomController::class)->except(['index', 'show']);
    Route::apiResource('room-type', RoomTypeController::class)->except(['index', 'show']);
});
