<?php

use App\Http\Controllers\Api\Auth\SignInController;
use App\Http\Controllers\Api\Auth\SignUpController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProfileImageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/auth')->group(function () {
    Route::post('/sign-in', [SignInController::class, 'handle'])->name('api.auth.sign-in');
    Route::post('/sign-up', [SignUpController::class, 'handle'])->name('api.auth.sign-up');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/user')->group(function () {
        Route::get('/', [ProfileController::class, 'profile'])->name('api.user');
        Route::patch('/', [ProfileController::class, 'update'])->name('api.user.update');

        Route::prefix('/image')->group(function () {
            Route::patch('/', [ProfileImageController::class, 'update'])->name('api.user.image.update');
            Route::delete('/', [ProfileImageController::class, 'destroy'])->name('api.user.image.destroy');
        });
    });
});
