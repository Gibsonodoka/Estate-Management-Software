<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralListing\Http\Controllers\PropertyListingController;
use Modules\GeneralListing\Http\Controllers\AgentProfileController;

Route::prefix('v1')->group(function () {

    // Public routes
    Route::prefix('listings')->group(function () {
        Route::get('/', [PropertyListingController::class, 'index']);
        Route::get('featured', [PropertyListingController::class, 'featured']);
        Route::get('search', [PropertyListingController::class, 'search']);
        Route::get('{id}', [PropertyListingController::class, 'show']);
    });

    Route::prefix('agents')->group(function () {
        Route::get('/', [AgentProfileController::class, 'index']);
        Route::get('verified', [AgentProfileController::class, 'verified']);
        Route::get('{id}', [AgentProfileController::class, 'show']);
        Route::get('{id}/listings', [AgentProfileController::class, 'getAgentListings']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('listings')->group(function () {
            Route::post('/', [PropertyListingController::class, 'store']);
            Route::put('{id}', [PropertyListingController::class, 'update']);
            Route::delete('{id}', [PropertyListingController::class, 'destroy']);
            Route::post('{id}/toggle-featured', [PropertyListingController::class, 'toggleFeatured']);
            Route::get('my-listings', [PropertyListingController::class, 'myListings']);
        });

        Route::prefix('agent-profile')->group(function () {
            Route::get('/', [AgentProfileController::class, 'getMyProfile']);
            Route::post('/', [AgentProfileController::class, 'createProfile']);
            Route::put('/', [AgentProfileController::class, 'updateProfile']);
            Route::post('request-verification', [AgentProfileController::class, 'requestVerification']);
        });

        Route::prefix('admin/agents')->group(function () {
            Route::get('pending', [AgentProfileController::class, 'pendingVerification']);
            Route::post('{id}/verify', [AgentProfileController::class, 'verifyAgent']);
            Route::post('{id}/reject', [AgentProfileController::class, 'rejectAgent']);
        });
    });
});
