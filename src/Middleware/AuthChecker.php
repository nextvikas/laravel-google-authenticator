<?php

namespace Nextvikas\Authenticator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Nextvikas\Authenticator\Helpers\Authenticator;
use Illuminate\Support\Facades\Config;

class AuthChecker
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authenticator = (new Authenticator)->getRoute($request);
        if (empty($authenticator) || (is_array($authenticator) && in_array('authenticator', $authenticator))) {
            return $next($request);
        }

        $login_route_name = Config::get($authenticator.'.login_route_name', 'login');
        $login_guard_name = Config::get($authenticator.'.login_guard_name', 'web');

        if (!Auth::guard($login_guard_name)->check()) {
            Session::put('url.intended', $request->url());
            return redirect()->route($login_route_name);
        }
        return $next($request);
    }

}
