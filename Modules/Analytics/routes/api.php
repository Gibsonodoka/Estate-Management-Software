<?php

use Illuminate\Support\Facades\Route;
use Modules\Analytics\Http\Controllers\ReportController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('reports')->group(function () {
        Route::get('financial', [ReportController::class, 'financialReport']);
        Route::get('occupancy', [ReportController::class, 'occupancyReport']);
        Route::get('maintenance', [ReportController::class, 'maintenanceReport']);
        Route::get('tenant', [ReportController::class, 'tenantReport']);
        Route::get('security', [ReportController::class, 'securityReport']);
        Route::get('user-activity', [ReportController::class, 'userActivityReport']);
        Route::get('estate-performance', [ReportController::class, 'estatePerformanceReport']);
        Route::post('export', [ReportController::class, 'exportReport']);
    });
});
