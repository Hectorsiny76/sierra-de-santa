<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomImageController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::pattern('room_type', '[0-9]+');
Route::pattern('package', '[0-9]+');
Route::pattern('reservation', '[0-9]+');
Route::pattern('room', '[0-9]+');
Route::pattern('user', '[0-9]+');
Route::pattern('image', '[0-9]+');

Route::get('/', function () {
   return response()->json([
       "message" => "Hello and welcome to our application!",
   ]);
});

Route::apiResource('room-types', RoomTypeController::class)->only(['index', 'show']);
Route::apiResource('rooms', RoomController::class)->only(['index', 'show']);
Route::apiResource('packages', PackageController::class)->only(['index', 'show']);
Route::post('register', [AuthController::class, 'register'])->name('register')->name('register');
Route::post('login', [AuthController::class, 'login'])->name("login");

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::apiResource('users', UserController::class)->only(['index', 'show', 'update']);
    Route::patch('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.updatepassword');
    Route::apiResource('packages', PackageController::class)->except(['index', 'show']);
    Route::apiResource('rooms', RoomController::class)->except(['index', 'show']);
    Route::apiResource('room-types', RoomTypeController::class)->except(['index', 'show']);
    Route::post('/rooms/{room}/images', [RoomImageController::class, 'store'])->name('room.images.store');
    Route::post('/rooms/{room}/images/bulk', [RoomImageController::class, 'storeMany'])->name('room.images.storeMany');
    Route::patch('/images/{image}', [RoomImageController::class, 'update'])->name('room.images.update');
    Route::delete('/images/{image}', [RoomImageController::class, 'destroy'])->name('room.images.destroy');
    Route::apiResource('users.reservations', ReservationController::class);
    Route::delete('reservations/{reservation}/delete', [ReservationController::class, 'forceDelete'])->name('reservations.forcedelete');
    Route::post('/users/{user}/reservations/{reservation}/restore', [ReservationController::class, 'restore'])->name('users.reservations.restore');
    Route::get('reservations/deleted', [ReservationController::class, 'viewTrashed'])->name('reservations.viewtrashed');
    Route::post('/users/{user}/reservations/{reservation}/checkout', [PaymentController::class, 'checkout'])->name('reservations.checkout');
});
