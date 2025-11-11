<?php

use Illuminate\Support\Facades\Route;
use Modules\EstateManagement\Http\Controllers\EstateManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('estatemanagements', EstateManagementController::class)->names('estatemanagement');
});
