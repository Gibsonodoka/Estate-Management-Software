<?php

use Illuminate\Support\Facades\Route;
use Modules\Messaging\Http\Controllers\MessageController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::post('/', [MessageController::class, 'store']);
        Route::get('conversations', [MessageController::class, 'conversations']);
        Route::get('conversation/{userId}', [MessageController::class, 'getConversation']);
        Route::get('unread-count', [MessageController::class, 'unreadCount']);
        Route::post('{id}/mark-read', [MessageController::class, 'markAsRead']);
        Route::delete('{id}', [MessageController::class, 'destroy']);
        Route::post('broadcast', [MessageController::class, 'broadcast']);
    });
});
