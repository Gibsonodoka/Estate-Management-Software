<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// =============================
// SUPER ADMIN CONTROLLERS
// =============================
use App\Http\Controllers\Admin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Admin\EstateController as SuperAdminEstateController;
use App\Http\Controllers\Admin\PropertyController as SuperAdminPropertyController;
use App\Http\Controllers\Admin\UserController as SuperAdminUserController;
use App\Http\Controllers\Admin\TenantController as SuperAdminTenantController;
use App\Http\Controllers\Admin\PaymentController as SuperAdminPaymentController;
use App\Http\Controllers\Admin\MaintenanceController as SuperAdminMaintenanceController;
use App\Http\Controllers\Admin\LandlordController as SuperAdminLandlordController;

// =============================
// ESTATE ADMIN CONTROLLERS
// =============================
use App\Http\Controllers\EstateAdmin\DashboardController as EstateAdminDashboardController;
use App\Http\Controllers\EstateAdmin\PropertyController as EstatePropertyController;
use App\Http\Controllers\EstateAdmin\TenantController as EstateTenantController;
use App\Http\Controllers\EstateAdmin\PaymentController as EstatePaymentController;
use App\Http\Controllers\EstateAdmin\MaintenanceController as EstateMaintenanceController;
use App\Http\Controllers\EstateAdmin\AnnouncementController;
use App\Http\Controllers\EstateAdmin\VisitorController;
use App\Http\Controllers\EstateAdmin\SettingsController;
use App\Http\Controllers\EstateAdmin\LandlordController as EstateAdminLandlordController;

// =============================
// WELCOME PAGE (REDIRECTS BASED ON ROLE)
// =============================
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

// =============================
// BREEZE DASHBOARD REDIRECT
// =============================
Route::get('/dashboard', function () {
    $user = Auth::user();

    if ($user->role === 'site_admin') {
        return redirect('/admin/dashboard');
    } elseif ($user->role === 'estate_admin') {
        return redirect('/estate/dashboard');
    }

    return redirect('/');
})->middleware(['auth'])->name('dashboard');

// =============================
// PROFILE ROUTES
// =============================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =============================
// SUPER ADMIN ROUTES
// =============================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('estates', SuperAdminEstateController::class);
    Route::resource('properties', SuperAdminPropertyController::class);
    Route::resource('users', SuperAdminUserController::class);
    Route::resource('tenants', SuperAdminTenantController::class);
    Route::resource('payments', SuperAdminPaymentController::class);
    Route::resource('maintenance', SuperAdminMaintenanceController::class);
    Route::resource('landlords', SuperAdminLandlordController::class);

    // Extra landlord-related routes
    Route::get('landlords/{landlord}/properties', [SuperAdminLandlordController::class, 'properties'])->name('landlords.properties');
    Route::get('landlords/{landlord}/tenants', [SuperAdminLandlordController::class, 'tenants'])->name('landlords.tenants');
    Route::get('landlords/{landlord}/maintenance-requests', [SuperAdminLandlordController::class, 'maintenanceRequests'])->name('landlords.maintenance-requests');
    Route::get('landlords/{landlord}/payment-records', [SuperAdminLandlordController::class, 'paymentRecords'])->name('landlords.payment-records');
});

// =============================
// ESTATE ADMIN ROUTES
// =============================
Route::middleware(['auth'])->prefix('estate')->name('estate.')->group(function () {
    Route::get('/dashboard', [EstateAdminDashboardController::class, 'index'])->name('dashboard');

    // Properties, Tenants, Payments, Maintenance
    Route::resource('properties', EstatePropertyController::class);
    Route::resource('tenants', EstateTenantController::class);
    Route::get('/payments', [EstatePaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [EstatePaymentController::class, 'show'])->name('payments.show');
    Route::resource('maintenance', EstateMaintenanceController::class);

    // Landlords
    Route::resource('landlords', EstateAdminLandlordController::class);
    Route::get('landlords/{landlord}/properties', [EstateAdminLandlordController::class, 'properties'])->name('landlords.properties');
    Route::get('landlords/{landlord}/tenants', [EstateAdminLandlordController::class, 'tenants'])->name('landlords.tenants');
    Route::get('landlords/{landlord}/maintenance-requests', [EstateAdminLandlordController::class, 'maintenanceRequests'])->name('landlords.maintenance-requests');
    Route::get('landlords/{landlord}/payment-records', [EstateAdminLandlordController::class, 'paymentRecords'])->name('landlords.payment-records');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);

    // Visitors
    Route::resource('visitors', VisitorController::class);

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
