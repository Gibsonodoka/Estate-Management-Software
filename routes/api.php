<?php


// ============================================
// FILE: routes/api.php (Main API routes - if needed)
// ============================================

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Estate Platform API',
        'version' => '1.0.0',
        'endpoints' => [
            'auth' => '/api/v1/auth',
            'users' => '/api/v1/users',
            'estates' => '/api/v1/estates',
            'maintenance' => '/api/v1/maintenance-requests',
            'messages' => '/api/v1/messages',
            'visitors' => '/api/v1/visitor-logs',
            'payments' => '/api/v1/payment-records',
            'listings' => '/api/v1/listings',
            'agents' => '/api/v1/agents',
        ]
    ]);
});
