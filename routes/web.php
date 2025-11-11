<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Admin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Admin\EstateController as SuperAdminEstateController;
use App\Http\Controllers\Admin\PropertyController as SuperAdminPropertyController;
use App\Http\Controllers\Admin\UserController as SuperAdminUserController;
use App\Http\Controllers\Admin\TenantController as SuperAdminTenantController;
use App\Http\Controllers\Admin\PaymentController as SuperAdminPaymentController;
use App\Http\Controllers\Admin\MaintenanceController as SuperAdminMaintenanceController;

use App\Http\Controllers\EstateAdmin\DashboardController as EstateAdminDashboardController;
use App\Http\Controllers\EstateAdmin\PropertyController as EstatePropertyController;
use App\Http\Controllers\EstateAdmin\TenantController as EstateTenantController;
use App\Http\Controllers\EstateAdmin\PaymentController as EstatePaymentController;
use App\Http\Controllers\EstateAdmin\MaintenanceController as EstateMaintenanceController;
use App\Http\Controllers\EstateAdmin\AnnouncementController;
use App\Http\Controllers\EstateAdmin\VisitorController;
use App\Http\Controllers\EstateAdmin\SettingsController;

// Welcome page for guests
Route::get('/', function () {
    if (!Auth::check()) {
        return view('welcome');
    }

    $user = Auth::user();
    if ($user->role === 'site_admin') {
        return redirect('/admin/dashboard');
    } elseif ($user->role === 'estate_admin') {
        return redirect('/estate/dashboard');
    }

    return view('welcome');
});

// Breeze dashboard redirect
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->role === 'site_admin') {
        return redirect('/admin/dashboard');
    } elseif ($user->role === 'estate_admin') {
        return redirect('/estate/dashboard');
    }

    return redirect('/');
})->middleware(['auth'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// SUPER ADMIN ROUTES
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('estates', SuperAdminEstateController::class);
    Route::resource('properties', SuperAdminPropertyController::class);
    Route::resource('users', SuperAdminUserController::class);
    Route::resource('tenants', SuperAdminTenantController::class);
    Route::resource('payments', SuperAdminPaymentController::class);
    Route::resource('maintenance', SuperAdminMaintenanceController::class);
});

// ESTATE ADMIN ROUTES
Route::middleware(['auth'])->prefix('estate')->name('estate.')->group(function () {
    Route::get('/dashboard', [EstateAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::resource('properties', EstatePropertyController::class);
    Route::resource('tenants', EstateTenantController::class);
    Route::get('/payments', [EstatePaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [EstatePaymentController::class, 'show'])->name('payments.show');
    Route::resource('maintenance', EstateMaintenanceController::class);
    Route::resource('announcements', AnnouncementController::class);
    Route::resource('visitors', VisitorController::class);
});

require __DIR__.'/auth.php';
