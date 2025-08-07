<?php

namespace Nextvikas\Authenticator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class TwoStepAuthenticator
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        if (empty($role)) {
            return $next($request);
        }

        $authenticator = 'authenticator.' . $role;
        $enabled = Config::get($authenticator . '.enabled', false);

        if (!$enabled) {
            return $next($request);
        }

        $login_route_name = Config::get($authenticator . '.login_route_name', 'login');
        $login_guard_name = Config::get($authenticator . '.login_guard_name', 'web');
        $secretColumn = Config::get('authenticator.otp_settings.secret_column_name', 'authenticator_secret');

        $user = Auth::guard($login_guard_name)->user();

        if (!$user) {
            return redirect()->route($login_route_name);
        }

        if (!Session::has('TwoStepAuthenticator' . $role)) {
            Session::put('url.intended', $request->url());
            if (!empty($user->{$secretColumn})) {
                return redirect()->route('authenticator.' . $role . '.verify');
            } else {
                return redirect()->route('authenticator.' . $role . '.scan');
            }
        }

        return $next($request);
    }
}
