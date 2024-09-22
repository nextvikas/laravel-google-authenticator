<?php

namespace Nextvikas\Authenticator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $login_route_name = Config('authenticator.login_route_name');
        $login_guard_name = Config('authenticator.login_guard_name');

        if (!Auth::guard($login_guard_name)->check()) {
            return redirect()->route($login_route_name);
        }
        return $next($request);
    }

}
