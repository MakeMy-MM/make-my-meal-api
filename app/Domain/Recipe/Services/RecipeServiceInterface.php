<?php

namespace App\Domain\Recipe\Services;

use App\Domain\Recipe\DTOs\CreateRecipeDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeDTO;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface RecipeServiceInterface
{
    public function create(CreateRecipeDTO $dto): Recipe;

    public function update(UpdateRecipeDTO $dto): Recipe;

    public function delete(Recipe $recipe): void;

    /** @return Collection<int, Recipe> */
    public function getByUser(User $user): Collection;
}
