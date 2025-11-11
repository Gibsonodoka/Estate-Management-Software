<?php

use Illuminate\Support\Facades\Route;
use Modules\PaymentProcessing\Http\Controllers\PaymentProcessingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('paymentprocessings', PaymentProcessingController::class)->names('paymentprocessing');
});
