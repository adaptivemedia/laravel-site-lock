<?php

namespace Adaptivemedia\SiteLock;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

class SiteLock
{
    /** @var array */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config->get('site-lock');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->config['enabled']) {
            return $next($request);
        }

        if (! app()->environment($this->config['environments'])) {
            return $next($request);
        }

        if (session($this->config['session-key']) === true) {
            return $next($request);
        }

        if ($this->ipIsAllowed($request)) {
            return $next($request);
        }

        if ($this->urlIsAccessUrl($request)) {
            return $next($request);
        }

        if ($this->urlIsWhitelisted($request)) {
            return $next($request);
        }

        return response($this->config['error-message'], $this->config['error-http-response']);
    }

    private function ipIsAllowed(Request $request): bool
    {
        $allowedIps = $this->config['allowed-ips'];
        if (! $allowedIps) {
            return false;
        }

        if (is_string($allowedIps)) {
            $allowedIps = array_map('trim', explode(',', $allowedIps));
        }

        return in_array($request->getClientIp(), (array) $allowedIps);
    }

    private function urlIsAccessUrl(Request $request): bool
    {
        $accessUrl = $this->config['access-url'];

        if ($accessUrl === false) {
            return false;
        }

        return ltrim($request->getRequestUri(), '/') === ltrim($accessUrl, '/');
    }

    private function urlIsWhitelisted(Request $request): bool
    {
        $whitelistedUrls = array_map(function ($url) {
            return ltrim($url, '/');
        }, $this->config['whitelisted-urls'] ?? []);

        if (count($whitelistedUrls) === 0) {
            return false;
        }

        return in_array(ltrim($request->getRequestUri(), '/'), $whitelistedUrls);
    }
}
