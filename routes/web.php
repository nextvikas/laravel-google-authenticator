<?php

use Nextvikas\Authenticator\Http\Controllers\AuthenticatorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Nextvikas\Authenticator\Middleware\AuthChecker;

// Retrieve the guard name from the configuration.
$guard_name = Config::get('authenticator.login_guard_name');

// Define a route group that applies the specified guard and the AuthChecker middleware.
Route::middleware([$guard_name, AuthChecker::class])->group(function () {

    // Route for displaying the two-step verification page.
    Route::get('/verify-two-step', [AuthenticatorController::class, 'verify_two_step'])
         ->name('authenticator.verify'); // Name the route for easy reference.

    // Route for processing the two-step verification form submission.
    Route::post('/verify-two-step', [AuthenticatorController::class, 'verify_two_step_process']);
    
    // Route for displaying the QR code scanning page for two-step verification.
    Route::get('/scan-two-step', [AuthenticatorController::class, 'scan_two_step'])
         ->name('authenticator.scan'); // Name the route for easy reference.

    // Route for processing the QR code scan form submission.
    Route::post('/scan-two-step', [AuthenticatorController::class, 'scan_two_step_process']);
});
