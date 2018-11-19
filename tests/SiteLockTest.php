<?php

namespace Adaptivemedia\SiteLock\Tests;

use Illuminate\Foundation\Testing\TestResponse;

class SiteLockTest extends TestCase
{
    /** @test */
    public function a_user_cannot_access_url_when_enabled()
    {
        $this->assertCannotVisitSecretUrl();
    }

    /** @test */
    public function a_user_can_access_url_when_disabled()
    {
        $this->app['config']->set('site-lock.enabled', false);

        $this->assertCanVisitSecretUrl();
    }

    /** @test */
    public function a_user_can_access_url_when_site_is_unlocked_via_url()
    {
        $this
            ->get($this->config['access-url'])
            ->assertRedirect($this->config['redirect-url']);

        $this->assertCanVisitSecretUrl();
    }

    /** @test */
    public function a_user_can_access_url_when_ip_is_allowed()
    {
        $allowedIps = ['192.0.0.1', '200.0.0.1'];

        $this->assertCannotVisitSecretUrl();

        $this->app['config']->set('site-lock.allowed-ips', $allowedIps);

        $this->assertCanVisitSecretUrl($allowedIps[0]);
    }

    protected function getWithIp(string $uri, string $ip): TestResponse
    {
        return $this->call('GET', $uri, [], [], [], ['REMOTE_ADDR' => $ip]);
    }

    protected function assertCanVisitSecretUrl(string $ip = '127.0.0.1')
    {
        $this->getWithIp('/locked-url', $ip)
            ->assertStatus(200)
            ->assertSee('locked');
    }

    protected function assertCannotVisitSecretUrl(string $ip = '127.0.0.1')
    {
        $this->getWithIp('/locked-url', $ip)
            ->assertStatus($this->config['error-http-response']);
    }
}