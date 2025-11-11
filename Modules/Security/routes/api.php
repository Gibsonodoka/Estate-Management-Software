<?php

use Illuminate\Support\Facades\Route;
use Modules\Security\Http\Controllers\VisitorLogController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('visitor-logs')->group(function () {
        Route::get('/', [VisitorLogController::class, 'index']);
        Route::post('/', [VisitorLogController::class, 'store']);
        Route::get('checked-in', [VisitorLogController::class, 'checkedIn']);
        Route::get('today', [VisitorLogController::class, 'today']);
        Route::get('statistics', [VisitorLogController::class, 'statistics']);
        Route::get('{id}', [VisitorLogController::class, 'show']);
        Route::post('{id}/check-out', [VisitorLogController::class, 'checkOut']);
        Route::post('{id}/deny', [VisitorLogController::class, 'deny']);
        Route::get('host/{hostId}', [VisitorLogController::class, 'getByHost']);
    });
});
