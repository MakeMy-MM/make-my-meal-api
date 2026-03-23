<?php

use App\Utils\RoutePatterns;
use Illuminate\Support\Facades\Route;

Route::patterns(RoutePatterns::getPatterns());

Route::get('/', function () {
    return response()->json(['status' => 'ok']);
});

require __DIR__ . '/versionning/v1/routes.php';
