<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\DonationController;
use App\Http\Controllers\API\V1\ClaimController;
use App\Http\Controllers\API\V1\CampaignController;
use App\Http\Controllers\API\V1\AdminController;

// V1 API Routes
Route::prefix('v1')->group(function () {
    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Donation Routes
        Route::apiResource('donations', DonationController::class);
        Route::get('donations/nearby', [DonationController::class, 'nearby']);

        // Claim Routes
        Route::apiResource('claims', ClaimController::class)->only(['index', 'store']);
        Route::put('claims/{id}/approve', [ClaimController::class, 'approve']);
        Route::put('claims/{id}/deliver', [ClaimController::class, 'deliver']);

        // Campaign Routes
        Route::apiResource('campaigns', CampaignController::class);
        Route::post('campaigns/{id}/donate', [CampaignController::class, 'donate']);

        // Admin Routes
        Route::prefix('admin')->middleware('admin')->group(function () {
            Route::get('reports/summary', [AdminController::class, 'summary']);
            Route::get('reports/donations-per-category', [AdminController::class, 'donationsPerCategory']);
        });
    });
});
