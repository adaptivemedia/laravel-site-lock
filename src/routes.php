<?php

Route::get(config('site-lock.access-url'), function () {
    session()->put(config('site-lock.session-key'), true);
    return redirect(config('site-lock.redirect-url'));
})->middleware('web');
