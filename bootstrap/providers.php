<?php

use App\Domain\Auth\Providers\AuthServiceProvider;
use App\Domain\Ingredient\Providers\IngredientServiceProvider;
use App\Domain\Recipe\Providers\RecipeServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    IngredientServiceProvider::class,
    RecipeServiceProvider::class,
];
