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

        if ($this->ipIsAllowed()) {
            return $next($request);
        }

        if (ltrim(request()->getRequestUri(), '/') === ltrim(config('site-lock.access-url'), '/')) {
            return $next($request);
        }

        return response(config('site-lock.error-message'), 401);
    }

    private function ipIsAllowed(): bool
    {
        $allowedIps = config('site-lock.allowed-ips');
        if (! $allowedIps) {
            return false;
        }

        if (is_string($allowedIps)) {
            $allowedIps = array_map('trim', explode(',', $allowedIps));
        }

        return in_array(request()->getClientIp(), (array) $allowedIps);
    }
}
