<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentProcessing\Http\Controllers\EstatePaymentController;
use Modules\PaymentProcessing\Http\Controllers\PaymentRecordController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    Route::prefix('estate-payments')->group(function () {
        Route::get('/', [EstatePaymentController::class, 'index']);
        Route::post('/', [EstatePaymentController::class, 'store']);
        Route::get('{id}', [EstatePaymentController::class, 'show']);
        Route::put('{id}', [EstatePaymentController::class, 'update']);
        Route::delete('{id}', [EstatePaymentController::class, 'destroy']);
        Route::post('{id}/toggle-active', [EstatePaymentController::class, 'toggleActive']);
    });

    Route::prefix('payment-records')->group(function () {
        Route::get('/', [PaymentRecordController::class, 'index']);
        Route::post('/', [PaymentRecordController::class, 'store']);
        Route::get('my-payments', [PaymentRecordController::class, 'myPayments']);
        Route::get('overdue', [PaymentRecordController::class, 'overdue']);
        Route::get('statistics', [PaymentRecordController::class, 'statistics']);
        Route::get('{id}', [PaymentRecordController::class, 'show']);
        Route::put('{id}', [PaymentRecordController::class, 'update']);
        Route::post('{id}/verify', [PaymentRecordController::class, 'verifyPayment']);
        Route::delete('{id}', [PaymentRecordController::class, 'destroy']);
    });
});
