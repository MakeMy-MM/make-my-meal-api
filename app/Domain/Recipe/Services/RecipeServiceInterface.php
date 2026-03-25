<?php

namespace App\Domain\Recipe\Services;

use App\Domain\Recipe\DTOs\CreateRecipeDTO;
use App\Domain\Recipe\Models\Recipe;

interface RecipeServiceInterface
{
    public function create(CreateRecipeDTO $dto): Recipe;
}
