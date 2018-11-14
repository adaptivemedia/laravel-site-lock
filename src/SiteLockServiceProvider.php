<?php

namespace Adaptivemedia\SiteLock;

use Illuminate\Support\ServiceProvider;

class SiteLockServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/site-lock.php' => config_path('site-lock.php'),
            ], 'config');
        }

        $this->mergeConfigFrom(__DIR__.'/../config/site-lock.php', 'site-lock');
    }

    public function register()
    {
    }
}
