<?php

use App\Http\Controllers\API\ActivityTypeController as ApiActivityTypeController;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\BusinessAccountController as ApiBusinessAccountController;
use App\Http\Controllers\API\CategoryController as ApiCategoryController;
use App\Http\Controllers\API\CityController as ApiCityController;
use App\Http\Controllers\API\ServiceController as ApiServiceController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/login/verify', [AuthController::class, 'loginVerify']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

// Public reference data
Route::get('/cities', [ApiCityController::class, 'index']);
Route::get('/activity-types', [ApiActivityTypeController::class, 'index']);
Route::get('/categories', [ApiCategoryController::class, 'index']);

// Public services (approved only)
Route::get('/services', [ApiServiceController::class, 'index']);
Route::get('/services/{service}', [ApiServiceController::class, 'show']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Business Accounts
    Route::get('/business-accounts', [ApiBusinessAccountController::class, 'index']);
    Route::post('/business-accounts', [ApiBusinessAccountController::class, 'store']);
    Route::get('/business-accounts/{businessAccount}', [ApiBusinessAccountController::class, 'show']);
    Route::put('/business-accounts/{businessAccount}', [ApiBusinessAccountController::class, 'update']);

    // Services
    Route::get('/my-services', [ApiServiceController::class, 'myServices']);
    Route::post('/services', [ApiServiceController::class, 'store']);
    Route::match(['PUT', 'POST', 'PATCH'], '/services/{service}', [ApiServiceController::class, 'update']);
    Route::delete('/services/{service}', [ApiServiceController::class, 'destroy']);
});
