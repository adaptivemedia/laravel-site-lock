<?php

namespace Adaptivemedia\SiteLock\Tests;

use Adaptivemedia\SiteLock\SiteLock;
use Adaptivemedia\SiteLock\SiteLockServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use Orchestra\Testbench\TestCase as Orchestra;
use Route;

class TestCase extends Orchestra
{
    /** @var array */
    protected $config = [];

    public function setUp()
    {
        parent::setUp();

        $this->registerMiddleWare();
        $this->config = $this->app['config']->get('site-lock');
        $this->setUpRoutes($this->app);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        foreach (require('./config/site-lock.php') as $key => $val) {
            $app['config']->set('site-lock.' . $key, $val);
        }
        $app['config']->set('site-lock.environments', ['testing']);
        $app['config']->set('app.key', 'base64:Y8OdIu1cSpsuMCeDGf1unPW3DsmS2r7XQCWUtzbzaQ1=');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SiteLockServiceProvider::class,
        ];
    }

    protected function registerMiddleware()
    {
        $this->app[Router::class]->aliasMiddleware('siteLock', SiteLock::class);
    }

    /**
     * @param Application $app
     */
    protected function setUpRoutes($app)
    {
        $this->app->get('router')->setRoutes(new RouteCollection());

        require './src/routes.php';

        Route::any('/locked-url', [
            'middleware' => ['web', 'siteLock'],
            function () {
                return 'locked';
            },
        ]);
    }
}