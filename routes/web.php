<?php

use Nextvikas\Authenticator\Http\Controllers\AuthenticatorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use Nextvikas\Authenticator\Middleware\AuthChecker;

Route::middleware(['web', AuthChecker::class])->group(function () {

     $guard_path = Config::get('authenticator');
     unset($guard_path['otp_settings']);

     if(!empty($guard_path)) {
          foreach($guard_path as $key => $guardpath) {

               Route::get($key.'/verify-two-step', [AuthenticatorController::class, 'verify_two_step'])
               ->name('authenticator.'.$key.'.verify');

               Route::post($key.'/verify-two-step', [AuthenticatorController::class, 'verify_two_step_process'])->name('authenticator.'.$key.'.verifypost');

               Route::get($key.'/scan-two-step', [AuthenticatorController::class, 'scan_two_step'])
               ->name('authenticator.'.$key.'.scan');

               Route::post($key.'/scan-two-step', [AuthenticatorController::class, 'scan_two_step_process'])->name('authenticator.'.$key.'.scanpost');
          }
     }
});
