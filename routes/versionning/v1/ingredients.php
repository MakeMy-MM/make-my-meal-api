<?php

use App\Domain\Ingredient\Http\Controllers\IngredientController;
use Illuminate\Support\Facades\Route;

Route::prefix('users/{user}/ingredients')->middleware('auth:api')->group(function () {
    Route::get('/', [IngredientController::class, 'index']);
    Route::post('/', [IngredientController::class, 'create']);
});
