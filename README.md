# SiteLock - prevent access to your site

Sometimes you don't want anybody to access your site. Use cases include:

- Site is under development
- Site is on a staging/testing server
- Site is an internal tool

This package adds a global web middleware that protects all routes until it's unlocked.

## Installation

You can install the package via composer:

```bash
composer require adaptivemedia/laravel-site-lock
```

You *MUST* publish the config file with:

```bash
php artisan vendor:publish --provider="Adaptivemedia\SiteLock\SiteLockServiceProvider" --tag="config"
```

This is the content of the config file:

```php
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
     * The url that will unlock the site.
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

```

## Usage

Add the middleware to the `$middlewareGroups` array in `App\Http\Kernel.php`:

```
protected $middlewareGroups = [
    'web' => [
        ...
        \Adaptivemedia\SiteLock\SiteLock::class,
        ...
    ]
]
```

When added, all routes are locked if the request is on a matching environment. You can also assign this middleware to specific routes
by adding an alias to the `$routeMiddleware` variable and then attaching that alias to a route. 

### Gain access via url
You can now gain access your site by visiting the configured url.

### Gain access via IP
You can add allowed IP addresses in the `allowed-ips` config variable. You can either use a comma separated string:
```
 'allowed-ips' => '127.0.0.1,192.168.0.1'
```
or use an array:
```
'allowed-ips' => [
    '127.0.0.1',
    '192.168.0.1'
]
```
To change allowed IPs without changing code, you can use your own `env`-variable, eg. `SITE_LOCK_ALLOWED_IPS`:
```
'allowed-ips' => env('SITE_LOCK_ALLOWED_IPS')
```
And set them in your `.env`:
```
SITE_LOCK_ALLOWED_IPS="127.0.0.1, 192.168.0.1"
```

### Whitelist urls
You can whitelist individual urls so they are excluded from the middleware. A common use case could
be a hook url that a third party service is calling in your app.
```
'whitelisted-urls' => [
    '/webhook-callback-url',
],
```

### Disable site lock
If you don't want to enable site lock, just set the env variable `SITE_LOCK_ENABLED` to false.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email info@adaptivemedia.se instead of using the issue tracker.

## Credits

- [Adaptivemedia](https://github.com/adaptivemedia)
- [All Contributors](../../contributors)

## Support us

Adaptivemedia is a web agency based in Stockholm, Sweden. Visit our [website](https://adaptivemedia.se/).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
