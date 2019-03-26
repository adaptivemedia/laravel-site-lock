<?php
// Load the access route but only if it is
// set so we don't load an empty url route
$accessRoute = config('site-lock.access-url');
if ($accessRoute) {
    Route::get(config('site-lock.access-url'), function () {
        session()->put(config('site-lock.session-key'), true);
        return redirect(config('site-lock.redirect-url'));
    })->middleware('web');
}
