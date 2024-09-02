<?php

namespace Adaptivemedia\SiteLock\Tests;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;

class SiteLockTest extends TestCase
{
    #[Test]
    public function a_user_cannot_access_url_when_enabled()
    {
        $this->assertCannotVisitSecretUrl();
    }

    #[Test]
    public function a_user_can_access_url_when_disabled()
    {
        $this->app['config']->set('site-lock.enabled', false);

        $this->assertCanVisitSecretUrl();
    }

    #[Test]
    public function a_user_can_access_url_when_site_is_unlocked_via_url()
    {
        $this
            ->get($this->config['access-url'])
            ->assertRedirect($this->config['redirect-url']);

        $this->assertCanVisitSecretUrl();
    }

    #[Test]
    public function a_user_can_access_url_when_ip_is_allowed()
    {
        $allowedIps = ['192.0.0.1', '200.0.0.1'];

        $this->assertCannotVisitSecretUrl();

        $this->app['config']->set('site-lock.allowed-ips', $allowedIps);

        $this->assertCanVisitSecretUrl('/locked-url', $allowedIps[0]);
    }

    #[Test]
    public function a_user_can_access_url_when_url_is_whitelisted()
    {
        app('config')->set('site-lock.whitelisted-urls', ['locked-url']);
        $this->assertCanVisitSecretUrl();
        $this->assertCannotVisitSecretUrl('api/another-locked-url');

        // Wildcard example
        app('config')->set('site-lock.whitelisted-urls', ['api/another-*']);
        $this->assertCanVisitSecretUrl('api/another-locked-url');
        $this->assertCannotVisitSecretUrl();
    }

    #[Test]
    public function a_user_cannot_access_site_if_access_url_is_false()
    {
        app('config')->set('site-lock.access-url', false);
        $this->assertCannotVisitSecretUrl();
    }

    protected function getWithIp($uri, $ip): TestResponse
    {
        return $this->call('GET', $uri, [], [], [], ['REMOTE_ADDR' => $ip]);
    }

    protected function assertCanVisitSecretUrl($url = '/locked-url', $ip = '127.0.0.1')
    {
        $this->getWithIp($url, $ip)
            ->assertStatus(200)
            ->assertSee('locked');
    }

    protected function assertCannotVisitSecretUrl($url = '/locked-url', $ip = '127.0.0.1')
    {
        $this->getWithIp($url, $ip)
            ->assertStatus($this->config['error-http-response']);
    }
}
