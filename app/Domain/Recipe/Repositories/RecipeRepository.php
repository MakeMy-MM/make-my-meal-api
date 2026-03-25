<?php

namespace App\Domain\Recipe\Repositories;

use App\Domain\Recipe\DTOs\CreateRecipeDTO;
use App\Domain\Recipe\DTOs\CreateRecipeIngredientDTO;
use App\Domain\Recipe\DTOs\CreateRecipeStepDTO;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Models\RecipeIngredient;
use App\Domain\Recipe\Models\RecipeStep;
use App\DTOs\BaseFieldDTO;
use App\Repositories\ModelRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends ModelRepository<Recipe>
 *
 * @method Collection<int, Recipe> findByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 * @method Recipe|null              findFirstByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 */
class RecipeRepository extends ModelRepository
{
    public function __construct(Recipe $model)
    {
        parent::__construct($model);
    }

    public function create(CreateRecipeDTO $dto): Recipe
    {
        return Recipe::create($dto->toArray());
    }

    public function createStep(CreateRecipeStepDTO $dto): RecipeStep
    {
        return RecipeStep::create($dto->toArray());
    }

    public function createIngredient(CreateRecipeIngredientDTO $dto): RecipeIngredient
    {
        return RecipeIngredient::create($dto->toArray());
    }
}
