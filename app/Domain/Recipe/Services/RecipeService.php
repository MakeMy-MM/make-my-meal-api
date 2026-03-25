<?php

namespace App\Domain\Recipe\Services;

use App\Domain\Recipe\DTOs\CreateRecipeDTO;
use App\Domain\Recipe\DTOs\CreateRecipeIngredientDTO;
use App\Domain\Recipe\DTOs\CreateRecipeStepDTO;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Repositories\RecipeRepository;
use Illuminate\Support\Facades\DB;

class RecipeService implements RecipeServiceInterface
{
    public function __construct(
        private readonly RecipeRepository $repository,
    ) {}

    public function create(CreateRecipeDTO $dto): Recipe
    {
        return DB::transaction(function () use ($dto) {
            $recipe = $this->repository->create($dto);

            foreach ($dto->getSteps() as $step) {
                $this->repository->createStep(CreateRecipeStepDTO::fromFields($step, $recipe->id));
            }

            foreach ($dto->getIngredients() as $ingredient) {
                $this->repository->createIngredient(CreateRecipeIngredientDTO::fromFields($ingredient, $recipe->id));
            }

            return $recipe->load(['steps', 'recipeIngredients.ingredient']);
        });
    }
}
