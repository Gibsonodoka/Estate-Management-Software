<?php

use Illuminate\Support\Facades\Route;
use Modules\GeneralListing\Http\Controllers\GeneralListingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('generallistings', GeneralListingController::class)->names('generallisting');
});
