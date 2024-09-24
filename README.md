# Laravel Google Authenticator

[![Total Downloads](https://img.shields.io/packagist/dt/nextvikas/laravel-google-authenticator.svg?logo=github&logoColor=white&style=flat-square)](https://packagist.org/packages/nextvikas/laravel-google-authenticator)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![GitHub tag](https://img.shields.io/badge/composer-laravel--extension-orange.svg)]()



This package provides seamless integration of **Google Authenticator** for two-step verification in **Laravel** applications. It enhances security by requiring users to enter a time-based one-time password (TOTP) generated by the **Google Authenticator app**, in addition to their primary login credentials. This ensures an extra layer of protection against unauthorized access. With an easy-to-use API, this package simplifies the implementation of two-factor authentication (2FA) and includes features like **QR code generation** and token validation


## Requirements

The current package requirements are:

- Laravel >= 7.x
- PHP >= 7.4


# Installation

**1. Add to composer.json**
```
composer require nextvikas/laravel-google-authenticator
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
Route::middleware(['authenticator:admin'])->group(function () {
  Route::get('/', [AccountController::class, 'index']);
});
```
or you can use multiple middleware in the same like:
```php
Route::middleware([ExampleMiddleware::class,'authenticator:admin'])->group(function () {
  Route::get('/', [AccountController::class, 'index']);
});
```
or you can use in single route middleware in the same like:
```php
Route::get('/admin', [AccountController::class, 'index'])->middleware('authenticator:admin');
```
or like:
```php
Route::get('/account', [AccountController::class, 'index'])->middleware('authenticator:account');
```
Note: Please note that any names you write in middleware **'authenticator:'**, alongside must be included in the configuration file **'config\authenticator.php'**

Simply change default configration values on **config\authenticator.php** file:
```php
// config\authenticator.php

 return [
    /*
    * This format will be displayed in the Google Authenticator app. You can customize the name however you like, and you can include user fields in the format {field}. For example, you can add {email}, {username}, {phone}, and so on.
    */
    'app_format' => 'Appname: {username}',


    /*
    * You can pass parameters to middleware in Laravel using a format like 'authenticator:admin'. 
    * Please note that whatever value you specify here will be received in your middleware. For instance, 
    * if you write 'newsecure', your middleware should be set up to handle it as 'authenticator:newsecure'.
    */
    'admin' => [
        // Determines whether the verification process is enabled. 
        // It pulls the value from the environment file (.env), with a default value of 'true' if not set.
        'enabled' => true,
   
        // The route name for the login page. 
        // This specifies where the user will be redirected for login, with a default route 'admin.login'.
        'login_route_name' => 'admin.login',
    
        // The name of the guard used for login. 
        // It is pulled from the .env file with 'web' as the default guard.
        // 'login_guard_name' => 'admin',
        'login_guard_name' => 'web',
    
        // The main layout used for the verification views.
        // Defaults to 'layouts.app', but can be overridden via the .env file.
        'main_layout' => 'layouts.app',
    
        // The route name for logout functionality. 
        // Default value is 'false'. If a route is set here, the verification page will show a logout button. 
        // Otherwise, the logout button will be hidden.
        // 'logout_route_name' => 'admin.logout',
        'logout_route_name' => false,
    
        // The route name for a successful verification. 
        // If set to false (default), the user will be redirected to the root page after successful verification. 
        // Otherwise, it will redirect to the specified route name.
        // 'success_route_name' => 'admin.home',
        'success_route_name' => false,
    ],

    /*
    * You can pass parameters to middleware in Laravel using a format like 'authenticator:account'. 
    * Please note that whatever value you specify here will be received in your middleware. For instance, 
    * if you write 'accountsecure', your middleware should be set up to handle it as 'authenticator:accountsecure'.
    */
    'account' => [
        // Determines whether the verification process is enabled. 
        // It pulls the value from the environment file (.env), with a default value of 'true' if not set.
        'enabled' => true,
   
        // The route name for the login page. 
        // This specifies where the user will be redirected for login, with a default route 'account.login'.
        'login_route_name' => 'account.login',
    
        // The name of the guard used for login. 
        // It is pulled from the .env file with 'web' as the default guard.
        // 'login_guard_name' => 'account',
        'login_guard_name' => 'web',
    
        // The main layout used for the verification views.
        // Defaults to 'layouts.app', but can be overridden via the .env file.
        'main_layout' => 'layouts.app',
    
        // The route name for logout functionality. 
        // Default value is 'false'. If a route is set here, the verification page will show a logout button. 
        // Otherwise, the logout button will be hidden.
        // 'logout_route_name' => 'account.logout',
        'logout_route_name' => false,
    
        // The route name for a successful verification. 
        // If set to false (default), the user will be redirected to the root page after successful verification. 
        // Otherwise, it will redirect to the specified route name.
        // 'success_route_name' => 'account.home',
        'success_route_name' => false,
    ],
];
```
You can also modify the view file from (**\resources\views\vendor\authenticator\scan.blade.php** and **\resources\views\vendor\authenticator\verify.blade.php**) location with your own code.