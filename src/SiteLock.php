<?php

namespace Adaptivemedia\SiteLock;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
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

        $clientIp = $request->getClientIp();

        // Loop over each IP and check if the request IP is in the list of allowed IPs
        // OR if it's within the range specified.
        foreach ($allowedIps as $allowedIp) {
            if (str_contains($allowedIp, '/') && $this->ipInRange($clientIp, $allowedIp)) {
                return true;
            } elseif ($clientIp === $allowedIp) {
                return true;
            }
        }

        return false;
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
            return trim($url, '/');
        }, $this->config['whitelisted-urls'] ?? []);

        if (count($whitelistedUrls) === 0) {
            return false;
        }

        $requestUrl = trim($request->getRequestUri(), '/');

        return Str::is($whitelistedUrls, $requestUrl);
    }

    private function ipInRange(string $ip, string $allowed): bool
    {
        if (! strpos($allowed, '/')) {
            $allowed .= '/32';
        }

        // $allowed is in IP/CIDR format eg 127.0.0.1/24
        [$allowed, $netmask] = explode('/', $allowed, 2);
        $range_decimal = ip2long($allowed);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
        $netmask_decimal = ~$wildcard_decimal;

        return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
    }
}
