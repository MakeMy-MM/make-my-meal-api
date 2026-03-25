<?php

namespace App\Domain\Recipe\Providers;

use App\Domain\Recipe\Services\RecipeService;
use App\Domain\Recipe\Services\RecipeServiceInterface;
use Illuminate\Support\ServiceProvider;

class RecipeServiceProvider extends ServiceProvider
{
    /** @var array<string, string> */
    public array $bindings = [
        RecipeServiceInterface::class => RecipeService::class,
    ];
}
