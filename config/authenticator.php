<?php
/*
 * This file is part of laravel-google-authenticator.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        'enabled' => env('VERIFICATION_ENABLED', true),
   
        // The route name for the login page. 
        // This specifies where the user will be redirected for login, with a default route 'account.login'.
        'login_route_name' => env('VERIFICATION_LOGIN_ROUTE_NAME', 'admin.login'),
    
        // The name of the guard used for login. 
        // It is pulled from the .env file with 'web' as the default guard.
        'login_guard_name' => env('VERIFICATION_LOGIN_GUARD_NAME', 'admin'),
    
        // The main layout used for the verification views.
        // Defaults to 'layouts.app', but can be overridden via the .env file.
        'main_layout' => env('VERIFICATION_MAIN_LAYOUT', 'layouts.app'),
    
        // The route name for logout functionality. 
        // Default value is 'false'. If a route is set here, the verification page will show a logout button. 
        // Otherwise, the logout button will be hidden.
        'logout_route_name' => env('VERIFICATION_LOGOUT_ROUTE_NAME', 'admin.logout'),
    
        // The route name for a successful verification. 
        // If set to false (default), the user will be redirected to the root page after successful verification. 
        // Otherwise, it will redirect to the specified route name.
        'success_route_name' => env('VERIFICATION_SUCCESS_ROUTE_NAME', 'admin.home'),
    ],

    /*
    * You can pass parameters to middleware in Laravel using a format like 'authenticator:account'. 
    * Please note that whatever value you specify here will be received in your middleware. For instance, 
    * if you write 'accountsecure', your middleware should be set up to handle it as 'authenticator:accountsecure'.
    */
    'account' => [
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
        'logout_route_name' => env('VERIFICATION_LOGOUT_ROUTE_NAME', 'account.logout'),
    
        // The route name for a successful verification. 
        // If set to false (default), the user will be redirected to the root page after successful verification. 
        // Otherwise, it will redirect to the specified route name.
        'success_route_name' => env('VERIFICATION_SUCCESS_ROUTE_NAME', 'account.home'),
    ]
];
 
