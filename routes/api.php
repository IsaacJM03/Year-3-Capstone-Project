<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DonationController;
use App\Http\Controllers\Api\V1\ClaimController;
use App\Http\Controllers\Api\V1\CampaignController;
use App\Http\Controllers\Api\V1\ReportController;

// API V1 Routes
Route::prefix('v1')->group(function () {
    
    // Public routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (require authentication)
    Route::middleware('auth:api')->group(function () {
        
        // Authentication routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);

        // Donation routes
        Route::apiResource('donations', DonationController::class);
        Route::get('/my-donations', [DonationController::class, 'myDonations'])
            ->middleware('role:donor');

        // Claim routes
        Route::apiResource('claims', ClaimController::class);
        Route::get('/my-claims', [ClaimController::class, 'myClaims'])
            ->middleware('role:receiver');

        // Campaign routes
        Route::apiResource('campaigns', CampaignController::class);
        Route::get('/my-campaigns', [CampaignController::class, 'myCampaigns'])
            ->middleware('role:receiver');

        // Report routes
        Route::prefix('reports')->group(function () {
            Route::get('/statistics', [ReportController::class, 'statistics'])
                ->middleware('role:admin');
            Route::get('/donations-by-food-type', [ReportController::class, 'donationsByFoodType'])
                ->middleware('role:admin');
            Route::get('/top-donors', [ReportController::class, 'topDonors'])
                ->middleware('role:admin');
            Route::get('/top-receivers', [ReportController::class, 'topReceivers'])
                ->middleware('role:admin');
            Route::get('/donations-over-time', [ReportController::class, 'donationsOverTime'])
                ->middleware('role:admin');
            Route::get('/user-report', [ReportController::class, 'userReport']);
        });
    });
});
