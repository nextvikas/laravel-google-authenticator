<?php

use Nextvikas\Authenticator\Http\Controllers\AuthenticatorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Nextvikas\Authenticator\Middleware\AuthChecker;


// Define a route group that applies the specified guard and the AuthChecker middleware.
Route::middleware([AuthChecker::class])->group(function () {

     // Retrieve the guard name from the configuration.
     $guard_path = Config::get('authenticator.guard_path');

    // Route for displaying the two-step verification page.
    Route::get($guard_path.'/verify-two-step', [AuthenticatorController::class, 'verify_two_step'])
         ->name('authenticator.verify'); // Name the route for easy reference.

    // Route for processing the two-step verification form submission.
    Route::post($guard_path.'/verify-two-step', [AuthenticatorController::class, 'verify_two_step_process']);
    
    // Route for displaying the QR code scanning page for two-step verification.
    Route::get($guard_path.'/scan-two-step', [AuthenticatorController::class, 'scan_two_step'])
         ->name('authenticator.scan'); // Name the route for easy reference.

    // Route for processing the QR code scan form submission.
    Route::post($guard_path.'/scan-two-step', [AuthenticatorController::class, 'scan_two_step_process']);
});
