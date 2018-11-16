# SiteLock - prevent access to your site

Sometimes you don't want anybody to access your site. Use cases include:

- Site is under development
- Site is on a staging/testing server
- Site is an internal tool

This package adds a global web middleware that protects all routes until it's unlocked by visiting a pre defined url.

## Installation

You can install the package via composer:

```bash
composer require adaptivemedia/laravel-site-lock
```

If you're on Laravel 5.5 or below, you must register the service provider:

```php
// config/app.php
'providers' => [
    ...
    Adaptivemedia\SiteLock\SiteLockServiceProvider::class,
];
```

You can optionally publish the config file with:

```bash
php artisan vendor:publish --provider="Adaptivemedia\SiteLock\SiteLockServiceProvider" --tag="config"
```

This is the content of the config file:

```php
<?php

return [
    'envs'          => ['staging', 'development'],
    'allowed-ips'   => [],
    'access-url'    => '/force-access',
    'redirect-url'  => '/',
    'session-key'   => 'site-lock',
    'error-message' => 'Access denied',
];
```

## Usage

Add the middleware to the `$middlewareGroups` array in `App\Http\Kernel.php`:

```
protected $middlewareGroups = [
    'web' => [
        ...
        \Adaptivemedia\SiteLock\Middleware\SiteLock::class,
        ...
    ]
]
```

When added, all routes are locked if the request is on a matching environment.

### Gain access via url
You can now gain access your site by visiting `/force-access`

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
