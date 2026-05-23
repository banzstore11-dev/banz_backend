<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->away('https://banzstore.com', 301);
});

Route::fallback(function () {
    return redirect()->away('https://banzstore.com', 301);
});
