<?php
use App\Http\Controllers\SuperAdmin\TenantRegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdmin\SuperAdminAuthController;

Route::prefix('super-admin')->group(function () {
    Route::post('/login', [SuperAdminAuthController::class, 'login']);

    Route::middleware('auth:super_admin')->group(function () {
        Route::post('/logout', [SuperAdminAuthController::class, 'logout']);
        Route::get('/profile', [SuperAdminAuthController::class, 'profile']);
        Route::post('/refresh', [SuperAdminAuthController::class, 'refresh']);
    });
});

Route::post('/register-tenant', [TenantRegisterController::class, 'register']);
Route::middleware(['tenant'])->group(function () {
    Route::post('/tenant-login', [AuthController::class, 'login']);
    Route::get('/user-profile', [AuthController::class, 'profile'])->middleware('auth:api');
});
Route::middleware(['tenant', 'auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::post('/refresh', [AuthController::class, 'refresh']);
