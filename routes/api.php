<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import all required controllers (some are new based on the spec)
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\DonationController;
use App\Http\Controllers\API\V1\OrganizationController; // New: For receiver registration/verification
use App\Http\Controllers\API\V1\PickupController;       // Replaces ClaimController to match spec
use App\Http\Controllers\API\V1\CampaignController;     // Kept for campaigns (currently commented out)
use App\Http\Controllers\API\V1\AdminController;
use App\Http\Controllers\API\V1\PushTokenController;    // New: For FCM tokens
use App\Http\Controllers\API\V1\PostController;         // New: For social posts
use App\Http\Controllers\API\V1\UploadController;       // New: For general photo uploads
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are defined to match the DRA-UG Frontend Specification.
|
*/

// V1 API Routes
Route::prefix('v1')->group(function () {

    // --- 1. Authentication Routes (Public) ---
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:api')->group(function () {

        // --- 2. Auth & User Routes ---
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        // Optional refresh token route (recommended by spec)
        Route::post('/auth/refresh', [AuthController::class, 'refresh']); 
        
        // Spec requires /users/me (Page 6)
        Route::get('/users/me', [AuthController::class, 'user']);
        Route::put('/users/me', [AuthController::class, 'updateProfile']); // Added for PUT /users/me

        // --- 3. FCM / Push Token Routes (Page 6) ---
        Route::post('/push-tokens', [PushTokenController::class, 'store']);
        Route::delete('/push-tokens/{id}', [PushTokenController::class, 'destroy']);

        // --- 4. Donations Routes ---
        // Includes index, store, show, update, destroy from apiResource
        Route::apiResource('donations', DonationController::class);
        // Custom nearby route (as in original file)
        Route::get('donations/nearby', [DonationController::class, 'nearby']); 
        
        // Spec: POST /donations/:id/request (Page 7)
        Route::post('donations/{id}/request', [PickupController::class, 'requestDonation']); 

        // --- 5. Organization Routes (Receiver Registration/Details) ---
        Route::apiResource('organizations', OrganizationController::class)->only(['store', 'show']);

        // --- 6. Requests & Pickups Routes (Spec replaces 'claims') ---
        // Pickups index/show (viewing pickup details/history)
        Route::apiResource('pickups', PickupController::class)->only(['index', 'show']);
        
        // Spec: POST /pickups/:id/assign (Driver Assignment, Page 7)
        Route::post('pickups/{id}/assign', [PickupController::class, 'assignDriver']);
        
        // Spec: POST /pickups/:id/status (Status update: picked, delivered, etc., Page 7)
        Route::post('pickups/{id}/status', [PickupController::class, 'updateStatus']);
        
        // Spec: POST /pickups/:id/location (Driver location updates, Page 7)
        Route::post('pickups/{id}/location', [PickupController::class, 'updateLocation']);

        // --- 7. General Utility Routes ---
        // Spec: POST /api/v1/uploads (Page 8)
        Route::post('/uploads', [UploadController::class, 'store']);
        
        // Spec: POST /api/v1/posts (Page 9)
        Route::post('/posts', [PostController::class, 'store']);

        // --- 8. Admin Routes (Require 'admin' middleware) ---
        Route::prefix('admin')->middleware('admin')->group(function () {
            // Existing Report Routes
            Route::get('reports/summary', [AdminController::class, 'summary']);
            Route::get('reports/donations-per-category', [AdminController::class, 'donationsPerCategory']);

            // Spec: POST /api/v1/organizations/:id/verify (Page 7)
            Route::post('organizations/{id}/verify', [OrganizationController::class, 'verify']);
        });


        // --- Commented-Out Original Routes (Not explicitly in spec) ---

        /*
        // Original Claim Routes: Replaced by dedicated Pickups routes above
        Route::apiResource('claims', ClaimController::class)->only(['index', 'store']);
        Route::put('claims/{id}/approve', [ClaimController::class, 'approve']);
        Route::put('claims/{id}/deliver', [ClaimController::class, 'deliver']);
        */
        
        /*
        // Campaign Routes: Not defined in the provided spec
        Route::apiResource('campaigns', CampaignController::class);
        Route::post('campaigns/{id}/donate', [CampaignController::class, 'donate']);
        */
    });
});

