<?php

use Nextvikas\Authenticator\Http\Controllers\AuthenticatorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Nextvikas\Authenticator\Middleware\AuthChecker;

// Define a route group that applies the specified guard and the AuthChecker middleware.
Route::middleware([AuthChecker::class])->group(function () {

     // Retrieve the guard name from the configuration.
     $guard_path = Config::get('authenticator');
     if(!empty($guard_path)) {
          foreach($guard_path as $key => $guardpath) {
               // Route for displaying the two-step verification page.
               Route::get($key.'/verify-two-step', [AuthenticatorController::class, 'verify_two_step'])
               ->name('authenticator.'.$key.'.verify'); // Name the route for easy reference.

               // Route for processing the two-step verification form submission.
               Route::post($key.'/verify-two-step', [AuthenticatorController::class, 'verify_two_step_process'])->name('authenticator.'.$key.'.verifypost');

               // Route for displaying the QR code scanning page for two-step verification.
               Route::get($key.'/scan-two-step', [AuthenticatorController::class, 'scan_two_step'])
               ->name('authenticator.'.$key.'.scan'); // Name the route for easy reference.

               // Route for processing the QR code scan form submission.
               Route::post($key.'/scan-two-step', [AuthenticatorController::class, 'scan_two_step_process'])->name('authenticator.'.$key.'.scanpost');
          }
     }
});
