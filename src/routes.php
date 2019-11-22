<?php
// Load the access route but only if it is
// set so we don't load an empty url route

$accessRoute = config('site-lock.access-url');
if ($accessRoute) {
    Route::get(config('site-lock.access-url'), Adaptivemedia\SiteLock\SiteLockController::class)
        ->middleware('web');
}
