<?php

namespace App\Domain\Ingredient\Services;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\Models\Ingredient;

interface IngredientServiceInterface
{
    public function create(CreateIngredientDTO $dto): Ingredient;
}
