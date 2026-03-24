<?php

namespace App\Domain\Ingredient\Providers;

use App\Domain\Ingredient\Services\IngredientService;
use App\Domain\Ingredient\Services\IngredientServiceInterface;
use Illuminate\Support\ServiceProvider;

class IngredientServiceProvider extends ServiceProvider
{
    /** @var array<string, string> */
    public array $bindings = [
        IngredientServiceInterface::class => IngredientService::class,
    ];
}
