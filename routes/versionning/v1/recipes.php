<?php

use App\Domain\Recipe\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::prefix('users/{user}/recipes')->middleware('auth:api')->group(function () {
    Route::post('/', [RecipeController::class, 'create']);
});
