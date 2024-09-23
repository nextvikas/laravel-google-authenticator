<?php
/*
* This file is part of laravel-google-authenticator.
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

return [
    // Determines whether the verification process is enabled. 
    // It pulls the value from the environment file (.env), with a default value of 'true' if not set.
    'enabled' => env('VERIFICATION_ENABLED', true),


    /*
    * This format will be displayed in the Google Authenticator app. You can customize the name however you like, and you can include user fields in the format {field}. For example, you can add {email}, {username}, {phone}, and so on.
    */
    'app_format' => 'Appname: {username}',


    // The route name for the login page. 
    // This specifies where the user will be redirected for login, with a default route 'account.login'.
    'login_route_name' => env('VERIFICATION_LOGIN_ROUTE_NAME', 'account.login'),

    // The name of the guard used for login. 
    // It is pulled from the .env file with 'web' as the default guard.
    'login_guard_name' => env('VERIFICATION_LOGIN_GUARD_NAME', 'web'),

    /*
    * The guard path is the route where you want the 2FA (Two-Factor Authentication) page to open, ensuring an extra layer of security before granting access.
    * Example : if set '/' then /verify-two-step, if set '/admin' then /admin/verify-two-step
    * It is pulled from the .env file with '/' as the default guard path.
    */
    'guard_path' => env('VERIFICATION_GUARD_PATH', '/'),

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
 
