# Laravel Google Authenticator
# Version: 1.0.0

This package provides seamless integration of **Google Authenticator** for two-step verification in **Laravel** applications. It enhances security by requiring users to enter a time-based one-time password (TOTP) generated by the **Google Authenticator app**, in addition to their primary login credentials. This ensures an extra layer of protection against unauthorized access. With an easy-to-use API, this package simplifies the implementation of two-factor authentication (2FA) and includes features like **QR code generation** and token validation


## Requirements

The current package requirements are:

- Laravel >= 7.x
- PHP >= 7.4


# Installation

**1. Add to composer.json**
```
"nextvikas/laravel-google-authenticator": "@dev"
```
or
```
composer require --prefer-dist "nextvikas/laravel-google-authenticator @dev"
```
**2. Publish the Files Using artisan vendor:publish command**
For a package, you typically use vendor:publish to copy files like migration or configuration files from the nextvikas/laravel-google-authenticator to your application.
```
php artisan vendor:publish --provider="Nextvikas\Authenticator\AuthenticatorServiceProvider"
```

**3. Run the Migration**
```
php artisan migrate --path=\vendor\nextvikas\laravel-google-authenticator\database\migrations\2024_09_22_000000_add_authenticator_columns_to_users.php
```

# Documentation

Once the extension is installed, Simply add **Authenticator** middleware to whatever you want to secure, and that's it, your work ends here and **Authenticator** begins...
```php
Route::middleware(['authenticator'])->group(function () {
  Route::get('/', [AccountController::class, 'index']);
});
```
or you can use multiple middleware in the same like:
```php
Route::middleware([ExampleMiddleware::class,'authenticator'])->group(function () {
  Route::get('/', [AccountController::class, 'index']);
});
```
or you can use in single route middleware in the same like:
```php
Route::get('/', [AccountController::class, 'index'])->middleware('authenticator');
```

Simply change default configration values on **config\authenticator.php** file or **.env** file:
```php
// config\authenticator.php

 return [
     // Determines whether the verification process is enabled. 
     // It pulls the value from the environment file (.env), with a default value of 'true' if not set.
     'enabled' => env('VERIFICATION_ENABLED', true),
 
     // The route name for the login page. 
     // This specifies where the user will be redirected for login, with a default route 'account.login'.
     'login_route_name' => env('VERIFICATION_LOGIN_ROUTE_NAME', 'account.login'),
 
     // The name of the guard used for login. 
     // It is pulled from the .env file with 'web' as the default guard.
     'login_guard_name' => env('VERIFICATION_LOGIN_GUARD_NAME', 'web'),
 
     // The main layout used for the verification views.
     // Defaults to 'layouts.app', but can be overridden via the .env file.
     'main_layout' => env('VERIFICATION_MAIN_LAYOUT', 'layouts.app'),
 
     // The route name for logout functionality. 
     // Default value is 'false'. If a route is set here, the verification page will show a logout button. 
     // Otherwise, the logout button will be hidden.
     'logout_route_name' => env('VERIFICATION_LOGOUT_ROUTE_NAME', false),
 
     // The route name for a successful verification. 
     // If set to false (default), the user will be redirected to the root page after successful verification. 
     // Otherwise, it will redirect to the specified route name.
     'success_route_name' => env('VERIFICATION_SUCCESS_ROUTE_NAME', false),
 ];
```
or

```php
// .env

VERIFICATION_ENABLED=true
VERIFICATION_LOGOUT_ROUTE_NAME=false
VERIFICATION_SUCCESS_ROUTE_NAME=false
```
