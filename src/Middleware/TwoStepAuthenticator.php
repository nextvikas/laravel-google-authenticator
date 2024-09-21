<?php

namespace Nextvikas\Authenticator\Middleware;

use Closure;
use DOMDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


abstract class TwoStepAuthenticator
{

    
    abstract protected function apply();

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
        $response = $next($request);


    }

}
