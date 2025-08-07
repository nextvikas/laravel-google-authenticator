<?php
/*
 * This file is part of laravel-google-authenticator.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 return [
    /*
     * Common OTP generation and verification settings. These apply globally
     * and are used by default across all authentication contexts.
     * Context-specific settings will override these if defined within the context.
     */
    'otp_settings' => [
        /*
        * This format will be displayed in the Google Authenticator app. You can customize the name however you like,
        * and you can include user fields in the format {field}. For example, you can add {email}, {name}, and so on.
        * It will be automatically prefixed with your application's name if not explicitly set.
        */
        'app_format' => env('AUTHENTICATOR_APP_FORMAT', config('app.name', 'Laravel App') . ': {name}'),

        // The name of the database column in the users table to store the 2FA secret key.
        'secret_column_name' => env('AUTHENTICATOR_SECRET_COLUMN', 'authenticator_secret'), // Changed default to avoid 'authenticator' field name collision if user wants to use it for other purposes.

        // The number of digits for the OTP code (typically 6 or 8).
        'otp_digits' => env('AUTHENTICATOR_OTP_DIGITS', 6),

        // The period in seconds for which an OTP code is valid (typically 30 seconds).
        'otp_period' => env('AUTHENTICATOR_OTP_PERIOD', 30),

        // The algorithm used for HMAC-based One-Time Passwords (TOTP).
        // Common values: 'sha1', 'sha256', 'sha512'.
        'otp_algorithm' => env('AUTHENTICATOR_OTP_ALGORITHM', 'sha1'),

        // The number of allowed disparities (time steps) for verification.
        // This helps account for clock drift between the server and the user's device.
        'otp_window' => env('AUTHENTICATOR_OTP_WINDOW', 1), // 1 means +/- 30 seconds (for 30s period)
    ],


    /*
    * You can pass parameters to middleware in Laravel using a format like 'authenticator:admin'. 
    * Please note that whatever value you specify here will be received in your middleware. For instance, 
    * if you write 'newsecure', your middleware should be set up to handle it as 'authenticator:newsecure'.
    */
    'admin' => [
        // Determines whether the verification process is enabled. 
        // It pulls the value from the environment file (.env), with a default value of 'true' if not set.
        'enabled' => env('AUTHENTICATOR_ADMIN_ENABLED', true),
   
        // The route name for the login page. 
        // This specifies where the user will be redirected for login, with a default route 'admin.login'.
        'login_route_name' => env('AUTHENTICATOR_ADMIN_LOGIN_ROUTE_NAME', 'admin.login'),
    
        // The name of the guard used for login. 
        // It is pulled from the .env file with 'web' as the default guard.
        // 'login_guard_name' => 'admin',
        'login_guard_name' => env('AUTHENTICATOR_ADMIN_GUARD_NAME', 'web'),
    
        // The main layout used for the verification views.
        // Defaults to 'layouts.app', but can be overridden via the .env file.
        'main_layout' => env('AUTHENTICATOR_ADMIN_MAIN_LAYOUT', 'layouts.app'),
    
        // The route name for logout functionality. 
        // Default value is 'false'. If a route is set here, the verification page will show a logout button. 
        // Otherwise, the logout button will be hidden.
        // 'logout_route_name' => 'admin.logout',
        'logout_route_name' => env('AUTHENTICATOR_ADMIN_LOGOUT_ROUTE_NAME', false),
    
        // The route name for a successful verification. 
        // If set to false (default), the user will be redirected to the root page after successful verification. 
        // Otherwise, it will redirect to the specified route name.
        // 'success_route_name' => 'admin.home',
        'success_route_name' => env('AUTHENTICATOR_ADMIN_SUCCESS_ROUTE_NAME', false),
    ],

    /*
    * You can pass parameters to middleware in Laravel using a format like 'authenticator:account'. 
    * Please note that whatever value you specify here will be received in your middleware. For instance, 
    * if you write 'accountsecure', your middleware should be set up to handle it as 'authenticator:accountsecure'.
    */
    'account' => [
        // Determines whether the verification process is enabled. 
        // It pulls the value from the environment file (.env), with a default value of 'true' if not set.
        'enabled' => env('AUTHENTICATOR_ACCOUNT_ENABLED', true),
   
        // The route name for the login page. 
        // This specifies where the user will be redirected for login, with a default route 'account.login'.
        'login_route_name' => env('AUTHENTICATOR_ACCOUNT_LOGIN_ROUTE_NAME', 'account.login'),
    
        // The name of the guard used for login. 
        // It is pulled from the .env file with 'web' as the default guard.
        // 'login_guard_name' => 'account',
        'login_guard_name' => env('AUTHENTICATOR_ACCOUNT_GUARD_NAME', 'web'),
    
        // The main layout used for the verification views.
        // Defaults to 'layouts.app', but can be overridden via the .env file.
        'main_layout' => env('AUTHENTICATOR_ACCOUNT_MAIN_LAYOUT', 'layouts.app'),
    
        // The route name for logout functionality. 
        // Default value is 'false'. If a route is set here, the verification page will show a logout button. 
        // Otherwise, the logout button will be hidden.
        // 'logout_route_name' => 'account.logout',
        'logout_route_name' => env('AUTHENTICATOR_ACCOUNT_LOGOUT_ROUTE_NAME', false),
    
        // The route name for a successful verification. 
        // If set to false (default), the user will be redirected to the root page after successful verification. 
        // Otherwise, it will redirect to the specified route name.
        // 'success_route_name' => 'account.home',
        'success_route_name' => env('AUTHENTICATOR_ACCOUNT_SUCCESS_ROUTE_NAME', false),
    ],
];
 
