<?php

namespace Adaptivemedia\SiteLock\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SiteLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! App::environment(config('site-lock.envs'))) {
            return $next($request);
        }

        if (session(config('site-lock.session-key')) === true) {
            return $next($request);
        }

        if (in_array(request()->getClientIp(), config('site-lock.allowed-ips'))) {
            return $next($request);
        }

        if (request()->getRequestUri() === ltrim(config('site-lock.access-url'), '/')) {
            return $next($request);
        }

        return response(config('site-lock.error-message'), 401);
    }
}
