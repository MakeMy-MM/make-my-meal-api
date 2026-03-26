<?php

namespace App\Domain\Recipe\Services;

use App\Domain\Recipe\DTOs\CreateRecipeDTO;
use App\Domain\Recipe\DTOs\CreateRecipeIngredientDTO;
use App\Domain\Recipe\DTOs\CreateRecipeStepDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeIngredientDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeIngredientItemDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeStepDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeStepItemDTO;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Models\RecipeIngredient;
use App\Domain\Recipe\Models\RecipeStep;
use App\Domain\Recipe\Repositories\RecipeRepository;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Webmozart\Assert\Assert;

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

    public function update(UpdateRecipeDTO $dto): Recipe
    {
        return DB::transaction(function () use ($dto) {
            $recipe = $this->repository->update($dto);

            if ($dto->hasSteps()) {
                /** @var array<int, UpdateRecipeStepItemDTO> $steps */
                $steps = $dto->getSteps();
                $this->mergeSteps($recipe, $steps);
            }

            if ($dto->hasIngredients()) {
                /** @var array<int, UpdateRecipeIngredientItemDTO> $ingredients */
                $ingredients = $dto->getIngredients();
                $this->mergeIngredients($recipe, $ingredients);
            }

            return $recipe->load(['steps', 'recipeIngredients.ingredient']);
        });
    }

    public function delete(Recipe $recipe): void
    {
        DB::transaction(fn() => $this->repository->delete($recipe));
    }

    /** @return Collection<int, Recipe> */
    public function getByUser(User $user): Collection
    {
        return $this->repository->findByFields(
            new FieldsRecipeDTO(userId: $user->id),
            with: ['steps', 'recipeIngredients.ingredient'],
        );
    }

    /**
     * @param UpdateRecipeStepItemDTO[] $items
     */
    private function mergeSteps(Recipe $recipe, array $items): void
    {
        /** @var Collection<string, RecipeStep> $existingSteps */
        $existingSteps = $recipe->steps->keyBy('id');
        /** @var array<int, string> $payloadIds */
        $payloadIds = array_filter(array_map(fn(UpdateRecipeStepItemDTO $item) => $item->getId(), $items));

        /** @var array<int, string> $toDeleteIds */
        $toDeleteIds = $existingSteps->keys()->diff($payloadIds)->all();
        if (!empty($toDeleteIds)) {
            $this->repository->deleteStepsByIds($toDeleteIds);
        }

        foreach ($items as $item) {
            if ($item->getId() !== null) {
                /** @var RecipeStep $step */
                $step = $existingSteps->get($item->getId());
                $this->repository->updateStep(new UpdateRecipeStepDTO(
                    model: $step,
                    position: $item->getPosition(),
                    description: $item->getDescription(),
                ));
            } else {
                Assert::notNull($item->getPosition());
                Assert::notNull($item->getDescription());
                $this->repository->createStep(new CreateRecipeStepDTO(
                    position: $item->getPosition(),
                    description: $item->getDescription(),
                    recipeId: $recipe->id,
                ));
            }
        }
    }

    /**
     * @param UpdateRecipeIngredientItemDTO[] $items
     */
    private function mergeIngredients(Recipe $recipe, array $items): void
    {
        /** @var Collection<string, RecipeIngredient> $existingIngredients */
        $existingIngredients = $recipe->recipeIngredients->keyBy('id');
        /** @var string[] $payloadIds */
        $payloadIds = array_filter(array_map(fn(UpdateRecipeIngredientItemDTO $item) => $item->getId(), $items));

        /** @var array<int, string> $toDeleteIds */
        $toDeleteIds = $existingIngredients->keys()->diff($payloadIds)->all();
        if (!empty($toDeleteIds)) {
            $this->repository->deleteIngredientsByIds($toDeleteIds);
        }

        foreach ($items as $item) {
            if ($item->getId() !== null) {
                /** @var RecipeIngredient $ingredient */
                $ingredient = $existingIngredients->get($item->getId());
                $this->repository->updateIngredient(new UpdateRecipeIngredientDTO(
                    model: $ingredient,
                    position: $item->getPosition(),
                    ingredientId: $item->getIngredientId(),
                    quantity: $item->getQuantity(),
                ));
            } else {
                Assert::notNull($item->getPosition());
                Assert::notNull($item->getIngredientId());
                Assert::notNull($item->getQuantity());
                $this->repository->createIngredient(new CreateRecipeIngredientDTO(
                    position: $item->getPosition(),
                    ingredientId: $item->getIngredientId(),
                    quantity: $item->getQuantity(),
                    recipeId: $recipe->id,
                ));
            }
        }
    }
}
