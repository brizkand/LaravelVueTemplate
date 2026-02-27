<?php

use Illuminate\Support\Facades\Route;

// Catch-all route for SPA (except API routes)
Route::get('/{any}', function () {
    return view(view: 'spa');
})->where('any', '^(?!api).*$');
