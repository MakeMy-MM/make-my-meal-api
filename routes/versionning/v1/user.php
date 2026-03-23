<?php

use App\Domain\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [UserController::class, 'me']);
});
