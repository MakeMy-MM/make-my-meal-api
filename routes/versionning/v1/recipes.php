<?php

use App\Domain\Recipe\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::prefix('users/{user}/recipes')->middleware('auth:api')->group(function () {
    Route::get('/', [RecipeController::class, 'index']);
    Route::post('/', [RecipeController::class, 'create']);

    Route::prefix('{recipe}')->group(function () {
        Route::delete('/', [RecipeController::class, 'destroy']);
    });
});
