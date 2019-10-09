<?php

return [

    /*
     * This is the master switch to enable site lock.
     */
    'enabled' => env('SITE_LOCK_ENABLED', true),

    /*
     * Environments that the site lock should be applied to.
     */
    'environments' => ['staging', 'development'],

    /*
     * The following IP's will automatically gain access to the
     * app without having to visit the `access-url` url.
     */
    'allowed-ips' => [],

    /*
     * List of urls that are whitelisted.
     *
     * Examples:
     * /a-webhook-url
     * a-webhook-url
     * api/a-webhook-url
     * api/a-webhook-*
     */
    'whitelisted-urls' => [],

    /*
     * The url that will unlock the site. Can be `false` to ignore url access
     */
    'access-url' => '/change-this-url-to-your-own',

    /*
     * After having gained access, visitors will be redirected to this url.
     */
    'redirect-url'  => '/',

    /*
     * The session key that holds the site lock.
     */
    'session-key'   => 'site-lock',

    /*
     * Error message displayed for users without access.
     */
    'error-message' => 'Access denied',

    /*
     * HTTP response for users without access.
     */
    'error-http-response' => 401,
];
