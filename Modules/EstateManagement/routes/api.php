<?php

// ============================================
// FILE: Modules/EstateManagement/routes/api.php (COMPLETE VERSION)
// ============================================

use Illuminate\Support\Facades\Route;
use Modules\EstateManagement\Http\Controllers\EstateController;
use Modules\EstateManagement\Http\Controllers\PropertyController;
use Modules\EstateManagement\Http\Controllers\TenantController;
use Modules\EstateManagement\Http\Controllers\AnnouncementController;
use Modules\EstateManagement\Http\Controllers\MaintenanceRequestController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // ========================================
    // ESTATE ROUTES
    // ========================================
    Route::prefix('estates')->group(function () {
        Route::get('/', [EstateController::class, 'index']);
        Route::post('/', [EstateController::class, 'store']);
        Route::get('{id}', [EstateController::class, 'show']);
        Route::put('{id}', [EstateController::class, 'update']);
        Route::delete('{id}', [EstateController::class, 'destroy']);
        Route::post('{id}/renew-subscription', [EstateController::class, 'renewSubscription']);
        Route::get('{id}/vacant-properties', [EstateController::class, 'getVacantProperties']);

        // ========================================
        // PROPERTY ROUTES (Nested under estates)
        // ========================================
        Route::prefix('{estateId}/properties')->group(function () {
            Route::get('/', [PropertyController::class, 'index']);
            Route::post('/', [PropertyController::class, 'store']);
            Route::get('{id}', [PropertyController::class, 'show']);
            Route::put('{id}', [PropertyController::class, 'update']);
            Route::delete('{id}', [PropertyController::class, 'destroy']);
            Route::post('{id}/toggle-listing', [PropertyController::class, 'toggleListing']);
        });

        // ========================================
        // TENANT ROUTES (Nested under estates)
        // ========================================
        Route::prefix('{estateId}/tenants')->group(function () {
            Route::get('/', [TenantController::class, 'index']);
            Route::post('/', [TenantController::class, 'store']);
            Route::get('{id}', [TenantController::class, 'show']);
            Route::put('{id}', [TenantController::class, 'update']);
            Route::delete('{id}', [TenantController::class, 'destroy']);
            Route::post('{id}/give-notice', [TenantController::class, 'giveNotice']);
            Route::post('{id}/move-out', [TenantController::class, 'moveOut']);
            Route::get('{id}/payment-history', [TenantController::class, 'paymentHistory']);
        });

        // ========================================
        // ANNOUNCEMENT ROUTES (Nested under estates)
        // ========================================
        Route::prefix('{estateId}/announcements')->group(function () {
            Route::get('/', [AnnouncementController::class, 'index']);
            Route::post('/', [AnnouncementController::class, 'store']);
            Route::get('my-announcements', [AnnouncementController::class, 'getMyAnnouncements']);
            Route::get('{id}', [AnnouncementController::class, 'show']);
            Route::put('{id}', [AnnouncementController::class, 'update']);
            Route::delete('{id}', [AnnouncementController::class, 'destroy']);
            Route::post('{id}/toggle-active', [AnnouncementController::class, 'toggleActive']);
        });
    });

    // ========================================
    // MAINTENANCE REQUEST ROUTES
    // ========================================
    Route::prefix('maintenance-requests')->group(function () {
        Route::get('/', [MaintenanceRequestController::class, 'index']);
        Route::post('/', [MaintenanceRequestController::class, 'store']);
        Route::get('statistics', [MaintenanceRequestController::class, 'statistics']);
        Route::get('{id}', [MaintenanceRequestController::class, 'show']);
        Route::put('{id}', [MaintenanceRequestController::class, 'update']);
        Route::delete('{id}', [MaintenanceRequestController::class, 'destroy']);
        Route::post('{id}/acknowledge', [MaintenanceRequestController::class, 'acknowledge']);
        Route::post('{id}/complete', [MaintenanceRequestController::class, 'markCompleted']);
    });
});
