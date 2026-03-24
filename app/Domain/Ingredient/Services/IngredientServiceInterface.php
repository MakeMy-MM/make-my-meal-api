<?php

namespace App\Domain\Ingredient\Services;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface IngredientServiceInterface
{
    public function create(CreateIngredientDTO $dto): Ingredient;

    /** @return Collection<int, Ingredient> */
    public function getByUser(User $user): Collection;
}
