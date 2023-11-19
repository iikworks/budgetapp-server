<?php

use App\Http\Controllers\Api\Auth\SignInController;
use App\Http\Controllers\Api\Auth\SignUpController;
use App\Http\Controllers\Api\ProfileController;
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
    Route::get('/user', [ProfileController::class, 'profile'])->name('api.user');
});
