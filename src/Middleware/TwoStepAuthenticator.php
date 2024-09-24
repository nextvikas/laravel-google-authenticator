<?php

namespace Nextvikas\Authenticator\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

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
        if(empty($role)) {
            return $next($request);
        }

        $authenticator = 'authenticator.'.$role;
        $enabled = Config($authenticator.'.enabled');

        if($enabled) {

            $login_route_name = Config($authenticator.'.login_route_name');
            $login_guard_name = Config($authenticator.'.login_guard_name');

            if (Auth::guard($login_guard_name)->check()) {
                if( ! Session::has('TwoStepAuthenticator'.$role) ) {
                    if(!empty(Auth::guard($login_guard_name)->user()->authenticator)) {
                        return redirect()->route('authenticator.'.$role.'.verify');
                    } else {
                        return redirect()->route('authenticator.'.$role.'.scan');
                    }
                }
            } else {
                return redirect()->route($login_route_name);
            }

        }
        return $next($request);
    }

}
