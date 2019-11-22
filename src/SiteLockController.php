<?php

namespace Adaptivemedia\SiteLock;

class SiteLockController
{
    public function __invoke()
    {
        session()->put(config('site-lock.session-key'), true);
        return redirect(config('site-lock.redirect-url'));
    }
}
